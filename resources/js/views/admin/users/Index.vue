<template>
    <div class="card p-4">
        <div class="surface-ground border-round p-4 mb-5 shadow-1">
            <h3 class="m-0 mb-4 font-bold text-xl text-gray-800 dark:text-white">Configuración de Perfil</h3>
            
            <div class="flex flex-column md:flex-row align-items-start md:align-items-center gap-5">
                
                <div class="flex flex-column align-items-center gap-3">
                    <Avatar 
                        :image="avatarPreview || authUser?.avatar_url || null" 
                        :label="(!avatarPreview && !authUser?.avatar_url) ? authUser?.name?.[0] : ''" 
                        size="xlarge" 
                        shape="circle" 
                        class="w-6rem h-6rem text-4xl shadow-2 bg-primary text-white" 
                    />
                    <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="onFileChange" />
                    <Button label="Cambiar Foto" icon="pi pi-camera" size="small" outlined @click="$refs.fileInput.click()" />
                </div>

                <div class="flex-1 w-full md:w-auto">
                    <div class="flex flex-column gap-2">
                        <label for="name" class="font-bold">Nombre de usuario</label>
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-user"></i>
                            </span>
                            <InputText id="name" v-model="profileName" placeholder="Escribe tu nombre..." />
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-auto mt-3 md:mt-0 md:align-self-end">
                    <Button label="Guardar Perfil" icon="pi pi-save" :loading="isSavingProfile" @click="saveProfile" class="w-full" />
                </div>
            </div>
        </div>

        <div class="flex justify-content-between align-items-center mb-4">
            <h2 class="m-0 font-bold text-2xl text-gray-800 dark:text-white">Mis Guías</h2>
            <Button label="Nueva Guía" icon="pi pi-plus" severity="success" @click="openNew" />
        </div>

        <DataTable :value="guides" paginator :rows="10" dataKey="id" class="p-datatable-sm shadow-2 border-round overflow-hidden">
            <template #empty> <div class="p-4 text-center">Aún no has creado ninguna guía.</div> </template>
            <Column field="id" header="ID" sortable style="width: 5rem"></Column>
            <Column field="game.title" header="Juego" sortable>
                <template #body="slotProps">
                    <span v-if="slotProps.data.game" class="font-bold text-primary">{{ slotProps.data.game.title }}</span>
                    <span v-else class="text-500">Sin juego</span>
                </template>
            </Column>
            <Column field="title" header="Título" sortable></Column>
            <Column header="Categorías">
                <template #body="slotProps">
                    <Tag v-for="cat in slotProps.data.categories" :key="cat.id" :value="cat.name" severity="info" class="mr-1" />
                </template>
            </Column>
            <Column header="Acciones" style="min-width: 14rem">
                <template #body="slotProps">
                    <div class="flex flex-column gap-2">
                        <div class="flex gap-2">
                            <Button icon="pi pi-eye" outlined rounded severity="info" @click="viewGuide(slotProps.data)" />
                            <template v-if="slotProps.data.user_id == authUser?.id || isSuperAdmin">
                                <Button icon="pi pi-pencil" outlined rounded severity="warning" @click="editGuide(slotProps.data)" />
                                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteGuide(slotProps.data)" />
                            </template>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog v-model:visible="guideDialog" :header="guide.id ? 'Editar Guía' : 'Nueva Guía'" modal class="p-fluid" :style="{width: '800px'}">
            <div class="field mb-4">
                <label for="game" class="font-bold block mb-2">Juego</label>
                <Dropdown id="game" v-model="guide.game_id" :options="allGames" optionLabel="title" optionValue="id" placeholder="Selecciona un juego" :filter="true" class="w-full" />
            </div>
            <div class="field mb-4">
                <label for="title" class="font-bold block mb-2">Título</label>
                <InputText id="title" v-model.trim="guide.title" required placeholder="Escribe el título..." />
            </div>
            <div class="field mb-4">
                <label for="content" class="font-bold block mb-2">Contenido</label>
                <Editor v-model="guide.content" editorStyle="height: 400px" @load="onEditorLoad" />
            </div>
            <div class="field mb-4">
                <label class="font-bold block mb-2">Categorías</label>
                <MultiSelect v-model="selectedCategories" :options="allCategories" optionLabel="name" optionValue="id" placeholder="Selecciona categorías" class="w-full" display="chip" />
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Guardar Guía" icon="pi pi-check" @click="saveGuide" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';
import Avatar from 'primevue/avatar';
import { authStore } from "@/store/auth";

const router = useRouter();
const auth = authStore();

// Estados
const guides = ref([]);
const allCategories = ref([]);
const allGames = ref([]); 
const selectedCategories = ref([]);
const guideDialog = ref(false);
const guide = ref({ title: '', content: '', game_id: null });

// Perfil
const fileInput = ref(null);
const avatarPreview = ref(null);
const avatarFile = ref(null);
const isSavingProfile = ref(false);
const profileName = ref(auth.user?.name || '');

// Observamos cambios en el store para actualizar el nombre si se recarga
watch(() => auth.user, (newVal) => {
    if (newVal) profileName.value = newVal.name;
}, { immediate: true });

const authUser = computed(() => auth.user);
const isSuperAdmin = computed(() => {
    if (!auth.user) return false;
    return auth.user.id == 1 || auth.user.roles?.some(role => role.name.toLowerCase().includes('admin'));
});

// Cargar Datos
const loadData = async () => {
    try {
        const [resGuides, resCats, resGames] = await Promise.all([
            axios.get('/api/guides'), 
            axios.get('/api/categories'),
            axios.get('/api/games')
        ]);
        const todas = resGuides.data.data || resGuides.data;
        if (authUser.value) {
            guides.value = todas.filter(g => g.user_id === authUser.value.id);
        }
        allCategories.value = resCats.data.data || resCats.data;
        allGames.value = resGames.data.data || resGames.data;
    } catch (e) { console.error(e); }
};

// Lógica de Perfil
const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        avatarFile.value = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const saveProfile = async () => {
    if (!profileName.value) return alert("Nombre requerido");
    isSavingProfile.value = true;
    const fd = new FormData();
    fd.append('name', profileName.value);
    if (avatarFile.value) fd.append('avatar', avatarFile.value);

    try {
        const res = await axios.post('/api/user/profile', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        auth.user = res.data.user;
        avatarPreview.value = null;
        avatarFile.value = null;
        alert("Perfil guardado");
    } catch (e) { console.error(e); }
    finally { isSavingProfile.value = false; }
};

// Lógica de Guías
const openNew = () => {
    guide.value = { title: '', content: '', game_id: null };
    selectedCategories.value = [];
    guideDialog.value = true;
};
const editGuide = (data) => {
    guide.value = { ...data };
    guide.value.game_id = data.game ? data.game.id : null;
    selectedCategories.value = data.categories ? data.categories.map(c => c.id) : [];
    guideDialog.value = true;
};
const saveGuide = async () => {
    const payload = { 
        title: guide.value.title,
        content: guide.value.content,
        game_id: guide.value.game_id,
        categories: selectedCategories.value,
        user_id: guide.value.id ? guide.value.user_id : authUser.value.id
    };
    try {
        if (guide.value.id) await axios.put(`/api/guides/${guide.value.id}`, payload);
        else await axios.post('/api/guides', payload);
        guideDialog.value = false;
        loadData();
    } catch (e) { console.error(e); }
};
const deleteGuide = async (data) => {
    if (confirm("¿Borrar?")) {
        await axios.delete(`/api/guides/${data.id}`);
        loadData();
    }
};
const viewGuide = (data) => router.push({ name: 'guides.show', params: { id: data.slug } });
const hideDialog = () => guideDialog.value = false;

onMounted(loadData);
</script>
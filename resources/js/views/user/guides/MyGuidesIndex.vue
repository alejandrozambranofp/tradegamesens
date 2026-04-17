<template>
    <div class="card p-4">
        <div class="surface-ground border-round p-4 mb-5 shadow-1">
            <h3 class="m-0 mb-4 font-bold text-xl text-gray-800 dark:text-white">Configuración de Perfil</h3>
            
            <div class="flex flex-column md:flex-row align-items-start md:align-items-center gap-5">
                <div class="flex flex-column align-items-center gap-3">
                    <Avatar 
                        :image="avatarPreview || authUser?.avatar_url" 
                        :label="(!avatarPreview && !authUser?.avatar_url) ? authUser?.name?.[0]?.toUpperCase() : ''" 
                        size="xlarge" 
                        shape="circle" 
                        class="w-6rem h-6rem text-4xl shadow-2 bg-primary text-white" 
                    />
                    <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="onFileChange" />
                    <Button label="Cambiar Foto" icon="pi pi-camera" size="small" outlined @click="fileInput?.click()" />
                </div>

                <div class="flex-1 w-full md:w-auto">
                    <div class="flex flex-column gap-2">
                        <label for="name" class="font-bold">Nombre de usuario</label>
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon"><i class="pi pi-user"></i></span>
                            <InputText id="name" v-model="profileName" placeholder="Cargando nombre..." />
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
            <template #empty> 
                <div class="p-4 text-center">
                    {{ authUser ? 'Aún no has creado ninguna guía.' : 'Cargando tus guías...' }}
                </div> 
            </template>
            <Column field="id" header="ID" sortable style="width: 5rem"></Column>
            <Column field="game.title" header="Juego" sortable>
                <template #body="slotProps">
                    <span v-if="slotProps.data.game" class="font-bold text-primary">{{ slotProps.data.game.title }}</span>
                </template>
            </Column>
            <Column field="title" header="Título" sortable></Column>
            <Column header="Acciones">
                <template #body="slotProps">
                    <div class="flex gap-2">
                        <Button icon="pi pi-pencil" outlined rounded severity="warning" @click="editGuide(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteGuide(slotProps.data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { authStore } from "@/store/auth";

const auth = authStore();
const guides = ref([]);
const isSavingProfile = ref(false);
const profileName = ref('');
const avatarPreview = ref(null);
const avatarFile = ref(null);
const fileInput = ref(null);

const authUser = computed(() => auth.user);

// 1. DEFINICIÓN DE FUNCIONES
const loadGuides = async () => {
    // Protección crucial: si no hay usuario, no pedimos nada
    if (!authUser.value?.id) return;

    try {
        const res = await axios.get('/api/guides');
        const allGuides = res.data.data || res.data;
        if (Array.isArray(allGuides)) {
            guides.value = allGuides.filter(g => g.user_id === authUser.value.id);
        }
    } catch (e) {
        console.error("Error al obtener guías:", e);
    }
};

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        avatarFile.value = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const saveProfile = async () => {
    if (!profileName.value) return;
    isSavingProfile.value = true;
    const fd = new FormData();
    fd.append('name', profileName.value);
    if (avatarFile.value) fd.append('avatar', avatarFile.value);

    try {
        const res = await axios.post('/api/user/profile', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        auth.user = res.data.data;
        localStorage.setItem('user', JSON.stringify(res.data.data));
        avatarPreview.value = null;
        alert("¡Perfil actualizado!");
    } catch (e) {
        console.error(e);
    } finally {
        isSavingProfile.value = false;
    }
};

// Funciones CRUD
const openNew = () => { console.log("Nueva guía"); };
const editGuide = (g) => { console.log("Editar", g); };
const deleteGuide = (g) => { console.log("Borrar", g); };

// 2. SINCRONIZACIÓN
// Escuchamos al usuario: cuando aparezca (aunque sea un segundo tarde), cargamos todo
watch(authUser, (user) => {
    if (user) {
        profileName.value = user.name || '';
        loadGuides();
    }
}, { immediate: true });

onMounted(() => {
    if (authUser.value) {
        profileName.value = authUser.value.name || '';
        loadGuides();
    }
});
</script>
<template>
    <div class="p-4 min-h-screen" style="background-color: #0b0f19;">
        <!-- HEADER DE PERFIL -->
        <div class="p-6 rounded-2xl shadow-xl mb-8 border border-gray-800" style="background-color: #111827;">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Avatar -->
                <div class="relative">
                    <Avatar 
                        :image="authUser?.avatar_url" 
                        :label="!authUser?.avatar_url ? authUser?.name?.[0]?.toUpperCase() : ''" 
                        class="w-32 h-32 md:w-40 md:h-40 text-6xl shadow-2xl border-4 border-primary bg-primary text-white" 
                        shape="circle" 
                    />
                </div>

                <!-- Info y Bio -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-bold m-0 mb-3 text-white tracking-tight">{{ authUser?.name }}</h1>
                    <p class="text-xl text-gray-400 mb-6 line-height-3 max-w-2xl mx-auto md:mx-0">
                        {{ authUser?.bio || 'Sin biografía aún. ¡Cuéntanos algo sobre ti!' }}
                    </p>

                    <!-- Stats -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-8 mt-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/20 p-2 rounded-lg">
                                <i class="pi pi-book text-primary text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm">Guías</div>
                                <div class="text-2xl font-bold text-white">{{ guides.length }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-500/20 p-2 rounded-lg">
                                <i class="pi pi-star text-yellow-500 text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm">Reseñas</div>
                                <div class="text-2xl font-bold text-white">27</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-wrap gap-4 mt-8 pt-8 border-t border-gray-800">
                <Button label="Personalizar" icon="pi pi-user-edit" severity="secondary" @click="openEditProfile" class="p-button-outlined border-gray-700 text-gray-400 hover:bg-gray-800" />
                <Button label="Mis guías" icon="pi pi-list" severity="secondary" outlined @click="scrollTo('mis-guias')" class="border-gray-700 text-gray-400 hover:bg-gray-800" />
                <Button label="Guías favoritas" icon="pi pi-heart" severity="secondary" outlined @click="scrollTo('favoritas')" class="border-gray-700 text-gray-400 hover:bg-gray-800" />
                <Button label="Volver a Inicio" icon="pi pi-home" severity="info" text @click="router.push('/')" class="ml-auto" />
            </div>
        </div>

        <!-- SECCIÓN: MIS GUÍAS -->
        <div id="mis-guias" class="mb-12">
            <div class="flex justify-between items-center mb-6 px-2">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3 m-0">
                    <span class="w-2 h-8 bg-primary rounded-full"></span>
                    Mis guías
                </h2>
                <Button label="Nueva Guía" icon="pi pi-plus" severity="success" rounded @click="openNew" class="shadow-lg shadow-green-500/20" />
            </div>

            <div v-if="guides.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2">
                <div v-for="g in guides" :key="g.id" class="flex">
                    <div class="bg-[#111827] rounded-2xl border border-gray-800 flex flex-col w-full hover:border-primary/50 transition-all duration-300 shadow-lg hover:shadow-2xl hover:-translate-y-1 overflow-hidden">
                        <!-- Imagen destacada -->
                        <div class="relative h-44 overflow-hidden bg-[#0b0f19] border-b border-gray-800 flex items-center justify-center">
                            <!-- Etiqueta de Estado -->
                            <div class="absolute top-3 right-3 z-10 shadow-md rounded-md">
                                <Tag v-if="g.status === 'published'" severity="success" value="Aprobada" />
                                <Tag v-else-if="g.status === 'pending'" value="Pendiente" class="!bg-yellow-500 !text-yellow-950 border-none font-bold" />
                                <Tag v-else-if="g.status === 'rejected'" severity="danger" value="Rechazada" />
                            </div>

                            <img v-if="g.image_url" :src="g.image_url" :alt="g.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div v-else class="text-gray-600 flex flex-col items-center gap-2">
                                <i class="pi pi-image text-3xl opacity-20"></i>
                                <span class="text-[10px] uppercase tracking-widest font-bold opacity-40">Sin imagen destacada</span>
                            </div>
                        </div>

                        <div class="p-6 flex flex-col gap-4">
                        <!-- Título -->
                        <h3 class="text-xl font-bold text-white m-0 line-clamp-2 min-h-[3.5rem] leading-tight">
                            {{ g.title }}
                        </h3>
                        
                        <!-- Acciones (Debajo del título) -->
                        <div class="flex gap-2 p-2 bg-gray-900/50 rounded-xl border border-gray-800/50">
                            <Button icon="pi pi-eye" text rounded severity="info" @click="viewGuide(g)" class="w-10 h-10" v-tooltip="'Ver guía'" />
                            <Button icon="pi pi-pencil" text rounded severity="warning" @click="editGuide(g)" class="w-10 h-10" v-tooltip="'Editar'" />
                            <Button icon="pi pi-trash" text rounded severity="danger" @click="deleteGuide(g)" class="w-10 h-10" v-tooltip="'Eliminar'" />
                        </div>

                        <!-- Info -->
                        <div class="mt-auto pt-4 border-t border-gray-800/50">
                            <div class="flex items-center gap-2 text-gray-400 text-sm">
                                <i class="pi pi-tag text-primary text-xs"></i>
                                <span class="font-medium truncate">{{ g.game?.title || 'Sin juego' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="bg-[#111827] rounded-2xl border border-gray-800 p-12 text-center text-gray-500 mx-2">
                <i class="pi pi-inbox text-4xl mb-4 block opacity-20"></i>
                Aún no has creado ninguna guía.
            </div>
        </div>

        <!-- SECCIÓN: GUÍAS FAVORITAS -->
        <div id="favoritas" class="mb-12">
            <div class="flex justify-between items-center mb-6 px-2">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3 m-0">
                    <span class="w-2 h-8 bg-red-500 rounded-full"></span>
                    Guías favoritas
                </h2>
            </div>

            <div v-if="favoriteGuides.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2">
                <div v-for="g in favoriteGuides" :key="g.id" class="flex">
                    <div class="bg-[#111827] rounded-2xl border-l-4 border-l-yellow-500 border-gray-800 flex flex-col w-full hover:border-primary/50 transition-all duration-300 shadow-lg relative overflow-hidden">
                        <!-- Imagen destacada -->
                        <div class="relative h-40 overflow-hidden bg-[#0b0f19] border-b border-gray-800 flex items-center justify-center">
                            <img v-if="g.image_url" :src="g.image_url" :alt="g.title" class="w-full h-full object-cover" />
                            <div v-else class="text-gray-600 flex flex-col items-center gap-2">
                                <i class="pi pi-image text-2xl opacity-20"></i>
                                <span class="text-[9px] uppercase tracking-widest font-bold opacity-40">Sin imagen destacada</span>
                            </div>
                        </div>

                        <div class="p-6 flex flex-col gap-4">
                        <!-- Título -->
                        <h3 class="text-xl font-bold text-white m-0 line-clamp-2 min-h-[3.5rem] leading-tight">
                            {{ g.title }}
                        </h3>

                        <!-- Acciones -->
                        <div class="flex gap-2 p-2 bg-gray-900/50 rounded-xl border border-gray-800/50">
                            <Button icon="pi pi-eye" text rounded severity="info" @click="viewGuide(g)" class="w-10 h-10" v-tooltip="'Ver guía'" />
                            <Button icon="pi pi-star-fill" text rounded severity="warning" @click="removeFavorite(g)" class="w-10 h-10" v-tooltip="'Quitar de favoritos'" />
                        </div>

                        <!-- Info -->
                        <div class="mt-auto pt-4 border-t border-gray-800/50">
                            <div class="flex items-center gap-2 text-gray-400 text-sm mb-1">
                                <i class="pi pi-tag text-primary text-xs"></i>
                                <span class="font-medium truncate">{{ g.game?.title || 'Sin juego' }}</span>
                            </div>
                            <div class="text-xs text-gray-500 italic">Por {{ g.user?.name || 'Usuario' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div v-else class="bg-[#111827] rounded-2xl border border-gray-800 p-12 text-center text-gray-500 mx-2">
                <i class="pi pi-heart text-4xl mb-4 block opacity-20"></i>
                No tienes guías guardadas en favoritos.
            </div>
        </div>

        <!-- DIÁLOGOS -->
        
        <!-- Dialog: Editar Perfil (Personalizar) -->
        <Dialog v-model:visible="profileDialog" header="Personalizar Perfil" modal class="p-fluid" :style="{width: '500px'}">
            <div class="flex flex-column align-items-center gap-3 mb-4">
                <Avatar 
                    :image="avatarPreview || authUser?.avatar_url" 
                    size="xlarge" shape="circle" 
                    class="w-8rem h-8rem shadow-2 border-2 border-primary" 
                />
                <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="onFileChange" />
                <Button label="Cambiar Foto" icon="pi pi-camera" size="small" outlined @click="fileInput?.click()" />
            </div>

            <div class="field mb-3">
                <label for="prof_name" class="font-bold">Nombre de usuario</label>
                <InputText id="prof_name" v-model="profileName" placeholder="Tu nombre..." />
            </div>

            <div class="field mb-3">
                <label for="prof_bio" class="font-bold">Biografía</label>
                <Textarea id="prof_bio" v-model="profileBio" rows="4" placeholder="Cuéntanos sobre ti..." class="w-full" />
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="profileDialog = false" />
                <Button label="Guardar Perfil" icon="pi pi-check" :loading="isSavingProfile" @click="saveProfile" />
            </template>
        </Dialog>

        <!-- Dialog: Nueva/Editar Guía -->
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
import { authStore } from "@/store/auth";
import Swal from 'sweetalert2';

// PrimeVue
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Avatar from 'primevue/avatar';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';
import Tag from 'primevue/tag';

const router = useRouter();
const auth = authStore();

// Estados Listas
const guides = ref([]);
const favoriteGuides = ref([]);
const allCategories = ref([]);
const allGames = ref([]);

// Estados Perfil
const profileDialog = ref(false);
const isSavingProfile = ref(false);
const profileName = ref('');
const profileBio = ref('');
const avatarPreview = ref(null);
const avatarFile = ref(null);
const fileInput = ref(null);

// Estados Guía
const guideDialog = ref(false);
const guide = ref({ title: '', content: '', game_id: null });
const selectedCategories = ref([]);

const authUser = computed(() => auth.user);

const loadData = async () => {
    if (!authUser.value?.id) return;

    try {
        const [resGuides, resFavs, resCats, resGames] = await Promise.all([
            axios.get('/api/guides/my-guides'), // Usamos el endpoint específico
            axios.get('/api/guides/favorites'),
            axios.get('/api/categories'),
            axios.get('/api/games')
        ]);
        
        guides.value = resGuides.data.data || resGuides.data;
        favoriteGuides.value = resFavs.data.data || resFavs.data;
        allCategories.value = resCats.data.data || resCats.data;
        allGames.value = resGames.data.data || resGames.data;
    } catch (e) {
        console.error("Error al obtener datos:", e);
    }
};

const onEditorLoad = ({ instance }) => {
    // Evitar que se añadan múltiples listeners si el editor se recarga
    if (instance.root._hasPasteListener) return;
    instance.root._hasPasteListener = true;

    // Anular el pegado automático de base64 de Quill para evitar duplicados
    instance.clipboard.addMatcher('IMG', (node, delta) => {
        if (node.src && node.src.startsWith('data:')) {
            delta.ops = []; // Vaciar el delta para que no inserte nada
        }
        return delta;
    });

    instance.root.addEventListener('paste', async (e) => {
        const clipboardData = e.clipboardData || window.clipboardData;
        if (!clipboardData) return;

        const items = clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                if (!file) continue;

                // Evitar que Quill inserte la imagen en base64 y detener otros eventos
                e.preventDefault();
                e.stopPropagation();

                const fd = new FormData();
                fd.append('image', file);

                try {
                    const res = await axios.post('/api/images/upload', fd, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    
                    const url = res.data.url;
                    
                    const range = instance.getSelection(true);
                    instance.insertEmbed(range.index, 'image', url);
                    instance.setSelection(range.index + 1);
                } catch (err) {
                    console.error("Error al subir imagen pegada:", err);
                    Swal.fire({
                        title: 'Ups...',
                        text: 'La imagen es demasiado grande. El límite es de 10MB.',
                        icon: 'error',
                        background: '#111827',
                        color: '#fff',
                        confirmButtonColor: '#3b82f6'
                    });
                }

                // Salir del bucle para no subir/insertar la imagen 2 veces si hay varios tipos en el portapapeles
                break;
            }
        }
    });
};

// LÓGICA PERFIL
const openEditProfile = () => {
    profileName.value = authUser.value.name || '';
    profileBio.value = authUser.value.bio || '';
    avatarPreview.value = null;
    profileDialog.value = true;
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
    fd.append('bio', profileBio.value || '');
    if (avatarFile.value) fd.append('avatar', avatarFile.value);

    try {
        const res = await axios.post('/api/user/profile', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        auth.user = res.data.data;
        localStorage.setItem('user', JSON.stringify(res.data.data));
        avatarPreview.value = null;
        profileDialog.value = false;
    } catch (e) {
        console.error(e);
    } finally {
        isSavingProfile.value = false;
    }
};

// LÓGICA GUÍAS
const openNew = () => {
    router.push({ name: 'contribuir' });
};

const editGuide = (g) => {
    // Redirigimos a la ruta 'contribuir' pasando el ID de la guía
    router.push({ name: 'contribuir', params: { id: g.id } });
};

const saveGuide = async () => {
    if (!guide.value.title || !guide.value.game_id) return;
    const payload = { 
        title: guide.value.title,
        content: guide.value.content,
        game_id: guide.value.game_id,
        categories: selectedCategories.value,
        user_id: authUser.value.id
    };

    try {
        if (guide.value.id) await axios.put(`/api/guides/${guide.value.id}`, payload);
        else await axios.post('/api/guides', payload);
        guideDialog.value = false;
        loadData();
    } catch (e) { console.error(e); }
};

const deleteGuide = async (data) => {
    if (confirm("¿Eliminar guía?")) {
        await axios.delete(`/api/guides/${data.id}`);
        loadData();
    }
};

const removeFavorite = async (g) => {
    if (confirm("¿Quitar de favoritos?")) {
        try {
            await axios.post(`/api/guides/${g.id}/favorite`);
            loadData();
        } catch (e) { console.error(e); }
    }
};

const viewGuide = (data) => router.push({ name: 'guides.show', params: { id: data.slug || data.id } });
const hideDialog = () => guideDialog.value = false;

// UTILIDADES
const scrollTo = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
};

// WATCHERS & MOUNTED
watch(authUser, (user) => { if (user) loadData(); }, { immediate: true });
onMounted(() => { if (authUser.value) loadData(); });
</script>

<style scoped>
.surface-card {
    background-color: var(--surface-card);
}
</style>
<template>
    <div class="p-4 min-h-screen" style="background-color: #111827;">
        <!-- HEADER DE PERFIL -->
        <div class="p-6 rounded-2xl shadow-xl mb-8" style="background-color: #111827;">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Avatar -->
                <div class="relative">
                    <div class="w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-[#5369F2] shadow-2xl overflow-hidden bg-[#1E293B] flex items-center justify-center flex-shrink-0">
                        <img v-if="authUser?.avatar_url" :src="authUser.avatar_url" :alt="authUser?.name" class="w-full h-full object-cover" />
                        <span v-else class="text-7xl font-bold text-white font-orbitron">{{ authUser?.name?.[0]?.toUpperCase() }}</span>
                    </div>
                </div>

                <!-- Info y Bio -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-bold text-white font-orbitron tracking-tighter uppercase m-0 mb-2">
                        Mi <span class="text-[#5369F2]">Sitio</span>
                    </h1>
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-4">
                        <span class="text-primary font-bold text-sm tracking-widest uppercase">{{ authUser?.name }}</span>
                    </div>
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

            <div class="flex flex-wrap gap-4 mt-8 pt-8 border-t border-gray-800">
                <Button label="Nueva Guía" icon="pi pi-plus" @click="openNew" class="!bg-[#5369F2] !border-none !text-white shadow-lg shadow-blue-500/20 !rounded-xl" />
                <Button label="Personalizar" icon="pi pi-user-edit" outlined @click="openEditProfile" class="!border-[#5369F2] !text-white hover:!bg-[#5369F2]/10 !rounded-xl" />
                <Button label="Cerrar Sesión" icon="pi pi-power-off" outlined @click="handleLogout" class="!border-red-500 !text-white hover:!bg-red-500/10 hover:!text-red-500 !rounded-xl ml-auto md:ml-0" />
            </div>
        </div>

        <div id="mis-guias" class="mb-12">
            <div class="flex justify-between items-center mb-6 px-2">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold text-white font-orbitron tracking-tighter uppercase m-0">
                        Mis <span class="text-[#5369F2]">guías</span>
                    </h2>
                </div>
            </div>

            <div v-if="guides.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2">
                <div v-for="g in guides" :key="g.id" class="flex">
                    <div @click="viewGuide(g)" class="bg-[#111827] rounded-2xl flex flex-col w-full hover:border-primary/50 transition-all duration-300 shadow-lg hover:shadow-2xl hover:-translate-y-1 overflow-hidden cursor-pointer group">
                        <!-- Imagen destacada -->
                        <div class="relative h-44 overflow-hidden bg-[#0b0f19] border-b border-gray-800 flex items-center justify-center">
                            <!-- Etiqueta de Estado -->
                            <div class="absolute top-3 right-3 z-10 shadow-md rounded-md">
                                <Tag v-if="g.status === 'published'" value="Aprobada" class="!bg-[#5369F2]/20 !text-[#5369F2] border-none font-bold" />
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
                        <div class="flex gap-2 p-2 bg-gray-900/50 rounded-xl border border-gray-800/50" @click.stop>
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

        <div id="favoritas" class="mb-12">
            <div class="flex justify-between items-center mb-6 px-2">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold text-white font-orbitron tracking-tighter uppercase m-0">
                        Guías <span class="text-[#5369F2]">favoritas</span>
                    </h2>
                </div>
            </div>

            <div v-if="favoriteGuides.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2">
                <div v-for="g in favoriteGuides" :key="g.id" class="flex">
                    <div @click="viewGuide(g)" class="bg-[#111827] rounded-2xl flex flex-col w-full hover:border-primary/50 transition-all duration-300 shadow-lg relative overflow-hidden cursor-pointer group">
                        <!-- Imagen destacada -->
                        <div class="relative h-40 overflow-hidden bg-[#0b0f19] border-b border-gray-800 flex items-center justify-center">
                            <!-- Botón Favorito (Overlay) -->
                            <div class="absolute top-3 right-3 z-10" @click.stop>
                                <Button 
                                    icon="pi pi-star-fill" 
                                    rounded 
                                    @click="removeFavorite(g)" 
                                    class="!w-10 !h-10 !bg-[#5369F2] !border-none !text-white shadow-lg shadow-blue-500/20" 
                                    v-tooltip.left="'Quitar de favoritos'" 
                                />
                            </div>

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
        <Dialog v-model:visible="profileDialog" modal class="admin-v2-dialog" :style="{width: '500px'}">
            <template #header>
                <h3 class="text-xl font-bold font-orbitron tracking-tighter uppercase m-0 text-white">
                    MI <span class="text-[#5369F2]">Perfil</span>
                </h3>
            </template>
            <div class="flex flex-col items-center gap-4 mb-6 pt-4">
                <div class="relative group">
                    <Avatar 
                        :image="avatarPreview || authUser?.avatar_url" 
                        size="xlarge" shape="circle" 
                        class="!w-32 !h-32 shadow-2 border-none bg-[#0b0f19]" 
                    />
                </div>
                <Button label="Cambiar Foto" icon="pi pi-images" size="small" text @click="showAvatarGrid = true" class="!text-[#5369F2] font-bold no-hover-bg" />
                <p class="text-gray-500 text-center uppercase text-[10px] tracking-widest font-bold -mt-2">Elige un avatar oficial</p>
            </div>

            <div class="field mb-6">
                <label for="prof_name" class="text-[#5369F2] font-medium mb-2 block uppercase text-xs tracking-wider">Nombre de usuario</label>
                <InputText id="prof_name" v-model="profileName" placeholder="Tu nombre..." 
                    class="!bg-[#111827] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none w-full" />
            </div>

            <div class="field mb-6">
                <label for="prof_bio" class="text-[#5369F2] font-medium mb-2 block uppercase text-xs tracking-wider">Biografía</label>
                <Textarea id="prof_bio" v-model="profileBio" rows="4" placeholder="Cuéntanos sobre ti..." 
                    class="!bg-[#111827] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none w-full" />
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                    <Button label="Cancelar" icon="pi pi-times" text @click="profileDialog = false" class="!text-gray-400 hover:!text-red-500 hover:!bg-red-500/10" />
                    <Button label="Guardar Perfil" icon="pi pi-check" :loading="isSavingProfile" @click="saveProfile" 
                        class="!bg-[#5369F2] !border-none !rounded-xl !px-6 !py-3 !font-bold shadow-lg shadow-[#5369F2]/20" />
                </div>
            </template>
        </Dialog>

        <!-- Dialog: Selector de Avatares Oficiales -->
        <Dialog v-model:visible="showAvatarGrid" modal class="admin-v2-dialog" :style="{width: '600px'}">
            <template #header>
                <h3 class="text-xl font-bold font-orbitron tracking-tighter uppercase m-0 text-white">
                    MIS <span class="text-[#5369F2]">Avatares</span>
                </h3>
            </template>
            <div class="grid grid-cols-3 md:grid-cols-4 gap-4 p-4">
                <div v-for="a in predefinedAvatars" :key="a.name" 
                    class="cursor-pointer group relative aspect-square rounded-2xl overflow-hidden border-2 transition-all"
                    :class="tempAvatarName === a.name ? 'border-[#5369F2]' : 'border-gray-800 hover:border-gray-600'"
                    @click="tempAvatarName = a.name">
                    <img :src="a.url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div v-if="tempAvatarName === a.name" class="absolute inset-0 bg-[#5369F2]/20 flex items-center justify-center">
                        <i class="pi pi-check text-white text-2xl"></i>
                    </div>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                    <Button label="Cancelar" icon="pi pi-times" text @click="showAvatarGrid = false" class="!text-gray-400 hover:!text-red-500 hover:!bg-red-500/10" />
                    <Button label="Confirmar Avatar" icon="pi pi-check" @click="confirmPredefinedAvatar" :disabled="!tempAvatarName" 
                        class="!bg-[#5369F2] !border-none !rounded-xl !px-6 !py-3 !font-bold shadow-lg shadow-[#5369F2]/20" />
                </div>
            </template>
        </Dialog>

        <!-- Dialog: Nueva/Editar Guía -->
        <Dialog v-model:visible="guideDialog" :header="guide.id ? 'Editar Guía' : 'Nueva Guía'" modal class="admin-v2-dialog p-fluid" :style="{width: '800px'}">
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
import useAuth from "@/composables/auth";
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
const { logout } = useAuth();

const handleLogout = () => {
    logout();
};

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
const showAvatarGrid = ref(false);
const predefinedAvatars = ref([]);
const tempAvatarName = ref(null);
const selectedAvatarFilename = ref(null);

const fetchPredefinedAvatars = async () => {
    try {
        const res = await axios.get('/api/avatars/predefined');
        predefinedAvatars.value = res.data;
    } catch (e) { console.error(e); }
};

// Estados Guía
const guideDialog = ref(false);
const guide = ref({ title: '', content: '', game_id: null });
const selectedCategories = ref([]);

const authUser = computed(() => auth.user);

const loadData = async () => {
    if (!authUser.value?.id) return;

    try {
        const [resGuides, resFavs, resCats, resGames] = await Promise.all([
            axios.get('/api/guides/my-guides'), 
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
    if (instance.root._hasPasteListener) return;
    instance.root._hasPasteListener = true;

    instance.clipboard.addMatcher('IMG', (node, delta) => {
        if (node.src && node.src.startsWith('data:')) {
            delta.ops = [];
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
    selectedAvatarFilename.value = null;
    tempAvatarName.value = null;
    profileDialog.value = true;
    fetchPredefinedAvatars();
};

const confirmPredefinedAvatar = () => {
    const avatar = predefinedAvatars.value.find(a => a.name === tempAvatarName.value);
    if (avatar) {
        avatarPreview.value = avatar.url;
        selectedAvatarFilename.value = avatar.name;
    }
    showAvatarGrid.value = false;
};

const saveProfile = async () => {
    if (!profileName.value) return;
    isSavingProfile.value = true;
    
    try {
        // 1. Guardar datos básicos
        const fd = new FormData();
        fd.append('name', profileName.value);
        fd.append('bio', profileBio.value || '');
        
        const resProfile = await axios.post('/api/user/profile', fd);
        
        // 2. Si seleccionó un avatar predefinido, guardarlo vía Spatie
        if (selectedAvatarFilename.value) {
            const resAvatar = await axios.post('/api/user/select-avatar', { 
                filename: selectedAvatarFilename.value 
            });
            auth.user = resAvatar.data.data;
        } else {
            auth.user = resProfile.data.data;
        }

        localStorage.setItem('user', JSON.stringify(auth.user));
        avatarPreview.value = null;
        selectedAvatarFilename.value = null;
        profileDialog.value = false;
        
        Swal.fire({
            title: '¡Perfil Actualizado!',
            icon: 'info',
            timer: 1500,
            showConfirmButton: false,
            background: '#111827',
            color: '#fff'
        });
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
.font-orbitron {
    font-family: 'Orbitron', sans-serif !important;
}

.surface-card {
    background-color: var(--surface-card);
}
</style>

<style>
/* Estilos globales para el diálogo (necesario si se teleporta al body) */
.admin-v2-dialog {
    border: none !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
}

.admin-v2-dialog .p-dialog-header {
    background-color: #111827 !important;
    border: none !important;
    color: white !important;
    padding: 1.5rem 1.5rem 1rem 1.5rem !important;
}

.admin-v2-dialog .p-dialog-content {
    background-color: #111827 !important;
    color: white !important;
    padding: 0 1.5rem 1.5rem 1.5rem !important;
}

.admin-v2-dialog .p-dialog-footer {
    background-color: #111827 !important;
    border: none !important;
    padding: 1rem 1.5rem 1.5rem 1.5rem !important;
}

.admin-v2-dialog .p-dialog-header-close {
    color: #9ca3af !important;
}

.admin-v2-dialog .p-dialog-header-close:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: white !important;
}

.no-hover-bg:hover {
    background-color: transparent !important;
}
</style>

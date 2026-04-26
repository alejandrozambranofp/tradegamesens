<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-3xl font-bold text-white m-0">Catálogo de Juegos</h1>
                <p class="text-gray-500 mt-1">Gestiona los títulos disponibles en la plataforma.</p>
            </div>
            <Button label="Añadir Juego" icon="pi pi-plus" class="shadow-lg shadow-primary/20" @click="openDialog" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div v-for="game in games" :key="game.id" 
                class="p-4 rounded-[32px] bg-[#111827] border border-gray-800 group hover:border-primary/30 transition-all overflow-hidden flex flex-col">
                
                <!-- Portada del Juego -->
                <div class="relative h-48 rounded-2xl overflow-hidden bg-[#0b0f19] mb-4">
                    <img :src="game.cover || '/images/default-game.png'" :alt="game.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-transparent to-transparent opacity-60"></div>
                </div>

                <div class="flex items-center justify-between px-2 pb-2">
                    <div>
                        <div class="text-white font-bold text-lg leading-tight">{{ game.title }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Slug: {{ game.slug }}</div>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Button icon="pi pi-pencil" text rounded severity="warning" size="small" @click="editGame(game)" />
                        <Button icon="pi pi-trash" text rounded severity="danger" size="small" @click="deleteGame(game)" />
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="gameDialog" :header="editingGame ? 'Editar Juego' : 'Nuevo Juego'" modal class="admin-v2-dialog" :style="{ width: '500px' }">
            <div class="p-fluid pt-4 space-y-6">
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Título del Juego</label>
                    <InputText v-model="gameForm.title" class="bg-[#0b0f19] border-gray-800 text-white p-3 rounded-xl" placeholder="Ej: Elden Ring" autofocus />
                </div>
                
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Portada del Juego</label>
                    
                    <div class="flex items-start gap-6">
                        <!-- Preview and Upload Trigger -->
                        <div class="relative w-40 h-56 rounded-3xl overflow-hidden border-2 border-dashed border-gray-800 bg-[#0b0f19] group hover:border-primary/50 transition-all">
                            <img v-if="previewUrl || gameForm.cover" :src="previewUrl || gameForm.cover" class="w-full h-full object-cover" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-gray-600 p-4 text-center">
                                <i class="pi pi-cloud-upload text-3xl mb-3"></i>
                                <span class="text-[10px] uppercase font-bold tracking-widest">Subir desde el ordenador</span>
                            </div>
                            
                            <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer text-white gap-2">
                                <i class="pi pi-camera text-2xl"></i>
                                <span class="text-[10px] font-bold uppercase">Cambiar Imagen</span>
                                <input type="file" class="hidden" accept="image/webp" @change="onFileSelect" />
                            </label>
                        </div>
                        
                        <div class="flex-grow pt-4">
                            <ul class="text-[10px] text-gray-500 space-y-2 list-disc pl-4 uppercase font-bold tracking-wider">
                                <li class="text-primary">Formato: Solo WebP</li>
                                <li>Tamaño máx: 5MB</li>
                                <li>Recomendado: 600x900px</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="gameDialog = false" />
                <Button label="Guardar" icon="pi pi-check" @click="saveGame" :loading="saving" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';

const games = ref([]);
const gameDialog = ref(false);
const editingGame = ref(null);
const saving = ref(false);
const gameForm = ref({ title: '', cover: '' });
const selectedFile = ref(null);
const previewUrl = ref(null);

const fetchGames = async () => {
    try {
        const response = await axios.get('/api/games');
        games.value = response.data.data || response.data;
    } catch (e) { console.error(e); }
};

const openDialog = () => {
    editingGame.value = null;
    gameForm.value = { title: '', cover: '' };
    selectedFile.value = null;
    previewUrl.value = null;
    gameDialog.value = true;
};

const editGame = (game) => {
    editingGame.value = game;
    gameForm.value = { title: game.title, cover: game.cover };
    selectedFile.value = null;
    previewUrl.value = null;
    gameDialog.value = true;
};

const onFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const saveGame = async () => {
    if (!gameForm.value.title) return;
    saving.value = true;
    
    const formData = new FormData();
    formData.append('title', gameForm.value.title);
    
    if (selectedFile.value) {
        formData.append('cover', selectedFile.value);
    }

    try {
        if (editingGame.value) {
            formData.append('_method', 'PUT');
            await axios.post(`/api/games/${editingGame.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await axios.post('/api/games', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }
        gameDialog.value = false;
        fetchGames();
    } catch (e) { 
        console.error(e);
        alert("Error al guardar juego."); 
    }
    finally { saving.value = false; }
};

const deleteGame = async (game) => {
    if (confirm(`¿Borrar el juego "${game.title}"?`)) {
        try {
            await axios.delete(`/api/games/${game.id}`);
            fetchGames();
        } catch (e) { 
            console.error(e);
            alert("Error al eliminar juego."); 
        }
    }
};

onMounted(fetchGames);
</script>

<style scoped>
.admin-v2-dialog :deep(.p-dialog-content) {
    background: #111827 !important;
}
</style>

<template>
    <div class="card p-4">
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
                        
                        <span class="text-xs text-gray-400 italic">
                            Tu ID de usuario: {{ authUser?.id }}
                        </span>
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
                <label for="content" class="font-bold block mb-2">Contenido (Puedes pegar imágenes directamente)</label>
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
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

// Importaciones de PrimeVue
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';

// RUTA ACTUALIZADA: De views/user/guides/ a store/ son 4 niveles arriba
import { authStore } from "@/store/auth";

const router = useRouter();
const auth = authStore();

const guides = ref([]);
const allCategories = ref([]);
const allGames = ref([]); 
const selectedCategories = ref([]);
const guideDialog = ref(false);
const guide = ref({ title: '', content: '', game_id: null });

const authUser = computed(() => auth.user);
const isSuperAdmin = computed(() => {
    if (!auth.user) return false;
    return auth.user.id == 1 || 
           auth.user.roles?.some(role => role.name.toLowerCase().includes('admin'));
});

const loadData = async () => {
    try {
        const [resGuides, resCats, resGames] = await Promise.all([
            axios.get('/api/guides'), 
            axios.get('/api/categories'),
            axios.get('/api/games')
        ]);
        
        const todasLasGuias = resGuides.data.data || resGuides.data;

        // FILTRO: Solo mis guías
        if (authUser.value) {
            guides.value = todasLasGuias.filter(g => g.user_id === authUser.value.id);
        }

        allCategories.value = resCats.data.data || resCats.data;
        allGames.value = resGames.data.data || resGames.data;
        
    } catch (error) { 
        console.error("Error cargando datos:", error);
    }
};

const onEditorLoad = ({ instance }) => {
    instance.root.addEventListener('paste', async (event) => {
        const items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                const file = item.getAsFile();
                const formData = new FormData();
                formData.append('image', file);
                try {
                    const response = await axios.post('/api/images/upload', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    const range = instance.getSelection();
                    instance.insertEmbed(range.index, 'image', response.data.url);
                    event.preventDefault();
                } catch (e) { 
                    console.error("Error subiendo imagen:", e); 
                }
            }
        }
    });
};

const openNew = () => {
    guide.value = { title: '', content: '', game_id: null };
    selectedCategories.value = [];
    guideDialog.value = true;
};

const editGuide = (data) => {
    guide.value = { ...data };
    guide.value.game_id = data.game ? data.game.id : null;
    // IMPORTANTE: Mapeamos solo los IDs para el MultiSelect
    selectedCategories.value = data.categories ? data.categories.map(c => c.id) : [];
    guideDialog.value = true;
};

const saveGuide = async () => {
    if (!guide.value.title || !guide.value.game_id) {
        alert("El título y el juego son obligatorios.");
        return;
    }

    if (selectedCategories.value.length === 0) {
        alert("Debes seleccionar al menos una categoría.");
        return;
    }
    
    // PAYLOAD LIMPIO: Evita enviar objetos anidados que rompen el backend
    const payload = { 
        title: guide.value.title,
        content: guide.value.content,
        game_id: guide.value.game_id,
        categories: selectedCategories.value, // Enviamos el array de IDs [1, 2...]
        user_id: guide.value.id ? guide.value.user_id : authUser.value.id
    };

    try {
        if (guide.value.id) { 
            await axios.put(`/api/guides/${guide.value.id}`, payload); 
        } else { 
            await axios.post('/api/guides', payload); 
        }
        guideDialog.value = false;
        await loadData();
    } catch (e) { 
        console.error("Error al guardar:", e.response?.data);
        alert("Hubo un error al guardar. Revisa que todos los campos sean correctos.");
    }
};

const deleteGuide = async (data) => {
    if (confirm(`¿Borrar "${data.title}"?`)) {
        try {
            await axios.delete(`/api/guides/${data.id}`);
            loadData();
        } catch (e) {
            alert("No se pudo eliminar la guía.");
        }
    }
};

const viewGuide = (data) => {
    router.push({ name: 'guides.show', params: { id: data.slug } });
};

const hideDialog = () => { guideDialog.value = false; };

onMounted(loadData);
</script>
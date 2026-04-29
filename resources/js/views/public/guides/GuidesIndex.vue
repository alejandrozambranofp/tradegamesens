    <div class="min-h-screen pt-28 md:pt-32 bg-[#0b0f19]">
        <div class="max-w-7xl mx-auto px-6 pb-20">
            <div class="card p-6 bg-[#111827] border border-white/5 rounded-3xl">
        <div class="flex flex-column md:flex-row justify-content-between align-items-center mb-4 gap-4">
            <h2 class="m-0 font-bold text-2xl text-gray-800 dark:text-white">Guías de la Comunidad</h2>
            
            <!-- Filter Feedback -->
            <div v-if="searchStore.query || searchStore.gameId || searchStore.categoryId" class="flex align-items-center gap-2 bg-blue-50 dark:bg-blue-900/20 p-2 px-3 border-round-xl border-1 border-blue-100 dark:border-blue-800">
                <span class="text-sm text-blue-700 dark:text-blue-300 font-inter">
                    <i class="pi pi-filter mr-1"></i>
                    Filtrando resultados...
                </span>
                <Button label="Limpiar Filtros" icon="pi pi-times" size="small" text severity="secondary" @click="searchStore.clearFilters()" class="!py-1 !px-2" />
            </div>
        </div>

        <DataTable :value="guides" paginator :rows="10" dataKey="id" class="p-datatable-sm shadow-2 border-round overflow-hidden">
            <template #empty> <div class="p-4 text-center">No se encontraron guías de otros usuarios.</div> </template>
            
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
                            
                            <!-- Botón Favoritos -->
                            <Button 
                                :icon="slotProps.data.is_favorite ? 'pi pi-star-fill' : 'pi pi-star'" 
                                :severity="slotProps.data.is_favorite ? 'warning' : 'secondary'"
                                outlined rounded 
                                @click="toggleFavorite(slotProps.data)" 
                                v-tooltip="'Guardar en favoritos'"
                            />

                            <template v-if="slotProps.data.user_id == authUser?.id || isSuperAdmin">
                                <Button icon="pi pi-pencil" outlined rounded severity="warning" @click="editGuide(slotProps.data)" />
                                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteGuide(slotProps.data)" />
                            </template>
                        </div>
                        
                        <span class="text-xs text-500">
                            Autor ID: {{ slotProps.data.user_id }}
                        </span>
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog v-model:visible="guideDialog" header="Editar Guía" modal class="p-fluid" :style="{width: '800px'}">
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
                <Button label="Guardar Cambios" icon="pi pi-check" @click="saveGuide" />
            </template>
        </Dialog>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

// Componentes PrimeVue (Asegúrate de tenerlos registrados globalmente o impórtalos aquí)
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';

// RUTA CORREGIDA: Usamos el alias '@' para ir directo a resources/js
import { authStore } from "@/store/auth";

import { useSearchStore } from '@/store/search';

const router = useRouter();
const auth = authStore();
const searchStore = useSearchStore();

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
        const params = {
            search: searchStore.query || undefined,
            game_id: searchStore.gameId || undefined,
            category_id: searchStore.categoryId || undefined
        };

        console.log('[GuidesIndex] Cargando guías con parámetros:', params);

        const [resGuides, resCats, resGames] = await Promise.all([
            axios.get('/api/guides', { params }),
            axios.get('/api/categories'),
            axios.get('/api/games')
        ]);
        
        const todasLasGuias = resGuides.data.data || resGuides.data;
        guides.value = todasLasGuias;

        allCategories.value = resCats.data.data || resCats.data;
        allGames.value = resGames.data.data || resGames.data;
        
    } catch (error) { 
        console.error("Error cargando datos en Comunidad:", error);
    }
};

// Recargar cuando cambien los filtros en el store (incluso al inicio)
import { watch } from 'vue';
watch(() => [searchStore.query, searchStore.gameId, searchStore.categoryId], () => {
    loadData();
}, { deep: true, immediate: true });

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

const editGuide = (data) => {
    guide.value = { ...data };
    guide.value.game_id = data.game ? data.game.id : null;
    selectedCategories.value = data.categories ? data.categories.map(c => c.id) : [];
    guideDialog.value = true;
};

const saveGuide = async () => {
    if (!guide.value.title || !guide.value.game_id) return;
    
    // Payload limpio para el servidor
    const payload = { 
        title: guide.value.title,
        content: guide.value.content,
        game_id: guide.value.game_id,
        categories: selectedCategories.value,
        user_id: guide.value.user_id // Mantenemos el autor original
    };

    try {
        await axios.put(`/api/guides/${guide.value.id}`, payload); 
        guideDialog.value = false;
        loadData();
    } catch (e) { 
        console.error("Error al guardar:", e.response?.data);
        alert("No se pudo guardar la edición.");
    }
};

const deleteGuide = async (data) => {
    if (confirm(`¿Borrar "${data.title}"?`)) {
        try {
            await axios.delete(`/api/guides/${data.id}`);
            loadData();
        } catch (e) {
            alert("No tienes permiso para borrar esta guía.");
        }
    }
};

const viewGuide = (data) => {
    router.push({ name: 'guides.show', params: { id: data.slug } });
};

const toggleFavorite = async (guide) => {
    try {
        await axios.post(`/api/guides/${guide.id}/favorite`);
        // Actualizamos el estado localmente para feedback inmediato
        guide.is_favorite = !guide.is_favorite;
    } catch (e) {
        console.error(e);
    }
};

const hideDialog = () => { guideDialog.value = false; };

onMounted(loadData);
</script>
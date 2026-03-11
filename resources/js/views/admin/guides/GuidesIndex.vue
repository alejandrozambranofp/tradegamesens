<template>
    <div class="card p-4">
        <div class="flex justify-content-between align-items-center mb-4">
            <h2 class="m-0 font-bold text-2xl text-gray-800 dark:text-white">Gestión de Guías</h2>
            <Button label="Nueva Guía" icon="pi pi-plus" severity="success" @click="openNew" />
        </div>

        <DataTable :value="guides" paginator :rows="10" dataKey="id" class="p-datatable-sm shadow-2 border-round overflow-hidden" responsiveLayout="scroll">
            <template #empty> <div class="p-4 text-center">No se encontraron guías.</div> </template>
            
            <Column field="id" header="ID" sortable style="width: 5rem"></Column>
            
            <Column field="game.title" header="Juego" sortable>
                <template #body="slotProps">
                    <span v-if="slotProps.data.game" class="font-bold text-primary">
                        {{ slotProps.data.game.title }}
                    </span>
                    <span v-else class="text-500">Sin juego</span>
                </template>
            </Column>

            <Column field="title" header="Título" sortable></Column>
            
            <Column header="Categorías">
                <template #body="slotProps">
                    <Tag v-for="cat in slotProps.data.categories" :key="cat.id" :value="cat.name" severity="info" class="mr-1" />
                </template>
            </Column>

            <Column header="Acciones" style="min-width: 12rem">
                <template #body="slotProps">
                    <div class="flex gap-2">
                        <Button icon="pi pi-eye" outlined rounded severity="info" @click="viewGuide(slotProps.data)" />
                        <Button icon="pi pi-pencil" outlined rounded @click="editGuide(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteGuide(slotProps.data)" />
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog v-model:visible="guideDialog" :header="guide.id ? 'Editar Guía' : 'Nueva Guía'" modal class="p-fluid" :style="{width: '550px'}">
            
            <div class="field mb-4">
                <label for="game" class="font-bold block mb-2">Juego</label>
                <Dropdown 
                    id="game" 
                    v-model="guide.game_id" 
                    :options="allGames" 
                    optionLabel="title" 
                    optionValue="id" 
                    placeholder="Selecciona un juego" 
                    :filter="true" 
                    class="w-full" 
                />
                <small class="p-error" v-if="submitted && !guide.game_id">El juego es obligatorio.</small>
            </div>

            <div class="field mb-4">
                <label for="title" class="font-bold block mb-2">Título</label>
                <InputText id="title" v-model.trim="guide.title" required autofocus placeholder="Escribe el título..." :class="{'p-invalid': submitted && !guide.title}" />
                <small class="p-error" v-if="submitted && !guide.title">El título es obligatorio.</small>
            </div>
            
            <div class="field mb-4">
                <label for="content" class="font-bold block mb-2">Contenido</label>
                <Textarea id="content" v-model="guide.content" required rows="8" placeholder="Escribe el contenido de la guía..." />
            </div>

            <div class="field mb-4">
                <label class="font-bold block mb-2">Categorías</label>
                <MultiSelect 
                    v-model="selectedCategories" 
                    :options="allCategories" 
                    optionLabel="name" 
                    optionValue="id" 
                    placeholder="Selecciona categorías" 
                    class="w-full" 
                    display="chip"
                />
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Guardar Guía" icon="pi pi-check" severity="primary" @click="saveGuide" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'; // Añadido para que funcione viewGuide
import axios from 'axios';

const router = useRouter(); // Inicializamos el router
const guides = ref([]);
const allCategories = ref([]);
const allGames = ref([]); 
const selectedCategories = ref([]);
const guideDialog = ref(false);
const submitted = ref(false);
const guide = ref({ title: '', content: '', game_id: null });

// Función para cargar todos los datos de la API
const loadData = async () => {
    try {
        const [resGuides, resCats, resGames] = await Promise.all([
            axios.get('/api/guides'),
            axios.get('/api/categories'),
            axios.get('/api/games')
        ]);
        guides.value = resGuides.data.data;
        allCategories.value = resCats.data.data;
        allGames.value = resGames.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);
    }
};

const openNew = () => {
    guide.value = { title: '', content: '', game_id: null };
    selectedCategories.value = [];
    submitted.value = false;
    guideDialog.value = true;
};

const editGuide = (data) => {
    guide.value = { ...data };
    guide.value.game_id = data.game ? data.game.id : null;
    selectedCategories.value = data.categories ? data.categories.map(c => c.id) : [];
    submitted.value = false;
    guideDialog.value = true;
};

const saveGuide = async () => {
    submitted.value = true;
    if (!guide.value.game_id || !guide.value.title || !guide.value.content) return;

    const payload = { 
        title: guide.value.title,
        content: guide.value.content,
        game_id: guide.value.game_id,
        categories: selectedCategories.value 
    };
    
    try {
        if (guide.value.id) {
            await axios.put(`/api/guides/${guide.value.id}`, payload);
        } else {
            await axios.post('/api/guides', payload);
        }
        guideDialog.value = false;
        loadData();
    } catch (error) {
        console.error("Error al guardar:", error.response?.data);
        alert("Error al guardar la guía: " + (error.response?.data?.message || "Error desconocido"));
    }
};

const hideDialog = () => { guideDialog.value = false; };

const deleteGuide = async (data) => {
    if (confirm(`¿Estás seguro de que quieres borrar la guía: "${data.title}"?`)) {
        try {
            await axios.delete(`/api/guides/${data.id}`);
            loadData();
        } catch (error) {
            alert("No se pudo eliminar la guía");
        }
    }
};

const viewGuide = (data) => {
    // Usamos el slug para navegar a la vista de detalle
    router.push({ name: 'guides.show', params: { id: data.slug } });
};

onMounted(loadData);
</script>
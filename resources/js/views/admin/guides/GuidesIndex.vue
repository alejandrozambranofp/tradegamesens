<template>
    <div class="card p-4">
        <div class="flex justify-content-between align-items-center mb-4">
            <h2 class="m-0 font-bold text-2xl text-gray-800 dark:text-white">Gestión de Guías - TradeGameSense</h2>
            <Button label="Nueva Guía" icon="pi pi-plus" severity="success" @click="openNew" />
        </div>

        <DataTable :value="guides" paginator :rows="10" dataKey="id" class="p-datatable-sm shadow-2 border-round overflow-hidden">
            <template #empty> <div class="p-4 text-center">No se encontraron guías.</div> </template>
            
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
                <label for="content" class="font-bold block mb-2">Contenido (Puedes pegar imágenes directamente con Ctrl+V)</label>
                <Editor v-model="guide.content" editorStyle="height: 400px" @load="onEditorLoad">
                    <template #toolbar>
                        <span class="ql-formats">
                            <button class="ql-bold" v-tooltip.bottom="'Negrita'"></button>
                            <button class="ql-italic" v-tooltip.bottom="'Cursiva'"></button>
                            <button class="ql-underline" v-tooltip.bottom="'Subrayado'"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-header" value="1" v-tooltip.bottom="'Título 1'"></button>
                            <button class="ql-header" value="2" v-tooltip.bottom="'Título 2'"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                        </span>
                    </template>
                </Editor>
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Editor from 'primevue/editor';

const router = useRouter();
const guides = ref([]);
const allCategories = ref([]);
const allGames = ref([]); 
const selectedCategories = ref([]);
const guideDialog = ref(false);
const submitted = ref(false);
const guide = ref({ title: '', content: '', game_id: null });

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
    } catch (error) { console.error("Error cargando datos:", error); }
};

// LOGICA PARA PEGAR IMAGENES
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
                } catch (e) { console.error("Error subiendo imagen:", e); }
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
    selectedCategories.value = data.categories ? data.categories.map(c => c.id) : [];
    guideDialog.value = true;
};

const saveGuide = async () => {
    if (!guide.value.title || !guide.value.game_id) return;
    const payload = { ...guide.value, categories: selectedCategories.value };
    try {
        if (guide.value.id) { await axios.put(`/api/guides/${guide.value.id}`, payload); }
        else { await axios.post('/api/guides', payload); }
        guideDialog.value = false;
        loadData();
    } catch (e) { alert("Error al guardar"); }
};

const deleteGuide = async (data) => {
    if (confirm(`¿Borrar "${data.title}"?`)) {
        await axios.delete(`/api/guides/${data.id}`);
        loadData();
    }
};

const viewGuide = (data) => {
    router.push({ name: 'guides.show', params: { id: data.slug } });
};

const hideDialog = () => { guideDialog.value = false; };
onMounted(loadData);
</script>
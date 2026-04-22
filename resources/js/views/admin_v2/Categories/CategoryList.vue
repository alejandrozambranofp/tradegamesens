<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-3xl font-bold text-white m-0">Categorías</h1>
                <p class="text-gray-500 mt-1">Organiza los temas de las guías.</p>
            </div>
            <Button label="Añadir Categoría" icon="pi pi-plus" class="shadow-lg shadow-emerald-500/20" severity="success" @click="openDialog" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="cat in categories" :key="cat.id" 
                class="p-6 rounded-3xl bg-[#111827] border border-gray-800 flex items-center justify-between group hover:border-emerald-500/30 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        <i class="pi pi-tag text-xl"></i>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg">{{ cat.name }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-widest">{{ cat.guides_count || 0 }} Guías</div>
                    </div>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <Button icon="pi pi-pencil" text rounded severity="warning" size="small" @click="editCategory(cat)" />
                    <Button icon="pi pi-trash" text rounded severity="danger" size="small" @click="deleteCategory(cat)" />
                </div>
            </div>
        </div>

        <Dialog v-model:visible="catDialog" :header="editingCat ? 'Editar Categoría' : 'Nueva Categoría'" modal class="admin-v2-dialog" :style="{ width: '400px' }">
            <div class="p-fluid pt-4">
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Nombre de la Categoría</label>
                    <InputText v-model="catForm.name" class="bg-[#0b0f19] border-gray-800 text-white p-3 rounded-xl" autofocus />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="catDialog = false" />
                <Button label="Guardar" icon="pi pi-check" severity="success" @click="saveCategory" :loading="saving" />
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

const categories = ref([]);
const catDialog = ref(false);
const editingCat = ref(null);
const saving = ref(false);
const catForm = ref({ name: '' });

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data.data || response.data;
    } catch (e) { console.error(e); }
};

const openDialog = () => {
    editingCat.value = null;
    catForm.value = { name: '' };
    catDialog.value = true;
};

const editCategory = (cat) => {
    editingCat.value = cat;
    catForm.value = { name: cat.name };
    catDialog.value = true;
};

const saveCategory = async () => {
    if (!catForm.value.name) return;
    saving.value = true;
    try {
        if (editingCat.value) {
            await axios.put(`/api/categories/${editingCat.value.id}`, catForm.value);
        } else {
            await axios.post('/api/categories', catForm.value);
        }
        catDialog.value = false;
        fetchCategories();
    } catch (e) { alert("Error al guardar"); }
    finally { saving.value = false; }
};

const deleteCategory = async (cat) => {
    if (confirm(`¿Borrar la categoría "${cat.name}"?`)) {
        try {
            await axios.delete(`/api/categories/${cat.id}`);
            fetchCategories();
        } catch (e) { alert("Error al eliminar"); }
    }
};

onMounted(fetchCategories);
</script>

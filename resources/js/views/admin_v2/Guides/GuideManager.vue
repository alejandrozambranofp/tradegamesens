<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-3xl font-bold text-white m-0">Gestión de Guías</h1>
                <p class="text-gray-500 mt-1">Revisa y publica el contenido de la comunidad.</p>
            </div>
            <Button label="Refrescar Lista" icon="pi pi-refresh" outlined severity="secondary" @click="loadGuides" />
        </div>

        <DataTable :value="guides" :loading="loading" paginator :rows="10" 
                   class="admin-v2-table" responsiveLayout="stack">
            
            <template #header>
                <div class="flex flex-wrap gap-4 justify-between items-center">
                    <span class="p-input-icon-left w-full md:w-80">
                        <i class="pi pi-search text-gray-500" />
                        <InputText v-model="filters['global'].value" placeholder="Buscar guía..." class="w-full bg-[#0b0f19] border-gray-800" />
                    </span>
                    <SelectButton v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" class="admin-selectbutton" />
                </div>
            </template>

            <Column field="title" header="GUÍA" sortable style="min-width: 300px">
                <template #body="{ data }">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-800 flex-shrink-0 overflow-hidden">
                            <img v-if="data.image" :src="data.image" class="w-full h-full object-cover" />
                            <i v-else class="pi pi-image text-gray-600 flex items-center justify-center h-full"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-white font-bold">{{ data.title }}</span>
                            <span class="text-xs text-primary font-bold uppercase tracking-tighter">{{ data.game?.title || 'Sin Juego' }}</span>
                        </div>
                    </div>
                </template>
            </Column>

            <Column field="user.name" header="AUTOR" sortable>
                <template #body="{ data }">
                    <div class="flex items-center gap-2">
                        <Avatar :image="data.user?.avatar_url" shape="circle" class="border border-gray-700" />
                        <span class="text-sm">{{ data.user?.name }}</span>
                    </div>
                </template>
            </Column>

            <Column field="status" header="ESTADO" sortable>
                <template #body="{ data }">
                    <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" rounded class="text-[10px] px-3 font-black" />
                </template>
            </Column>

            <Column header="ACCIONES" class="text-right">
                <template #body="{ data }">
                    <div class="flex justify-end gap-2">
                        <Button icon="pi pi-eye" label="Previsualizar" size="small" outlined severity="info" @click="openPreview(data)" />
                        <Button v-if="data.status === 'pending'" icon="pi pi-check" size="small" severity="success" @click="updateStatus(data, 'published')" />
                        <Button icon="pi pi-trash" size="small" severity="danger" text @click="confirmDelete(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>

        <!-- PREVIEW DIALOG (COPIADO DEL MANAGER V2) -->
        <Dialog v-model:visible="previewVisible" modal :showHeader="false" 
                :style="{ width: '90vw', maxWidth: '1200px' }" :contentStyle="{ padding: '0', overflow: 'hidden' }">
            <div v-if="selectedGuide" class="flex flex-col h-[85vh]">
                <div class="p-6 bg-[#111827] border-b border-gray-800 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <Button icon="pi pi-arrow-left" text rounded severity="secondary" @click="previewVisible = false" />
                        <div>
                            <h2 class="text-xl font-bold text-white m-0">{{ selectedGuide.title }}</h2>
                            <p class="text-xs text-gray-500 m-0">Autor: {{ selectedGuide.user?.name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button v-if="selectedGuide.status === 'pending'" label="Publicar" icon="pi pi-check" severity="success" @click="updateStatus(selectedGuide, 'published'); previewVisible = false" />
                        <Button v-if="selectedGuide.status === 'pending'" label="Rechazar" icon="pi pi-times" severity="danger" outlined @click="updateStatus(selectedGuide, 'rejected'); previewVisible = false" />
                        <Button v-if="selectedGuide.status === 'published'" label="Retirar" icon="pi pi-arrow-down" severity="warning" outlined @click="updateStatus(selectedGuide, 'pending'); previewVisible = false" />
                    </div>
                </div>
                <div class="flex-grow overflow-y-auto p-10 bg-[#0b0f19]">
                    <div class="max-w-4xl mx-auto">
                        <div class="prose prose-invert prose-blue max-w-none" v-html="selectedGuide.content"></div>
                    </div>
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import SelectButton from 'primevue/selectbutton';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Avatar from 'primevue/avatar';

const guides = ref([]);
const loading = ref(true);
const previewVisible = ref(false);
const selectedGuide = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const statusFilter = ref('all');
const statusOptions = [
    { label: 'Todas', value: 'all' },
    { label: 'Pendientes', value: 'pending' },
    { label: 'Publicadas', value: 'published' }
];

const loadGuides = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/guides');
        guides.value = response.data.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const openPreview = (guide) => {
    selectedGuide.value = guide;
    previewVisible.value = true;
};

const updateStatus = async (guide, status) => {
    try {
        await axios.patch(`/api/admin/guides/${guide.id}/status`, { status });
        guide.status = status;
    } catch (e) { console.error(e); }
};

const confirmDelete = async (guide) => {
    if (confirm(`¿Eliminar guía "${guide.title}"?`)) {
        try {
            await axios.delete(`/api/guides/${guide.id}`);
            loadGuides();
        } catch (e) { console.error(e); }
    }
};

const getStatusLabel = (s) => s === 'published' ? 'PUBLICADA' : (s === 'pending' ? 'PENDIENTE' : 'RECHAZADA');
const getStatusSeverity = (s) => s === 'published' ? 'success' : (s === 'pending' ? 'warning' : 'danger');

onMounted(loadGuides);
</script>

<style scoped>
:deep(.admin-v2-table) { background: transparent; }
:deep(.p-datatable-header) { background: transparent; border: none; padding: 0 0 1.5rem 0; }
:deep(.p-datatable-thead > tr > th) { background: #1a2233; color: #4b5563; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; padding: 1.5rem; border: none; }
:deep(.p-datatable-tbody > tr) { background: transparent; color: #d1d5db; border-bottom: 1px solid #1f2937; }
:deep(.p-datatable-tbody > tr:hover) { background: #1a2233; }
</style>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-white m-0">Gestión de Guías</h1>
            <Button label="Refrescar Lista" icon="pi pi-refresh" outlined severity="secondary" @click="loadGuides" />
        </div>

        <DataTable :value="guides" :loading="loading" paginator :rows="10" 
                   class="admin-datatable" responsiveLayout="stack"
                   v-model:filters="filters" filterDisplay="menu"
                   :globalFilterFields="['title', 'user.name']">
            
            <template #header>
                <div class="flex flex-wrap gap-4 justify-between items-center">
                    <span class="p-input-icon-left w-full md:w-auto">
                        <i class="pi pi-search text-gray-500" />
                        <InputText v-model="filters['global'].value" placeholder="Buscar por título o autor..." class="w-full md:w-80 bg-[#0b0f19] border-gray-800" />
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
                        <Button v-if="data.status === 'pending'" icon="pi pi-check" size="small" severity="success" @click="updateStatus(data, 'published')" v-tooltip.top="'Publicar'" />
                        <Button icon="pi pi-trash" size="small" severity="danger" text @click="confirmDelete(data)" v-tooltip.top="'Eliminar'" />
                    </div>
                </template>
            </Column>
        </DataTable>

        <!-- MODAL DE PREVISUALIZACIÓN PREMIUM -->
        <Dialog v-model:visible="previewVisible" modal :showHeader="false" class="admin-preview-dialog" 
                :style="{ width: '90vw', maxWidth: '1200px' }" :contentStyle="{ padding: '0', overflow: 'hidden' }">
            <div v-if="selectedGuide" class="flex flex-col h-[85vh]">
                <!-- Header del Preview -->
                <div class="p-6 bg-[#111827] border-b border-gray-800 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <Button icon="pi pi-arrow-left" text rounded severity="secondary" @click="previewVisible = false" />
                        <div>
                            <h2 class="text-xl font-bold text-white m-0">{{ selectedGuide.title }}</h2>
                            <p class="text-xs text-gray-500 m-0">Autor: {{ selectedGuide.user?.name }} | Estado: <span class="text-primary font-bold uppercase">{{ selectedGuide.status }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button v-if="selectedGuide.status === 'pending'" label="Aprobar y Publicar" icon="pi pi-check" severity="success" @click="updateStatus(selectedGuide, 'published'); previewVisible = false" />
                        <Button v-if="selectedGuide.status === 'pending'" label="Rechazar" icon="pi pi-times" severity="danger" outlined @click="updateStatus(selectedGuide, 'rejected'); previewVisible = false" />
                        <Button v-if="selectedGuide.status === 'published'" label="Retirar" icon="pi pi-arrow-down" severity="warning" outlined @click="updateStatus(selectedGuide, 'pending'); previewVisible = false" />
                        <Button icon="pi pi-external-link" severity="secondary" text @click="goToPublic(selectedGuide)" v-tooltip.bottom="'Ver en la web pública'" />
                    </div>
                </div>

                <!-- Contenido del Preview (Copia del diseño de GuideShow) -->
                <div class="flex-grow overflow-y-auto p-10 bg-[#0b0f19]">
                    <div class="max-w-4xl mx-auto">
                        <!-- Portada de la guía -->
                        <div v-if="selectedGuide.image" class="w-full h-64 md:h-96 rounded-3xl overflow-hidden mb-10 shadow-2xl">
                            <img :src="selectedGuide.image" class="w-full h-full object-cover" />
                        </div>
                        
                        <!-- Contenido -->
                        <div class="prose prose-invert prose-blue max-w-none guide-content-render" v-html="selectedGuide.content"></div>
                        
                        <div class="mt-16 pt-10 border-t border-gray-800 flex flex-wrap gap-2">
                            <Tag v-for="cat in selectedGuide.categories" :key="cat.id" :value="cat.name" severity="secondary" outlined />
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { FilterMatchMode } from 'primevue/api';
import { useRouter } from 'vue-router';

const guides = ref([]);
const loading = ref(true);
const previewVisible = ref(false);
const selectedGuide = ref(null);
const router = useRouter();

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
    } catch (e) {
        console.error("Error cargando guías:", e);
    } finally {
        loading.value = false;
    }
};

const openPreview = (guide) => {
    selectedGuide.value = guide;
    previewVisible.value = true;
};

const updateStatus = async (guide, status) => {
    try {
        await axios.patch(`/api/admin/guides/${guide.id}/status`, { status });
        guide.status = status;
        // Opcional: mostrar notificación
    } catch (e) {
        console.error("Error actualizando estado:", e);
    }
};

const confirmDelete = async (guide) => {
    if (confirm(`¿Estás seguro de eliminar permanentemente la guía "${guide.title}"?`)) {
        try {
            await axios.delete(`/api/guides/${guide.id}`);
            guides.value = guides.value.filter(g => g.id !== guide.id);
        } catch (e) {
            console.error(e);
        }
    }
};

const goToPublic = (guide) => {
    window.open(`/guides/${guide.slug}`, '_blank');
};

const getStatusLabel = (s) => s === 'published' ? 'PUBLICADA' : (s === 'pending' ? 'PENDIENTE' : 'RECHAZADA');
const getStatusSeverity = (s) => s === 'published' ? 'success' : (s === 'pending' ? 'warning' : 'danger');

onMounted(loadGuides);
</script>

<style scoped>
/* Estilos específicos para que el DataTable encaje en el tema oscuro */
:deep(.admin-datatable) {
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 1.5rem;
    overflow: hidden;
}
:deep(.admin-datatable .p-datatable-header) {
    background: #111827;
    border-bottom: 1px solid #1f2937;
    padding: 1.5rem;
}
:deep(.admin-datatable .p-datatable-thead > tr > th) {
    background: #1a2233;
    color: #9ca3af;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1.5rem;
    border-bottom: 1px solid #1f2937;
}
:deep(.admin-datatable .p-datatable-tbody > tr) {
    background: #111827;
    color: #d1d5db;
    transition: background 0.2s;
}
:deep(.admin-datatable .p-datatable-tbody > tr:hover) {
    background: #1a2233;
}
:deep(.admin-datatable .p-datatable-tbody > tr > td) {
    padding: 1.5rem;
    border-bottom: 1px solid #1f2937;
}

/* Estilos para el render de la guía en el preview */
.guide-content-render :deep(img) {
    border-radius: 1.5rem;
    margin: 2rem 0;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
.guide-content-render :deep(h2) {
    color: white;
    font-size: 2rem;
    margin-top: 3rem;
}
.guide-content-render :deep(p) {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #9ca3af;
}
</style>

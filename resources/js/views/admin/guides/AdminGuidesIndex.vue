<template>
    <div class="p-4 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white m-0">Gestión de Guías</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Revisa, publica o rechaza las guías de la comunidad.</p>
            </div>
            <div class="flex gap-2">
                <Button label="Refrescar" icon="pi pi-refresh" outlined severity="secondary" @click="loadGuides" />
            </div>
        </div>

        <DataTable :value="guides" :loading="loading" paginator :rows="10" 
                   class="p-datatable-sm" responsiveLayout="stack" breakpoint="960px"
                   v-model:filters="filters" filterDisplay="menu"
                   :globalFilterFields="['title', 'user.name', 'game.title']">
            
            <template #header>
                <div class="flex justify-between items-center">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Buscar guía..." class="w-full md:w-80" />
                    </span>
                    <SelectButton v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" @change="onStatusFilterChange" />
                </div>
            </template>

            <template #empty> No se encontraron guías. </template>
            <template #loading> Cargando guías, por favor espere... </template>

            <Column field="title" header="Título" sortable style="min-width: 250px">
                <template #body="{ data }">
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-900 dark:text-white">{{ data.title }}</span>
                        <span class="text-xs text-gray-500 uppercase">{{ data.game?.title || 'Sin juego' }}</span>
                    </div>
                </template>
            </Column>

            <Column field="user.name" header="Autor" sortable>
                <template #body="{ data }">
                    <div class="flex items-center gap-2">
                        <Avatar :image="data.user?.avatar_url" :label="!data.user?.avatar_url ? data.user?.name?.charAt(0) : ''" shape="circle" size="small" />
                        <span>{{ data.user?.name }}</span>
                    </div>
                </template>
            </Column>

            <Column field="created_at" header="Fecha" sortable>
                <template #body="{ data }">
                    {{ data.created_at }}
                </template>
            </Column>

            <Column field="status" header="Estado" sortable>
                <template #body="{ data }">
                    <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" rounded />
                </template>
            </Column>

            <Column header="Acciones" style="min-width: 150px">
                <template #body="{ data }">
                    <div class="flex gap-2">
                        <Button icon="pi pi-eye" text rounded severity="info" v-tooltip.top="'Ver Guía'" @click="viewGuide(data)" />
                        
                        <template v-if="data.status === 'pending'">
                            <Button icon="pi pi-check" text rounded severity="success" v-tooltip.top="'Publicar'" @click="updateStatus(data, 'published')" />
                            <Button icon="pi pi-times" text rounded severity="danger" v-tooltip.top="'Rechazar'" @click="updateStatus(data, 'rejected')" />
                        </template>

                        <template v-if="data.status === 'published'">
                            <Button icon="pi pi-arrow-down" text rounded severity="warning" v-tooltip.top="'Retirar a Pendiente'" @click="updateStatus(data, 'pending')" />
                        </template>

                         <template v-if="data.status === 'rejected'">
                            <Button icon="pi pi-undo" text rounded severity="secondary" v-tooltip.top="'Revertir a Pendiente'" @click="updateStatus(data, 'pending')" />
                        </template>
                    </div>
                </template>
            </Column>
        </DataTable>

        <!-- Dialog para vista previa rápida -->
        <Dialog v-model:visible="viewDialog" :header="selectedGuide?.title" modal class="p-fluid" :style="{width: '70vw'}" :breakpoints="{'960px': '90vw'}">
            <div v-if="selectedGuide" class="space-y-6">
                <div class="flex gap-4 items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <Avatar :image="selectedGuide.user?.avatar_url" shape="circle" size="xlarge" />
                    <div>
                        <div class="text-xl font-bold">{{ selectedGuide.user?.name }}</div>
                        <div class="text-sm text-gray-500">Publicado el {{ selectedGuide.created_at }}</div>
                    </div>
                    <div class="ml-auto">
                        <Tag :value="getStatusLabel(selectedGuide.status)" :severity="getStatusSeverity(selectedGuide.status)" />
                    </div>
                </div>

                <div class="p-6 border border-gray-100 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <div class="guide-content-preview prose dark:prose-invert max-w-none" v-html="selectedGuide.content"></div>
                </div>
            </div>
            <template #footer>
                <Button label="Cerrar" icon="pi pi-times" text @click="viewDialog = false" />
                <Button v-if="selectedGuide?.status === 'pending'" label="Publicar Ahora" icon="pi pi-check" severity="success" @click="updateStatus(selectedGuide, 'published'); viewDialog = false" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { FilterMatchMode } from 'primevue/api';
import { useRouter } from 'vue-router';

// Componentes PrimeVue (Ya registrados globalmente en app.js pero importamos por si acaso para IDE)
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import InputText from 'primevue/inputtext';
import SelectButton from 'primevue/selectbutton';
import Dialog from 'primevue/dialog';
import Avatar from 'primevue/avatar';

const guides = ref([]);
const loading = ref(true);
const viewDialog = ref(false);
const selectedGuide = ref(null);
const router = useRouter();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const statusFilter = ref('all');
const statusOptions = [
    { label: 'Todas', value: 'all' },
    { label: 'Pendientes', value: 'pending' },
    { label: 'Publicadas', value: 'published' },
    { label: 'Rechazadas', value: 'rejected' }
];

const loadGuides = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/guides');
        guides.value = response.data.data;
    } catch (e) {
        console.error("Error al cargar guías para admin:", e);
    } finally {
        loading.value = false;
    }
};

const updateStatus = async (guide, status) => {
    try {
        await axios.patch(`/api/admin/guides/${guide.id}/status`, { status });
        // Actualizar localmente
        guide.status = status;
        alert(`Guía actualizada a ${status}`);
    } catch (e) {
        console.error("Error al actualizar estado:", e);
        alert("Error al actualizar el estado de la guía.");
    }
};

const viewGuide = (guide) => {
    selectedGuide.value = guide;
    viewDialog.value = true;
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'published': return 'Publicada';
        case 'pending': return 'Pendiente';
        case 'rejected': return 'Rechazada';
        default: return status;
    }
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'published': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'info';
    }
};

const onStatusFilterChange = () => {
    // Aquí podrías filtrar localmente o volver a pedir a la API
    if (statusFilter.value === 'all') {
        loadGuides();
    } else {
        // Filtro local simple para este ejemplo
        // Lo ideal sería que la API soportara el filtro
    }
};

onMounted(loadGuides);
</script>

<style scoped>
.guide-content-preview :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
}
</style>

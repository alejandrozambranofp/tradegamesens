<template>
    <div class="min-h-screen bg-[#0b0f19] text-white flex flex-col">
        <!-- Menú Global (el de la Home) -->
        <LandingNavbar />

        <!-- Espaciado para el Navbar fijo -->
        <div class="pt-24 pb-12 flex-grow">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Aquí se cargan las vistas (Mis Guías, Comunidad, etc.) -->
                <router-view />
            </div>
        </div>

        <LandingFooter />
    </div>
</template>

<script setup>
import LandingNavbar from './LandingNavbar.vue';
import LandingFooter from './LandingFooter.vue';
import { onMounted } from 'vue';

onMounted(() => {
    // Aseguramos que el fondo del body sea el correcto si hay scroll
    document.body.style.backgroundColor = '#0b0f19';
});
</script>

<style>
/* Reset de estilos para asegurar que el tema oscuro se vea bien en todas las vistas del panel */
.p-datatable {
    background-color: #111827 !important;
}
.p-datatable .p-datatable-thead > tr > th {
    background-color: #1a2332 !important;
    color: #9ca3af !important;
    border-color: #374151 !important;
}
.p-datatable .p-datatable-tbody > tr {
    background-color: #111827 !important;
    color: #d1d5db !important;
    border-color: #374151 !important;
}
.p-card {
    background-color: #111827 !important;
    border: 1px solid #374151 !important;
    color: white !important;
}
</style>
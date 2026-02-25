<template>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Gestión de Guías (SmokeCan)</h2>
            <router-link :to="{ name: 'guides.create' }" class="btn btn-success">
                Nueva Guía
            </router-link>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Contenido</th>
                            <th>Categorías</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="guide in guides" :key="guide.id">
                            <td>{{ guide.title }}</td>
                            <td>{{ guide.content.substring(0, 50) }}...</td>
                            <td>
                                <span v-for="cat in guide.categories" :key="cat.id" class="badge bg-info me-1">
                                    {{ cat.name }}
                                </span>
                            </td>
                            <td>
                                <button @click="deleteGuide(guide.id)" class="btn btn-outline-danger btn-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="guides.length === 0">
                            <td colspan="4" class="text-center">No hay guías disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import useGuides from '../composables/guides' // Asegúrate de que la ruta al archivo es correcta

const { guides, getGuides, deleteGuide } = useGuides()

// Al cargar el componente, traemos los datos de la API
onMounted(() => {
    getGuides()
})
</script>
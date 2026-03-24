<template>
    <div v-if="guide" class="max-w-4xl mx-auto py-8 px-4">
        <div class="mb-8">
            <Button icon="pi pi-arrow-left" label="Volver" text class="mb-4" @click="$router.back()" />
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">{{ guide.title }}</h1>
            
            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <i class="pi pi-calendar"></i> {{ formatDate(guide.created_at) }}
                </span>
                <Tag v-if="guide.game" :value="guide.game.title" severity="secondary" />
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-8 rounded-r-lg">
            <p class="text-blue-800 dark:text-blue-300 italic">
                Esta es una guía oficial de <strong>TradeGameSense</strong>.
            </p>
        </div>

        <div class="guide-content prose prose-lg dark:prose-invert max-w-none" v-html="guide.content">
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200">
            <h3 class="font-bold mb-3">Categorías:</h3>
            <div class="flex gap-2">
                <Tag v-for="cat in guide.categories" :key="cat.id" :value="cat.name" severity="info" />
            </div>
        </div>
    </div>
    <div v-else class="flex justify-center p-8">
        <ProgressSpinner />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const guide = ref(null);

const fetchGuide = async () => {
    try {
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data;
    } catch (error) {
        console.error("Error al obtener la guía:", error);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};

onMounted(fetchGuide);
</script>

<style>
/* Estilos para que el contenido HTML se vea bien */
.guide-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.guide-content p {
    margin-bottom: 1.2em;
    line-height: 1.6;
}
.guide-content h1, .guide-content h2 {
    margin-top: 1.5em;
    font-weight: bold;
}
</style>
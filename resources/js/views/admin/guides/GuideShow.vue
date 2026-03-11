<template>
    <div v-if="guide" class="min-h-screen bg-gray-50 dark:bg-gray-950 p-4 md:p-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <Button 
                    icon="pi pi-arrow-left" 
                    label="Volver al listado" 
                    class="p-button-text p-button-secondary" 
                    @click="$router.push('/admin/guides')" 
                />
                <Button 
                    icon="pi pi-pencil" 
                    label="Editar esta guía" 
                    severity="warning"
                    text
                    @click="$router.push('/admin/guides')" 
                />
            </div>

            <article class="bg-white dark:bg-gray-900 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-800 transition-all duration-300">
                
                <div class="p-8 md:p-12 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-br from-blue-50/50 via-transparent to-transparent dark:from-blue-950/20">
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <Tag :value="guide.game?.title" severity="warn" class="px-4 py-1 text-sm shadow-sm" />
                        <div class="flex gap-2">
                            <Tag v-for="cat in guide.categories" :key="cat.id" :value="cat.name" severity="info" rounded />
                        </div>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                        {{ guide.title }}
                    </h1>

                    <div class="flex items-center gap-4">
                        <Avatar icon="pi pi-user" shape="circle" size="xlarge" class="bg-blue-600 text-white shadow-md" />
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">{{ guide.user?.name || 'Administrador' }}</span>
                            <span class="text-sm text-gray-500 font-medium tracking-wide uppercase">Publicado el {{ formatDate(guide.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-14">
                    
                    <div class="guide-body text-gray-800 dark:text-gray-200 whitespace-pre-line text-xl leading-relaxed font-normal">
                        {{ guide.content }}
                    </div>
                </div>

                <div class="px-8 py-8 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 font-semibold tracking-widest uppercase flex items-center gap-2">
                        <i class="pi pi-check-circle text-green-500"></i>
                        Fin de la Guía
                    </div>
                    <div class="flex gap-4">
                        <Button icon="pi pi-thumbs-up" label="Útil" severity="secondary" rounded />
                        <Button icon="pi pi-share-alt" severity="secondary" rounded text />
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-950">
        <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="4" />
        <p class="mt-4 text-gray-500 font-bold animate-pulse uppercase tracking-widest">Preparando Guía...</p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const guide = ref(null);

const loadGuide = async () => {
    try {
        // Buscamos la guía usando el parámetro 'id' de la ruta (que enviamos como slug)
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data;
    } catch (error) {
        console.error("Error al cargar la guía:", error);
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

onMounted(loadGuide);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

.guide-body {
    font-family: 'Inter', sans-serif;
    letter-spacing: -0.011em;
}
</style>
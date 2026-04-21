<template>
    <div class="min-h-screen p-4 pb-12" style="background-color: #0b0f19;">
        <div v-if="guide" class="max-w-5xl mx-auto">
            
            <div class="flex justify-between items-center mb-8">
                <Button icon="pi pi-arrow-left" label="Volver" text @click="$router.back()" class="text-gray-400 hover:bg-gray-800" />
                <div class="flex gap-3">
                    <Button 
                        :icon="isFavorite ? 'pi pi-star-fill' : 'pi pi-star'" 
                        :severity="isFavorite ? 'warning' : 'secondary'" 
                        rounded 
                        outlined 
                        class="border-gray-800 transition-all duration-300"
                        :class="{ 'text-yellow-500': isFavorite, 'text-gray-500': !isFavorite }"
                        @click="handleFavoriteToggle"
                        :loading="loadingFavorite"
                    />
                </div>
            </div>

            <header class="p-8 md:p-12 rounded-t-3xl border-x border-t border-gray-800 shadow-2xl" style="background-color: #111827;">
                <div class="flex flex-wrap gap-3 mb-6">
                    <Tag v-if="guide.game" :value="guide.game.title" class="bg-primary/20 text-primary border border-primary/30 px-3 py-1" />
                    <Tag v-for="cat in guide.categories" :key="cat.id" :value="cat.name" class="bg-gray-800 text-gray-300 border border-gray-700 px-3 py-1" />
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tighter">
                    {{ guide.title }}
                </h1>

                <div class="flex items-center gap-4 pt-8 border-t border-gray-800/50">
                    <Avatar 
                        :image="guide.user?.avatar_url" 
                        shape="circle" size="large" class="border-2 border-primary shadow-lg" 
                    />
                    <div>
                        <div class="text-white font-bold text-lg">{{ guide.user?.name || 'Autor' }}</div>
                        <div class="text-gray-500 text-sm flex items-center gap-2">
                            <i class="pi pi-calendar-plus text-xs"></i>
                            Publicado el {{ formatDate(guide.created_at) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8 md:p-16 rounded-b-3xl border border-gray-800 shadow-xl bg-[#0d121d]">
                <article class="guide-content" v-html="guide.content"></article>
                <footer class="mt-16 pt-12 border-t border-gray-800 text-center text-gray-600 text-sm italic">
                    Fin de la guía oficial de TradeGameSense
                </footer>
            </main>
        </div>

        <div v-else class="max-w-4xl mx-auto mt-20 text-center">
            <ProgressSpinner strokeWidth="3" animationDuration=".5s" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import ProgressSpinner from 'primevue/progressspinner';
import { authStore } from "@/store/auth"; // Importamos para saber quién es el usuario

const route = useRoute();
const auth = authStore();
const guide = ref(null);
const isFavorite = ref(false);
const loadingFavorite = ref(false);

const fetchGuide = async () => {
    try {
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data || response.data;
        
        // Comprobamos si la guía ya está en los favoritos del usuario actual
        // Esto depende de si tu GuideResource incluye un campo 'is_favorite'
        // Si no lo incluye, lo ideal es que el backend lo envíe.
        isFavorite.value = guide.value.is_favorite || false;
    } catch (error) {
        console.error("Error al obtener la guía:", error);
    }
};

const handleFavoriteToggle = async () => {
    if (!auth.user) return alert("Debes iniciar sesión para guardar favoritos");
    
    loadingFavorite.value = true;
    try {
        // Llamada al método toggleFavorite de tu GuideController.php
        await axios.post(`/api/guides/${guide.value.id}/favorite`);
        
        // Cambiamos el estado localmente para que el icono cambie al instante
        isFavorite.value = !isFavorite.value;
    } catch (error) {
        console.error("Error al actualizar favorito:", error);
    } finally {
        loadingFavorite.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};

onMounted(fetchGuide);
</script>

<style>
.guide-content { color: #cbd5e1; font-size: 1.15rem; line-height: 1.8; }
.guide-content h2 { color: #ffffff; font-size: 2rem; font-weight: 800; margin: 2.5rem 0 1.5rem 0; }
.guide-content p { margin-bottom: 1.5rem; }
.guide-content img { max-width: 100%; border-radius: 1.5rem; margin: 2.5rem 0; border: 1px solid #1e293b; }
</style>
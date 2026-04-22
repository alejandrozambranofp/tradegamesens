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

            <!-- SECCIÓN DE VALORACIONES Y COMENTARIOS -->
            <section class="mt-12 space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-3xl font-bold text-white m-0 flex items-center gap-3">
                        <i class="pi pi-comments text-primary"></i>
                        Comunidad
                    </h2>
                    <div class="bg-gray-800/50 px-4 py-2 rounded-full border border-gray-700">
                        <span class="text-yellow-500 font-bold text-xl">{{ guide.rating || 0 }}</span>
                        <span class="text-gray-500 ml-1">/ 5 ({{ guide.ratings_count || 0 }} votos)</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Listado de Comentarios -->
                    <div class="lg:col-span-7 space-y-6">
                        <div v-if="guide.ratings?.length > 0" class="space-y-4">
                            <div v-for="r in guide.ratings" :key="r.id" class="p-6 rounded-2xl bg-[#111827] border border-gray-800 transition-all hover:border-gray-700">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <Avatar :image="r.user?.avatar_url" shape="circle" size="large" class="border border-primary/30" />
                                        <div>
                                            <div class="text-white font-bold">{{ r.user?.name || 'Usuario' }}</div>
                                            <div class="text-gray-500 text-xs">{{ r.created_at }}</div>
                                        </div>
                                    </div>
                                    <Rating v-model="r.score" readonly :cancel="false" />
                                </div>
                                <p class="text-gray-300 leading-relaxed m-0 italic">"{{ r.comment || 'Sin comentario' }}"</p>
                            </div>
                        </div>
                        <div v-else class="p-12 rounded-3xl border-2 border-dashed border-gray-800 text-center text-gray-600">
                            <i class="pi pi-comment text-4xl mb-4 opacity-20"></i>
                            <p>Aún no hay comentarios. ¡Sé el primero en opinar!</p>
                        </div>
                    </div>

                    <!-- Formulario de Valoración -->
                    <div class="lg:col-span-5">
                        <div class="p-8 rounded-3xl bg-[#111827] border border-primary/20 sticky top-24 shadow-2xl">
                            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                                <i class="pi pi-star-fill text-yellow-500"></i>
                                Deja tu valoración
                            </h3>
                            
                            <div v-if="auth.user" class="space-y-6">
                                <div class="flex flex-col items-center gap-3 p-4 bg-gray-900/50 rounded-2xl border border-gray-800">
                                    <span class="text-gray-400 text-sm uppercase tracking-wider">Tu puntuación</span>
                                    <Rating v-model="userRating.score" :cancel="false" class="scale-150 py-2" />
                                </div>

                                <div class="space-y-2">
                                    <label class="text-gray-400 text-sm px-1">Tu comentario (opcional)</label>
                                    <Textarea v-model="userRating.comment" rows="4" autoResize placeholder="¿Qué te ha parecido esta guía?" class="w-full bg-[#0b0f19] border-gray-700 text-white rounded-xl focus:border-primary p-4" />
                                </div>

                                <Button 
                                    label="Publicar Valoración" 
                                    icon="pi pi-send" 
                                    class="w-full p-4 text-lg font-bold shadow-lg shadow-primary/20" 
                                    :loading="isSubmitting"
                                    @click="submitRating"
                                />
                            </div>
                            <div v-else class="text-center py-8">
                                <p class="text-gray-400 mb-6">Inicia sesión para valorar esta guía</p>
                                <Button label="Iniciar Sesión" icon="pi pi-user" outlined class="w-full" @click="$router.push('/login')" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
import Rating from 'primevue/rating';
import Textarea from 'primevue/textarea';
import { authStore } from "@/store/auth";

const route = useRoute();
const auth = authStore();
const guide = ref(null);
const isFavorite = ref(false);
const loadingFavorite = ref(false);

// Estado para la valoración del usuario
const userRating = ref({
    score: 0,
    comment: ''
});
const isSubmitting = ref(false);

const fetchGuide = async () => {
    try {
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data || response.data;
        
        // Comprobamos si la guía ya está en los favoritos del usuario actual
        // Esto depende de si tu GuideResource incluye un campo 'is_favorite'
        // Si no lo incluye, lo ideal es que el backend lo envíe.
        isFavorite.value = guide.value.is_favorite || false;

        // Comprobamos si el usuario actual ya tiene una valoración para pre-rellenar el formulario
        if (auth.user) {
            const existingRating = (guide.value.ratings || []).find(r => r.user_id === auth.user.id);
            if (existingRating) {
                userRating.value.score = existingRating.score;
                userRating.value.comment = existingRating.comment;
            }
        }
    } catch (error) {
        console.error("Error al obtener la guía:", error);
        // Evitamos que se quede el cargando infinitamente si hay error
        guide.value = { title: "Error al cargar la guía", content: "No se pudo encontrar la guía solicitada o hubo un problema en el servidor.", categories: [] };
    }
};

const submitRating = async () => {
    if (userRating.value.score === 0) return alert("Por favor, selecciona una puntuación");
    
    isSubmitting.value = true;
    try {
        await axios.post('/api/ratings', {
            guide_id: guide.value.id,
            score: userRating.value.score,
            comment: userRating.value.comment
        });
        
        // Recargamos la guía para ver la nueva valoración y el promedio actualizado
        await fetchGuide();
        alert("¡Gracias por tu valoración!");
    } catch (error) {
        console.error("Error al enviar valoración:", error);
        alert("No se pudo guardar la valoración.");
    } finally {
        isSubmitting.value = false;
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
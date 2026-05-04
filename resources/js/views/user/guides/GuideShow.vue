<template>
    <div class="min-h-screen pt-28 pb-12 bg-[#0b0f19]">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Cabecera de Botones Simétricos -->
            <div class="flex justify-between items-center mb-6 pt-4">
                <!-- Botón Volver (Izquierda) -->
                <Button label="Volver" icon="pi pi-chevron-left" @click="$router.back()" 
                    rounded outlined
                    class="!border-[#5369F2] transition-all duration-300 font-bold !text-[#5369F2] hover:!bg-[#5369F2]/10" />
                
                <!-- Botón Favoritos (Derecha) -->
                <div v-if="guide">
                    <Button 
                        :label="isFavorite ? 'En favoritos' : 'Guardar en favoritos'"
                        :icon="isFavorite ? 'pi pi-star-fill' : 'pi pi-star'" 
                        rounded outlined 
                        class="transition-all duration-300 font-bold"
                        :class="{ '!text-[#5369F2] !border-[#5369F2]/50 !bg-[#5369F2]/5': isFavorite, '!text-gray-400 !border-gray-800 hover:!text-[#5369F2] hover:!border-[#5369F2] hover:!bg-[#5369F2]/10': !isFavorite }"
                        @click="handleFavoriteToggle"
                        :loading="loadingFavorite"
                    />
                </div>
            </div>

            <div v-if="guide">
            <header class="p-8 md:p-12 rounded-t-3xl border-x border-t border-gray-800 shadow-2xl" style="background-color: #111827;">
                <div class="flex flex-wrap gap-3 mb-6">
                    <Tag v-if="guide.game" :value="guide.game.title" class="!bg-[#5369F2]/20 !text-[#5369F2] !border-[#5369F2]/30 px-3 py-1 font-bold" />
                    <Tag v-if="guide.difficulty" :value="'Dificultad ' + guide.difficulty" :class="getDifficultyClass(guide.difficulty)" class="px-3 py-1 border" />
                    <Tag v-for="cat in guide.categories" :key="cat.id" :value="cat.name" class="!bg-[#5369F2]/20 !text-[#5369F2] !border-[#5369F2]/30 px-3 py-1 font-bold" />
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tighter break-words">
                    {{ guide.title }}
                </h1>

                <div class="flex items-center gap-4 pt-8 border-t border-gray-800/50">
                    <Avatar 
                        :image="guide.user?.avatar_url" 
                        shape="circle" size="large" class="border-2 border-[#5369F2] shadow-lg" 
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
                        <i class="pi pi-comments text-[#5369F2]"></i>
                        Comunidad
                    </h2>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Listado de Comentarios -->
                    <div class="lg:col-span-7 space-y-6">
                        <div v-if="guide.ratings?.length > 0" class="space-y-4">
                            <div v-for="r in guide.ratings" :key="r.id" class="p-6 rounded-2xl bg-[#111827] border border-gray-800 transition-all hover:border-gray-700">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <Avatar :image="r.user?.avatar_url" shape="circle" size="large" class="border border-[#5369F2]/30" />
                                        <div>
                                            <div class="text-white font-bold">{{ r.user?.name || 'Usuario' }}</div>
                                            <div class="text-gray-500 text-xs">{{ r.created_at }}</div>
                                        </div>
                                    </div>
                                    <Rating v-model="r.score" readonly :cancel="false" :pt="{ onIcon: { class: '!text-[#5369F2] !fill-[#5369F2]' }, offIcon: { class: '!text-gray-500' } }" />
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
                        <div class="p-8 rounded-3xl bg-[#111827] border border-[#5369F2]/20 sticky top-24 shadow-2xl">
                            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                                <i class="pi pi-star-fill text-yellow-500"></i>
                                Deja tu valoración
                            </h3>
                            
                            <div v-if="auth.user?.name" class="space-y-6">
                                <div class="flex flex-col items-center gap-3 p-4 bg-gray-900/50 rounded-2xl border border-gray-800">
                                    <span class="text-gray-400 text-sm uppercase tracking-wider">Tu puntuación</span>
                                    <Rating v-model="userRating.score" :cancel="false" class="scale-150 py-2 custom-rating" :pt="{ 
                                        onIcon: { class: '!text-[#5369F2] !fill-[#5369F2]' },
                                        offIcon: { class: 'hover:!text-[#5369F2] hover:!fill-transparent hover:!stroke-[#5369F2] transition-colors' },
                                        item: { class: 'hover:!text-[#5369F2] hover:!fill-transparent transition-colors' }
                                    }" />
                                </div>

                                <div class="space-y-2">
                                    <label class="text-gray-400 text-sm px-1">Tu comentario (opcional)</label>
                                    <Textarea v-model="userRating.comment" rows="4" autoResize placeholder="¿Qué te ha parecido esta guía?" class="w-full bg-[#0b0f19] border-gray-700 text-white rounded-xl focus:border-[#5369F2] p-4" />
                                </div>

                                <div class="flex gap-3">
                                    <Button 
                                        v-if="existingUserRating"
                                        label="Borrar" 
                                        icon="pi pi-trash" 
                                        severity="danger"
                                        outlined
                                        class="flex-1 p-4 font-bold" 
                                        :loading="isSubmitting"
                                        @click="deleteRating"
                                    />
                                    <Button 
                                        :label="existingUserRating ? 'Actualizar' : 'Publicar'" 
                                        icon="pi pi-send" 
                                        class="flex-1 p-4 font-bold shadow-lg shadow-[#5369F2]/20 !bg-[#5369F2] !border-[#5369F2] !text-white" 
                                        :loading="isSubmitting"
                                        @click="submitRating"
                                    />
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <p class="text-gray-400 mb-6">Inicia sesión para valorar esta guía</p>
                                <Button label="Iniciar Sesión" icon="pi pi-user" outlined class="w-full !text-[#5369F2] !border-[#5369F2] hover:!bg-[#5369F2]/10" @click="$router.push('/login')" />
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
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import Rating from 'primevue/rating';
import Textarea from 'primevue/textarea';
import { authStore } from "@/store/auth";

const route = useRoute();
const router = useRouter();
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

const existingUserRating = computed(() => {
    if (!guide.value || !guide.value.ratings || !auth.user?.name) return null;
    return guide.value.ratings.find(r => r.user_id === auth.user.id);
});

const fetchGuide = async () => {
    try {
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data || response.data;
        
        // Comprobamos si la guía ya está en los favoritos del usuario actual
        // Esto depende de si tu GuideResource incluye un campo 'is_favorite'
        // Si no lo incluye, lo ideal es que el backend lo envíe.
        isFavorite.value = guide.value.is_favorite || false;

        // Comprobamos si el usuario actual ya tiene una valoración para pre-rellenar el formulario
        if (auth.user?.name) {
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
    if (!auth.user?.name) {
        return router.push('/login');
    }
    if (userRating.value.score === 0) return alert("Por favor, selecciona una puntuación");
    
    isSubmitting.value = true;
    try {
        await axios.post('/api/ratings', {
            guide_id: guide.value.id,
            score: userRating.value.score,
            comment: userRating.value.comment
        });
        
        await fetchGuide();
        alert(existingUserRating.value ? "¡Valoración actualizada!" : "¡Gracias por tu valoración!");
    } catch (error) {
        console.error("Error al enviar valoración:", error);
        if (error.response?.status === 401) {
            alert("Para poner tu valoración tienes que tener una cuenta. Redirigiendo...");
            router.push('/login');
        } else {
            alert("No se pudo guardar la valoración.");
        }
    } finally {
        isSubmitting.value = false;
    }
};

const deleteRating = async () => {
    if (!existingUserRating.value) return;
    if (!confirm("¿Seguro que quieres borrar tu valoración?")) return;
    
    isSubmitting.value = true;
    try {
        await axios.delete(`/api/ratings/${existingUserRating.value.id}`);
        
        // Limpiamos el formulario local
        userRating.value.score = 0;
        userRating.value.comment = '';
        
        await fetchGuide();
        alert("Valoración eliminada.");
    } catch (error) {
        console.error("Error al borrar valoración:", error);
        alert("No se pudo borrar la valoración.");
    } finally {
        isSubmitting.value = false;
    }
};

const handleFavoriteToggle = async () => {
    if (!auth.user?.name) {
        return router.push('/login');
    }
    
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

const getDifficultyClass = (diff) => {
    switch(diff) {
        case 'S': return 'bg-red-900/40 text-red-400 border-red-500/30';
        case 'A': return 'bg-orange-900/40 text-orange-400 border-orange-500/30';
        case 'B': return 'bg-yellow-900/40 text-yellow-400 border-yellow-500/30';
        case 'C': return 'bg-blue-900/40 text-blue-400 border-blue-500/30';
        case 'D': return 'bg-blue-900/20 text-blue-400 border-blue-500/20';
        default: return 'bg-gray-800 text-gray-300 border-gray-700';
    }
};

onMounted(fetchGuide);
</script>

<style>
.guide-content { 
    color: #cbd5e1; 
    font-size: 1.15rem; 
    line-height: 1.8; 
    overflow-wrap: break-word; 
    word-break: break-word; 
}
.guide-content h2 { color: #ffffff; font-size: 2rem; font-weight: 800; margin: 2.5rem 0 1.5rem 0; overflow-wrap: break-word; }
.guide-content p { margin-bottom: 1.5rem; overflow-wrap: break-word; }
.guide-content img { max-width: 100%; height: auto; border-radius: 1.5rem; margin: 2.5rem 0; border: 1px solid #1e293b; }
.guide-content * { max-width: 100%; overflow-wrap: break-word; }

/* Forzar azul en hover para las estrellas (por si pt falla) */
.custom-rating [data-pc-section="item"]:hover svg,
.custom-rating [data-pc-section="item"]:hover .p-rating-icon {
    color: #5369F2 !important;
    stroke: #5369F2 !important;
}
</style>
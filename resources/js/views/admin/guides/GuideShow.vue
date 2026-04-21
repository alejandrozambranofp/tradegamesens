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

        <div v-if="guide.user_id === 1" class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-8 rounded-r-lg">
            <p class="text-blue-800 dark:text-blue-300 italic mb-0">
                Esta es una guía oficial de <strong>TradeGameSense</strong>
            </p>
        </div>
        <div v-else class="bg-gray-50 dark:bg-gray-800 border-l-4 border-gray-500 p-4 mb-8 rounded-r-lg flex items-center gap-2">
            <i class="pi pi-user text-xl text-gray-500 dark:text-gray-400"></i>
            <p class="text-gray-800 dark:text-gray-300 italic mb-0">
                Guía de la comunidad creada por <strong>{{ guide.user?.name || 'Usuario' }}</strong>
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

        <!-- SECCIÓN DE VALORACIONES Y COMENTARIOS -->
        <div class="mt-10 pt-6 border-t border-gray-200">
            <h3 class="font-bold text-2xl mb-6">Valoraciones de la Comunidad</h3>
            
            <!-- List existing ratings -->
            <div v-if="guide.ratings && guide.ratings.length > 0" class="flex flex-col gap-4 mb-8">
                <div v-for="rating in guide.ratings" :key="rating.id" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-user text-gray-500 bg-gray-100 dark:bg-gray-700 p-2 rounded-full"></i>
                            <span class="font-bold text-sm">{{ rating.user?.name || 'Usuario Anónimo' }}</span>
                        </div>
                        <Rating :modelValue="rating.score" readonly :cancel="false" />
                    </div>
                    <p v-if="rating.comment" class="text-gray-700 dark:text-gray-300 text-sm mt-3 mb-0 italic">"{{ rating.comment }}"</p>
                    <span class="text-xs text-gray-400 mt-3 block flex items-center gap-1"><i class="pi pi-clock" style="font-size: 0.7rem"></i> {{ rating.created_at }}</span>
                </div>
            </div>
            <div v-else class="bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center text-gray-500 mb-8 italic">
                Aún no hay valoraciones. ¡Sé el primero en opinar!
            </div>

            <!-- Add / Edit Rating Form -->
            <div v-if="authUser" class="bg-blue-50/50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-200 dark:border-blue-800/50 shadow-sm">
                <h4 class="font-bold text-lg mb-4 text-blue-900 dark:text-blue-300">
                    <i class="pi pi-star-fill mr-2 text-yellow-500"></i>
                    {{ existingUserRating ? 'Modifica tu valoración' : 'Deja tu valoración' }}
                </h4>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 w-fit">
                        <span class="font-semibold text-sm">Tu puntuación:</span>
                        <Rating v-model="userRatingForm.score" :cancel="false" />
                    </div>
                    <div>
                        <label class="font-semibold text-sm block mb-2 text-gray-700 dark:text-gray-300">Comentario (Opcional):</label>
                        <Textarea v-model="userRatingForm.comment" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="¿Qué te pareció la guía? Deja tus consejos..." />
                    </div>
                    <div class="flex gap-3 justify-end mt-2">
                        <Button v-if="existingUserRating" label="Borrar valoración" severity="danger" outlined icon="pi pi-trash" @click="deleteRating" :loading="isSubmitting" />
                        <Button :label="existingUserRating ? 'Actualizar valoración' : 'Enviar valoración'" icon="pi pi-send" @click="submitRating" :loading="isSubmitting" :disabled="userRatingForm.score === 0" severity="primary" class="shadow-md" />
                    </div>
                </div>
            </div>
            <div v-else class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl text-center border border-gray-200 dark:border-gray-700">
                <i class="pi pi-lock text-3xl text-gray-400 mb-3 block"></i>
                <span class="text-gray-600 dark:text-gray-400 font-medium">Debes iniciar sesión para valorar esta guía.</span>
            </div>
        </div>
    </div>
    <div v-else class="flex justify-center p-8">
        <ProgressSpinner />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { authStore } from "@/store/auth";

import Rating from 'primevue/rating';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import ProgressSpinner from 'primevue/progressspinner';

const route = useRoute();
const auth = authStore();
const guide = ref(null);

const authUser = computed(() => auth.user);
const existingUserRating = computed(() => {
    if (!guide.value || !guide.value.ratings || !authUser.value) return null;
    return guide.value.ratings.find(r => r.user_id === authUser.value.id);
});

const userRatingForm = ref({ score: 0, comment: '' });
const isSubmitting = ref(false);

const fetchGuide = async () => {
    try {
        const response = await axios.get(`/api/guides/${route.params.id}`);
        guide.value = response.data.data;
        
        if (existingUserRating.value) {
            userRatingForm.value = {
                score: existingUserRating.value.score,
                comment: existingUserRating.value.comment || ''
            };
        } else {
            userRatingForm.value = { score: 0, comment: '' };
        }
    } catch (error) {
        console.error("Error al obtener la guía:", error);
    }
};

const submitRating = async () => {
    if (userRatingForm.value.score === 0) return;
    
    isSubmitting.value = true;
    try {
        await axios.post('/api/ratings', {
            guide_id: guide.value.id,
            score: userRatingForm.value.score,
            comment: userRatingForm.value.comment
        });
        await fetchGuide(); // Refresh data to show changes
    } catch (error) {
        console.error("Error submitting rating", error);
        alert("Ocurrió un error al enviar tu valoración.");
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
        await fetchGuide();
    } catch (error) {
        console.error("Error deleting rating", error);
        alert("No se pudo borrar la valoración.");
    } finally {
        isSubmitting.value = false;
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
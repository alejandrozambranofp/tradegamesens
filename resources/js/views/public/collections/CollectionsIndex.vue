<template>
    <div class="min-h-screen p-6 md:p-12 pt-28 md:pt-32 bg-[#0b0f19]">
        <div class="max-w-[1600px] mx-auto">
            <!-- Cabecera de la Página -->
            <div class="mb-12">
                <h1 class="text-4xl font-black text-white font-orbitron tracking-widest mb-2 uppercase">
                    Explorar <span class="text-primary">Colecciones</span>
                </h1>
                <p class="text-gray-400 font-medium">Descubre las mejores guías organizadas por juego.</p>
                <div class="w-20 h-1 bg-primary mt-4 rounded-full"></div>
            </div>

            <!-- Grid de Colecciones -->
            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div v-for="i in 8" :key="i" class="h-[400px] bg-gray-800/20 rounded-3xl animate-pulse"></div>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="game in games" :key="game.id" 
                    @click="filterByGame(game.id)"
                    class="collection-card group relative bg-[#111827] h-48 rounded-3xl border border-white/5 overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] cursor-pointer">
                    
                    <!-- Imagen de Fondo con poca opacidad -->
                    <div class="absolute inset-0 z-0">
                        <img :src="game.cover || '/images/default-game.png'" :alt="game.title" 
                            class="w-full h-full object-cover opacity-20 grayscale group-hover:grayscale-0 group-hover:opacity-40 transition-all duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/40 to-transparent"></div>
                    </div>

                    <!-- Brillo de Cristal (Efecto Shine) -->
                    <div class="absolute inset-0 z-10 pointer-events-none overflow-hidden rounded-3xl">
                        <div class="shine-effect absolute -inset-full top-0 block h-full w-1/2 -skew-x-12 bg-gradient-to-r from-transparent via-white/5 to-transparent group-hover:animate-shine"></div>
                    </div>

                    <!-- Contenido Centrado -->
                    <div class="relative z-20 h-full flex flex-col items-center justify-center p-6 text-center">
                        <h2 class="text-2xl font-black text-white font-orbitron tracking-wider mb-2 uppercase drop-shadow-lg group-hover:text-primary transition-colors">
                            {{ game.title }}
                        </h2>
                        
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-[1px] bg-primary/50"></span>
                            <span class="text-xs font-bold text-primary uppercase tracking-widest bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                                {{ getGuidesCount(game.id) }}
                            </span>
                            <span class="w-8 h-[1px] bg-primary/50"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';

const router = useRouter();
const games = ref([]);
const guides = ref([]);
const loading = ref(true);

const loadData = async () => {
    try {
        const [resGames, resGuides] = await Promise.all([
            axios.get('/api/games'),
            axios.get('/api/guides')
        ]);
        games.value = resGames.data.data || resGames.data;
        guides.value = resGuides.data.data || resGuides.data;
    } catch (e) {
        console.error("Error cargando colecciones:", e);
    } finally {
        loading.value = false;
    }
};

const getGameGuides = (gameId) => {
    return guides.value.filter(g => g.game_id === gameId);
};

const getGuidesCount = (gameId) => {
    const count = getGameGuides(gameId).length;
    return count === 1 ? '1 Guía' : `${count} Guías`;
};

const viewGuide = (guide) => {
    router.push({ name: 'guides.show', params: { id: guide.slug || guide.id } });
};

const filterByGame = (gameId) => {
    router.push({ name: 'collections.show', params: { id: gameId } });
};

onMounted(loadData);
</script>

<style scoped>
.collection-card {
    backdrop-filter: blur(10px);
}

/* Efecto Brillo de Cristal (Shine) */
@keyframes shine {
    100% {
        left: 200%;
    }
}

.animate-shine {
    animation: shine 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}

/* Animación suave para los items de la lista */
.group\/item:hover {
    transform: translateX(4px);
}
</style>

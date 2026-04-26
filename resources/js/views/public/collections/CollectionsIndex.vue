<template>
    <div class="min-h-screen p-6 md:p-12 pt-28 md:pt-32 bg-[#0b0f19]">
        <div class="max-w-[1600px] mx-auto">
            <!-- Cabecera de la Página -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-8">
                <div class="flex-shrink-0">
                    <h1 class="text-4xl font-black text-white font-orbitron tracking-widest mb-2 uppercase">
                        Explorar <span class="text-primary">Juegos</span>
                    </h1>
                    <p class="text-gray-400 font-medium">Encuentra las mejores guías para tus títulos favoritos.</p>
                    <div class="w-20 h-1 bg-primary mt-4 rounded-full"></div>
                </div>

                <!-- Buscador Integrado -->
                <div class="relative max-w-2xl w-full">
                    <div class="flex items-center bg-[#111827] rounded-2xl p-1.5 border border-white/5 shadow-2xl focus-within:border-primary/50 transition-all">
                        <div class="flex-grow flex items-center px-4">
                            <i class="pi pi-search text-gray-500 mr-2"></i>
                            <InputText 
                                v-model="searchStore.query" 
                                placeholder="Buscar guías..." 
                                class="!w-full !border-none !bg-transparent !text-white !py-2 placeholder:!text-gray-500 !shadow-none focus:!ring-0 font-inter text-sm"
                            />
                        </div>
                        
                        <div class="h-6 w-px bg-white/10 mx-2 hidden sm:block"></div>

                        <div class="hidden sm:block">
                            <Select 
                                v-model="searchStore.gameId" 
                                :options="games" 
                                optionLabel="title" 
                                optionValue="id" 
                                placeholder="Juego" 
                                class="!border-none !bg-transparent !text-gray-400 !w-32 focus:!ring-0"
                                filter
                                showClear
                                :pt="{
                                    root: '!bg-transparent',
                                    label: '!py-1 !text-xs !font-bold !uppercase !tracking-wider',
                                    trigger: '!w-6'
                                }"
                            />
                        </div>

                        <div class="h-6 w-px bg-white/10 mx-2 hidden md:block"></div>

                        <div class="hidden md:block">
                            <Select 
                                v-model="searchStore.categoryId" 
                                :options="categories" 
                                optionLabel="name" 
                                optionValue="id" 
                                placeholder="Categoría" 
                                class="!border-none !bg-transparent !text-gray-400 !w-32 focus:!ring-0"
                                showClear
                                :pt="{
                                    root: '!bg-transparent',
                                    label: '!py-1 !text-xs !font-bold !uppercase !tracking-wider',
                                    trigger: '!w-6'
                                }"
                            />
                        </div>

                        <Button 
                            icon="pi pi-search" 
                            class="!rounded-xl !w-10 !h-10 !bg-primary !border-none !flex-shrink-0 !shadow-lg hover:!scale-105 transition-transform ml-2"
                            @click="loadGuides"
                        />
                    </div>
                </div>
            </div>

            <!-- Resultados: Guías (Cuando hay filtros) -->
            <div v-if="hasFilters">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-white uppercase tracking-widest flex items-center gap-3">
                        <i class="pi pi-filter text-primary"></i>
                        Resultados de búsqueda
                    </h2>
                    <Button label="Limpiar Filtros" icon="pi pi-times" text severity="secondary" @click="searchStore.clearFilters()" class="!text-xs !font-bold" />
                </div>

                <div v-if="loadingGuides" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div v-for="i in 8" :key="i" class="h-80 bg-white/5 rounded-[32px] animate-pulse"></div>
                </div>

                <div v-else-if="filteredGuides.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div v-for="guide in filteredGuides" :key="guide.id" 
                        @click="viewGuide(guide)"
                        class="guide-card group bg-[#111827] rounded-[32px] border border-white/5 overflow-hidden transition-all duration-500 hover:-translate-y-2 cursor-pointer shadow-xl">
                        
                        <div class="p-6">
                            <!-- Título y Badge -->
                            <div class="flex justify-between items-start gap-4 mb-4">
                                <h3 class="text-base font-bold text-white line-clamp-2 leading-tight group-hover:text-primary transition-colors font-inter">
                                    {{ guide.title }}
                                </h3>
                                <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded-lg border border-yellow-500/20 flex-shrink-0">
                                    <i class="pi pi-star-fill text-yellow-500 text-[10px]"></i>
                                    <span class="text-[10px] font-black text-yellow-500">{{ guide.rating || '0.0' }}</span>
                                </div>
                            </div>

                            <!-- Imagen Destacada -->
                            <div class="relative h-40 overflow-hidden rounded-2xl bg-[#0b0f19] border border-white/5 mb-4">
                                <img v-if="guide.image_url" :src="guide.image_url" :alt="guide.title" 
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                <div v-else class="h-full flex flex-col items-center justify-center text-gray-700 gap-2 opacity-30">
                                    <i class="pi pi-image text-2xl"></i>
                                    <span class="text-[8px] font-black uppercase tracking-widest">Sin imagen</span>
                                </div>
                                
                                <div class="absolute top-2 left-2">
                                    <Tag :value="guide.game?.title" severity="info" class="!bg-primary/80 !backdrop-blur-md !text-[9px] !font-black !uppercase !px-2 !py-0.5" />
                                </div>
                            </div>

                            <!-- Footer Card -->
                            <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-auto">
                                <div class="flex items-center gap-2">
                                    <Avatar :image="guide.user?.avatar_url" shape="circle" size="small" class="!w-6 !h-6 border border-white/10" />
                                    <span class="text-[10px] text-gray-500 font-medium">Por {{ guide.user?.name }}</span>
                                </div>
                                <i class="pi pi-arrow-up-right text-gray-600 group-hover:text-primary transition-colors text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado Vacío -->
                <div v-else class="text-center py-20 bg-[#111827] rounded-[40px] border border-dashed border-white/10">
                    <i class="pi pi-search text-5xl text-gray-700 mb-4"></i>
                    <h2 class="text-xl font-bold text-gray-500 font-orbitron">No se encontraron guías</h2>
                    <p class="text-gray-600 mt-2">Prueba con otros términos o filtros.</p>
                    <Button label="Limpiar Búsqueda" icon="pi pi-refresh" outlined class="mt-6 !border-white/10 !text-white" @click="searchStore.clearFilters()" />
                </div>
            </div>

            <!-- Rejilla de Juegos (Cuando NO hay filtros) -->
            <div v-else>
                <h2 class="text-xl font-bold text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="pi pi-th-large text-primary"></i>
                    Colecciones Destacadas
                </h2>

                <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div v-for="i in 8" :key="i" class="h-48 bg-gray-800/20 rounded-3xl animate-pulse"></div>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="game in games" :key="game.id" 
                        @click="filterByGame(game.id)"
                        class="collection-card group relative bg-[#111827] h-48 rounded-3xl border border-white/5 overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] cursor-pointer">
                        
                        <!-- Imagen de Fondo -->
                        <div class="absolute inset-0 z-0">
                            <img :src="game.cover || '/images/default-game.png'" :alt="game.title" 
                                class="w-full h-full object-cover opacity-20 grayscale group-hover:grayscale-0 group-hover:opacity-40 transition-all duration-700 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/40 to-transparent"></div>
                        </div>

                        <!-- Contenido Centrado -->
                        <div class="relative z-20 h-full flex flex-col items-center justify-center p-6 text-center">
                            <h2 class="text-2xl font-black text-white font-orbitron tracking-wider mb-2 uppercase drop-shadow-lg group-hover:text-primary transition-colors">
                                {{ game.title }}
                            </h2>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em] bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                                    {{ getGuidesCount(game.id) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { useSearchStore } from '@/store/search';

const router = useRouter();
const searchStore = useSearchStore();

const games = ref([]);
const categories = ref([]);
const guides = ref([]);
const filteredGuides = ref([]);
const loading = ref(true);
const loadingGuides = ref(false);

const hasFilters = computed(() => {
    return searchStore.query || searchStore.gameId || searchStore.categoryId;
});

const loadInitialData = async () => {
    try {
        const [resGames, resCats, resGuides] = await Promise.all([
            axios.get('/api/games'),
            axios.get('/api/categories'),
            axios.get('/api/guides')
        ]);
        games.value = resGames.data.data || resGames.data;
        categories.value = resCats.data.data || resCats.data;
        guides.value = resGuides.data.data || resGuides.data;
    } catch (e) {
        console.error("Error cargando colecciones:", e);
    } finally {
        loading.value = false;
    }
};

const loadGuides = async () => {
    if (!hasFilters.value) return;
    
    loadingGuides.value = true;
    try {
        const params = {
            search: searchStore.query || undefined,
            game_id: searchStore.gameId || undefined,
            category_id: searchStore.categoryId || undefined
        };
        const res = await axios.get('/api/guides', { params });
        filteredGuides.value = res.data.data || res.data;
    } catch (e) {
        console.error("Error cargando guías filtradas:", e);
    } finally {
        loadingGuides.value = false;
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
    searchStore.setGame(gameId);
    // Ya no redirigimos a collections.show si queremos quedarnos aquí filtrando
    // router.push({ name: 'collections.show', params: { id: gameId } });
};

watch(() => [searchStore.query, searchStore.gameId, searchStore.categoryId], () => {
    if (hasFilters.value) {
        loadGuides();
    }
}, { immediate: true });

onMounted(loadInitialData);
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

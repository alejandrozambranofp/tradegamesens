<template>
    <div class="min-h-screen bg-[#0b0f19] text-white pt-20">
        <!-- Hero del Juego -->
        <div class="relative h-64 md:h-80 overflow-hidden border-b border-white/5">
            <img v-if="game?.cover" :src="game.cover" class="w-full h-full object-cover opacity-30 blur-sm" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#0b0f19] to-transparent"></div>
            
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                <div class="absolute top-6 left-6 md:top-10 md:left-10">
                    <Button label="Volver" icon="pi pi-chevron-left" @click="router.back()" 
                        class="!bg-white/5 hover:!bg-white/10 !border-white/10 !backdrop-blur-md !text-white !rounded-xl !px-4 !py-2" />
                </div>
                
                <h1 class="text-4xl md:text-6xl font-black font-orbitron tracking-tighter uppercase mb-4">
                    {{ game?.title || 'Cargando...' }}
                </h1>
                <div class="flex items-center gap-4 text-primary font-bold tracking-widest uppercase text-sm">
                    <span class="w-12 h-[1px] bg-primary"></span>
                    <span>{{ guides.length }} Guías Disponibles</span>
                    <span class="w-12 h-[1px] bg-primary"></span>
                </div>
            </div>
        </div>

        <!-- Lista de Guías -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="i in 6" :key="i" class="h-64 bg-white/5 rounded-3xl animate-pulse"></div>
            </div>

            <div v-else-if="guides.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="guide in guides" :key="guide.id" 
                    @click="viewGuide(guide)"
                    class="guide-card group bg-[#111827] rounded-3xl border border-white/5 overflow-hidden transition-all duration-500 hover:-translate-y-2 cursor-pointer shadow-xl">
                    
                    <div class="p-6">
                        <!-- Título y Valoración -->
                        <div class="flex justify-between items-start gap-4 mb-4">
                            <h3 class="text-lg font-bold text-white line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                                {{ guide.title }}
                            </h3>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded-lg border border-yellow-500/20">
                                    <i class="pi pi-star-fill text-yellow-500 text-xs"></i>
                                    <span class="text-xs font-bold text-yellow-500">{{ guide.rating || '0.0' }}</span>
                                </div>
                                <Button 
                                    :icon="guide.is_favorite ? 'pi pi-star-fill' : 'pi pi-star'" 
                                    :severity="guide.is_favorite ? 'warning' : 'secondary'"
                                    text rounded 
                                    @click.stop="toggleFavorite(guide)" 
                                    class="!w-8 !h-8"
                                />
                            </div>
                        </div>

                        <!-- Imagen Destacada (Tamaño Unificado) -->
                        <div class="relative h-48 overflow-hidden rounded-2xl bg-[#0b0f19] border border-white/5">
                            <img v-if="guide.image_url" :src="guide.image_url" :alt="guide.title" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                            <div v-else class="h-full flex flex-col items-center justify-center text-gray-700 gap-2 opacity-30">
                                <i class="pi pi-image text-3xl"></i>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Sin imagen</span>
                            </div>
                            
                            <!-- Overlay al hover -->
                            <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-white text-primary px-4 py-2 rounded-full font-bold text-sm shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                    Leer Guía
                                </span>
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div v-if="guide.categories?.length" class="mt-4 flex flex-wrap gap-2">
                            <span v-for="cat in guide.categories" :key="cat.id" 
                                class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-primary/10 text-primary border border-primary/20">
                                {{ cat.name }}
                            </span>
                        </div>

                        <!-- Autor e Info -->
                        <div class="mt-6 flex items-center justify-between border-t border-white/5 pt-4">
                            <div class="flex items-center gap-2">
                                <Avatar :image="guide.user?.avatar_url" shape="circle" size="small" class="border border-white/10" />
                                <span class="text-xs text-gray-500">Por {{ guide.user?.name }}</span>
                            </div>
                            <span class="text-[10px] text-gray-600 uppercase font-bold">{{ guide.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado Vacío -->
            <div v-else class="text-center py-20 bg-[#111827] rounded-[40px] border border-dashed border-white/10">
                <i class="pi pi-info-circle text-5xl text-gray-700 mb-4"></i>
                <h2 class="text-xl font-bold text-gray-500">No hay guías para este juego todavía.</h2>
                <Button label="Sé el primero en contribuir" icon="pi pi-plus" class="mt-6" @click="router.push({name: 'contribuir'})" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Button from 'primevue/button';
import Avatar from 'primevue/avatar';
import { authStore } from "@/store/auth";

const route = useRoute();
const router = useRouter();
const game = ref(null);
const guides = ref([]);
const loading = ref(true);

const loadData = async () => {
    const gameId = route.params.id;
    try {
        // Obtenemos info del juego y sus guías
        // Nota: Asumo que tienes un endpoint para filtrar guías por juego o que /api/guides devuelve el juego
        const [resGame, resGuides] = await Promise.all([
            axios.get(`/api/games/${gameId}`),
            axios.get(`/api/guides?game_id=${gameId}`) // Ajusta según tu API
        ]);
        
        game.value = resGame.data.data || resGame.data;
        const allGuides = resGuides.data.data || resGuides.data;
        
        // Filtramos en el cliente por si el endpoint no soporta el query param
        guides.value = allGuides.filter(g => g.game_id == gameId);
        
    } catch (e) {
        console.error("Error cargando detalle de colección:", e);
    } finally {
        loading.value = false;
    }
};

const viewGuide = (guide) => {
    router.push({ name: 'guides.show', params: { id: guide.slug || guide.id } });
};

const toggleFavorite = async (guide) => {
    if (!authStore().user?.name) {
        return router.push('/login');
    }
    try {
        await axios.post(`/api/guides/${guide.id}/favorite`);
        guide.is_favorite = !guide.is_favorite;
    } catch (e) {
        console.error(e);
    }
};

onMounted(loadData);
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}

.guide-card {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.guide-card:hover {
    box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5);
}
</style>

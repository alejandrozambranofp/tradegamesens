<template>
    <div class="flex flex-col gap-24 pb-20 overflow-x-hidden bg-[#0F172A] min-h-screen">
        
        <!-- Hero Section (Reverted to original design) -->
        <section class="relative min-h-[500px] flex flex-col items-center justify-center text-center px-6 overflow-hidden">
            <!-- Background with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="/images/home/home-hero.png" alt="Hero background" class="w-full h-full object-cover"/>
                <div class="absolute inset-0 bg-gradient-to-b from-black/75 via-black/30 to-[#0F172A] h-full"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 max-w-6xl mx-auto space-y-6">
                <!-- Title - Orbitron -->
                <h1 class="text-3xl md:text-5xl font-bold text-white tracking-wider font-orbitron">
                    Domina tus juegos favoritos
                </h1>
                
                <!-- Subtitle - Inter -->
                <p class="text-sm md:text-base text-white/90 max-w-4xl mx-auto font-inter font-semibold">
                    Únete a la comunidad definitiva de guías, trucos y estrategias validadas por jugadores.
                </p>

                <!-- Search Bar -->
                <div class="relative max-w-xl mx-auto w-full mt-8">
                    <form @submit.prevent="onSearch" class="flex items-center bg-white rounded-full p-1 shadow-2xl transition-all focus-within:ring-4 focus-within:ring-blue-500/20">
                        <InputText 
                            v-model="searchQuery" 
                            placeholder="¿De qué juego buscas ayuda?" 
                            class="flex-grow !border-none !bg-transparent !text-gray-800 !py-2 !pl-6 placeholder:!text-gray-400 !shadow-none focus:!ring-0 font-inter font-normal text-base"
                        />
                        <Button 
                            type="submit"
                            icon="pi pi-search" 
                            class="!rounded-full !w-10 !h-10 !bg-[#4F46E5] !border-none !flex-shrink-0 !shadow-lg hover:!scale-105 transition-transform"
                        />
                    </form>
                </div>
            </div>
        </section>

        <!-- Lo más votado esta semana -->
        <section class="max-w-7xl mx-auto px-6 w-full">
            <div class="flex items-end justify-between mb-10">
                <div class="space-y-1">
                    <h2 class="text-3xl font-bold text-white font-orbitron tracking-wide m-0">
                        Lo más votado esta semana
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="guide in topGuides" :key="guide.id" class="group bg-[#1E293B] rounded-2xl overflow-hidden border border-white/5 hover:border-blue-500/40 transition-all hover:-translate-y-2 duration-500 shadow-2xl flex flex-col h-full cursor-pointer" @click="onGuideClick(guide)">
                    <div class="relative h-52 overflow-hidden bg-[#0b0f19] flex items-center justify-center">
                        <img v-if="guide.image_url"
                            :src="guide.image_url" 
                            :alt="guide.title" 
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        />
                        <div v-else class="text-gray-600 flex flex-col items-center gap-2">
                            <i class="pi pi-image text-4xl opacity-20"></i>
                            <span class="text-xs uppercase tracking-widest font-bold opacity-40">Sin imagen destacada</span>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#1E293B] to-transparent"></div>
                        <div class="absolute top-3 right-3">
                            <Tag :value="guide.game?.title" severity="info" class="!bg-blue-600/80 !backdrop-blur-md !text-[10px] !uppercase !tracking-tighter" />
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow space-y-6">
                        <h3 class="font-bold text-lg text-white leading-tight line-clamp-2 min-h-[3rem] font-inter">
                            {{ guide.title }}
                        </h3>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/5">
                            <div class="flex items-center gap-2.5">
                                <Avatar :image="guide.user?.avatar_url" shape="circle" size="small" class="border border-white/10" />
                                <span class="text-xs text-gray-400 font-medium font-inter">{{ guide.user?.name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i v-for="i in 5" :key="i" class="pi pi-star-fill text-[10px] mr-0.5" :class="i <= guide.rating ? 'text-yellow-500' : 'text-gray-700'"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Empty State -->
            <div v-if="topGuides.length === 0" class="flex flex-col items-center justify-center p-20 bg-[#1E293B]/30 rounded-3xl border border-dashed border-white/10">
                <i class="pi pi-info-circle text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-500">Cargando las mejores guías...</p>
            </div>
        </section>

        <!-- Colecciones -->
        <section class="max-w-7xl mx-auto px-6 w-full">
            <div class="flex flex-col mb-10">
                <h2 class="text-3xl font-bold text-white font-orbitron tracking-wide">Colecciones</h2>
            </div>
            <div class="relative px-4">
                <Carousel 
                    :value="collections" 
                    :numVisible="5" 
                    :numScroll="1" 
                    :circular="true"
                    :showIndicators="false"
                    :autoplayInterval="4000"
                    :responsiveOptions="[
                        { breakpoint: '1280px', numVisible: 4, numScroll: 1 },
                        { breakpoint: '1024px', numVisible: 3, numScroll: 1 },
                        { breakpoint: '768px', numVisible: 2, numScroll: 1 },
                        { breakpoint: '560px', numVisible: 1, numScroll: 1 }
                    ]"
                    :pt="{
                        item: 'px-4',
                        previousButton: '!text-white/40 hover:!text-white !bg-[#1E293B] !border-none !rounded-full !w-12 !h-12 !shadow-xl absolute top-1/2 -left-6 -translate-y-1/2 z-10 hover:!bg-blue-600 transition-all',
                        nextButton: '!text-white/40 hover:!text-white !bg-[#1E293B] !border-none !rounded-full !w-12 !h-12 !shadow-xl absolute top-1/2 -right-6 -translate-y-1/2 z-10 hover:!bg-blue-600 transition-all',
                        indicatorList: 'hidden'
                    }"
                >
                    <template #item="slotProps">
                        <div class="relative w-full h-[380px] rounded-2xl overflow-hidden group cursor-pointer shadow-xl border border-white/5" @click="onGameClick(slotProps.data)">
                            <!-- Dynamic Overlay -->
                            <div class="absolute inset-0 z-10 bg-gradient-to-t from-[#0F172A] via-[#5369F2]/50 to-[#5369F2]/30 opacity-100"></div>
                            <div class="absolute inset-0 z-10 bg-[#5369F2]/20 group-hover:bg-[#5369F2]/10 transition-all duration-500"></div>
                            
                            <img :src="slotProps.data.cover || slotProps.data.image" :alt="slotProps.data.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"/>
                            
                            <div class="absolute bottom-8 left-0 right-0 px-6 z-20 transform group-hover:-translate-y-2 transition-transform duration-500">
                                <h4 class="text-base font-black text-white text-center tracking-wider uppercase font-orbitron leading-tight drop-shadow-2xl">
                                    {{ slotProps.data.title }}
                                </h4>
                                <div class="w-12 h-1 bg-blue-500 mx-auto mt-4 rounded-full scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                            </div>
                        </div>
                    </template>
                </Carousel>
            </div>
        </section>

        <!-- Estadísticas de TradeGameSense -->
        <section class="max-w-7xl mx-auto px-6 w-full mb-20">
            <div class="bg-[#1E293B] border border-white/5 rounded-[40px] p-12 md:p-20 relative shadow-2xl overflow-hidden">
                <!-- Background ambient lights -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-blue-600/10 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-400/5 rounded-full blur-[100px] pointer-events-none"></div>

                <h2 class="text-3xl md:text-4xl font-black text-center text-white mb-20 font-orbitron tracking-widest relative z-10">
                    Estadísticas de Trade<span class="text-[#5369F2]">Game</span>Sense
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 relative z-10">
                    <div v-for="stat in stats" :key="stat.label" class="flex flex-col items-center text-center group">
                        <div class="mb-4">
                            <span class="text-xs uppercase tracking-[0.3em] text-gray-500 font-black font-montserrat">{{ stat.label }}</span>
                        </div>
                        <div class="mb-8">
                            <span class="text-5xl font-black text-white font-orbitron tracking-tight tabular-nums">{{ stat.value }}</span>
                        </div>
                        <div class="w-20 h-20 rounded-[24px] bg-[#0F172A] border border-white/5 flex items-center justify-center text-blue-500 text-4xl group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110 group-hover:shadow-[0_0_30px_rgba(37,99,235,0.4)] transition-all duration-500">
                            <i :class="stat.icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

// PrimeVue
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import Carousel from 'primevue/carousel';

const router = useRouter();
const searchQuery = ref('');

// Dynamic Data
const topGuides = ref([]);
const collections = ref([]);

const fetchInitialData = async () => {
    try {
        const [resTop, resGames] = await Promise.all([
            axios.get('/api/guides/top-rated'),
            axios.get('/api/games')
        ]);
        topGuides.value = resTop.data.data || resTop.data;
        collections.value = resGames.data.data || resGames.data;
    } catch (e) {
        console.error("Error fetching home data", e);
    }
};

const onSearch = () => {
    if (!searchQuery.value.trim()) return;
    router.push({ name: 'guides.index', query: { search: searchQuery.value } });
};

const onGameClick = (game) => {
    router.push({ name: 'collections.show', params: { id: game.id } });
};

const onGuideClick = (guide) => {
    router.push({ name: 'guides.show', params: { id: guide.id } });
};

const stats = ref([
    { label: 'Total de visitas', value: '103K', icon: 'pi pi-eye' },
    { label: 'Colecciones', value: '9', icon: 'pi pi-bookmark' },
    { label: 'Usuarios', value: '765', icon: 'pi pi-user-plus' },
    { label: 'Total de guías', value: '202', icon: 'pi pi-file-edit' }
]);

onMounted(() => {
    fetchInitialData();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap');

.font-orbitron { font-family: 'Orbitron', sans-serif; }
.font-inter { font-family: 'Inter', sans-serif; }
.font-montserrat { font-family: 'Montserrat', sans-serif; }

:deep(.p-carousel-prev), :deep(.p-carousel-next) {
    transition: all 0.3s ease;
}

:deep(.p-inputtext) {
    background: transparent !important;
}

/* Custom shadow for the stats card to match prototype depth */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
</style>

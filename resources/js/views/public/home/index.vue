<template>
    <div class="flex flex-col gap-16 pb-20 overflow-x-hidden">
        <!-- Hero Section -->
        <section class="relative min-h-[500px] flex flex-col items-center justify-center text-center px-6 overflow-hidden">
            <!-- Background with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="/images/home/home-hero.png" alt="Hero background" class="w-full h-full object-cover"/>
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-[#0f172a]"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 max-w-4xl mx-auto space-y-6">
                <!-- Title - Orbitron Bold White -->
                <h1 class="text-4xl md:text-6xl font-bold text-white tracking-widest font-orbitron">
                    Domina tus juegos favoritos
                </h1>
                
                <!-- Subtitle - Inter Semi Bold -->
                <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto font-inter font-semibold">
                    Únete a la comunidad definitiva de guías, trucos y estrategias validadas por jugadores.
                </p>

                <!-- Search Bar - White background, pill shaped, icon right -->
                <div class="relative max-w-2xl mx-auto w-full mt-12">
                    <div class="flex items-center bg-white rounded-full p-1.5 shadow-2xl transition-all focus-within:ring-4 focus-within:ring-blue-500/20">
                        <InputText 
                            v-model="searchQuery" 
                            placeholder="¿De qué juego buscas ayuda?" 
                            class="flex-grow !border-none !bg-transparent !text-gray-800 !py-3 !pl-8 placeholder:!text-gray-400 !shadow-none focus:!ring-0 font-inter font-normal text-lg"
                        />
                        <Button 
                            icon="pi pi-search" 
                            class="!rounded-full !w-12 !h-12 !bg-[#4F46E5] !border-none !flex-shrink-0 !shadow-lg hover:!scale-105 transition-transform"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Most Voted Section -->
        <section class="max-w-7xl mx-auto px-6 w-full">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold dark:text-white">Lo más votado esta semana</h2>
                <Button label="Ver todo" text icon="pi pi-arrow-right" iconPos="right" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="guide in recommendedGuides" :key="guide.id" class="group bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-blue-500/50 transition-all hover:-translate-y-1 duration-300">
                    <div class="relative h-48 overflow-hidden">
                        <img :src="guide.image" :alt="guide.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                        <div class="absolute top-2 right-2">
                            <Tag :value="guide.platform" severity="secondary" class="!bg-black/60 !backdrop-blur-md" />
                        </div>
                    </div>
                    <div class="p-4 space-y-4">
                        <h3 class="font-bold text-lg text-white leading-snug line-clamp-2 min-h-[3.5rem]">
                            {{ guide.title }}
                        </h3>
                        <div class="flex items-center justify-between border-t border-gray-800 pt-4">
                            <div class="flex items-center gap-2">
                                <Avatar :image="guide.authorAvatar" shape="circle" size="small" />
                                <span class="text-sm text-gray-400">{{ guide.author }}</span>
                            </div>
                            <Rating v-model="guide.rating" readonly :cancel="false" class="scale-75 origin-right" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collections Section -->
        <section class="max-w-7xl mx-auto px-6 w-full">
            <h2 class="text-3xl font-bold dark:text-white mb-8">Colecciones</h2>
            <div class="relative">
                <div class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide snap-x">
                    <div v-for="collection in collections" :key="collection.id" class="relative min-w-[240px] h-[360px] rounded-2xl overflow-hidden snap-start group cursor-pointer shadow-xl">
                        <img :src="collection.image" :alt="collection.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h4 class="text-xl font-black text-white text-center tracking-wider uppercase">
                                {{ collection.name }}
                            </h4>
                        </div>
                    </div>
                </div>
                <!-- Navigation Arrows (Visual only as requested for "structure") -->
                <button class="absolute -left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center hover:bg-white/20 transition-all hidden md:flex">
                    <i class="pi pi-chevron-left"></i>
                </button>
                <button class="absolute -right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center hover:bg-white/20 transition-all hidden md:flex">
                    <i class="pi pi-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="max-w-7xl mx-auto px-6 w-full">
            <div class="bg-[#1e293b]/50 backdrop-blur-xl border border-white/5 rounded-3xl p-12 relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-purple-600/20 rounded-full blur-3xl"></div>

                <h2 class="text-3xl md:text-4xl font-bold text-center text-white mb-16">
                    Estadísticas de <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">TradeGameSense</span>
                </h2>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
                    <div v-for="stat in stats" :key="stat.label" class="flex flex-col items-center text-center space-y-4 group">
                        <span class="text-sm uppercase tracking-[0.2em] text-gray-400 font-semibold">{{ stat.label }}</span>
                        <span class="text-5xl font-black text-white group-hover:text-blue-400 transition-colors">{{ stat.value }}</span>
                        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 text-3xl group-hover:scale-110 transition-transform">
                            <i :class="stat.icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { authStore } from "@/store/auth";

const searchQuery = ref('');

const recommendedGuides = ref([
    {
        id: 1,
        title: 'Guía para conseguir chaqueta de David Martínez (PC)',
        platform: 'PC',
        image: '/images/home/game-cyb.png',
        author: 'David M.',
        authorAvatar: 'https://i.pravatar.cc/150?u=1',
        rating: 5
    },
    {
        id: 2,
        title: 'Cómo conseguir el traje de Link oscuro (N.Switch)',
        platform: 'Switch',
        image: '/images/home/game-zelda.png',
        author: 'ZeldaFan',
        authorAvatar: 'https://i.pravatar.cc/150?u=2',
        rating: 4
    },
    {
        id: 3,
        title: 'Como salvar a Hans Capon en el castillo de Trosky',
        platform: 'PC',
        image: '/images/home/game-medieval.png',
        author: 'Henry K.',
        authorAvatar: 'https://i.pravatar.cc/150?u=3',
        rating: 3
    },
    {
        id: 4,
        title: 'Guía para encontrar todos los coleccionables TLOU2 (PS5)',
        platform: 'PS5',
        image: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2070&auto=format&fit=crop',
        author: 'Ellie',
        authorAvatar: 'https://i.pravatar.cc/150?u=4',
        rating: 4
    }
]);

const collections = ref([
    { id: 1, name: 'The Legend of Zelda', image: '/images/home/coll-zelda.png' },
    { id: 2, name: 'Assassin\'s Creed', image: '/images/home/coll-ac.png' },
    { id: 3, name: 'Resident Evil', image: 'https://images.unsplash.com/photo-1589241062272-c0a000072dfa?q=80&w=2000&auto=format&fit=crop' },
    { id: 4, name: 'Soulsborne', image: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop' },
    { id: 5, name: 'Tomb Rider', image: 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?q=80&w=2000&auto=format&fit=crop' }
]);

const stats = ref([
    { label: 'Total de visitas', value: '103K', icon: 'pi pi-eye' },
    { label: 'Colecciones', value: '9', icon: 'pi pi-bookmark' },
    { label: 'Usuarios registrados', value: '765', icon: 'pi pi-user-plus' },
    { label: 'Todas las guías', value: '202', icon: 'pi pi-file-edit' }
]);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

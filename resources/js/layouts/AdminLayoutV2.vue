<template>
    <div class="min-h-screen bg-[#0b0f19] text-gray-200 font-inter">
        <!-- Barra de Navegación Superior -->
        <header class="sticky top-0 z-50 bg-[#111827]/80 backdrop-blur-xl border-b border-gray-800 shadow-2xl">
            <div class="max-w-[1600px] mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-8">
                    <router-link to="/" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
                            <i class="pi pi-shield text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-orbitron font-bold tracking-widest text-white hidden md:block">ADMIN <span class="text-primary">PANEL</span></span>
                    </router-link>

                    <nav class="hidden lg:flex items-center gap-2">
                        <router-link v-for="item in menuItems" :key="item.to" :to="item.to" 
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-2 hover:bg-gray-800"
                            :class="[route.path === item.to ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-400 hover:text-white']">
                            <i :class="item.icon"></i>
                            {{ item.label }}
                        </router-link>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-bold text-white">{{ auth.user?.name }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-primary font-bold">Administrator</span>
                    </div>
                    <Button icon="pi pi-sign-out" text rounded severity="danger" @click="handleLogout" v-tooltip.bottom="'Cerrar Sesión'" />
                    <Button icon="pi pi-home" rounded severity="secondary" @click="router.push('/')" v-tooltip.bottom="'Volver a la Web'" />
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="max-w-[1600px] mx-auto p-6 md:p-10">
            <Suspense>
                <template #default>
                    <router-view v-slot="{ Component }">
                        <transition name="page-fade" mode="out-in">
                            <component :is="Component" />
                        </transition>
                    </router-view>
                </template>
                <template #fallback>
                    <div class="flex items-center justify-center h-[60vh]">
                        <ProgressSpinner />
                    </div>
                </template>
            </Suspense>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { authStore } from '../store/auth';
import useAuth from '../composables/auth';
import Button from 'primevue/button';
import ProgressSpinner from 'primevue/progressspinner';

const route = useRoute();
const router = useRouter();
const auth = authStore();
const { logout } = useAuth();

const menuItems = [
    { label: 'Dashboard', icon: 'pi pi-th-large', to: '/admin' },
    { label: 'Guías', icon: 'pi pi-file-edit', to: '/admin/guides' },
    { label: 'Usuarios', icon: 'pi pi-users', to: '/admin/users' },
    { label: 'Categorías', icon: 'pi pi-tags', to: '/admin/categories' },
    { label: 'Juegos', icon: 'pi pi-desktop', to: '/admin/games' },
    { label: 'Roles', icon: 'pi pi-shield', to: '/admin/roles' },
];

const handleLogout = async () => {
    await logout();
    router.push('/login');
};
</script>

<style scoped>
.page-fade-enter-active, .page-fade-leave-active {
    transition: all 0.3s ease;
}
.page-fade-enter-from, .page-fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

:deep(.p-button.p-button-text:enabled:hover) {
    background: rgba(255, 255, 255, 0.05);
}
</style>

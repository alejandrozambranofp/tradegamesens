<template>
    <div class="fixed w-full z-50 bg-[#0F172A] border-b border-white/5 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Branding -->
            <router-link to="/" class="flex items-center gap-4 group">
                <img src="/images/logo.png" alt="TradeGameSense Logo" class="h-10 w-auto transition-transform group-hover:scale-110"/>
                <span class="text-2xl font-bold text-white font-orbitron tracking-tighter">
                    Trade<span class="text-[#5369F2]">Game</span>Sense
                </span>
            </router-link>

            <!-- Desktop Navigation -->
            <div v-if="isDesktop" class="flex items-center gap-10">
                <div class="flex items-center gap-8">
                    <button 
                        v-for="link in navLinks" 
                        :key="link.label" 
                        @click="handleLinkClick(link)"
                        class="text-sm font-bold text-white hover:text-[#5369F2] uppercase tracking-[0.2em] font-montserrat transition-all relative group"
                    >
                        {{ link.label }}
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#5369F2] transition-all group-hover:w-full"></span>
                    </button>
                </div>
                
                <!-- User Icon Menu -->
                <div class="flex items-center ml-4 relative">
                    <button 
                        type="button" 
                        @click="toggleUserMenu"
                        class="w-10 h-10 rounded-full bg-[#1E293B] flex items-center justify-center text-white hover:bg-[#5369F2] transition-all shadow-lg active:scale-95"
                    >
                        <i :class="authStore().user?.name ? 'pi pi-user' : 'pi pi-sign-in'" class="text-lg"></i>
                    </button>
                    <Menu ref="userMenu" :model="menuItems" popup />
                </div>
            </div>

            <!-- Mobile Toggle -->
            <button
                v-if="!isDesktop"
                @click="visibleMobileMenu = true"
                class="p-2 rounded-lg bg-[#1E293B] text-white hover:bg-[#5369F2] transition-colors"
            >
                <i class="pi pi-bars text-xl"></i>
            </button>
        </nav>

        <!-- Mobile Menu (Simplified) -->
        <Sidebar v-model:visible="visibleMobileMenu" position="right" class="!bg-[#0F172A] !border-l !border-white/5 !w-full sm:!w-[350px]">
            <template #header>
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" alt="logo" class="h-8"/>
                    <span class="font-orbitron font-bold text-white">MENU</span>
                </div>
            </template>
            <div class="flex flex-col gap-6 p-4 mt-8">
                <button 
                    v-for="link in navLinks" 
                    :key="link.label"
                    @click="handleLinkClick(link)"
                    class="flex items-center gap-4 text-lg font-bold text-white p-3 rounded-xl hover:bg-[#1E293B] transition-colors uppercase tracking-widest font-montserrat"
                >
                    {{ link.label }}
                </button>
                
                <div class="border-t border-white/5 my-4"></div>
                
                <template v-if="!authStore().user?.name">
                    <Button label="Iniciar Sesión" icon="pi pi-sign-in" class="!bg-[#5369F2] !border-none !rounded-xl" @click="router.push('/login')" />
                    <Button label="Registrarse" icon="pi pi-user-plus" outlined class="!text-white !border-white/10 !rounded-xl" @click="router.push('/register')" />
                </template>
                <template v-else>
                    <div class="p-4 rounded-xl bg-[#1E293B] flex items-center gap-3 mb-4">
                        <Avatar :image="authStore().user.avatar" :label="authStore().user.name[0]" shape="circle" class="!bg-[#5369F2]" />
                        <div class="flex flex-col">
                            <span class="text-white font-bold">{{ authStore().user.name }}</span>
                            <span class="text-gray-400 text-xs">{{ authStore().user.email }}</span>
                        </div>
                    </div>
                    <Button label="Mi Sitio" icon="pi pi-th-large" text class="!text-white !justify-start" @click="router.push('/app/my-guides')" />
                    <Button label="Cerrar Sesión" icon="pi pi-power-off" severity="danger" text class="!justify-start" @click="handleLogout" />
                </template>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { authStore } from "../store/auth";
import useAuth from "@/composables/auth";

// PrimeVue components normally imported in main.js but available here
import Menu from 'primevue/menu';
import Sidebar from 'primevue/sidebar';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';

const router = useRouter();
const userMenu = ref();
const visibleMobileMenu = ref(false);
const isDesktop = ref(window.innerWidth >= 992);

const { logout } = useAuth();

const navLinks = [
    { label: 'Juegos', route: { name: 'app.guides.community' }, protected: false },
    { label: 'Blog', route: { name: 'blog.index' }, protected: false },
    { label: 'Contribuir', route: { name: 'contribuir' }, protected: true },
    { label: 'Mi Sitio', route: { name: 'app.guides.my' }, protected: true }
];

const menuItems = computed(() => {
    if (authStore().user?.name) {
        return [
            {
                items: [
                    { 
                        label: 'Panel Admin', 
                        icon: 'pi pi-cog', 
                        command: () => router.push('/admin'),
                        visible: authStore().user?.roles?.some(r => r.name.toLowerCase().includes('admin')) || false
                    },
                    { label: 'Mi Sitio', icon: 'pi pi-th-large', command: () => router.push('/app') },
                    { separator: true },
                    {
                        label: 'CERRAR SESIÓN',
                        icon: 'pi pi-power-off',
                        class: 'text-red-500 font-bold',
                        command: () => handleLogout()
                    }
                ]
            }
        ];
    } else {
        return [
            {
                items: [
                    { label: 'Iniciar Sesión', icon: 'pi pi-sign-in', command: () => router.push('/login') },
                    { label: 'Registrarse', icon: 'pi pi-user-plus', command: () => router.push('/register') }
                ]
            }
        ];
    }
});

const handleLinkClick = (link) => {
    visibleMobileMenu.value = false;
    
    // Guest redirection logic
    if (link.protected && !authStore().user?.name) {
        router.push('/login');
        return;
    }

    if (link.route) {
        router.push(link.route);
    } else {
        // For Blog/Contribute placeholders
        console.log(`Navigating to ${link.label} (placeholder)`);
    }
};

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const handleLogout = () => {
    visibleMobileMenu.value = false;
    logout();
};

const handleResize = () => {
    isDesktop.value = window.innerWidth >= 992;
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Montserrat:wght@400;500;600;700;800;900&display=swap');

.font-orbitron { font-family: 'Orbitron', sans-serif; }
.font-montserrat { font-family: 'Montserrat', sans-serif; }

:deep(.p-menu) {
    background: #1E293B !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-radius: 12px !important;
    padding: 8px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
}

:deep(.p-menuitem-link) {
    color: white !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    padding: 10px 16px !important;
    border-radius: 8px !important;
}

:deep(.p-menuitem-link:hover) {
    background: rgba(83, 105, 242, 0.1) !important;
    color: #5369F2 !important;
}

:deep(.p-menuitem-icon) {
    color: inherit !important;
    margin-right: 12px !important;
}

:deep(.p-sidebar) {
    padding: 0 !important;
}
</style>
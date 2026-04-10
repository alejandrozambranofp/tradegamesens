<template>
    <header class="sticky top-0 z-999 flex w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="flex grow items-center justify-between px-4 py-3 md:px-6">
            <div class="flex items-center gap-2">
                <button @click="emit('toggleSidebar')" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i class="pi pi-bars"></i>
                </button>

                <button @click="emit('toggleCollapse')" class="hidden lg:flex p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i :class="props.isCollapsed ? 'pi pi-angle-right' : 'pi pi-angle-left'"></i>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <button @click="toggleDarkMode" class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <i :class="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'"></i>
                </button>

                <div class="relative">
                    <button @click="toggleDropdown" class="flex items-center gap-3 p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                        <div class="hidden text-right lg:block">
                            <span class="block text-sm font-semibold dark:text-white">{{ user?.name }}</span>
                            <span class="block text-xs text-gray-500 capitalize">{{ user?.roles?.[0]?.name }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                            {{ user?.name?.charAt(0).toUpperCase() }}
                        </div>
                    </button>

                    <transition name="dropdown-fade">
                        <div v-show="dropdownOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-xl z-50 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                                <p class="text-sm font-bold dark:text-white">{{ user?.email }}</p>
                            </div>
                            <div class="p-2">
                                <router-link to="/app/profile" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="pi pi-user"></i> Mi Perfil
                                </router-link>
                                <button @click="logout" class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-sm text-red-600">
                                    <i class="pi pi-sign-out"></i> Cerrar Sesión
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useLayout } from '../composables/layout';
import useAuth from '../composables/auth';
import { authStore } from '../store/auth';

const props = defineProps({
    sidebarOpen: Boolean,
    isCollapsed: Boolean
});

const emit = defineEmits(['toggleSidebar', 'toggleCollapse']);
const { toggleDarkMode, isDarkTheme } = useLayout();
const { logout: logoutAuth } = useAuth();
const auth = authStore();
const dropdownOpen = ref(false);

const user = computed(() => auth.user);
const toggleDropdown = () => { dropdownOpen.value = !dropdownOpen.value; };
const logout = () => { logoutAuth(); };

const closeDropdown = (e) => { if (!e.target.closest('.relative')) dropdownOpen.value = false; };
onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));
</script>

<style scoped>
.dropdown-fade-enter-active, .dropdown-fade-leave-active { transition: all 0.2s; }
.dropdown-fade-enter-from, .dropdown-fade-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
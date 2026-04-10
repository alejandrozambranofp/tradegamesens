<template>
    <aside 
        :class="[
            props.sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            props.isCollapsed ? 'w-[70px]' : 'w-64',
            'fixed left-0 top-0 z-50 flex h-screen flex-col overflow-hidden bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out lg:static lg:translate-x-0 shadow-lg lg:shadow-none sidebar-container group'
        ]"
    >
        <div class="flex items-center justify-center p-4 border-b border-gray-100 dark:border-gray-800 shrink-0 transition-all duration-300"
             :class="props.isCollapsed ? 'h-16' : 'h-24'">
            <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap transition-all duration-300 w-full justify-center">
                <img src="/images/logo.svg" alt="Logo" class="transition-all duration-300 object-contain" 
                     :class="props.isCollapsed ? 'h-8 w-8' : 'h-16 w-auto max-w-full'"/>
            </div>
        </div>

        <div class="flex flex-1 flex-col overflow-y-auto overflow-x-hidden p-3 gap-1 scrollbar-hide">
            <template v-for="(item, index) in menuModel" :key="index">
                <div v-if="item.label && item.items" class="px-3 mt-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap transition-opacity duration-200"
                     :class="props.isCollapsed ? 'hidden' : 'opacity-100'">
                    {{ item.label }}
                </div>

                <template v-if="item.items">
                     <template v-for="(subItem, subIndex) in item.items" :key="subItem.label">
                        <router-link :to="subItem.route" v-if="subItem.route" custom v-slot="{ href, navigate, isActive }">
                            <a :href="href" @click="navigate" 
                               v-tooltip.right="props.isCollapsed ? subItem.label : ''"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
                               :class="[
                                   isActive ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200'
                               ]"
                            >
                                <i class="text-lg shrink-0 transition-colors" :class="[subItem.icon, isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-500']"></i>
                                
                                <span class="whitespace-nowrap transition-all duration-300 origin-left"
                                      :class="[props.isCollapsed ? 'hidden' : 'w-auto opacity-100']">
                                    {{ subItem.label }}
                                </span>

                                <span v-if="isActive" class="absolute right-2 w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                            </a>
                        </router-link>
                     </template>
                </template>
            </template>
        </div>
    </aside>

    <div v-if="props.sidebarOpen" @click="emit('toggleSidebar')" class="lg:hidden fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
</template>

<script setup>
import { computed } from 'vue';
import { useAbility } from '@casl/vue';

const { can } = useAbility();

const props = defineProps({
    sidebarOpen: { type: Boolean, default: true },
    isCollapsed: { type: Boolean, default: false },
    menuItems: { type: Array, default: null }
});

const emit = defineEmits(['toggleSidebar']);

const menuModel = computed(() => {
    // Si se pasan items por props (como hace UserLayout), úsalos directamente
    if (props.menuItems && props.menuItems.length > 0) return props.menuItems;

    // Menú por defecto (Admin)
    const defaultItems = [
        {
            label: 'PRINCIPAL',
            items: [{ label: 'Dashboard', icon: 'pi pi-compass', route: '/admin', permission: 'all' }]
        },
        {
            label: 'Gestión Privada',
            items: [
                { label: 'Usuarios', icon: 'pi pi-users', route: '/admin/users', permission: 'user-list' },
                { label: 'Roles', icon: 'pi pi-shield', route: '/admin/roles', permission: 'role-list' },
                { label: 'Permisos', icon: 'pi pi-key', route: '/admin/permissions', permission: 'permission-list' }
            ]
        },
        {
            label: 'Tu Contenido',
            items: [
                { label: 'Categorías', icon: 'pi pi-tags', route: '/admin/categories', permission: 'category-list' },
                // Aquí usamos rutas completas (path) o nombres (name), es más seguro usar 'name' como acordamos en el router
                { label: 'Mis Guías', icon: 'pi pi-bookmark', route: { name: 'app.guides.my' }, permission: 'all' },
                { label: 'Guías de la Comunidad', icon: 'pi pi-globe', route: { name: 'app.guides.community' }, permission: 'all' }
            ]
        }
    ];

    return defaultItems.map(item => {
        const newItem = { ...item };
        if (newItem.items) {
            newItem.items = newItem.items.filter(child => {
                if (!child.permission || child.permission === 'all') return true;
                try { return can(child.permission); } catch (e) { return false; }
            });
        }
        return newItem;
    }).filter(item => item.items && item.items.length > 0);
});
</script>
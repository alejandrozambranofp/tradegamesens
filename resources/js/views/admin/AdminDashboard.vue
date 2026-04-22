<template>
    <div class="space-y-10">
        <!-- Bienvenida -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white m-0">Bienvenido de nuevo, <span class="text-primary">{{ auth.user?.name }}</span></h1>
                <p class="text-gray-400 mt-2 text-lg">Tienes el control total de la plataforma TradeGameSense.</p>
            </div>
            <div class="px-6 py-3 bg-[#111827] border border-gray-800 rounded-2xl flex items-center gap-3">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-300">Sistema Operativo</span>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="stat in stats" :key="stat.label" 
                class="p-8 rounded-3xl bg-[#111827] border border-gray-800 hover:border-primary/30 transition-all group relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i :class="stat.icon" class="text-9xl"></i>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div :class="stat.bg" class="p-3 rounded-xl">
                        <i :class="stat.icon" class="text-xl text-white"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Live Data</span>
                </div>
                <div class="text-4xl font-black text-white mb-1">{{ stat.value }}</div>
                <div class="text-gray-400 font-medium">{{ stat.label }}</div>
            </div>
        </div>

        <!-- Acciones Directas -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-6">
                <div class="p-8 rounded-3xl bg-[#111827] border border-gray-800 h-full">
                    <h2 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                        <i class="pi pi-bolt text-yellow-500"></i>
                        Accesos Directos
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <router-link v-for="action in quickActions" :key="action.title" :to="action.to"
                            class="p-6 rounded-2xl bg-[#0b0f19] border border-gray-800 hover:border-primary hover:bg-primary/5 transition-all flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gray-800 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <i :class="action.icon" class="text-xl group-hover:text-primary transition-colors"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-white">{{ action.title }}</div>
                                    <div class="text-xs text-gray-500">{{ action.desc }}</div>
                                </div>
                            </div>
                            <i class="pi pi-chevron-right text-gray-600 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Registro de Actividad o Info -->
            <div class="lg:col-span-4">
                <div class="p-8 rounded-3xl bg-gradient-to-br from-primary/20 to-purple-600/10 border border-primary/20 h-full flex flex-col justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-4">Estado del Servidor</h2>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Todos los servicios están funcionando correctamente. Las guías pendientes de revisión se han reducido un 15% esta semana.
                        </p>
                    </div>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-black/20 rounded-xl">
                            <span class="text-sm text-gray-300">Base de Datos</span>
                            <Tag value="Online" severity="success" rounded />
                        </div>
                        <div class="flex items-center justify-between p-3 bg-black/20 rounded-xl">
                            <span class="text-sm text-gray-300">API Gateway</span>
                            <Tag value="Online" severity="success" rounded />
                        </div>
                    </div>
                    <Button label="Ver Logs del Sistema" icon="pi pi-list" class="mt-8 w-full" outlined />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { authStore } from '../../store/auth';
import axios from 'axios';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

const auth = authStore();

const stats = ref([
    { label: 'Usuarios', value: '0', icon: 'pi pi-users', bg: 'bg-blue-600' },
    { label: 'Guías Totales', value: '0', icon: 'pi pi-file-edit', bg: 'bg-indigo-600' },
    { label: 'Categorías', value: '0', icon: 'pi pi-tags', bg: 'bg-emerald-600' },
    { label: 'Roles', value: '0', icon: 'pi pi-shield', bg: 'bg-amber-600' },
]);

const quickActions = [
    { title: 'Revisar Guías', desc: 'Aprobar o rechazar aportes', icon: 'pi pi-check-square', to: '/admin/guides' },
    { title: 'Usuarios', desc: 'Gestionar permisos y baneos', icon: 'pi pi-user-edit', to: '/admin/users' },
    { title: 'Categorías', desc: 'Añadir nuevos temas', icon: 'pi pi-tag', to: '/admin/categories' },
    { title: 'Configuración', desc: 'Ajustes globales del sitio', icon: 'pi pi-cog', to: '/admin/roles' },
];

const loadStats = async () => {
    try {
        const [resUsers, resGuides, resCats, resRoles] = await Promise.all([
            axios.get('/api/users'),
            axios.get('/api/admin/guides'),
            axios.get('/api/categories'),
            axios.get('/api/roles')
        ]);
        
        stats.value[0].value = resUsers.data.total || resUsers.data.data?.length || 0;
        stats.value[1].value = resGuides.data.total || resGuides.data.data?.length || 0;
        stats.value[2].value = resCats.data.total || resCats.data.data?.length || 0;
        stats.value[3].value = resRoles.data.total || resRoles.data.data?.length || 0;
    } catch (e) {
        console.error("Error al cargar estadísticas:", e);
    }
};

onMounted(loadStats);
</script>

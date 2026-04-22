<template>
    <div class="space-y-6">
        <div class="px-2">
            <h1 class="text-3xl font-bold text-white m-0">Roles y Permisos</h1>
            <p class="text-gray-500 mt-1">Configuración de niveles de acceso del sistema.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="role in roles" :key="role.id" 
                class="p-8 rounded-3xl bg-[#111827] border border-gray-800 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 text-6xl opacity-5 group-hover:text-primary transition-colors">
                    <i class="pi pi-shield"></i>
                </div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                        <i class="pi pi-shield text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white m-0">{{ role.name }}</h3>
                        <span class="text-[10px] uppercase tracking-[0.2em] text-gray-500">Guard: {{ role.guard_name }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Permisos Asignados</div>
                    <div class="flex flex-wrap gap-2">
                        <Tag v-for="perm in role.permissions" :key="perm.id" :value="perm.name" severity="secondary" rounded class="text-[10px]" />
                        <span v-if="!role.permissions?.length" class="text-gray-600 italic text-sm">Sin permisos específicos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Tag from 'primevue/tag';

const roles = ref([]);

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data.data || response.data;
    } catch (e) { console.error(e); }
};

onMounted(fetchRoles);
</script>

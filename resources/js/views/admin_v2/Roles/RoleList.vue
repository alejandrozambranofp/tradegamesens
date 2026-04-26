<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-3xl font-bold text-white m-0">Roles y Permisos</h1>
                <p class="text-gray-500 mt-1">Configuración de niveles de acceso del sistema.</p>
            </div>
            <Button label="Nuevo Rol" icon="pi pi-plus" class="shadow-lg shadow-primary/20" @click="openDialog" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="role in roles" :key="role.id" 
                class="p-8 rounded-3xl bg-[#111827] border border-gray-800 relative overflow-hidden group hover:border-primary/30 transition-all">
                <div class="absolute -right-4 -top-4 text-6xl opacity-5 group-hover:text-primary transition-colors">
                    <i class="pi pi-shield"></i>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                            <i class="pi pi-shield text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white m-0">{{ role.name }}</h3>
                            <span class="text-[10px] uppercase tracking-[0.2em] text-gray-500">Guard: {{ role.guard_name }}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Button icon="pi pi-pencil" text rounded severity="warning" @click="editRole(role)" />
                        <Button icon="pi pi-trash" text rounded severity="danger" @click="deleteRole(role)" />
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

        <Dialog v-model:visible="roleDialog" :header="editingRole ? 'Editar Rol' : 'Nuevo Rol'" modal class="admin-v2-dialog" :style="{ width: '400px' }">
            <div class="p-fluid pt-4">
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Nombre del Rol</label>
                    <InputText v-model="roleForm.name" class="bg-[#0b0f19] border-gray-800 text-white p-3 rounded-xl" autofocus />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="roleDialog = false" />
                <Button label="Guardar" icon="pi pi-check" @click="saveRole" :loading="saving" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';

const roles = ref([]);
const roleDialog = ref(false);
const editingRole = ref(null);
const saving = ref(false);
const roleForm = ref({ name: '' });

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data.data || response.data;
    } catch (e) { console.error(e); }
};

const openDialog = () => {
    editingRole.value = null;
    roleForm.value = { name: '' };
    roleDialog.value = true;
};

const editRole = (role) => {
    editingRole.value = role;
    roleForm.value = { name: role.name };
    roleDialog.value = true;
};

const saveRole = async () => {
    if (!roleForm.value.name) return;
    saving.value = true;
    try {
        if (editingRole.value) {
            await axios.put(`/api/roles/${editingRole.value.id}`, roleForm.value);
        } else {
            await axios.post('/api/roles', roleForm.value);
        }
        roleDialog.value = false;
        fetchRoles();
    } catch (e) { 
        console.error(e);
        alert("Error al guardar rol. Asegúrate de tener permisos."); 
    }
    finally { saving.value = false; }
};

const deleteRole = async (role) => {
    if (confirm(`¿Borrar el rol "${role.name}"?`)) {
        try {
            await axios.delete(`/api/roles/${role.id}`);
            fetchRoles();
        } catch (e) { 
            console.error(e);
            alert("Error al eliminar rol."); 
        }
    }
};

onMounted(fetchRoles);
</script>

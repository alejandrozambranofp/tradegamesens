<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-3xl font-bold text-white m-0">Gestión de Usuarios</h1>
                <p class="text-gray-500 mt-1">Administra los accesos y roles de la plataforma.</p>
            </div>
            <div class="flex gap-3">
                <Button label="Refrescar" icon="pi pi-refresh" text severity="secondary" @click="fetchUsers" />
                <Button label="Nuevo Usuario" icon="pi pi-plus" class="shadow-lg shadow-primary/20" @click="openCreateDialog" />
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="p-6 bg-[#111827] border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
            <DataTable :value="users" :loading="loading" paginator :rows="10" 
                       class="admin-v2-table" responsiveLayout="stack">
                
                <template #header>
                    <div class="flex justify-start">
                        <span class="p-input-icon-left w-full md:w-80">
                            <i class="pi pi-search text-gray-600" />
                            <InputText v-model="searchQuery" placeholder="Buscar por nombre o email..." class="w-full bg-[#0b0f19] border-gray-800" @input="handleSearch" />
                        </span>
                    </div>
                </template>

                <Column field="name" header="USUARIO" sortable>
                    <template #body="{ data }">
                        <div class="flex items-center gap-3">
                            <Avatar :image="data.avatar_url" :label="!data.avatar_url ? data.name.charAt(0) : ''" shape="circle" class="border border-gray-700 bg-gray-800" />
                            <div class="flex flex-col">
                                <span class="text-white font-bold">{{ data.name }}</span>
                                <span class="text-xs text-gray-500">{{ data.email }}</span>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column header="ROLES">
                    <template #body="{ data }">
                        <div class="flex flex-wrap gap-1">
                            <Tag v-for="role in data.roles" :key="role.id" :value="role.name" severity="info" rounded class="text-[9px] px-2 font-bold" />
                        </div>
                    </template>
                </Column>

                <Column field="created_at" header="REGISTRO" sortable></Column>

                <Column header="ACCIONES" class="text-right">
                    <template #body="{ data }">
                        <div class="flex justify-end gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="warning" @click="editUser(data)" />
                            <Button v-if="data.id !== 1" icon="pi pi-trash" text rounded severity="danger" @click="confirmDelete(data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Modal de Edición/Creación -->
        <Dialog v-model:visible="userDialog" :header="editingUser ? 'Editar Usuario' : 'Nuevo Usuario'" modal class="admin-v2-dialog" :style="{ width: '450px' }">
            <div class="p-fluid space-y-6 pt-4">
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Nombre Completo</label>
                    <InputText v-model="userForm.name" class="bg-[#0b0f19] border-gray-800 text-white" />
                </div>
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Email</label>
                    <InputText v-model="userForm.email" class="bg-[#0b0f19] border-gray-800 text-white" />
                </div>
                <div v-if="!editingUser" class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Contraseña</label>
                    <InputText type="password" v-model="userForm.password" class="bg-[#0b0f19] border-gray-800 text-white" />
                </div>
                <div class="field">
                    <label class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 block">Roles</label>
                    <MultiSelect v-model="userForm.roles" :options="allRoles" optionLabel="name" optionValue="id" 
                        placeholder="Asignar roles" class="bg-[#0b0f19] border-gray-800" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="userDialog = false" />
                <Button label="Guardar Usuario" icon="pi pi-check" @click="saveUser" :loading="saving" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Avatar from 'primevue/avatar';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import MultiSelect from 'primevue/multiselect';

const users = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const userDialog = ref(false);
const editingUser = ref(null);
const saving = ref(false);
const allRoles = ref([]);

const userForm = ref({
    name: '',
    email: '',
    password: '',
    roles: []
});

const fetchUsers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/users'); // Usamos el endpoint que devuelve usuarios
        users.value = response.data.data || response.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/roles');
        allRoles.value = response.data.data || response.data;
    } catch (e) { console.error(e); }
};

const openCreateDialog = () => {
    editingUser.value = null;
    userForm.value = { name: '', email: '', password: '', roles: [] };
    userDialog.value = true;
};

const editUser = (user) => {
    editingUser.value = user;
    userForm.value = { 
        name: user.name, 
        email: user.email, 
        roles: user.roles?.map(r => r.id) || [] 
    };
    userDialog.value = true;
};

const saveUser = async () => {
    saving.value = true;
    try {
        if (editingUser.value) {
            await axios.put(`/api/users/${editingUser.value.id}`, userForm.value);
        } else {
            await axios.post('/api/users', userForm.value);
        }
        userDialog.value = false;
        fetchUsers();
    } catch (e) {
        alert("Error al guardar usuario");
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async (user) => {
    if (confirm(`¿Eliminar al usuario ${user.name}?`)) {
        try {
            await axios.delete(`/api/users/${user.id}`);
            fetchUsers();
        } catch (e) { alert("Error al eliminar"); }
    }
};

onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script>

<style scoped>
:deep(.admin-v2-table) {
    background: transparent;
}
:deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 0 0 1.5rem 0;
}
:deep(.p-datatable-thead > tr > th) {
    background: #1a2233;
    color: #4b5563;
    font-size: 0.7rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 1rem 1.5rem;
    border: none;
}
:deep(.p-datatable-tbody > tr) {
    background: transparent;
    color: #d1d5db;
    border-bottom: 1px solid #1f2937;
}
:deep(.p-datatable-tbody > tr:hover) {
    background: #0b0f19;
}
:deep(.p-datatable-tbody > tr > td) {
    padding: 1.25rem 1.5rem;
    border: none;
}
</style>

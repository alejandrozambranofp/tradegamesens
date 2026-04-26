<template>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Avatar Section -->
        <div class="col-span-1 md:col-span-4 lg:col-span-3">
            <Card class="h-full border-gray-800 bg-[#111827]">
                <template #title>
                    <div class="text-white text-lg font-bold">Tu Perfil</div>
                </template>
                <template #content>
                    <div class="flex flex-col items-center gap-6">
                        <!-- Current Avatar -->
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-primary/20 shadow-2xl shadow-primary/10">
                                <img :src="user.avatar_url || '/images/placeholder-avatar.jpg'" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <div class="w-full">
                            <Button label="Cambiar Imagen de Perfil" icon="pi pi-user-edit" class="w-full p-3 font-bold shadow-lg shadow-primary/20" @click="showAvatarDialog = true" />
                            <p class="text-[10px] text-gray-500 text-center mt-3 uppercase tracking-widest font-bold">Selecciona uno de los avatares oficiales</p>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Dialog para elegir avatar -->
        <Dialog v-model:visible="showAvatarDialog" header="Selecciona un Avatar" modal class="admin-v2-dialog" :style="{ width: '50vw' }">
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                <div v-for="avatar in predefinedAvatars" :key="avatar.name" 
                    class="cursor-pointer group relative aspect-square rounded-xl overflow-hidden border-2 transition-all"
                    :class="selectedAvatarName === avatar.name ? 'border-primary' : 'border-gray-800 hover:border-gray-600'"
                    @click="selectPredefinedAvatar(avatar)">
                    <img :src="avatar.url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div v-if="selectedAvatarName === avatar.name" class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                        <i class="pi pi-check text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Cerrar" icon="pi pi-times" text severity="secondary" @click="showAvatarDialog = false" />
                <Button label="Confirmar" icon="pi pi-check" @click="confirmAvatar" :loading="savingAvatar" :disabled="!selectedAvatarName" />
            </template>
        </Dialog>

        <!-- Personal Data Section -->
        <div class="col-span-1 md:col-span-8 lg:col-span-9">
            <Card>
                <template #title>Datos Personales</template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="field">
                            <label class="font-bold block mb-2">Nombre</label>
                            <div class="p-3 bg-surface-50 dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700">
                                {{ user.name }}
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="font-bold block mb-2">Email</label>
                            <div class="p-3 bg-surface-50 dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="field">
                            <label class="font-bold block mb-2">Primer Apellido</label>
                            <div class="p-3 bg-surface-50 dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700">
                                {{ user.surname1 || '-' }}
                            </div>
                        </div>

                        <div class="field">
                            <label class="font-bold block mb-2">Segundo Apellido</label>
                            <div class="p-3 bg-surface-50 dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700">
                                {{ user.surname2 || '-' }}
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { usePrimeVue } from 'primevue/config';
import useUsers from "@/composables/users";
import { authStore } from "@/store/auth";
import axios from 'axios';
import Button from 'primevue/button';
import Card from 'primevue/card';
import FileUpload from 'primevue/fileupload';
import Dialog from 'primevue/dialog';

const auth = authStore();
const $primevue = usePrimeVue();
const { getUser, user } = useUsers();

const showAvatarDialog = ref(false);
const predefinedAvatars = ref([]);
const selectedAvatarName = ref(null);
const savingAvatar = ref(false);

const fetchPredefinedAvatars = async () => {
    try {
        const response = await axios.get('/api/avatars/predefined');
        predefinedAvatars.value = response.data;
    } catch (e) {
        console.error("Error cargando avatares", e);
    }
};

onMounted(async () => {
    await getUser(auth.user.id);
    fetchPredefinedAvatars();
});

const selectPredefinedAvatar = (avatar) => {
    selectedAvatarName.value = avatar.name;
};

const confirmAvatar = async () => {
    if (!selectedAvatarName.value) return;
    savingAvatar.value = true;
    try {
        await axios.post('/api/user/select-avatar', { filename: selectedAvatarName.value });
        await getUser(auth.user.id);
        showAvatarDialog.value = false;
    } catch (e) {
        console.error("Error al guardar avatar", e);
    } finally {
        savingAvatar.value = false;
    }
};
</script>

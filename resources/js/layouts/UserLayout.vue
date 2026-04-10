<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const items = ref([
    {
        label: 'Contenido',
        items: [
            {
                label: 'Posts',
                icon: 'pi pi-th-large',
                route: '/app/posts'
            },
            { 
                label: 'Mis Guías', 
                icon: 'pi pi-bookmark', 
                route: '/app/my-guides' 
            },
            { 
                label: 'Guías de la Comunidad', 
                icon: 'pi pi-globe', 
                route: '/app/guides' 
            }
        ]
    },
    {
        label: 'Cuenta',
        items: [
            {
                label: 'Perfil',
                icon: 'pi pi-user',
                route: '/app/profile'
            }
        ]
    }
]);
</script>
<template>
    <div class="flex h-screen overflow-hidden">
        <MainSidebar 
            :sidebarOpen="sidebarOpen" 
            :isCollapsed="isCollapsed"
            :menuItems="props.menuItems"
            @toggleSidebar="toggleSidebar"
        />

        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <MainHeader 
                :sidebarOpen="sidebarOpen" 
                :isCollapsed="isCollapsed"
                @toggleSidebar="toggleSidebar"
                @toggleCollapse="toggleCollapse"
            />
        
            <main>
                <div class="p-4 md:p-6 2xl:p-10">
                    <Suspense>
                        <router-view />
                    </Suspense>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useLayout } from '../composables/layout';
import MainSidebar from './MainSidebar.vue';
import MainHeader from './MainHeader.vue';

const props = defineProps({
    menuItems: { type: Array, default: null }
});

const { setDefaultMode, isDarkTheme } = useLayout();
const sidebarOpen = ref(true);
const isCollapsed = ref(false);

onMounted(() => {
    setDefaultMode();
});

watch(isDarkTheme, (val) => {
    const html = document.documentElement;
    if (val) {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
}, { immediate: true });

const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value; };
const toggleCollapse = () => { isCollapsed.value = !isCollapsed.value; };
</script>
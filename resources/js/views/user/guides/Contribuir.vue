<template>
    <div class="p-4 min-h-screen" style="background-color: #0b0f19;">
        <div class="flex items-center justify-between mb-8 px-2">
            <div class="flex items-center gap-4">
                <Button icon="pi pi-arrow-left" text rounded severity="secondary" @click="router.back()" class="text-gray-400 hover:bg-gray-800" />
                <h1 class="text-3xl font-bold text-white m-0 tracking-tight">Publicar Nueva Guía</h1>
            </div>
            <div class="hidden md:flex items-center gap-2 text-gray-500">
                <i class="pi pi-info-circle"></i>
                <span>Tu guía será visible para toda la comunidad</span>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-8">
                <div class="p-8 rounded-2xl border border-gray-800 shadow-xl" style="background-color: #111827;">
                    <div class="p-fluid flex flex-col gap-6">
                        
                        <div class="field">
                            <label for="title" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Título de la Guía</label>
                            <InputText id="title" v-model="guide.title" 
                                placeholder="Escribe el título de tu guía..." 
                                class="bg-[#0b0f19] border-gray-700 text-white p-3 rounded-xl focus:border-primary" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="field">
                                <label for="game" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Juego Relacionado</label>
                                <Dropdown id="game" v-model="guide.game_id" :options="games" optionLabel="title" optionValue="id" 
                                    placeholder="Selecciona un juego" :filter="true"
                                    class="bg-[#0b0f19] border-gray-700 text-white rounded-xl" />
                            </div>

                            <div class="field">
                                <label for="cats" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Categorías</label>
                                <MultiSelect id="cats" v-model="selectedCategories" :options="categories" optionLabel="name" optionValue="id" 
                                    placeholder="Elige temas" display="chip"
                                    class="bg-[#0b0f19] border-gray-700 text-white rounded-xl" />
                            </div>
                        </div>

                        <div class="field">
                            <label class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Contenido de la Guía</label>
                            <div class="editor-dark-wrapper border border-gray-700 rounded-xl overflow-hidden">
                                <Editor v-model="guide.content" editorStyle="height: 450px" placeholder="Escribe aquí tu nueva guia..." />
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4 mt-4 pt-6 border-t border-gray-800">
                            <Button label="Publicar Guía Ahora" icon="pi pi-send" @click="saveGuide" :loading="isSaving" 
                                class="flex-1 p-3 text-lg font-bold shadow-lg shadow-primary/20" />
                            <Button label="Guardar como Borrador" icon="pi pi-save" severity="secondary" outlined 
                                class="border-gray-700 text-gray-400 hover:bg-gray-800" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                <div class="p-6 rounded-2xl border border-gray-800" style="background-color: #111827;">
                    <h3 class="text-white font-bold m-0 mb-4 flex items-center gap-2">
                        <i class="pi pi-lightbulb text-yellow-500"></i>
                        Consejos de Redacción
                    </h3>
                    <ul class="p-0 m-0 list-none flex flex-col gap-4">
                        <li class="flex gap-3">
                            <i class="pi pi-check-circle text-primary mt-1"></i>
                            <span class="text-gray-400 text-sm">Usa <strong>negritas</strong> para resaltar puntos clave del combate o rutas.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="pi pi-check-circle text-primary mt-1"></i>
                            <span class="text-gray-400 text-sm">Añade listas paso a paso para que sea fácil de leer en móviles.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="pi pi-check-circle text-primary mt-1"></i>
                            <span class="text-gray-400 text-sm">Si es una guía de trofeos, indica el nivel de dificultad al inicio.</span>
                        </li>
                    </ul>
                </div>

                <div class="p-6 rounded-2xl border border-dashed border-gray-700 opacity-60">
                    <h4 class="text-gray-400 m-0 mb-2">Vista Previa</h4>
                    <p class="text-gray-600 text-xs">Próximamente podrás ver cómo queda tu guía antes de publicarla.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router'; // Importamos useRoute para leer el ID
import axios from 'axios';
import { authStore } from "@/store/auth";

// Componentes de PrimeVue
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';

const router = useRouter();
const route = useRoute(); // Acceso a los parámetros de la URL
const auth = authStore();

const isSaving = ref(false);
const games = ref([]);
const categories = ref([]);
const selectedCategories = ref([]);

// Objeto de la guía inicializado vacío
const guide = ref({
    title: '',
    content: '',
    game_id: null,
    user_id: auth.user?.id
});

const loadInitialData = async () => {
    try {
        const [resGames, resCats] = await Promise.all([
            axios.get('/api/games'),
            axios.get('/api/categories')
        ]);
        games.value = resGames.data.data || resGames.data;
        categories.value = resCats.data.data || resCats.data;
    } catch (e) {
        console.error("Error cargando juegos/categorías:", e);
    }
};

const loadGuideToEdit = async (id) => {
    try {
        // Usamos el endpoint de show o edit de tu API
        const res = await axios.get(`/api/guides/${id}`);
        const data = res.data.data || res.data;

        // Rellenamos el ref 'guide' con los datos que vienen de la base de datos
        guide.value = {
            id: data.id,
            title: data.title,
            content: data.content,
            game_id: data.game_id,
            user_id: data.user_id
        };

        // Si la guía tiene categorías, extraemos solo los IDs para el MultiSelect
        if (data.categories) {
            selectedCategories.value = data.categories.map(c => c.id);
        }
    } catch (e) {
        console.error("Error al cargar la guía para editar:", e);
        alert("No se pudo cargar la información de la guía.");
    }
};

const saveGuide = async () => {
    // 1. Validación previa para evitar peticiones mal formadas
    if (!guide.value.title || !guide.value.game_id || !guide.value.content) {
        alert("Por favor, completa todos los campos obligatorios.");
        return;
    }

    isSaving.value = true;
    
    try {
        // 2. Construir el payload exacto que espera tu API
        const payload = {
            title: guide.value.title,
            content: guide.value.content,
            game_id: guide.value.game_id,
            categories: selectedCategories.value,
            user_id: auth.user?.id // El uso de '?' evita el error de "reading id"
        };
        
        // 3. Ejecutar la petición según si es edición o creación (SOLO UNA VEZ)
        const res = guide.value.id 
            ? await axios.put(`/api/guides/${guide.value.id}`, payload)
            : await axios.post('/api/guides', payload);

        // Obtenemos la guía desde la respuesta del servidor
        const nuevaGuia = res.data.data || res.data;

        // Redirigimos directamente a la vista de detalle (el "ojo azul")
        // Usamos el slug si tu API lo genera, o el ID como fallback
        router.push({ 
            name: 'guides.show', 
            params: { id: nuevaGuia.slug || nuevaGuia.id } 
        });

    } catch (e) {
        // 5. SOLO si la petición falla realmente (error 400, 500, etc.)
        console.error("Error real del servidor:", e.response?.data || e);
        
        // Evita mostrar la alerta si el error es de una extensión o algo externo
        if (e.response) {
            alert("Error del servidor: " + (e.response.data.message || "No se pudo guardar"));
        }
    } finally {
        isSaving.value = false;
    }
};

onMounted(async () => {
    await loadInitialData(); // Primero cargamos los selectores (juegos y categorías)
    
    // Si existe un ID en la ruta, cargamos los datos de esa guía
    if (route.params.id) {
        await loadGuideToEdit(route.params.id);
    }
});
</script>

<style>
/* Ajustes para el Editor en modo oscuro */
.editor-dark-wrapper .p-editor-toolbar {
    background-color: #1a2332 !important;
    border-color: #374151 !important;
}
.editor-dark-wrapper .p-editor-toolbar button {
    color: #9ca3af !important;
}
.editor-dark-wrapper .p-editor-content {
    background-color: #0b0f19 !important;
    border-color: #374151 !important;
    color: white !important;
}
.editor-dark-wrapper .ql-editor.ql-blank::before {
    color: #4b5563 !important;
}

/* Estilo para los chips de MultiSelect */
.p-multiselect-token {
    background-color: var(--primary-color) !important;
    color: white !important;
}
</style>
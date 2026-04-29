<template>
    <div class="p-4 min-h-screen" style="background-color: #0b0f19;">
        <div class="mb-12 px-2">
            <div class="flex flex-col">
                <h1 class="text-4xl font-bold text-white font-orbitron tracking-tighter uppercase">
                    Publicar <span class="text-[#5369F2]">Guía</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Contribuye con tu guia en la comunidad de tradegamesense!</p>
            </div>
        </div>

        <!-- Sección de Consejos (Horizontal en la parte superior) -->
        <div class="mb-8 p-6 rounded-2xl border border-gray-800" style="background-color: #111827;">
            <div class="flex flex-col md:flex-row gap-6 md:gap-12 items-center justify-center">
                <div class="flex items-center gap-3">
                    <i class="pi pi-check-circle text-[#5369F2] text-xl"></i>
                    <span class="text-gray-300 text-sm leading-relaxed">Usa <strong>negritas</strong> para resaltar puntos clave del combate o rutas.</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="pi pi-check-circle text-[#5369F2] text-xl"></i>
                    <span class="text-gray-300 text-sm leading-relaxed">Añade listas paso a paso para que sea fácil de leer en móviles.</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="pi pi-check-circle text-[#5369F2] text-xl"></i>
                    <span class="text-gray-300 text-sm leading-relaxed">Verifica la ortografía antes de publicar tu guía.</span>
                </div>
            </div>
        </div>

        <!-- Formulario Principal (Ancho Completo) -->
        <div class="p-8 rounded-2xl border border-gray-800 shadow-xl mb-12" style="background-color: #111827;">
            <div class="p-fluid flex flex-col gap-6">
                
                <!-- Imagen de Portada -->
                <div class="field">
                    <label class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Imagen de Portada (Obligatoria)</label>
                    <div class="flex flex-col items-center gap-4 p-6 border-2 border-dashed border-gray-700 rounded-2xl bg-[#0b0f19] hover:border-primary/50 transition-colors cursor-pointer"
                        @click="coverInput.click()">
                        <input type="file" ref="coverInput" class="hidden" accept="image/*" @change="onImageSelect" />
                        
                        <div v-if="!coverPreview && !guide.image" class="text-center p-8">
                            <i class="pi pi-cloud-upload text-5xl text-[#5369F2] mb-4"></i>
                            <p class="text-gray-300 font-bold m-0 text-lg">Haz clic para subir la imagen de portada</p>
                            <p class="text-gray-500 text-sm mt-2">Formatos recomendados: JPG, PNG, WEBP</p>
                        </div>
                        
                        <img v-else :src="coverPreview || guide.image" class="w-full max-h-[400px] object-cover rounded-xl shadow-lg" />
                        
                        <Button v-if="coverPreview || guide.image" label="Cambiar Imagen" icon="pi pi-refresh" severity="secondary" size="small" text />
                    </div>
                </div>
                
                <div class="field">
                    <label for="title" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Título de la Guía</label>
                    <InputText id="title" v-model="guide.title" 
                        placeholder="Escribe el título de tu guía..." 
                        class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="field">
                        <label for="game" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Juego Relacionado</label>
                        <Dropdown id="game" v-model="guide.game_id" :options="games" optionLabel="title" optionValue="id" 
                            placeholder="Selecciona un juego" :filter="true" appendTo="self"
                            class="!bg-[#0b0f19] !border-white/10 !text-white !rounded-xl" />
                    </div>

                    <div class="field">
                        <label for="cats" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Categorías</label>
                        <MultiSelect id="cats" v-model="selectedCategories" :options="categories" optionLabel="name" optionValue="id" 
                            placeholder="Elige temas" display="chip" appendTo="self"
                            class="!bg-[#0b0f19] !border-white/10 !text-white !rounded-xl w-full" />
                    </div>

                    <div class="field">
                        <label for="difficulty" class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Dificultad (Obligatoria)</label>
                        <Dropdown id="difficulty" v-model="guide.difficulty" :options="['D', 'C', 'B', 'A', 'S']" 
                            placeholder="Selecciona un nivel" appendTo="self"
                            class="!bg-[#0b0f19] !border-white/10 !text-white !rounded-xl w-full" />
                    </div>
                </div>

                <div class="field">
                    <label class="text-gray-400 font-medium mb-2 block uppercase text-xs tracking-wider">Contenido de la Guía</label>
                    <div class="editor-dark-wrapper border border-gray-700 rounded-xl overflow-hidden">
                        <Editor v-model="guide.content" editorStyle="height: 500px" @load="onEditorLoad" placeholder="Escribe aquí tu nueva guia..." />
                    </div>
                </div>

                <div class="flex justify-center mt-4 pt-6 border-t border-gray-800">
                    <Button label="Publicar Guía Ahora" icon="pi pi-send" @click="saveGuide" :loading="isSaving" 
                        class="!bg-[#5369F2] !border-none !rounded-xl !px-12 !py-4 !text-lg !font-bold shadow-lg shadow-[#5369F2]/30 transition-all" />
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
import Swal from 'sweetalert2';

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
    user_id: auth.user?.id,
    image: null, // Para guardar la URL de la imagen si ya existe
    difficulty: null
});

const coverInput = ref(null);
const coverFile = ref(null);
const coverPreview = ref(null);

const onImageSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        coverFile.value = file;
        coverPreview.value = URL.createObjectURL(file);
    }
};

const onEditorLoad = ({ instance }) => {
    // Evitar que se añadan múltiples listeners si el editor se recarga
    if (instance.root._hasPasteListener) return;
    instance.root._hasPasteListener = true;

    // Anular el pegado automático de base64 de Quill para evitar duplicados
    instance.clipboard.addMatcher('IMG', (node, delta) => {
        if (node.src && node.src.startsWith('data:')) {
            delta.ops = []; // Vaciar el delta para que no inserte nada
        }
        return delta;
    });

    instance.root.addEventListener('paste', async (e) => {
        const clipboardData = e.clipboardData || window.clipboardData;
        if (!clipboardData) return;

        const items = clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                if (!file) continue;

                // Evitar que Quill inserte la imagen en base64 y detener otros eventos
                e.preventDefault();
                e.stopPropagation();

                const fd = new FormData();
                fd.append('image', file);

                try {
                    const res = await axios.post('/api/images/upload', fd, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    
                    const url = res.data.url;
                    
                    // Insertar la URL en el editor
                    const range = instance.getSelection(true);
                    instance.insertEmbed(range.index, 'image', url);
                    instance.setSelection(range.index + 1);
                } catch (err) {
                    console.error("Error al subir imagen pegada:", err);
                    Swal.fire({
                        title: 'Ups...',
                        text: 'La imagen es demasiado grande. El límite es de 10MB.',
                        icon: 'error',
                        background: '#111827',
                        color: '#fff',
                        confirmButtonColor: '#3b82f6'
                    });
                }

                // Salir del bucle para no subir/insertar la imagen 2 veces si hay varios tipos en el portapapeles
                break;
            }
        }
    });
};

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
            user_id: data.user_id,
            image: data.image_url, // Usamos image_url que es lo que devuelve el Resource
            difficulty: data.difficulty
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
    // 1. Validación previa
    if (!guide.value.title || !guide.value.game_id || !guide.value.content || !guide.value.difficulty) {
        alert("Por favor, completa todos los campos obligatorios (incluyendo la Dificultad).");
        return;
    }

    // Validación de imagen (solo obligatoria si es creación nueva)
    if (!guide.value.id && !coverFile.value) {
        alert("Debes subir una imagen de portada para publicar la guía.");
        return;
    }

    isSaving.value = true;
    
    try {
        // 2. Usamos FormData para poder enviar el archivo
        const fd = new FormData();
        fd.append('title', guide.value.title);
        fd.append('content', guide.value.content);
        fd.append('game_id', guide.value.game_id);
        if (guide.value.difficulty) {
            fd.append('difficulty', guide.value.difficulty);
        }
        fd.append('user_id', auth.user?.id || 1);
        
        if (selectedCategories.value.length) {
            selectedCategories.value.forEach(id => fd.append('categories[]', id));
        }

        if (coverFile.value) {
            fd.append('image', coverFile.value);
        }

        // Si es edición, Laravel necesita el spoofing de PUT en FormData
        if (guide.value.id) {
            fd.append('_method', 'PUT');
        }
        
        // 3. Ejecutar la petición (Usamos POST incluso para PUT por el spoofing de FormData)
        const url = guide.value.id ? `/api/guides/${guide.value.id}` : '/api/guides';
        const res = await axios.post(url, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

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
<style scoped>
/* Cambiar checks verdes a azul del logo */
:deep(.p-checkbox .p-checkbox-box.p-highlight),
:deep(.p-checkbox.p-checkbox-checked .p-checkbox-box) {
    background: #5369F2 !important;
    border-color: #5369F2 !important;
}

:deep(.p-multiselect-panel .p-multiselect-items .p-multiselect-item.p-highlight) {
    background: rgba(83, 105, 242, 0.1) !important;
    color: #5369F2 !important;
}

:deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item.p-highlight) {
    background: rgba(83, 105, 242, 0.1) !important;
    color: #5369F2 !important;
}

.font-orbitron {
    font-family: 'Orbitron', sans-serif !important;
}
</style>

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
.editor-dark-wrapper .ql-editor {
    overflow-wrap: break-word !important;
    word-break: break-word !important;
}

/* Estilo para los chips de MultiSelect */
.p-multiselect-token {
    background-color: var(--primary-color) !important;
    color: white !important;
}

.font-orbitron {
    font-family: 'Orbitron', sans-serif !important;
}
</style>
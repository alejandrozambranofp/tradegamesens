import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import * as yup from 'yup'
import { useValidation } from './useValidation'
import { useToast } from './useToast'

export default function useGuides() {
    const { errors, validate, clearErrors, hasError, getError } = useValidation()
    const router = useRouter()
    const toast = useToast()

    const isLoading = ref(false)
    const guides = ref([])
    
    // Cambiamos la estructura inicial para "Guide"
    const initialGuide = {
        title: '',
        content: '',
        categories: [],
        thumbnail: null
    }
    const guide = ref({ ...initialGuide })
    const validationErrors = errors

    // Esquema de validación (Punto 4 del examen)
    const guideSchema = yup.object({
        title: yup.string().trim().required('El título es obligatorio'),
        content: yup.string().trim().required('El contenido es obligatorio'),
        categories: yup.array().nullable(),
        thumbnail: yup.mixed().nullable(),
    })

    // Obtener todas las guías (READ)
    const getGuides = async () => {
        return axios.get('/api/guides') // Apunta a tu nueva API
            .then(response => {
                guides.value = response.data;
                return response;
            })
    }

    // Obtener una sola guía
    const getGuide = async (id) => {
        return axios.get('/api/guides/' + id)
            .then(response => {
                guide.value = response.data.data;
                return response;
            })
    }

    // Crear guía (CREATE)
    const storeGuide = async (guideData) => {
        if (isLoading.value) return;

        isLoading.value = true
        clearErrors()

        const { isValid } = validate(guideSchema, guideData)
        if (!isValid) {
            isLoading.value = false
            return
        }

        const serializedGuide = serializeGuide(guideData)

        axios.post('/api/guides', serializedGuide, {
            headers: { "content-type": "multipart/form-data" }
        })
            .then(response => {
                toast.crud.created('Guía')
                router.push({ name: 'guides.index' }) // Asegúrate de tener esta ruta
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const resetGuide = () => {
        guide.value = { ...initialGuide }
        clearErrors()
    }

    // Actualizar guía (UPDATE)
    const updateGuide = async (guideData) => {
        if (isLoading.value) return;

        isLoading.value = true
        clearErrors()

        const { isValid } = validate(guideSchema, guideData)
        if (!isValid) {
            isLoading.value = false
            return
        }

        axios.put('/api/guides/' + guideData.id, guideData)
            .then(response => {
                router.push({ name: 'guides.index' })
                toast.crud.updated('Guía')
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    // Borrar guía (DELETE)
    const deleteGuide = async (id) => {
        axios.delete('/api/guides/' + id)
            .then(response => {
                getGuides() // Recarga la lista
                toast.crud.deleted('Guía')
            })
            .catch(error => {
                toast.error('Error', 'No se pudo eliminar la guía')
            })
    }

    // Serializador para enviar archivos (imágenes)
    const serializeGuide = (data) => {
        const form = new FormData()
        Object.entries(data).forEach(([key, value]) => {
            if (value === undefined || value === null) return
            if (Array.isArray(value)) {
                value.forEach(item => form.append(`${key}[]`, item))
            } else {
                form.append(key, value)
            }
        })
        return form
    }

    return {
        guides,
        guide,
        getGuides,
        getGuide,
        storeGuide,
        updateGuide,
        deleteGuide,
        resetGuide,
        hasError,
        getError,
        validationErrors,
        isLoading
    }
}
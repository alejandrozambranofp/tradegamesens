<template>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Crear Nueva Guía</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="storeGuide(guide)">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input v-model="guide.title" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contenido</label>
                        <textarea v-model="guide.content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Guía</button>
                    <router-link :to="{ name: 'guides.index' }" class="btn btn-secondary ms-2">Volver</router-link>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'
import useGuides from '../../../composables/guides'

const { storeGuide } = useGuides()
const guide = reactive({ title: '', content: '' })
</script>
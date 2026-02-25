import { createRouter, createWebHistory } from "vue-router";
import routes from './routes.js'

const router = createRouter({
    history: createWebHistory(),
    routes, // Aquí se cargan las rutas que definiste en el otro archivo
})

// Opcional: Esto cambia el título de la pestaña del navegador automáticamente
router.beforeEach((to, from, next) => {
    document.title = to.meta.title ? `${to.meta.title} - SmokeCan` : 'SmokeCan';
    next();
});

export default router;
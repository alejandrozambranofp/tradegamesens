import { authStore } from "../store/auth";

const AuthenticatedLayout = () => import('../layouts/AdminLayout.vue');
const AuthenticatedUserLayout = () => import('../layouts/UserLayout.vue');
const GuestLayout = () => import('../layouts/GuestLayout.vue');

async function requireLogin(to, from, next) {
    const auth = authStore();
    const isLogin = !!auth.authenticated;

    if (isLogin) {
        next()
    } else {
        next('/login')
    }
}

const hasAdmin = (roles = []) =>
    roles.some((role) => role?.name?.toLowerCase().includes('admin'));

async function guest(to, from, next) {
    const auth = authStore()
    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next('/app') 
    } else {
        next()
    }
}

async function requireAdmin(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated;
    let user = auth.user;

    if (isLogin) {
        if (hasAdmin(user.roles)) {
            next();
        } else {
            next('/app');
        }
    } else {
        next('/login');
    }
}

export default [
    {
        path: '/',
        component: GuestLayout,
        children: [
            {
                path: '/',
                name: 'home',
                component: () => import('../views/public/home/index.vue'),
            },
            {
                path: 'login',
                name: 'auth.login',
                component: () => import('../views/auth/login/Login.vue'),
                beforeEnter: guest,
            },
            {
                path: 'register',
                name: 'auth.register',
                component: () => import('../views/auth/register/index.vue'),
                beforeEnter: guest,
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: guest,
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: guest,
            },
        ]
    },

    // --- PANEL DE USUARIO (APP) ---
    {
        path: '/app',
        component: AuthenticatedUserLayout,
        beforeEnter: requireLogin,
        meta: { breadCrumb: 'Inicio' },
        children: [
            {
                path: '',
                name: 'app.index',
                // Corregido: Ruta exacta que me pasaste
                component: () => import('../views/admin/users/Index.vue'), 
            },
            {
                name: 'app.profile',
                path: 'profile',
                // Corregido: Apuntamos al profile que sí existía en tu código original
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Perfil' },
            },
            {
                name: 'app.posts',
                path: 'posts',
                component: () => import('../views/admin/posts/index.vue'),
                meta: { breadCrumb: 'Posts' },
            },
            // RUTAS DE GUÍAS PARA EL USUARIO
            {
                name: 'app.guides.my',
                path: 'my-guides',
                // Restaurado a 'user' (singular) como lo tenías originalmente para que no falle
                component: () => import('../views/user/guides/MyGuidesIndex.vue'),
                meta: { breadCrumb: 'Mis Guías' },
            },
            {
                name: 'app.guides.community',
                path: 'guides',
                component: () => import('../views/public/guides/GuidesIndex.vue'),
                meta: { breadCrumb: 'Guías de la Comunidad' },
            }
        ]
    },

    // --- PANEL DE ADMINISTRACIÓN ---
    {
        path: '/admin',
        component: AuthenticatedLayout,
        beforeEnter: requireAdmin,
        meta: { breadCrumb: 'Dashboard' },
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: { breadCrumb: 'Admin', hideBreadcrumb: true }
            },
            {
                name: 'admin.categories.index',
                path: 'categories',
                component: () => import('../views/admin/categories/Index.vue'),
                meta: { breadCrumb: 'Categorías' }
            },
            {
                name: 'admin.users.index',
                path: 'users',
                // Nota: Usas la misma vista que en app.index, está perfecto
                component: () => import('../views/admin/users/Index.vue'),
                meta: { breadCrumb: 'Usuarios' }
            },
            {
                name: 'admin.users.create',
                path: 'users/create',
                component: () => import('../views/admin/users/Create.vue'),
                meta: { breadCrumb: 'Crear Usuario' }
            },
            {
                name: 'admin.users.edit',
                path: 'users/edit/:id',
                component: () => import('../views/admin/users/Edit.vue'),
                meta: { breadCrumb: 'Editar Usuario' }
            },
            {
                name: 'admin.roles.index',
                path: 'roles',
                component: () => import('../views/admin/roles/Index.vue'),
                meta: { breadCrumb: 'Roles' }
            },
            {
                name: 'admin.roles.edit',
                path: 'roles/edit/:id',
                component: () => import('../views/admin/roles/Edit.vue'),
                meta: { breadCrumb: 'Editar Rol' }
            },
            {
                name: 'admin.permissions.index',
                path: 'permissions',
                component: () => import('../views/admin/permissions/Index.vue'),
                meta: { breadCrumb: 'Permisos' }
            }
        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
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
    let user = auth.user;

    if (isLogin) {
        if (hasAdmin(user?.roles)) {
            next('/admin')
        } else {
            next('/')
        }
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
                name: 'guides.show',
                path: 'guides/:id',
                component: () => import('../views/user/guides/GuideShow.vue'),
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
                // Mostrar "Mis Guías" como inicio del panel de usuario
                component: () => import('../views/user/guides/MyGuidesIndex.vue'),
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
            },
            {
                name: 'contribuir',
                path: 'contribuir/:id?',
                component: () => import('../views/user/guides/Contribuir.vue'),
                meta: { breadCrumb: 'Contribuir' },
            }
        ]
    },

    // --- PANEL DE ADMINISTRACIÓN (DISEÑO NUEVO V2) ---
    {
        path: '/admin',
        component: () => import('../layouts/AdminLayoutV2.vue'),
        beforeEnter: requireAdmin,
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin_v2/Dashboard/AdminDashboard.vue'),
            },
            {
                name: 'admin.guides.index',
                path: 'guides',
                component: () => import('../views/admin_v2/Guides/GuideManager.vue'),
            },
            {
                name: 'admin.users.index',
                path: 'users',
                component: () => import('../views/admin_v2/Users/UserList.vue'),
            },
            {
                name: 'admin.categories.index',
                path: 'categories',
                component: () => import('../views/admin_v2/Categories/CategoryList.vue'),
            },
            {
                name: 'admin.roles.index',
                path: 'roles',
                component: () => import('../views/admin_v2/Roles/RoleList.vue'),
            }
        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
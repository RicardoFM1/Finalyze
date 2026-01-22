import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../views/Home.vue'),
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue'),
    },
    {
        path: '/cadastro',
        name: 'Register',
        component: () => import('../views/Register.vue'),
    },
    {
        path: '/painel',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue'),
    },
    {
        path: '/planos',
        name: 'Plans',
        component: () => import('../views/Plans.vue'),
    },
    {
        path: '/lancamentos',
        name: 'Transactions',
        component: () => import('../views/Transactions.vue'),
    },
    {
        path: '/perfil',
        name: 'Profile',
        component: () => import('../views/Profile.vue'),
    },
    {
        path: '/relatorios',
        name: 'Reports',
        component: () => import('../views/Reports.vue'),
    },
    {
        path: '/admin',
        name: 'Admin',
        component: () => import('../views/Admin.vue'),
    },
    {
        path: '/pagamento',
        name: 'Checkout',
        component: () => import('../views/Checkout.vue'),
    },
    // Add other routes here
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const authPages = ['Login', 'Register'];
    const protectedRoutes = ['Dashboard', 'Transactions', 'Profile', 'Reports', 'Admin', 'Checkout'];

    if (token && authPages.includes(to.name)) {
        return next('/painel');
    }

    if (!token && protectedRoutes.includes(to.name)) {
        return next('/login');
    }

    next();
});

export default router

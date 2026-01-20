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

    // If logged in, prevent access to login/register
    // If logged in, prevent access to login/register
    if (token && (to.name === 'Login' || to.name === 'Register')) {
        next('/painel');
    }
    // Protected routes
    else if (to.path.startsWith('/painel') || to.path.startsWith('/lancamentos') || to.path.startsWith('/relatorios')) {
        if (!token) {
            next('/login');
        } else {
            // Check for active plan (requires user data to be loaded in store usually)
            // But store might not be ready. Let's assume user object is in localStorage for quick check
            // or we rely on the component mounted check / backend 403.
            // A better way is to use the store if persisted.
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (!user.plan_id && to.name !== 'Plans' && to.name !== 'Checkout' && to.name !== 'Profile') {
                // Allow Profile so they can logout or update info? Actually user said "impossível acessar essa parte"
                // Redirect to plans with warning
                // alert('Você precisa de um plano ativo para acessar o painel.'); // Bad UX, use toast in view or query param
                next({ path: '/planos', query: { msg: 'no_plan' } });
            } else {
                next();
            }
        }
    }
    else {
        next();
    }
});

export default router

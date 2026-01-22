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


    if (token && (to.name === 'Login' || to.name === 'Register')) {
        next('/painel');
    }

    else if (to.path.startsWith('/painel') || to.path.startsWith('/lancamentos') || to.path.startsWith('/relatorios')) {
        if (!token) {
            next('/login');
        } else {
        
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (!user.plan_id && to.name !== 'Plans' && to.name !== 'Checkout' && to.name !== 'Profile' && user.role != "admin") {
        
                next({ path: '/planos', query: { msg: 'no_plan' } });
     
            }
            next();
        }
    
    }
    else {
        next();
    }
});

export default router

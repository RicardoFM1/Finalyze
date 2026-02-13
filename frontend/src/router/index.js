import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { toast } from 'vue3-toastify'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: () => import('@/views/Home.vue'),
      
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/Login.vue'),
      meta: {RequiresNoAuth: true}
    },
    {
      path: '/cadastro',
      name: 'Register',
      component: () => import('@/views/Register.vue'),
      meta: {RequiresNoAuth: true}
    },
    {
      path: '/planos',
      name: 'Plans',
      component: () => import('@/views/Plans.vue')
    },
    {
      path: '/pagamento',
      name: 'Checkout',
      component: () => import('@/views/Checkout.vue')
    },

    {
      path: '/painel',
      name: 'Dashboard',
      component: () => import('@/views/Dashboard.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'painel-financeiro' }
    },
    {
      path: '/lancamentos',
      name: 'Lancamentos',
      component: () => import('@/views/Lancamentos.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'lancamentos' }
    },
    {
      path: '/metas',
      name: 'Metas',
      component: () => import('@/views/Metas.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'metas' }
    },
    {
      path: '/relatorios',
      name: 'Reports',
      component: () => import('@/views/Reports.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'relatorios-graficos' }
    },
    {
      path: '/perfil',
      name: 'Profile',
      component: () => import('@/views/Profile.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/configuracoes',
      name: 'Settings',
      component: () => import('@/views/Settings.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/admin',
      name: 'Admin',
      component: () => import('@/views/Admin.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },

    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFound.vue')
    }
  ]
})


router.beforeEach(async (to) => {
  const auth = useAuthStore();
  const ui = useUiStore();
  if (auth.isAuthenticated && !auth.user) {
    await auth.fetchUser();
  }

  if(to.meta.RequiresNoAuth && auth.isAuthenticated) {
    return {name: 'Home'}
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'Login' };
  }

  if (auth.user?.admin) {
    return true;
  }

  if (to.meta.requiresAdmin && !auth.user?.admin) {
    toast.error('Acesso restrito para administradores');
    return { name: 'Dashboard' };
  }

  if (to.meta.requiresPlan && !auth.hasActivePlan) {
    toast.error('Seu plano não permite acessar essa funcionalidade');
    return { name: 'Plans' };
  }

  if (to.meta.requiresFeature && !auth.hasFeature(to.meta.requiresFeature)) {
    toast.error('Funcionalidade não disponível no seu plano');
    return { name: 'Dashboard' };
  }
})

export default router

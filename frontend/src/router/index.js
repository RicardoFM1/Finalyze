import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: () => import('@/views/Home.vue')
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/Login.vue')
    },
    {
      path: '/cadastro',
      name: 'Register',
      component: () => import('@/views/Register.vue')
    },
    {
      path: '/planos',
      name: 'Plans',
      component: () => import('@/views/Plans.vue')
    },
    {
      path: '/pagamento',
      name: 'Checkout',
      component: () => import('@/views/Checkout.vue'),
      meta: { requiresAuth: true }
    },

    {
      path: '/painel',
      name: 'Dashboard',
      component: () => import('@/views/Dashboard.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Dashboard' }
    },
    {
      path: '/lancamentos',
      name: 'Lancamentos',
      component: () => import('@/views/Lancamentos.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Lançamentos' }
    },
    {
      path: '/metas',
      name: 'Metas',
      component: () => import('@/views/Metas.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Metas' }
    },
    {
      path: '/relatorios',
      name: 'Reports',
      component: () => import('@/views/Reports.vue'),
      meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Relatórios Gráficos' }
    },
    {
      path: '/perfil',
      name: 'Profile',
      component: () => import('@/views/Profile.vue'),
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


router.beforeEach((to) => {
  const auth = useAuthStore()
  const ui = useUiStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'Login' }
  }

  if (to.meta.requiresAdmin && !auth.user?.admin) {
    ui.notify('Acesso restrito para administradores')
    return { name: 'Dashboard' }
  }

  if (to.meta.requiresPlan && !auth.hasActivePlan) {
    ui.notify('Seu plano não permite acessar essa funcionalidade')
    return { name: 'Plans' }
  }

  if (to.meta.requiresFeature && !auth.hasFeature(to.meta.requiresFeature)) {
    ui.notify('Funcionalidade não disponível no seu plano')
    return { name: 'Dashboard' }
  }
})

export default router

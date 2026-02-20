import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const router = createRouter({
  history: createWebHashHistory(),
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
      meta: { RequiresNoAuth: true }
    },
    {
      path: '/cadastro',
      name: 'Register',
      component: () => import('@/views/Register.vue'),
      meta: { RequiresNoAuth: true }
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
      meta: { hideNavBar: true }
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('@/views/Dashboard.vue'),
      meta: { requiresAuth: true, requiresFeature: 'Painel Financeiro' }
    },
    {
      path: '/lancamentos',
      name: 'Lancamentos',
      component: () => import('@/views/Lancamentos.vue'),
      meta: { requiresAuth: true, requiresFeature: 'Lançamentos' }
    },
    {
      path: '/metas',
      name: 'Metas',
      component: () => import('@/views/Metas.vue'),
      meta: { requiresAuth: true, requiresFeature: 'Metas' }
    },
    {
      path: '/relatorios',
      name: 'Reports',
      component: () => import('@/views/Reports.vue'),
      meta: { requiresAuth: true, requiresFeature: 'Relatórios Gráficos' }
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

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  const ui = useUiStore()

  try {
    // 1. Initial State: If we have a token but no user, fetch user first
    if (auth.isAuthenticated && !auth.user) {
      ui.setLoading(true)
      await auth.fetchUser()
      if (!auth.user) {
        auth.logout()
        return { name: 'Login' }
      }
    }

    // 2. Auth Check
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
      return { name: 'Login' }
    }

    // 3. Admin Check
    if (to.meta.requiresAdmin && !auth.user?.admin) {
      return { name: 'NotFound' }
    }

    // 4. Plan Check (Public routes that need a plan, or features)
    if (to.meta.requiresPlan && !auth.user?.plano_id && !auth.user?.admin) {
      return { name: 'Plans' }
    }

    // 5. Feature Check (Instant block)
    if (to.meta.requiresFeature && !auth.hasFeature(to.meta.requiresFeature)) {
      return { name: 'NotFound' }
    }

    // 6. No Auth Check
    if (to.meta.RequiresNoAuth && auth.isAuthenticated) {
      return { name: 'Home' }
    }
  } catch {
    auth.logout()
    return { name: 'Login' }
  } finally {
    ui.setLoading(false)
  }
})

export default router

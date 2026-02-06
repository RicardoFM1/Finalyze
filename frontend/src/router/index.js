import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { nextTick } from 'vue'

const routes = [
  { path: '/', name: 'Home', component: () => import('../views/Home.vue') },
  { path: '/login', name: 'Login', component: () => import('../views/Login.vue') },
  { path: '/cadastro', name: 'Register', component: () => import('../views/Register.vue') },
  { path: '/painel', name: 'Dashboard', component: () => import('../views/Dashboard.vue'), meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Painel Financeiro' } },
  { path: '/planos', name: 'Plans', component: () => import('../views/Plans.vue') },
  { path: '/lancamentos', name: 'Lancamentos', component: () => import('../views/Lancamentos.vue'), meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Lançamentos' } },
  { path: '/perfil', name: 'Profile', component: () => import('../views/Profile.vue'), meta: { requiresAuth: true } },
  { path: '/metas', name: 'Metas', component: () => import('../views/Metas.vue'), meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Metas' } },
  { path: '/relatorios', name: 'Reports', component: () => import('../views/Reports.vue'), meta: { requiresAuth: true, requiresPlan: true, requiresFeature: 'Relatórios Gráficos' } },
  { path: '/admin', name: 'Admin', component: () => import('../views/Admin.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/pagamento', name: 'Checkout', component: () => import('../views/Checkout.vue') },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('../views/NotFound.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  const ui = useUiStore()

  // 1. Initial State: If we have a token but no user, fetch user first
  if (auth.isAuthenticated && !auth.user) {
    try {
      ui.setLoading(true)
      await auth.fetchUser()
    } catch {
      ui.setLoading(false)
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
})


router.afterEach(() => {
  const ui = useUiStore()
  ui.setLoading(false)
})

export default router

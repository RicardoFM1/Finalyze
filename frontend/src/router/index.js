import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { toast } from 'vue3-toastify'

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


router.beforeEach(async (to) => {
  const auth = useAuthStore();
  const ui = useUiStore();
  if (auth.isAuthenticated && !auth.user) {
    await auth.fetchUser();
  }

  if (to.meta.RequiresNoAuth && auth.isAuthenticated) {
    return { name: 'Home' }
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

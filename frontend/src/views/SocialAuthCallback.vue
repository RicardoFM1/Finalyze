<template>
  <v-container class="fill-height justify-center align-center">
    <div class="text-center">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      <p class="mt-4 text-h6">{{ $t('auth.processing_login') || 'Processando login...' }}</p>
    </div>
  </v-container>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { toast } from 'vue3-toastify'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const ui = useUiStore()

onMounted(async () => {
    const q = route.query
    
    if (q.error) {
        toast.error(q.error)
        router.push({ name: 'Login' })
        return
    }

    console.log('SocialAuthCallback - Params:', q)

    authStore.clearAuthModals()

    if (q.requer_cadastro_completo == '1' || q.requer_cadastro_completo === 'true' || q.requer_cadastro_completo === true) {
        console.log('Global Onboarding set')
        authStore.mustCompleteRegistration = {
            usuario_id: q.usuario_id,
            email: q.email,
            onboarding_token: q.onboarding_token
        }
        router.replace({ name: 'Home' })
    } else if (q.requer_verificacao == '1' || q.requer_verificacao === 'true' || q.requer_verificacao === true) {
        console.log('Global Verification set')
        authStore.mustVerifyEmail = q.email
        router.replace({ name: 'Home' })
    } else if (q.access_token) {
        console.log('Direct Token Login')
        authStore.token = q.access_token
        localStorage.setItem('token', q.access_token)
        
        ui.setLoading(true)
        try {
            await authStore.fetchUser(true)
            await authStore.fetchSharedAccounts()

            if (authStore.user?.admin) {
                authStore.clearAuthModals()
            }
            
            router.replace({ name: 'Home' })
        } catch (e) {
            console.error('Error loading user data:', e)
            router.replace({ name: 'Login' })
        } finally {
            ui.setLoading(false)
        }
    } else {
        console.warn('No valid social auth params found, redirecting to login')
        router.replace({ name: 'Login' })
    }
})
</script>

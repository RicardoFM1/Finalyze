<template>
  <v-container class="fill-height justify-center align-center">
    <div v-if="!showOnboarding && !showVerification" class="text-center">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      <p class="mt-4 text-h6">{{ $t('auth.processing_login') || 'Processando login...' }}</p>
    </div>

    <!-- Onboarding Modal -->
    <ModalCompleteSocialRegistration 
        v-model="showOnboarding"
        :usuario-id="usuarioId"
        :email="email"
        :onboarding-token="onboardingToken"
        @complete="handleOnboardingComplete"
    />

    <!-- Verification Modal -->
    <ModalBase v-model="showVerification" persistent maxWidth="450px" :title="$t('auth.verify_email_title') || 'Verifique seu e-mail'">
        <div class="pa-6">
            <EmailVerification 
                :email="email"
                :loading="loading"
                @verify="handleVerify"
                @resend="handleResend"
                @back="router.push({ name: 'Login' })"
            />
        </div>
    </ModalBase>
  </v-container>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import ModalCompleteSocialRegistration from '../components/Auth/ModalCompleteSocialRegistration.vue'
import EmailVerification from '../components/Auth/EmailVerification.vue'
import ModalBase from '../components/Modals/modalBase.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const showOnboarding = ref(false)
const showVerification = ref(false)
const loading = ref(false)

const usuarioId = ref(null)
const email = ref('')
const onboardingToken = ref('')

onMounted(() => {
    const q = route.query
    
    if (q.error) {
        toast.error(q.error)
        router.push({ name: 'Login' })
        return
    }

    console.log('SocialAuthCallback - Params:', q)
    
    email.value = q.email || ''
    usuarioId.value = q.usuario_id || null

    if (q.requer_cadastro_completo == '1' || q.requer_cadastro_completo === 'true' || q.requer_cadastro_completo === true) {
        console.log('Showing Onboarding')
        showOnboarding.value = true
    } else if (q.requer_verificacao == '1' || q.requer_verificacao === 'true' || q.requer_verificacao === true) {
        console.log('Showing Verification')
        showVerification.value = true
    } else if (q.access_token) {
        console.log('Direct Token Login')
        authStore.token = q.access_token
        localStorage.setItem('token', q.access_token)
        authStore.fetchUser(true).then(() => {
            router.push({ name: 'Home' })
        })
    } else {
        console.warn('No valid social auth params found, redirecting to login')
        router.push({ name: 'Login' })
    }
})

const handleOnboardingComplete = async (result) => {
    showOnboarding.value = false
    if (result.email) {
        email.value = result.email
    }
    if (result.requer_verificacao) {
        showVerification.value = true
    } else {
        router.push({ name: 'Login' })
    }
}

const handleVerify = async (code) => {
    loading.value = true
    try {
        await authStore.verifyCode(email.value, code)
        toast.success('Login realizado com sucesso!')
        router.push({ name: 'Home' })
    } catch (e) {
        toast.error(e.message)
    } finally {
        loading.value = false
    }
}

const handleResend = async () => {
    try {
        await authStore.resendCode(email.value)
        toast.success('Código reenviado!')
    } catch (e) {
        toast.error(e.message)
    }
}
</script>

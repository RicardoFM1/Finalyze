<template>
  <v-container class="fill-height pa-0 auth-wrapper" fluid>
    <v-row no-gutters class="fill-height">
      
      <v-col cols="12" md="6" lg="7" class="d-none d-md-flex flex-column justify-center align-center bg-primary relative overflow-hidden">
        <div class="visual-bg-pattern"></div>
        <div class="visual-content text-center animate-fade-in px-16">
          <v-icon size="120" color="white" class="mb-8 floating-icon">mdi-shield-lock-outline</v-icon>
          <h1 class="text-h2 font-weight-black text-white mb-6">
            {{ $t('landing.hero_title_alt') }}<br>
            <span class="vibrant-secondary">{{ t('landing.destiny') }}</span>
          </h1>
          <p class="text-h6 text-white text-opacity-80 font-weight-light max-w-600 mx-auto">
            {{ $t('landing.hero_subtitle_alt') }}
          </p>
          
            <v-row class="mt-12 justify-center gap-4">
              <v-chip color="rgba(255,255,255,0.15)" class="px-6 py-4 text-white" size="large" variant="flat">
                <v-icon start icon="mdi-check-decagram" color="#5CBBF6"></v-icon>
                <span class="font-weight-medium">Segurança Total</span>
              </v-chip>
              <v-chip color="rgba(255,255,255,0.15)" class="px-6 py-4 text-white" size="large" variant="flat">
                <v-icon start icon="mdi-chart-areaspline" color="#5CBBF6"></v-icon>
                <span class="font-weight-medium">Análise Inteligente</span>
              </v-chip>
            </v-row>
        </div>
        
        <div class="visual-footer mt-12 pl-4 absolute-bottom  text-white opacity-50 text-caption">
          &copy; {{ new Date().getFullYear() }} {{ t('login.login_footer') }}
        </div>
      </v-col>

     
      <v-col cols="12" md="6" lg="5" class="d-flex align-center justify-center relative bg-surface">
        <v-btn
          icon="mdi-arrow-left"
          variant="text"
          class="absolute-top-left ma-4 d-md-none"
          @click="router.push({ name: 'Home' })"
        ></v-btn>

        <v-card flat max-width="450" width="100%" class="pa-8 pa-md-12 bg-transparent">
          <template v-if="!showVerification">
            <div class="text-center text-md-left mb-10">
              <div class="d-flex align-center justify-center justify-md-start mb-4">
              </div>
              <h2 class="text-h4 font-weight-bold mb-2">{{ $t('login.welcome_back') }}</h2>
              <div>
                <p class="text-body-2 text-medium-emphasis">
                  {{ $t('login.no_account') }} 
                  <router-link to="/cadastro" class="text-primary font-weight-bold text-decoration-none ms-1 hover-underline">
                    {{ $t('login.register_link') }}
                  </router-link>
                </p>
              </div>
            </div>

            <AuthForm 
              v-model="form"
              mode="login"
              :loading="loading"
              :error="error"
              @submit="handleLogin"
            />
          </template>

          <EmailVerification 
            v-else
            :email="form.email"
            :loading="loading"
            :error="error"
            @verify="handleVerify"
            @resend="handleResend"
            @back="showVerification = false"
          />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import { useI18n } from 'vue-i18n'
import AuthForm from '../components/Auth/AuthForm.vue'
import EmailVerification from '../components/Auth/EmailVerification.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  senha: ''
})

const loading = ref(false)
const error = ref('')
const showVerification = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const result = await authStore.login(form.value.email, form.value.senha)
    
    if (result && result.requer_verificacao) {
      showVerification.value = true
    } else {
      toast.success(t('toasts.login_success'))
      router.push({ name: 'Dashboard' })
    }
  } catch (err) {
    error.value = err.message || t('toasts.login_error')
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const handleVerify = async (code) => {
  loading.value = true
  error.value = ''
  try {
    await authStore.verifyCode(form.value.email, code)
    toast.success(t('toasts.login_success'))
    router.push({ name: 'Dashboard' })
  } catch (err) {
    error.value = err.message || 'Erro ao verificar código'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const handleResend = async () => {
  try {
    await authStore.resendCode(form.value.email)
    toast.success('Novo código enviado com sucesso!')
  } catch (err) {
    toast.error(err.message || 'Erro ao reenviar código')
  }
}
</script>

<style scoped>
.auth-wrapper {
  overflow: hidden;
  height: 100vh;
}

.bg-primary {
  background: linear-gradient(135deg, #1867C0 0%, #1A237E 100%) !important;
}

.visual-bg-pattern {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
  background-size: 32px 32px;
  opacity: 0.5;
}

.floating-icon {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.gradient-text {
  background: linear-gradient(90deg, #1867C0, #1A237E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

[data-v-theme="dark"] .gradient-text {
  background: linear-gradient(90deg, #5CBBF6, #A2D9FF);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.max-w-600 {
  max-width: 600px;
}

.absolute-bottom {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
}

.absolute-top-left {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 10;
}

.hover-underline:hover {
  text-decoration: underline !important;
}

.vibrant-secondary {
  color: #5CBBF6 !important;
}
</style>

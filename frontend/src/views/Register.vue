<template>
  <v-container class="fill-height pa-0 auth-wrapper" fluid>
    <v-row no-gutters class="fill-height flex-row-reverse">

      <v-col cols="12" md="6" lg="7" class="d-none d-md-flex flex-column justify-center align-center bg-primary relative overflow-hidden">
        <div class="visual-bg-pattern"></div>
        <div class="visual-content text-center animate-fade-in px-16">
          <v-icon size="120" color="white" class="mb-8 floating-icon">mdi-account-plus-outline</v-icon>
          <h1 class="text-h1 font-weight-black text-white mb-6">
            {{ $t('auth.join_us_title') }}
          </h1>
          <p class="text-h6 text-white text-opacity-80 font-weight-light max-w-600 mx-auto">
            {{ $t('auth.join_us_subtitle') }}
          </p>
          
          <v-list bg-transparent class="mt-12 text-left mx-auto transparent-list" max-width="400">
            <v-list-item class="px-0 mb-4 bg-transparent" prepend-icon="mdi-check-circle" base-color="white">
              <v-list-item-title class="text-white font-weight-medium">{{ $t('auth.feature_list.dashboard') }}</v-list-item-title>
            </v-list-item>
            <v-list-item class="px-0 mb-4 bg-transparent" prepend-icon="mdi-check-circle" base-color="white">
              <v-list-item-title class="text-white font-weight-medium">{{ $t('auth.feature_list.reports') }}</v-list-item-title>
            </v-list-item>
            <v-list-item class="px-0 mb-4 bg-transparent" prepend-icon="mdi-check-circle" base-color="white">
              <v-list-item-title class="text-white font-weight-medium">{{ $t('auth.feature_list.goals') }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </div>
        
        <div class="visual-footer absolute-bottom pr-3 text-white opacity-50 text-caption text-right">
          &copy; {{ new Date().getFullYear() }} {{t("register.register_footer")}}
        </div>
      </v-col>

    
      <v-col cols="12" md="6" lg="5" class="d-flex align-center justify-center relative bg-surface scroll-y">
        <v-btn
          icon="mdi-tag-multiple-outline"
          variant="text"
          class="absolute-top-left ma-4 d-md-none"
          @click="router.push({ name: 'Plans' })"
        ></v-btn>

        <v-card flat max-width="500" width="100%" class="pa-4 pa-sm-8 pa-md-12 bg-transparent my-auto">
          <template v-if="!showVerification">
            <div class="text-center mb-10">
              <div class="d-flex align-center justify-center mb-6">
                <img :src="logotipo" alt="Logo" class="logo-mobile mb-2" />
                <h1 class="text-h4 font-weight-black gradient-text ml-3">Finalyze</h1>
              </div>
              <h2 class="text-h4 font-weight-bold mb-2">{{ $t('register.title') }}</h2>
              <div>
                <p class="text-body-2 text-medium-emphasis">
                  {{ $t('register.has_account_text') }} 
                  <router-link to="/login" class="text-primary font-weight-bold text-decoration-none ms-1 hover-underline">
                    {{ $t('register.login_link') }}
                  </router-link>
                </p>
              </div>
            </div>

            <AuthForm 
              v-model="form"
              mode="register"
              :loading="loading"
              :error="error"
              :errors="errors"
              :validateAge="validateAge"
              :validateCPF="validateCPF"
              :passwordRules="passwordRules"
              @submit="handleRegister"
              @update:cpf="handleCpfInput"
            />

            <div class="d-flex align-center my-6">
              <v-divider></v-divider>
              <span class="mx-4 text-caption text-medium-emphasis text-uppercase font-weight-bold">{{ $t('common.or') || 'OU' }}</span>
              <v-divider></v-divider>
            </div>

            <v-btn
              block
              variant="outlined"
              color="medium-emphasis"
              size="large"
              class="rounded-xl font-weight-bold text-none social-btn"
              :disabled="loading"
              @click="authStore.googleLogin()"
            >
              <img src="https://authjs.dev/img/providers/google.svg" width="20" class="me-3" alt="Google" />
              {{ $t('auth.continue_with_google') || 'Continuar com Google' }}
            </v-btn>
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
import logotipo from '../assets/logotipo.png'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  nome: '',
  email: '',
  senha: '',
  senha_confirmation: '',
  cpf: '',
  data_nascimento: '',
  aceita_termos: false,
  aceita_notificacoes: true
})

const loading = ref(false)
const error = ref('')
const errors = ref({})
const showVerification = ref(false)

const passwordRules = [
  v => !!v || t('register.rules.pass_required'),
  v => v.length >= 8 || t('register.rules.min_chars', { count: 8 }),
  v => /[A-Z]/.test(v) || t('register.rules.uppercase'),
  v => /[a-z]/.test(v) || t('register.rules.lowercase'),
  v => /[0-9]/.test(v) || t('register.rules.number'),
  v => /[^A-Za-z0-9]/.test(v) || t('register.rules.special_char')
]

const validateAge = (v) => {
  if (!v) return true
  let birth
  if (typeof v === 'string') {
    const parts = v.split(/[-/]/)
    if (parts.length >= 3) {
      let year, month, day
      if (parts[0].length === 4) { // YYYY-MM-DD
        year = parseInt(parts[0])
        month = parseInt(parts[1])
        day = parseInt(parts[2])
      } else { // DD/MM/YYYY
        day = parseInt(parts[0])
        month = parseInt(parts[1])
        year = parseInt(parts[2])
      }
      // Construct date string with T00:00:00 to ensure local time interpretation
      birth = new Date(`${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}T00:00:00`)
    } else {
      // If it's a string but not in YYYY-MM-DD or DD/MM/YYYY format, try parsing as is, but be aware of potential issues.
      // For robustness, it's better to always parse into year, month, day components.
      // Assuming 'v' might be a simple YYYY-MM-DD string here, which new Date() can misinterpret as UTC.
      // To avoid this, we can re-parse it into components if it looks like YYYY-MM-DD.
      const simpleDateParts = v.split('-')
      if (simpleDateParts.length === 3 && simpleDateParts[0].length === 4) {
        birth = new Date(`${simpleDateParts[0]}-${simpleDateParts[1]}-${simpleDateParts[2]}T00:00:00`)
      } else {
        birth = new Date(v)
      }
    }
  } else {
    // If 'v' is already a Date object or a number (timestamp), new Date(v) is fine.
    birth = new Date(v)
  }
  
  if (!birth || isNaN(birth.getTime())) return false

  const today = new Date()
  let age = today.getFullYear() - birth.getFullYear()
  const m = today.getMonth() - birth.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    age--
  }
  return age >= 18 || t('validation.age_restriction')
}

const validateCPF = (v) => {
    if (!v) return true
    const cpf = v.replace(/\D/g, '')
    if (cpf.length !== 11) return t('validation.cpf_invalid')
    if (/^(\d)\1+$/.test(cpf)) return t('validation.cpf_invalid')
    let sum = 0
    let remainder
    for (let i = 1; i <= 9; i++) sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i)
    remainder = (sum * 10) % 11
    if ((remainder === 10) || (remainder === 11)) remainder = 0
    if (remainder !== parseInt(cpf.substring(9, 10)) ) return t('validation.cpf_invalid')
    sum = 0
    for (let i = 1; i <= 10; i++) sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i)
    remainder = (sum * 10) % 11
    if ((remainder === 10) || (remainder === 11)) remainder = 0
    if (remainder !== parseInt(cpf.substring(10, 11))) return t('validation.cpf_invalid')
    return true
}

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  errors.value = {}
  try {
    const cleanCpf = form.value.cpf.replace(/\D/g, '')
    await authStore.register(
      form.value.nome, 
      form.value.email, 
      form.value.senha, 
      form.value.senha_confirmation,
      cleanCpf,
      form.value.data_nascimento,
      form.value.aceita_termos,
      form.value.aceita_notificacoes
    )
    toast.success('Cadastro realizado! Por favor, verifique seu e-mail.')
    showVerification.value = true
  } catch (err) {
    if (err.response && err.response.status === 422 && err.response.data && err.response.data.errors) {
        errors.value = Object.keys(err.response.data.errors).reduce((acc, key) => {
            acc[key] = err.response.data.errors[key][0]
            return acc
        }, {})
        error.value = t('toasts.check_errors')
    } else {
        error.value = err.message || t('toasts.register_error')
    }
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
    toast.success(t('toasts.register_success'))
    router.push({ name: 'Dashboard' })
  } catch (err) {
    error.value = err.message || 'Erro ao verificar cÃ³digo'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const handleResend = async () => {
  try {
    await authStore.resendCode(form.value.email)
    toast.success('Novo cÃ³digo enviado com sucesso!')
  } catch (err) {
    toast.error(err.message || 'Erro ao reenviar cÃ³digo')
  }
}

const handleCpfInput = (event) => {
  errors.value.cpf = ''
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 11) value = value.substring(0, 11)
  if (value.length > 9) {
    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  } else if (value.length > 6) {
    value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3')
  } else if (value.length > 3) {
    value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2')
  }
  form.value.cpf = value
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
  background: linear-gradient(90deg, #1867C0, #0288D1);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.v-theme--dark .gradient-text {
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

.scroll-y {
  overflow-y: auto !important;
}

.hover-underline:hover {
  text-decoration: underline !important;
}

.transparent-list, .transparent-list :deep(.v-list-item) {
  background: transparent !important;
}

.logo-mobile {
  height: 48px;
  width: auto;
  /* invert white logo to dark in light mode */
  filter: invert(1) brightness(0);
}

:root[data-theme="dark"] .logo-mobile,
.v-theme--dark .logo-mobile {
  filter: none;
}
</style>

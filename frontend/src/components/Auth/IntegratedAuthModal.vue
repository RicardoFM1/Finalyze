<template>
  <ModalBase 
    v-model="model" 
    :title="mode === 'login' ? $t('login.welcome_back') : $t('register.title')" 
    maxWidth="500px"
    :persistent="loading"
  >
    <div class="pa-4 pt-0">
      <div class="text-center mb-6">
        <v-btn-toggle
          v-model="mode"
          mandatory
          color="primary"
          variant="tonal"
          class="rounded-xl overflow-hidden"
          density="comfortable"
          :disabled="loading"
        >
          <v-btn value="login" class="px-8 text-none font-weight-bold">
            {{ $t('checkout.auth_tabs.login') }}
          </v-btn>
          <v-btn value="register" class="px-8 text-none font-weight-bold">
            {{ $t('checkout.auth_tabs.register') }}
          </v-btn>
        </v-btn-toggle>
      </div>

      <template v-if="!showVerification">
        <v-btn
          block
          variant="outlined"
          color="medium-emphasis"
          size="large"
          class="rounded-xl font-weight-bold text-none social-btn mb-6"
          :disabled="loading"
          @click="handleGoogleLogin"
        >
          <img src="https://authjs.dev/img/providers/google.svg" width="20" class="me-3" alt="Google" />
          {{ $t('auth.continue_with_google') || 'Continuar com Google' }}
        </v-btn>

        <div class="d-flex align-center mb-6">
          <v-divider></v-divider>
          <span class="mx-4 text-caption text-medium-emphasis text-uppercase font-weight-bold">{{ $t('common.or') || 'ou' }}</span>
          <v-divider></v-divider>
        </div>

        <AuthForm 
          v-model="form"
          :mode="mode"
          :loading="loading"
          :error="error"
          :errors="errors"
          hide-nav
          show-forgot-password
          @submit="handleSubmit"
          @update:cpf="handleCpfInput"
          @forgot-password="showForgotModal = true"
        />
      </template>

      <div v-else class="text-center">
        <v-icon color="primary" size="64" class="mb-4">mdi-email-check-outline</v-icon>
        <p class="text-body-1 mb-6">
          {{ $t('auth.verify_email_sent', { email: form.email }) || 'Enviamos um código de verificação para seu e-mail.' }}
        </p>
        <EmailVerification 
          :email="form.email"
          :loading="loading"
          :error="error"
          @verify="handleVerify"
          @resend="handleResend"
          @back="showVerification = false"
        />
      </div>
    </div>

    <ModalForgotPassword v-model="showForgotModal" />
  </ModalBase>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue3-toastify'
import ModalBase from '../Modals/modalBase.vue'
import AuthForm from './AuthForm.vue'
import EmailVerification from './EmailVerification.vue'
import ModalForgotPassword from './ModalForgotPassword.vue'

const props = defineProps({
  modelValue: Boolean,
  initialMode: {
    type: String,
    default: 'login'
  }
})

const emit = defineEmits(['update:modelValue', 'success'])

const model = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const { t } = useI18n()
const authStore = useAuthStore()
const mode = ref(props.initialMode)
const loading = ref(false)
const error = ref('')
const errors = ref({})
const showVerification = ref(false)
const showForgotModal = ref(false)

const form = ref({
  nome: '',
  email: '',
  senha: '',
  senha_confirmation: '',
  cpf: '',
  data_nascimento: '',
  aceita_termos: true,
  aceita_notificacoes: true
})

watch(() => props.initialMode, (v) => {
  mode.value = v
})

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

const handleSubmit = async () => {
    loading.value = true
    error.value = ''
    errors.value = {}

    try {
        if (mode.value === 'login') {
            const result = await authStore.login(form.value.email, form.value.senha)
            if (result && result.requer_verificacao) {
                showVerification.value = true
            } else {
                toast.success(t('toasts.login_success'))
                emit('success')
            }
        } else {
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
            toast.success('Cadastro realizado! Verifique seu e-mail.')
            showVerification.value = true
        }
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
        emit('success')
    } catch (err) {
        error.value = err.message || 'Erro ao verificar código'
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

const handleGoogleLogin = () => {
    authStore.googleLogin()
}

</script>

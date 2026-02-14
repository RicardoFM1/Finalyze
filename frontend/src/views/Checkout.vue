<template>
  <v-container class="py-10">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card class="rounded-xl overflow-hidden" elevation="12">
          <v-stepper v-model="step" :items="[$t('checkout.steps.identification'), 'Verificação', $t('checkout.steps.payment')]" hide-actions>
            
            <!-- Step 1: Identification -->
            <template v-slot:item.1>
              <div v-if="!authStore.isAuthenticated">
                <v-tabs v-model="authTab" color="primary" grow class="mb-6 unique-tabs-no-outline">
                  <v-tab value="login" class="no-outline">{{ $t('checkout.auth_tabs.login') }}</v-tab>
                  <v-tab value="register" class="no-outline">{{ $t('checkout.auth_tabs.register') }}</v-tab>
                </v-tabs>

                <div class="pa-4">
                  <AuthForm 
                    v-if="authTab === 'login'"
                    v-model="loginForm"
                    mode="login"
                    :loading="loading"
                    :error="error"
                    hide-nav
                    @submit="handleLogin"
                  />
                  <AuthForm 
                    v-else
                    v-model="registerForm"
                    mode="register"
                    :loading="loading"
                    :error="error"
                    :errors="errors"
                    :validateAge="validateAge"
                    :validateCPF="validateCPF"
                    :passwordRules="registerPasswordRules"
                    hide-nav
                    @submit="handleRegister"
                    @update:cpf="handleCpfInput"
                  />
                </div>
              </div>
              <div v-else class="text-center pa-10">
                <v-icon color="success" size="64" icon="mdi-account-check" class="mb-4"></v-icon>
                <h3 class="text-h5 mb-2">{{ $t('checkout.identified_as', { name: authStore.user?.nome }) }}</h3>
                <p class="text-medium-emphasis mb-6">{{ $t('checkout.ready_to_pay') }}</p>
                <v-btn color="primary" size="large" @click="step = 3">{{ $t('checkout.btn_payment_continue') }}</v-btn>
              </div>
            </template>

            <!-- Step 2: Verification -->
            <template v-slot:item.2>
              <div class="pa-10">
                <EmailVerification 
                  :email="activeEmail"
                  :loading="loading"
                  :error="error"
                  @verify="handleVerify"
                  @resend="handleResend"
                  @back="step = 1"
                />
              </div>
            </template>

            <!-- Step 3: Payment -->
            <template v-slot:item.3>
               <div class="pa-4">
                  <div class="d-flex align-center mb-6">
                    <v-btn icon="mdi-arrow-left" variant="text" @click="step = authStore.isAuthenticated ? 1 : 2" class="mr-2"></v-btn>
                    <h3 class="text-h5 font-weight-bold">{{ $t('checkout.payment_data') }}</h3>
                  </div>

                  <v-alert v-if="planInfo" type="info" variant="tonal" class="mb-6 rounded-lg">
                    {{ $t('checkout.plan_selected', { plan: planInfo.nome, period: periodInfo?.nome }) }}
                    <div class="text-h6 mt-2">{{ $t('checkout.total') }}: {{ formatPrice(periodInfo?.pivot?.valor_centavos / 100) }}</div>
                  </v-alert>

                  <PaymentBrick 
                    v-if="preferenceId" 
                    :preferenceId="preferenceId" 
                    :plan-id="planId"
                    :period-id="periodId"
                  />
                  
                  <div v-if="preferenceId" class="text-center mt-6">
                    <v-btn 
                        variant="text" 
                        color="error" 
                        size="small"
                        @click="cancelPendingPayment"
                        :loading="cancelling"
                    >
                        {{ $t('checkout.cancel_pending') }}
                    </v-btn>
                  </div>

                  <div v-else-if="!checkoutError" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    <p class="mt-4">{{ $t('checkout.preparing_payment') }}</p>
                  </div>
                  <v-alert v-else type="error" variant="tonal" class="mt-4">
                    {{ checkoutError }}
                    <v-btn block color="error" variant="outlined" class="mt-4" @click="initPayment">{{ $t('checkout.try_again') }}</v-btn>
                    <v-btn block variant="text" class="mt-2" @click="router.push({name: 'Plans'})">{{ $t('checkout.back_to_plans') }}</v-btn>
                  </v-alert>
               </div>
            </template>
          </v-stepper>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import PaymentBrick from '../components/PaymentBrick.vue'
import AuthForm from '../components/Auth/AuthForm.vue'
import EmailVerification from '../components/Auth/EmailVerification.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const step = ref(1)
const authTab = ref('login')
const loading = ref(false)
const preferenceId = ref(null)
const checkoutError = ref(null)
const error = ref('')

const planId = ref(route.query.plan)
const periodId = ref(route.query.period)
const planInfo = ref(null)
const periodInfo = ref(null)

const loginForm = ref({ email: '', senha: '' })
const registerForm = ref({ 
    nome: '', email: '', senha: '', senha_confirmation: '', 
    cpf: '', data_nascimento: '' 
})
const errors = ref({})

const activeEmail = computed(() => {
  return authTab.value === 'login' ? loginForm.value.email : registerForm.value.email
})

const registerPasswordRules = [
  v => !!v || t('register.rules.pass_required'),
  v => v.length >= 8 || t('register.rules.min_chars', { count: 8 }),
  v => /[A-Z]/.test(v) || t('register.rules.uppercase'),
  v => /[a-z]/.test(v) || t('register.rules.lowercase'),
  v => /[0-9]/.test(v) || t('register.rules.number'),
  v => /[^A-Za-z0-9]/.test(v) || t('register.rules.special_char')
]

onMounted(async () => {
    if (authStore.isAuthenticated) {
        try {
            const response = await authStore.apiFetch('/checkout/preferencia')
            if (response.ok) {
                const data = await response.json()
                
                if (route.query.plan && Number(data.plan.id) !== Number(route.query.plan)) {
                    preferenceId.value = null
                } else {
                    preferenceId.value = data.id
                    planInfo.value = data.plan
                    planId.value = data.plan.id
                
                    if (route.query.period && data.plan.periodos) {
                        const found = data.plan.periodos.find(p => p.id == route.query.period)
                        if (found) {
                            periodId.value = found.id
                            periodInfo.value = found
                        }
                    }
                    
                    if (!periodInfo.value && data.period_id) {
                        periodInfo.value = data.plan.periodos.find(p => p.id == data.period_id)
                        periodId.value = data.period_id
                    }

                    step.value = 3
                }
            }
        } catch (e) {
            console.warn('No pending preference found or error:', e)
        }
    }

    if (!preferenceId.value && planId.value) {
        try {
            const response = await authStore.apiFetch(`/planos`)
            const plans = await response.json()
            planInfo.value = plans.find(p => Number(p.id) === Number(planId.value))
            
            if (planInfo.value && periodId.value) {
                periodInfo.value = planInfo.value.periodos.find(p => p.id == periodId.value)
            } else if (planInfo.value) {
                periodInfo.value = planInfo.value.periodos[0]
                periodId.value = periodInfo.value?.id
            }

            if (authStore.isAuthenticated) {
                step.value = 3
                await initPayment()
            }
        } catch (e) {
            console.error('Error loading plan info:', e)
        }
    }

    if (!preferenceId.value && !planId.value) {
        router.push({ name: 'Plans' })
    }
})

watch(step, (newStep) => {
    if (newStep === 3 && authStore.isAuthenticated && !preferenceId.value) {
        initPayment()
    }
})

watch(() => route.query, async (newQuery) => {
    if (newQuery.plan) planId.value = newQuery.plan
    if (newQuery.period) periodId.value = newQuery.period
    
    if (authStore.isAuthenticated && (newQuery.plan || newQuery.period)) {
        preferenceId.value = null
        planInfo.value = null
        periodInfo.value = null
        
        // Recarrega informações do plano e inicia novo pagamento
        try {
            const response = await authStore.apiFetch(`/planos`)
            const plans = await response.json()
            planInfo.value = plans.find(p => Number(p.id) === Number(planId.value))
            
            if (planInfo.value && periodId.value) {
                periodInfo.value = planInfo.value.periodos.find(p => p.id == periodId.value)
            } else if (planInfo.value) {
                periodInfo.value = planInfo.value.periodos[0]
                periodId.value = periodInfo.value?.id
            }

            if (step.value === 3) {
                await initPayment()
            }
        } catch (e) {
            console.error('Error reloading plan on query change:', e)
        }
    }
}, { deep: true })

const handleLogin = async () => {
    loading.value = true
    error.value = ''
    try {
      const result = await authStore.login(loginForm.value.email, loginForm.value.senha)
      if (result && result.requer_verificacao) {
        step.value = 2
      } else {
        toast.success(t('toasts.login_success'))
        step.value = 3
        await initPayment()
      }
    } catch (e) {
      error.value = e.message || t('toasts.login_error')
      toast.error(error.value)
    } finally {
      loading.value = false
    }
}

const handleRegister = async () => {
    loading.value = true
    error.value = ''
    errors.value = {}
    try {
        const cleanCpf = registerForm.value.cpf.replace(/\D/g, '')
        await authStore.register(
            registerForm.value.nome,
            registerForm.value.email,
            registerForm.value.senha,
            registerForm.value.senha_confirmation,
            cleanCpf,
            registerForm.value.data_nascimento
        )
        toast.success('Cadastro realizado! Por favor, verifique seu e-mail.')
        step.value = 2
    } catch (e) {
        if (e.response && e.response.status === 422 && e.response.data && e.response.data.errors) {
            errors.value = Object.keys(e.response.data.errors).reduce((acc, key) => {
                acc[key] = e.response.data.errors[key][0] 
                return acc
            }, {})
            error.value = t('toasts.check_errors')
        } else {
            error.value = e.message || t('toasts.register_error')
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
    await authStore.verifyCode(activeEmail.value, code)
    toast.success('Conta verificada com sucesso!')
    step.value = 3
    await initPayment()
  } catch (err) {
    error.value = err.message || 'Erro ao verificar código'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const handleResend = async () => {
  try {
    await authStore.resendCode(activeEmail.value)
    toast.success('Novo código enviado com sucesso!')
  } catch (err) {
    toast.error(err.message || 'Erro ao reenviar código')
  }
}

const initPayment = async () => {
    if (!planId.value || !periodId.value) return
    checkoutError.value = null
    try {
        const response = await authStore.apiFetch('/checkout/preferencia', {
            method: 'POST',
            body: JSON.stringify({
                plano_id: planId.value,
                periodo_id: periodId.value
            })
        })
        const data = await response.json()
        if (response.ok) {
            preferenceId.value = data.id
        } else {
            throw new Error(data.error || t('toasts.error_generic'))
        }
    } catch (e) {
        checkoutError.value = e.message || t('toasts.error_generic')
        toast.error(checkoutError.value)
    }
}

const cancelling = ref(false)
const cancelPendingPayment = async () => {
    cancelling.value = true
    try {
        await authStore.apiFetch('/checkout/cancelar_pagamento', { method: 'PUT' })
        preferenceId.value = null
        toast.success(t('plans.toast_cancel_success'))
        router.push({ name: 'Plans' })
    } catch (e) {
        preferenceId.value = null
        router.push({ name: 'Plans' })
    } finally {
        cancelling.value = false
    }
}

const formatPrice = (value) => {
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    const currency = t('common.currency') === 'R$' ? 'BRL' : 'USD'
    return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(value)
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
  registerForm.value.cpf = value
}

const validateAge = (v) => {
  if (!v) return true
  const birth = new Date(v)
  const today = new Date()
  let age = today.getFullYear() - birth.getFullYear()
  const m = today.getMonth() - birth.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--
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
</script>

<style scoped>
.no-outline :deep(.v-btn__overlay) {
  opacity: 0 !important;
}
.no-outline :deep(.v-ripple__container) {
  display: none !important;
}
.no-outline:focus-visible {
    outline: none !important;
}
</style>
<style>
.unique-tabs-no-outline .v-tab--selected .v-tab__slider {
    display: block; 
}
.unique-tabs-no-outline .v-btn,
.unique-tabs-no-outline .v-btn:focus,
.unique-tabs-no-outline .v-btn:hover,
.unique-tabs-no-outline .v-btn:active,
.unique-tabs-no-outline .v-tab {
    outline: none !important;
    box-shadow: none !important;
}
.unique-tabs-no-outline .v-btn:focus-visible::after,
.unique-tabs-no-outline .v-tab--selected .v-btn__overlay,
.unique-tabs-no-outline .v-tab__overlay {
    opacity: 0 !important;
}
</style>

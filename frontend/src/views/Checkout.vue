<template>
  <v-container class="py-10">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card class="rounded-xl overflow-hidden" elevation="12">
          <v-stepper v-model="step" :items="[$t('checkout.steps.identification'), $t('checkout.steps.payment')]" hide-actions>
            <template v-slot:item.1>
              <div v-if="!authStore.isAuthenticated">
                <v-tabs v-model="authTab" color="primary" grow class="mb-6 unique-tabs-no-outline">
                  <v-tab value="login" class="no-outline">{{ $t('checkout.auth_tabs.login') }}</v-tab>
                  <v-tab value="register" class="no-outline">{{ $t('checkout.auth_tabs.register') }}</v-tab>
                </v-tabs>

                <v-window v-model="authTab">
                  <v-window-item value="login">
                    <v-form @submit.prevent="handleLogin" class="pa-4" v-model="isLoginFormValid">
                      <v-text-field 
                        v-model="loginForm.email" 
                        :label="$t('login.email_label')" 
                        variant="outlined" 
                        prepend-inner-icon="mdi-email"
                        :rules="[v => !!v || $t('login.rules.email_required')]"
                      ></v-text-field>
                      <v-text-field 
                        v-model="loginForm.senha" 
                        :label="$t('login.password_label')" 
                        :type="showPassword ? 'text' : 'password'" 
                        variant="outlined" 
                        prepend-inner-icon="mdi-lock"
                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append-inner="showPassword = !showPassword"
                        :rules="loginPasswordRules"
                        @paste.prevent
                      ></v-text-field>
                      <v-btn class="mt-6" block color="primary" size="large" type="submit" :loading="loading" :disabled="loading || !isLoginFormValid">{{ $t('checkout.btn_login_continue') }}</v-btn>
                    </v-form>
                  </v-window-item>

                  <v-window-item value="register">
                    <v-form @submit.prevent="handleRegister" class="pa-4" v-model="isRegisterFormValid">
                      <v-row>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.nome" 
                            :label="$t('register.name_label')" 
                            variant="outlined"
                            :rules="[v => !!v || $t('register.rules.name_required')]"
                            :error-messages="errors.nome"
                            @input="errors.nome = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.email" 
                            :label="$t('register.email_label')" 
                            variant="outlined"
                            :rules="[v => !!v || $t('register.rules.email_required')]"
                            :error-messages="errors.email"
                            @input="errors.email = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.senha" 
                            :label="$t('register.password_label')" 
                            :type="showRegisterPassword ? 'text' : 'password'" 
                            variant="outlined"
                            :append-inner-icon="showRegisterPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append-inner="showRegisterPassword = !showRegisterPassword"
                            :rules="registerPasswordRules"
                            :error-messages="errors.senha"
                            @input="errors.senha = ''"
                            @paste.prevent
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.senha_confirmation" 
                            :label="$t('register.password_confirm_label')" 
                            :type="showRegisterPassword ? 'text' : 'password'" 
                            variant="outlined"
                            :rules="[v => !!v || $t('register.rules.confirm_required'), v => v === registerForm.senha || $t('register.rules.passwords_match')]"
                            :error-messages="errors.senha_confirmation"
                            @input="errors.senha_confirmation = ''"
                            @paste.prevent
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.cpf" 
                            :label="$t('profile.labels.cpf')" 
                            variant="outlined"
                            @input="handleCpfInput"
                            maxlength="14"
                            :error-messages="errors.cpf"
                            :rules="[v => !!v || $t('register.rules.required', { field: 'CPF' }), validateCPF]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.data_nascimento" 
                            :label="$t('profile.labels.birthdate')" 
                            type="date" 
                            variant="outlined" 
                            :rules="[v => !!v || $t('validation.required'), validateAge]"
                            :error-messages="errors.data_nascimento"
                            @input="errors.data_nascimento = ''"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                      <v-btn class="mt-6" block color="primary" size="large" type="submit" :loading="loading" :disabled="loading || !isRegisterFormValid">{{ $t('checkout.btn_register_continue') }}</v-btn>
                    </v-form>
                  </v-window-item>
                </v-window>
              </div>
              <div v-else class="text-center pa-10">
                <v-icon color="success" size="64" icon="mdi-account-check" class="mb-4"></v-icon>
                <h3 class="text-h5 mb-2">{{ $t('checkout.identified_as', { name: authStore.user?.nome }) }}</h3>
                <p class="text-medium-emphasis mb-6">{{ $t('checkout.ready_to_pay') }}</p>
                <v-btn color="primary" size="large" @click="step = 2">{{ $t('checkout.btn_payment_continue') }}</v-btn>
              </div>
            </template>

            <template v-slot:item.2>
               <div class="pa-4">
                  <div class="d-flex align-center mb-6">
                    <v-btn icon="mdi-arrow-left" variant="text" @click="step = 1" class="mr-2"></v-btn>
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
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import PaymentBrick from '../components/PaymentBrick.vue'
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
const showPassword = ref(false)
const showRegisterPassword = ref(false)

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
const isRegisterFormValid = ref(false)
const isLoginFormValid = ref(false)

const registerPasswordRules = [
  v => !!v || t('register.rules.pass_required'),
  v => v.length >= 8 || t('register.rules.min_chars', { count: 8 }),
  v => /[A-Z]/.test(v) || t('register.rules.uppercase'),
  v => /[a-z]/.test(v) || t('register.rules.lowercase'),
  v => /[0-9]/.test(v) || t('register.rules.number'),
  v => /[^A-Za-z0-9]/.test(v) || t('register.rules.special_char')
]

const loginPasswordRules = [
    v => !!v || t('login.rules.password_required')
]

onMounted(async () => {
   
    if (authStore.isAuthenticated) {
        try {
            const response = await authStore.apiFetch('/checkout/preferencia')
            if (response.ok) {
                const data = await response.json()
                
                if (route.query.plan && Number(data.plan.id) !== Number(route.query.plan)) {
                    console.log('[Checkout] Ignoring pending preference because user selected a new plan.')
                    preferenceId.value = null // This will trigger the next block to create a new preference
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

                    step.value = 2
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
                // Default to first period if missing
                periodInfo.value = planInfo.value.periodos[0]
                periodId.value = periodInfo.value?.id
            }

            if (authStore.isAuthenticated) {
                step.value = 2
                await initPayment()
            }
        } catch (e) {
            console.error('Error loading plan info:', e)
        }
    }

    // 3. Fallback: if even after checks we have no preference and no plan selected, go to plans
    if (!preferenceId.value && !planId.value) {
        router.push({ name: 'Plans' })
    }
})

watch(step, (newStep) => {
    if (newStep === 2 && authStore.isAuthenticated && !preferenceId.value) {
        initPayment()
    }
})

const validateAge = (v) => {
  if (!v) return true
  const birth = new Date(v)
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
    
    if (cpf.length !== 11) {
        // Only show error if we have enough chars to start validating or if it's blur? 
        // For strict validation let's return error if not empty and not 11
        return t('validation.cpf_invalid')
    }

    if (/^(\d)\1+$/.test(cpf)) return t('validation.cpf_invalid')

    let sum = 0
    let remainder
    
    for (let i = 1; i <= 9; i++) 
        sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i)
    remainder = (sum * 10) % 11
    
    if ((remainder === 10) || (remainder === 11))  remainder = 0
    if (remainder !== parseInt(cpf.substring(9, 10)) ) return t('validation.cpf_invalid')

    sum = 0
    for (let i = 1; i <= 10; i++) 
        sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i)
    remainder = (sum * 10) % 11

    if ((remainder === 10) || (remainder === 11))  remainder = 0
    if (remainder !== parseInt(cpf.substring(10, 11))) return t('validation.cpf_invalid')

    return true
}

const handleLogin = async () => {
    loading.value = true
    try {
      await authStore.login(loginForm.value.email, loginForm.value.senha)
      toast.success(t('toasts.login_success'))
      // Proceed to payment step
      step.value = 2
      await initPayment()
    } catch (e) {
      toast.error(e.message || t('toasts.login_error'))
    } finally {
      loading.value = false
    }
}

const handleRegister = async () => {
    loading.value = true
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
        toast.success(t('toasts.register_success'))
        
        if (authStore.isAuthenticated) {
             step.value = 2
             await initPayment()
        } else {
            authTab.value = 'login'
            loginForm.value.email = registerForm.value.email
            toast.info(t('auth.VDS'))
        }
        
    } catch (e) {
        if (e.response && e.response.status === 422 && e.response.data && e.response.data.errors) {
          
            errors.value = Object.keys(e.response.data.errors).reduce((acc, key) => {
                acc[key] = e.response.data.errors[key][0] 
                return acc
            }, {})
            toast.error(t('toasts.check_errors'))
        } else {
             toast.error(e.message || t('toasts.register_error'))
        }
    } finally {
        loading.value = false
    }
}

const initPayment = async () => {
    if (!planId.value || !periodId.value) {
        console.warn('[Checkout] Missing planId or periodId for initPayment')
        return
    }
    
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
        console.error('Init payment error:', e)
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
        planId.value = null
        periodId.value = null
        toast.success(t('plans.toast_cancel_success'))
        router.push({ name: 'Plans' })
    } catch (e) {
        console.error(e)
        // Even if error, force reset to allow user to proceed
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
  formatCPF(event)
}

const formatCPF = (event) => {
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

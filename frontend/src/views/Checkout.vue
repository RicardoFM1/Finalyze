<template>
  <v-container class="py-10">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card class="rounded-xl overflow-hidden" elevation="12">
          <v-stepper v-model="step" :items="['Identificação', 'Pagamento']" hide-actions>
            <template v-slot:item.1>
              <div v-if="!authStore.isAuthenticated">
                <v-tabs v-model="authTab" color="primary" grow class="mb-6 unique-tabs-no-outline">
                  <v-tab value="login" class="no-outline">Entrar</v-tab>
                  <v-tab value="register" class="no-outline">Cadastrar</v-tab>
                </v-tabs>

                <v-window v-model="authTab">
                  <v-window-item value="login">
                    <v-form @submit.prevent="handleLogin" class="pa-4" v-model="isLoginFormValid">
                      <v-text-field 
                        v-model="loginForm.email" 
                        label="E-mail" 
                        variant="outlined" 
                        prepend-inner-icon="mdi-email"
                        :rules="[v => !!v || 'E-mail é obrigatório']"
                      ></v-text-field>
                      <v-text-field 
                        v-model="loginForm.senha" 
                        label="Senha" 
                        :type="showPassword ? 'text' : 'password'" 
                        variant="outlined" 
                        prepend-inner-icon="mdi-lock"
                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append-inner="showPassword = !showPassword"
                        :rules="[v => !!v || 'Senha é obrigatória']"
                      ></v-text-field>
                      <v-btn block color="primary" size="large" type="submit" :loading="loading" :disabled="loading || !isLoginFormValid">Entrar e Continuar</v-btn>
                    </v-form>
                  </v-window-item>

                  <v-window-item value="register">
                    <v-form @submit.prevent="handleRegister" class="pa-4" v-model="isRegisterFormValid">
                      <v-row>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.nome" 
                            label="Nome Completo" 
                            variant="outlined"
                            :rules="[v => !!v || 'Nome é obrigatório']"
                            :error-messages="errors.nome"
                            @input="errors.nome = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.email" 
                            label="E-mail" 
                            variant="outlined"
                            :rules="[v => !!v || 'E-mail é obrigatório']"
                            :error-messages="errors.email"
                            @input="errors.email = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.senha" 
                            label="Senha" 
                            :type="showRegisterPassword ? 'text' : 'password'" 
                            variant="outlined"
                            :append-inner-icon="showRegisterPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append-inner="showRegisterPassword = !showRegisterPassword"
                            :rules="passwordRules"
                            :error-messages="errors.senha"
                            @input="errors.senha = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.senha_confirmation" 
                            label="Confirmar Senha" 
                            :type="showRegisterPassword ? 'text' : 'password'" 
                            variant="outlined"
                            :rules="[v => !!v || 'Confirmação é obrigatória', v => v === registerForm.senha || 'As senhas não coincidem']"
                            :error-messages="errors.senha_confirmation"
                            @input="errors.senha_confirmation = ''"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.cpf" 
                            label="CPF" 
                            variant="outlined"
                            @input="handleCpfInput"
                            maxlength="14"
                            :error-messages="errors.cpf"
                            :rules="[v => !!v || 'CPF é obrigatório', validateCPF]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field 
                            v-model="registerForm.data_nascimento" 
                            label="Data de Nascimento" 
                            type="date" 
                            variant="outlined" 
                            :rules="[v => !!v || 'Campo obrigatório', validateAge]"
                            :error-messages="errors.data_nascimento"
                            @input="errors.data_nascimento = ''"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                      <v-btn block color="primary" size="large" type="submit" :loading="loading" :disabled="loading || !isRegisterFormValid">Cadastrar e Continuar</v-btn>
                    </v-form>
                  </v-window-item>
                </v-window>
              </div>
              <div v-else class="text-center pa-10">
                <v-icon color="success" size="64" icon="mdi-account-check" class="mb-4"></v-icon>
                <h3 class="text-h5 mb-2">Identificado como {{ authStore.user?.nome }}</h3>
                <p class="text-medium-emphasis mb-6">Você está pronto para prosseguir com o pagamento.</p>
                <v-btn color="primary" size="large" @click="step = 2">Continuar para Pagamento</v-btn>
              </div>
            </template>

            <template v-slot:item.2>
               <div class="pa-4">
                  <div class="d-flex align-center mb-6">
                    <v-btn icon="mdi-arrow-left" variant="text" @click="step = 1" class="mr-2"></v-btn>
                    <h3 class="text-h5 font-weight-bold">Dados de Pagamento</h3>
                  </div>

                  <v-alert v-if="planInfo" type="info" variant="tonal" class="mb-6 rounded-lg">
                    Você selecionou o plano <strong>{{ planInfo.nome }}</strong> ({{ periodInfo?.nome }}).
                    <div class="text-h6 mt-2">Total: {{ formatPrice(periodInfo?.pivot?.valor_centavos / 100) }}</div>
                  </v-alert>

                  <PaymentBrick 
                    v-if="preferenceId" 
                    :preferenceId="preferenceId" 
                    :plan-id="planId"
                    :period-id="periodId"
                  />
                  <div v-else-if="!checkoutError" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    <p class="mt-4">Preparando ambiente de pagamento...</p>
                  </div>
                  <v-alert v-else type="error" variant="tonal" class="mt-4">
                    {{ checkoutError }}
                    <v-btn block color="error" variant="outlined" class="mt-4" @click="initPayment">Tentar Novamente</v-btn>
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

const passwordRules = [
  v => !!v || 'Senha é obrigatória',
  v => v.length >= 8 || 'Mínimo 8 caracteres',
  v => /[A-Z]/.test(v) || 'Deve conter uma letra maiúscula',
  v => /[a-z]/.test(v) || 'Deve conter uma letra minúscula',
  v => /[0-9]/.test(v) || 'Deve conter um número',
  v => /[^A-Za-z0-9]/.test(v) || 'Deve conter um caractere especial (!@#$%...)'
]

onMounted(async () => {
    // 1. If authenticated, ALWAYS check for a pending preference first
    if (authStore.isAuthenticated) {
        try {
            const response = await authStore.apiFetch('/checkout/preferencia')
            if (response.ok) {
                const data = await response.json()
                preferenceId.value = data.id
                planInfo.value = data.plan
                planId.value = data.plan.id
                // If the user came with a specific query that matches the pending, use it to set periodInfo
                if (route.query.period && data.plan.periodos) {
                     const found = data.plan.periodos.find(p => p.id == route.query.period)
                     if (found) {
                         periodId.value = found.id
                         periodInfo.value = found
                     }
                }
                
                // If we still don't have periodInfo, try to use what the backend sent
                if (!periodInfo.value && data.period_id) {
                    periodInfo.value = data.plan.periodos.find(p => p.id == data.period_id)
                    periodId.value = data.period_id
                }

                step.value = 2
            }
        } catch (e) {
            console.warn('No pending preference found or error:', e)
        }
    }

    // 2. If no preference found but we have query params, prepare the planInfo
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

            // If already authenticated and we have plan info, init payment
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
  return age >= 18 || 'Você deve ter pelo menos 18 anos.'
}

const validateCPF = (v) => {
    if (!v) return true
    
    // Remove non-digits
    const cpf = v.replace(/\D/g, '')
    
    // Check if empty (optional field handled by required elsewhere) or incorrect length
    // But here we want to validate format if typing has occurred
    if (cpf.length !== 11) {
        // Only show error if we have enough chars to start validating or if it's blur? 
        // For strict validation let's return error if not empty and not 11
        return 'CPF inválido'
    }

    // Check for known invalid patterns (all same digits)
    if (/^(\d)\1+$/.test(cpf)) return 'CPF inválido'

    let sum = 0
    let remainder

    for (let i = 1; i <= 9; i++) 
        sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i)
    remainder = (sum * 10) % 11
    
    if ((remainder === 10) || (remainder === 11))  remainder = 0
    if (remainder !== parseInt(cpf.substring(9, 10)) ) return 'CPF inválido'

    sum = 0
    for (let i = 1; i <= 10; i++) 
        sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i)
    remainder = (sum * 10) % 11

    if ((remainder === 10) || (remainder === 11))  remainder = 0
    if (remainder !== parseInt(cpf.substring(10, 11))) return 'CPF inválido'

    return true
}

const handleLogin = async () => {
    loading.value = true
    try {
        await authStore.login(loginForm.value.email, loginForm.value.senha)
        toast.success('Login realizado com sucesso!')
        step.value = 2
    } catch (e) {
        toast.error(e.message || 'Erro ao fazer login')
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
        toast.success('Cadastro realizado com sucesso!')
        authTab.value = 'login'
    } catch (e) {
        if (e.response && e.response.status === 422 && e.response.data && e.response.data.errors) {
          
            errors.value = Object.keys(e.response.data.errors).reduce((acc, key) => {
                acc[key] = e.response.data.errors[key][0] 
                return acc
            }, {})
            toast.error('Verifique os campos em vermelho.')
        } else {
             toast.error(e.message || 'Erro ao realizar cadastro')
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
            throw new Error(data.error || 'Erro ao gerar preferência')
        }
    } catch (e) {
        console.error('Init payment error:', e)
        checkoutError.value = e.message || 'Falha ao iniciar ambiente de pagamento. Verifique sua conexão.'
        toast.error(checkoutError.value)
    }
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
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

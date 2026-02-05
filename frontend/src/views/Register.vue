<template>
  <v-container class="fill-height" fluid style="background: linear-gradient(135deg, #1867C0 0%, #5CBBF6 100%);">
    <v-row align="center" justify="center" class="h-100">
      <v-col cols="12" md="8" lg="6" xl="4">
        <v-card elevation="24" rounded="lg" class="overflow-hidden">
          <v-row no-gutters>
            <v-col cols="12" md="5" class="d-none d-md-flex align-center justify-center bg-primary pa-10">
              <div class="text-center">
                <v-icon size="80" color="white" class="mb-6">mdi-finance</v-icon>
                <h2 class="text-h4 font-weight-bold text-white mb-2">Junte-se a nós</h2>
                <p class="text-white text-opacity-75">Comece sua jornada para a liberdade financeira hoje mesmo.</p>
              </div>
            </v-col>
            <v-col cols="12" md="7" class="pa-8">
              <div class="text-center mb-8">
                <h2 class="text-h4 font-weight-bold text-primary">Criar Conta</h2>
                <p class="text-medium-emphasis">Preencha seus dados abaixo</p>
              </div>
              
              <v-form @submit.prevent="handleRegister" v-model="isValid">
                <v-text-field
                  v-model="form.nome"
                  label="Nome Completo"
                  prepend-inner-icon="mdi-account"
                  variant="outlined"
                  color="primary"
                  :rules="[v => !!v || 'Nome é obrigatório']"
                  :error-messages="errors.nome"
                  @input="errors.nome = ''"
                ></v-text-field>

                <v-text-field
                  v-model="form.email"
                  label="E-mail"
                  prepend-inner-icon="mdi-email"
                  variant="outlined"
                  color="primary"
                  type="email"
                  :rules="[v => !!v || 'E-mail é obrigatório']"
                  :error-messages="errors.email"
                  @input="errors.email = ''"
                ></v-text-field>

                <v-text-field
                  v-model="form.senha"
                  label="Senha"
                  prepend-inner-icon="mdi-lock"
                  variant="outlined"
                  color="primary"
                  :type="showPass ? 'text' : 'password'"
                  :append-inner-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append-inner="showPass = !showPass"
                  :rules="passwordRules"
                  :error-messages="errors.senha"
                  @input="errors.senha = ''"
                ></v-text-field>

                  <v-text-field
                    v-model="form.senha_confirmation"
                    label="Confirmar Senha"
                    prepend-inner-icon="mdi-lock-check"
                    variant="outlined"
                    color="primary"
                    :type="showPass ? 'text' : 'password'"
                    :rules="[v => !!v || 'Confirmação é obrigatória', v => v === form.senha || 'As senhas não coincidem']"
                    :error-messages="errors.senha_confirmation"
                    @input="errors.senha_confirmation = ''"
                  ></v-text-field>

                <v-text-field
                  v-model="form.cpf"
                  label="CPF"
                  prepend-inner-icon="mdi-card-account-details"
                  variant="outlined"
                  color="primary"
                  @input="handleCpfInput"
                  maxlength="14"
                  :rules="[v => !!v || 'CPF é obrigatório', validateCPF]"
                  :error-messages="errors.cpf"
                ></v-text-field>

                <v-text-field
                  v-model="form.data_nascimento"
                  label="Data de Nascimento"
                  prepend-inner-icon="mdi-calendar"
                  variant="outlined"
                  color="primary"
                  type="date"
                  :rules="[v => !!v || 'Data de nascimento é obrigatória', validateAge]"
                  :error-messages="errors.data_nascimento"
                  @input="errors.data_nascimento = ''"
                ></v-text-field>

                <v-alert v-if="error" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>
                
                <v-btn
                  block
                  color="primary"
                  size="x-large"
                  type="submit"
                  :loading="loading"
                  class="mb-4 font-weight-bold"
                  elevation="4"
                  :disabled="buttonDesativado"
                >
                  Cadastrar
                </v-btn>
              </v-form>
               <v-btn
                  block
                  color="green"
                  size="x-large"
                  type="button"
                  class="mb-4 font-weight-bold"
                  elevation="4"
                  @click="router.push({ name: 'Home' })"
                >
                  Página inicial
                </v-btn>
              <div class="text-center">
                <span class="text-body-2 text-medium-emphasis">Já tem uma conta? </span>
                <router-link to="/login" class="text-primary font-weight-bold text-decoration-none">Entrar</router-link>
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify';

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  nome: '',
  email: '',
  senha: '',
  senha_confirmation: '',
  cpf: '',
  data_nascimento: ''
})

const showPass = ref(false)
const loading = ref(false)
const isValid = ref(false)
const error = ref('')
const errors = ref({})

const passwordRules = [
  v => !!v || 'Senha é obrigatória',
  v => v.length >= 8 || 'Mínimo 8 caracteres',
  v => /[A-Z]/.test(v) || 'Deve conter uma letra maiúscula',
  v => /[a-z]/.test(v) || 'Deve conter uma letra minúscula',
  v => /[0-9]/.test(v) || 'Deve conter um número',
  v => /[^A-Za-z0-9]/.test(v) || 'Deve conter um caractere especial (!@#$%...)'
]

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
    
    
    const cpf = v.replace(/\D/g, '')
    
    if (cpf.length !== 11) {
        return 'CPF inválido'
    }

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

const handleRegister = async () => {
  if (!isValid.value) return 
  
  loading.value = true
  error.value = ''
  
  try {
    console.log('Dados do formulário:', form.value)
    const cleanCpf = form.value.cpf.replace(/\D/g, '')
    await authStore.register(
      form.value.nome, 
      form.value.email, 
      form.value.senha, 
      form.value.senha_confirmation,
      cleanCpf,
      form.value.data_nascimento
    )
    toast.success("Cadastro realizado com sucesso!")
    router.push({ name: 'Login' })
  } catch (err) {
    if (err.response && err.response.status === 422 && err.response.data && err.response.data.errors) {
        
        errors.value = Object.keys(err.response.data.errors).reduce((acc, key) => {
            acc[key] = err.response.data.errors[key][0]
            return acc
        }, {})
        error.value = 'Verifique os campos em vermelho.'
    } else {
        error.value = err.message || 'Erro ao cadastrar'
    }
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const buttonDesativado = computed(() => 
form.value.nome === '' 
|| form.value.email === '' 
|| form.value.senha === '' 
|| form.value.senha_confirmation === '' 
|| form.value.cpf === ''
|| form.value.data_nascimento === ''
|| form.value.senha !== form.value.senha_confirmation
|| !isValid.value
)

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
  form.value.cpf = value
}
</script>

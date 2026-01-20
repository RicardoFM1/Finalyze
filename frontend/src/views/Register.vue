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
                  v-model="form.name"
                  label="Nome Completo"
                  prepend-inner-icon="mdi-account"
                  variant="outlined"
                  color="primary"
                  :rules="[v => !!v || 'Nome é obrigatório']"
                ></v-text-field>

                <v-text-field
                  v-model="form.email"
                  label="E-mail"
                  prepend-inner-icon="mdi-email"
                  variant="outlined"
                  color="primary"
                  type="email"
                  :rules="[v => !!v || 'E-mail é obrigatório']"
                ></v-text-field>

                <v-text-field
                  v-model="form.password"
                  label="Senha"
                  prepend-inner-icon="mdi-lock"
                  variant="outlined"
                  color="primary"
                  :type="showPass ? 'text' : 'password'"
                  :append-inner-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append-inner="showPass = !showPass"
                  :rules="passwordRules"
                ></v-text-field>

                <v-text-field
                  v-model="form.password_confirmation"
                  label="Confirmar Senha"
                  prepend-inner-icon="mdi-lock-check"
                  variant="outlined"
                  color="primary"
                  type="password"
                  :rules="[v => !!v || 'Confirmação é obrigatória', v => v === form.password || 'As senhas não coincidem']"
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
                >
                  Cadastrar
                </v-btn>
              </v-form>

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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify';

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const showPass = ref(false)
const loading = ref(false)
const isValid = ref(false)
const error = ref('')

const passwordRules = [
  v => !!v || 'Senha é obrigatória',
  v => v.length >= 8 || 'Mínimo 8 caracteres',
  v => /[A-Z]/.test(v) || 'Deve conter uma letra maiúscula',
  v => /[a-z]/.test(v) || 'Deve conter uma letra minúscula',
  v => /[0-9]/.test(v) || 'Deve conter um número',
  v => /[^A-Za-z0-9]/.test(v) || 'Deve conter um caractere especial (!@#$%...)'
]

const handleRegister = async () => {
  if (!isValid.value) return 
  
  loading.value = true
  error.value = ''
  
  try {
    await authStore.register(form.value.name, form.value.email, form.value.password, form.value.password_confirmation)
    toast.success("Cadastro realizado com sucesso! Faça login para continuar.")
    router.push('/login')
  } catch (err) {
    error.value = err.message || 'Erro ao cadastrar'
    // Extract validation errors if available
    if (err.response && err.response.data && err.response.data.errors) {
         error.value = Object.values(err.response.data.errors).flat().join('\n');
    }
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}
</script>

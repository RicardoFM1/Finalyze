<template>
  <v-container class="fill-height py-4 py-md-0" fluid style="background: linear-gradient(135deg, #1867C0 0%, #5CBBF6 100%);">
    <v-row align="center" justify="center" class="h-100">
      <v-col cols="12" md="8" lg="6" xl="4">
        <v-card elevation="24" rounded="lg" class="overflow-hidden min-h-0">
          <v-row no-gutters>
             <v-col cols="12" md="7" class="pa-4 pa-md-8">
              <div class="text-center mb-4 mb-md-8">
               <h2 class="text-h6 text-md-h4 font-weight-bold text-primary">
                Bem-vindo(a)
              </h2>

               <p class="text-body-2 text-md-body-1 text-medium-emphasis">
                Entre para acessar suas finanças
              </p>

              </div>
              
              <v-form @submit.prevent="handleLogin" v-model="isValid">
                <v-text-field
                  density="compact"
                  v-model="form.email"
                  label="E-mail"
                  :prepend-inner-icon="showIcon ? 'mdi-email' : ''"
                  variant="outlined"
                  color="primary"
                  type="email"
                  class="mb-1 mb-md-2"
                  :rules="[v => !!v || 'E-mail é obrigatório']"
                ></v-text-field>

                <v-text-field
                class="mb-1 mb-md-2"
                  density="compact"
                  v-model="form.password"
                  label="Senha"
                  prepend-inner-icon="mdi-lock"
                  variant="outlined"
                  color="primary"
                  :type="showPass ? 'text' : 'password'"
                  :append-inner-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append-inner="showPass = !showPass"
                  :rules="[v => !!v || 'Senha é obrigatória']"
                ></v-text-field>

                <div class="d-flex justify-end mb-4">
                    <a href="#"  class="text-caption text-md-body-2" >Esqueceu a senha?</a>
                </div>

                <v-alert v-if="error" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>
                
                <v-btn
                  
                  color="primary"
                  block
                  size="default"
                  class="py-2 py-md-4 text-body-2 text-md-body-1"
                  type="submit"
                  :loading="loading"
                  elevation="4"
                >
                  Entrar
                </v-btn>
              </v-form>

              <div class="text-center mt-3 mt-md-6">
                <span class="text-caption text-md-body-2 text-medium-emphasis">Novo por aqui? </span>
                <router-link to="/cadastro" class="text-caption text-md-body-2 text-primary font-weight-bold text-decoration-none">Criar Conta</router-link>
              </div>
            </v-col>
            <v-col cols="12" md="5" class="d-none d-md-flex align-center justify-center bg-primary pa-10">
              <div class="text-center">
                <v-icon size="80" color="white" class="mb-6">mdi-chart-pie</v-icon>
                <h2 class="text-h4 font-weight-bold text-white mb-2">Finalyze</h2>
                <p class="text-white text-opacity-75">Sua vida financeira na palma da mão.</p>
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
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify';

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const isValid = ref(false)
const showPass = ref(false)
const error = ref('')

const handleLogin = async () => {
  if (!isValid.value) return
  
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login(form.value.email, form.value.password)
    toast.success("Login realizado com sucesso!")
    
    const redirectPath = route.query.redirect || '/painel'
    const planId = route.query.plan
    
    // If there's a plan selected, we might want to store it or pass it along
    if (planId) {
        router.push({ path: '/' + redirectPath, query: { plan: planId } })
    } else {
        router.push(redirectPath)
    }
    
  } catch (err) {
    error.value = 'Credenciais inválidas'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}
</script>


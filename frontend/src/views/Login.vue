<template>
  <v-container class="fill-height pa-0 auth-wrapper" fluid>
    <v-row no-gutters class="fill-height">
      
      <v-col cols="12" md="6" lg="7" class="d-none d-md-flex flex-column justify-center align-center bg-primary relative overflow-hidden">
        <div class="visual-bg-pattern"></div>
        <div class="visual-content text-center animate-fade-in px-16">
          <v-icon size="120" color="white" class="mb-8 floating-icon">mdi-shield-lock-outline</v-icon>
          <h1 class="text-h2 font-weight-black text-white mb-6">
            {{ $t('landing.hero_title_alt') }}<br>
            <span class="text-secondary">{{ $t('landing.destiny') }}</span>
          </h1>
          <p class="text-h6 text-white text-opacity-80 font-weight-light max-w-600 mx-auto">
            {{ $t('landing.hero_subtitle_alt') }}
          </p>
          
            <v-row class="mt-12 justify-center gap-4">
              <v-chip color="rgba(255,255,255,0.15)" class="px-6 py-4 text-white" size="large" variant="flat">
                <v-icon start icon="mdi-check-decagram" color="secondary"></v-icon>
                <span class="font-weight-medium">Segurança Total</span>
              </v-chip>
              <v-chip color="rgba(255,255,255,0.15)" class="px-6 py-4 text-white" size="large" variant="flat">
                <v-icon start icon="mdi-chart-areaspline" color="secondary"></v-icon>
                <span class="font-weight-medium">Análise Inteligente</span>
              </v-chip>
            </v-row>
        </div>
        
        <div class="visual-footer absolute-bottom pa-8 text-white opacity-50 text-caption">
          &copy; {{ new Date().getFullYear() }} Finalyze Finance. Design Premium & Segurança Bancária.
        </div>
      </v-col>

      <!-- Right Side: Login Form -->
      <v-col cols="12" md="6" lg="5" class="d-flex align-center justify-center relative bg-surface">
        <v-btn
          icon="mdi-arrow-left"
          variant="text"
          class="absolute-top-left ma-4 d-md-none"
          @click="router.push({ name: 'Home' })"
        ></v-btn>

        <v-card flat max-width="450" width="100%" class="pa-8 pa-md-12 bg-transparent">
          <div class="text-center text-md-left mb-10">
            <div class="d-flex align-center justify-center justify-md-start mb-4">
              <v-avatar color="primary" size="48" class="mr-3 elevation-4">
                <v-icon icon="mdi-finance" color="white"></v-icon>
              </v-avatar>
              <span class="text-h5 font-weight-black gradient-text">Finalyze</span>
            </div>
            <h2 class="text-h4 font-weight-bold mb-2">{{ $t('login.welcome_back') }}</h2>
            <p class="text-medium-emphasis">{{ $t('login.subtitle') }}</p>
          </div>

          <v-form @submit.prevent="handleLogin" v-model="isValid" class="mt-4">
            <div class="form-group mb-6">
              <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('login.email_label') }}</label>
              <v-text-field
                v-model="form.email"
                placeholder="exemplo@email.com"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
                color="primary"
                type="email"
                density="comfortable"
                class="rounded-lg"
                :disabled="loading"
                :rules="[v => !!v || $t('login.rules.email_required')]"
                hide-details="auto"
              ></v-text-field>
            </div>

            <div class="form-group mb-4">
              <div class="d-flex justify-space-between align-center mb-2">
                <label class="text-caption font-weight-bold text-medium-emphasis ms-1">{{ $t('login.password_label') }}</label>
                <router-link to="/recuperar-senha" class="text-caption text-primary font-weight-bold text-decoration-none">Esqueceu a senha?</router-link>
              </div>
              <v-text-field
                v-model="form.senha"
                placeholder="********"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="rounded-lg"
                :type="showPass ? 'text' : 'password'"
                :append-inner-icon="showPass ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                @click:append-inner="showPass = !showPass"
                :disabled="loading"
                :rules="[v => !!v || $t('login.rules.password_required')]"
                hide-details="auto"
                @paste.prevent
              ></v-text-field>
            </div>

            <v-alert v-if="error" type="error" variant="tonal" class="mb-6 rounded-lg" density="compact" closable>{{ error }}</v-alert>

            <v-btn
              color="primary"
              block
              size="x-large"
              class="mt-8 rounded-xl font-weight-bold py-4 text-none elevation-8"
              type="submit"
              :loading="loading"
              :disabled="buttonDesativado"
            >
              {{ $t('login.btn_login') }}
              <v-icon end icon="mdi-chevron-right" class="ms-2"></v-icon>
            </v-btn>

            <v-btn
              variant="tonal"
              block
              size="large"
              class="mt-4 rounded-xl font-weight-medium text-none"
              :to="{ name: 'Register' }"
            >
              {{ $t('login.register_link') }}
            </v-btn>
          </v-form>

          <v-divider class="my-10">
            <span class="text-caption text-medium-emphasis px-4">OU</span>
          </v-divider>

          <div class="text-center">
            <p class="text-body-2 text-medium-emphasis">
              {{ $t('login.no_account') }} 
              <router-link to="/cadastro" class="text-primary font-weight-bold text-decoration-none ms-1 hover-underline">
                {{ $t('login.register_link') }}
              </router-link>
            </p>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({
  email: '',
  senha: ''
})

const loading = ref(false)
const isValid = ref(false)
const showPass = ref(false)
const error = ref('')

const handleLogin = async () => {
  if (!isValid.value) return
  
  loading.value = true
  
  try {
    await authStore.login(form.value.email, form.value.senha)
    toast.success(t('toasts.login_success'))
    router.push({ name: 'Dashboard' })
  } catch (err) {
    toast.error(err.message || t('toasts.login_error'))
  } finally {
    loading.value = false
  }
}
const buttonDesativado = computed(() => 
  form.value.email === '' || 
  form.value.senha === '' || 
  !isValid.value ||
  loading.value
)
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

.form-group :deep(.v-field__outline) {
  --v-field-border-opacity: 0.2;
  transition: all 0.2s;
}

.form-group :deep(.v-field--focused .v-field__outline) {
  --v-field-border-opacity: 1;
}

.hover-underline:hover {
  text-decoration: underline !important;
}
</style>



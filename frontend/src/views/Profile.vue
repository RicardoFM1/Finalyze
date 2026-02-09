<template>
  <v-container>

    <!-- HEADER -->
    <div class="d-flex align-center mb-6">
      <v-icon icon="mdi-account-circle" color="primary" size="32" class="mr-3" />
      <h1 class="text-h4 font-weight-bold">{{ $t('profile.title') }}</h1>
    </div>

    <v-card class="rounded-xl overflow-hidden" elevation="3">

      <!-- TABS -->
      <v-tabs
        v-model="activeTab"
        bg-color="primary"
        color="white"
        grow
        class="profile-tabs"
      >
        <v-tab value="personal">
          <v-icon start icon="mdi-account-circle" />
          {{ $t('profile.tabs.personal') }}
        </v-tab>

        <v-tab value="assinatura">
          <v-icon start icon="mdi-star-circle" />
          {{ $t('profile.tabs.subscription') }}
        </v-tab>

        <v-tab value="historico">
          <v-icon start icon="mdi-receipt" />
          {{ $t('profile.tabs.history') }}
        </v-tab>
      </v-tabs>

      <!-- WINDOWS -->
      <v-window v-model="activeTab">

        <!-- ================= PERFIL ================= -->
        <v-window-item value="personal">
          <v-container>

            <!-- LOADING -->
            <div
              v-if="loadingUser"
              class="text-center py-10 d-flex flex-column align-center"
            >
              <v-progress-circular indeterminate color="primary" />
              {{ $t('profile.loading') }}
            </div>

            <!-- CONTEÚDO -->
            <template v-else>

              <!-- PERFIL -->
              <v-row class="pa-6 pa-md-10">
                <v-col cols="12" md="4" class="text-center">

                  <div class="avatar-wrapper mb-6">
                    <v-avatar size="160" color="primary-lighten-4" class="avatar-main">
                      <v-img
                        v-if="user.avatar || previewAvatar"
                        :src="previewAvatar || `http://localhost:8000/storage/${user.avatar}`"
                        cover
                      />
                      <span v-else class="text-h2 font-weight-bold text-primary">
                        {{ getInitials(user.nome) }}
                      </span>
                    </v-avatar>

                    <v-btn
                      icon="mdi-camera"
                      color="primary"
                      size="small"
                      class="avatar-edit-btn"
                      @click="triggerFileInput"
                    />

                    <v-btn
                      v-if="user.avatar"
                      icon="mdi-delete"
                      color="error"
                      size="x-small"
                      class="avatar-delete-btn"
                      @click="removeAvatar"
                    />

                    <input
                      type="file"
                      ref="fileInput"
                      class="d-none"
                      accept="image/*"
                      @change="handleFileChange"
                    />
                  </div>

                  <v-chip
                    :color="user.admin ? 'deep-purple' : 'primary'"
                    class="font-weight-bold"
                  >
                    {{ user.admin ? $t('profile.roles.admin') : $t('profile.roles.client') }}
                  </v-chip>
                </v-col>
              </v-row>

              <v-btn variant="outlined" color="primary" to="/planos">
                {{ $t('profile.btn_upgrade') }}
              </v-btn>

              <Calendar @select="handleCalendarSelect" />


              <ReminderModal
                v-model="showReminderModal"
                :date="selectedDate"
              />

              <v-btn color="primary" @click="open = true">
                Compartilhar
              </v-btn>

              <CompartilharModal
                v-model="open"
                @invite="sendInvite"
              />

              <!-- ================= ASSINATURA ================= -->
              <div
                v-if="!hasActiveOrValidSubscription"
                class="text-center py-10 no-plan-empty"
              >
                <v-icon icon="mdi-alert-circle-outline" size="64" color="grey" />
                <h3 class="text-h5 mt-4">
                  {{ $t('profile.subscription.no_active') }}
                </h3>
                <p class="text-medium-emphasis mb-6">
                  {{ $t('profile.subscription.no_active_desc') }}
                </p>
                <v-btn color="primary" :to="{ name: 'Plans' }">
                  {{ $t('profile.subscription.view_plans') }}
                </v-btn>
              </div>

              <v-row v-else>
                <!-- cards de assinatura (mantém os seus) -->
              </v-row>

            </template>
          </v-container>
        </v-window-item>

        <!-- ================= HISTÓRICO ================= -->
        <v-window-item value="historico">
          <v-container class="pa-6 pa-md-10">

            <h3 class="text-h6 font-weight-bold mb-6">
              {{ $t('profile.subscription.recent_payments') }}
            </h3>

            <div v-if="loadingSub" class="text-center py-10">
              <v-progress-circular indeterminate color="primary" />
            </div>

            <v-table
              v-else-if="subscriptionData.historico?.length"
              class="billing-table"
            >
              <!-- tabela mantida -->
            </v-table>

            <div v-else class="text-center py-10 text-medium-emphasis">
              {{ $t('profile.subscription.no_history') }}
            </div>

          </v-container>
        </v-window-item>

      </v-window>
    </v-card>
  </v-container>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import Calendar from '../components/Calendar/Calendar.vue'
import ReminderModal from '../components/avisos/ReminderModal/ReminderModal.vue'
import { startOfDay, isAfter } from 'date-fns'
import CompartilharModal from '../components/avisos/CompartilharModal/CompartilharModal.vue'




const showReminderModal = ref(false)
const selectedDate = ref(null)


const { t } = useI18n()

const authStore = useAuthStore()
const router = useRouter()
const activeTab = ref('personal')
const user = ref({
    nome: '',
    email: '',
    admin: false,
    plano: null,
    avatar: null,
    cpf: '',
    data_nascimento: ''
})

const subscriptionData = ref({
    assinatura: null,
    historico: []
})

const loadingSub = ref(true)
const saving = ref(false)
const confirmCancel = ref(false)
const cancelling = ref(false)
const open =ref(false)

const confirmRemoveAvatarDialog = ref(false)

onMounted(async () => {
   await fetchUser()
   await fetchSubscription()
})

const loadingUser = ref(false)
const fetchUser = async () => {
    try {
        loadingUser.value = true
        const response = await authStore.apiFetch('/usuario')
        const data = await response.json()
        user.value = data
        
        
        if (user.value.cpf) {
          formatCPF({ target: { value: user.value.cpf } })
        }
        
        
        if (user.value.data_nascimento && typeof user.value.data_nascimento === 'string') {
          user.value.data_nascimento = user.value.data_nascimento.substring(0, 10)
        } else {
          user.value.data_nascimento = '' 
        }
    } catch (e) {
        console.error(e)
    }finally{
      loadingUser.value = false
    }
}
const fetchSubscription = async () => {
    try {
        loadingSub.value = true
        const response = await authStore.apiFetch('/assinaturas')
        if (response.ok) {
            subscriptionData.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loadingSub.value = false
    }
}

const ativarAutoRenovacao = async () => {
    try {
        const response = await authStore.apiFetch('/assinaturas/ligar-auto-renovacao', { method: 'POST' })
        if (response.ok) {
            const data = await response.json()
            subscriptionData.value.assinatura.renovacao_automatica = data.active
            toast.success(data.message)
        }
    } catch (e) {
        toast.error(t('profile.warnings.renewal_error'))
    }
}


const payAhead = () => {
    router.push({ name: 'Plans' })
}

const progressPercentage = computed(() => {
    if (!subscriptionData.value.assinatura) return 0
    const start = new Date(subscriptionData.value.assinatura.inicia_em).getTime()
    const end = new Date(subscriptionData.value.assinatura.termina_em).getTime()
    const now = new Date().getTime()
    const total = end - start
    const elapsed = now - start
    return Math.min(100, Math.max(0, (elapsed / total) * 100))
})

const daysRemaining = computed(() => {
    if (!subscriptionData.value.assinatura) return 0
    const end = new Date(subscriptionData.value.assinatura.termina_em).getTime()
    const now = new Date().getTime()
    const diff = end - now
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
})

const hasActiveOrValidSubscription = computed(() => {
    if (!subscriptionData.value.assinatura) return false
    const s = subscriptionData.value.assinatura
    
    if (s.status === 'active') return true
    
 
    const end = new Date(s.termina_em).getTime()
    const now = new Date().getTime()
    return end > now
})

const fileInput = ref(null)
const previewAvatar = ref(null)
const selectedFile = ref(null)

const triggerFileInput = () => {
    fileInput.value.click()
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            toast.warning(t('profile.warnings.image_size'))
            event.target.value = ''
            return
        }
        selectedFile.value = file
        previewAvatar.value = URL.createObjectURL(file)
    }
}

const removeAvatar = () => {
    confirmRemoveAvatarDialog.value = true
}


const saveProfile = async () => {
    saving.value = true
    try {
        const formData = new FormData()
        formData.append('nome', user.value.nome)
        formData.append('email', user.value.email)
        if (user.value.cpf && typeof user.value.cpf === 'string') {
            formData.append('cpf', user.value.cpf.replace(/\D/g, ''))
        }
        if (user.value.data_nascimento) {
            formData.append('data_nascimento', user.value.data_nascimento)
        }
        formData.append('_method', 'PUT') 
        
        if (selectedFile.value) {
            formData.append('avatar', selectedFile.value)
        }

        const response = await authStore.apiFetch('/usuario', {
            method: 'POST', 
            body: formData
        })
        
        if (response.ok) {
            toast.success(t('profile.toast_success'))
            const updated = await response.json()
            authStore.user = updated
            user.value = { ...updated }
            
           
            if (user.value.cpf) formatCPF({ target: { value: user.value.cpf } })
            if (user.value.data_nascimento && typeof user.value.data_nascimento === 'string') {
              user.value.data_nascimento = user.value.data_nascimento.substring(0, 10)
            } else {
              user.value.data_nascimento = ''
            }
            
            selectedFile.value = null 
        } else {
             const errorData = await response.json().catch(() => ({}))
             if (errorData.errors) {
               
                 const firstErrorKey = Object.keys(errorData.errors)[0]
                 toast.warning(errorData.errors[firstErrorKey][0])
             } else {
                 toast.error(errorData.message || t('profile.warnings.update_error'))
             }
        }
    } catch (e) {
        toast.error(t('profile.toast_connection_error'))
    } finally {
        saving.value = false
    }
}

const getInitials = (name) => {
    if (!name) return ''
    return name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const validateCPF = (cpf) => {
  cpf = cpf.replace(/\D/g, '')
  if (cpf === '') return false
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false
  let add = 0
  for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i)
  let rev = 11 - (add % 11)
  if (rev === 10 || rev === 11) rev = 0
  if (rev !== parseInt(cpf.charAt(9))) return false
  add = 0
  for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i)
  rev = 11 - (add % 11)
  if (rev === 10 || rev === 11) rev = 0
  return rev === parseInt(cpf.charAt(10))
}

const ageRules = [
  v => {
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
]

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
  user.value.cpf = value
}

function sendInvite(payload) {
  console.log('Teste Email enviado', payload.email)
}


function handleCalendarSelect({ date, isFuture }) {
  if (!isFuture) {
    toast.warning('Só é possível criar lembretes para datas futuras')
    return
  }

  selectedDate.value = date
  showReminderModal.value = true
}


</script>

<style scoped>
.avatar-wrapper {
  position: relative;
  display: inline-block;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 5px;
  right: 5px;
  border: 4px solid white !important;
  z-index: 2;
}

.avatar-delete-btn {
  position: absolute;
  bottom: 5px;
  left: 5px;
  border: 2px solid white !important;
  z-index: 2;
}

.profile-tabs :deep(.v-tab--selected) {
  font-weight: 800;
}

.profile-tabs :deep(.v-tab__slider) {
  height: 4px;
  border-radius: 4px 4px 0 0;
}


.profile-tabs :deep(.v-tab) {
  outline: none !important;
}

.profile-tabs :deep(.v-btn__overlay),
.profile-tabs :deep(.v-ripple__container) {
  display: none !important;
}

.plan-hero-card {
  background: linear-gradient(135deg, #1867c0 0%, #004ba0 100%);
  position: relative;
  overflow: hidden;
}

.plan-hero-card::after {
  content: "";
  position: absolute;
  top: -20%;
  right: -20%;
  width: 200px;
  height: 200px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
}

.border-left-lg {
  border-left: 6px solid #1867c0;
}

.billing-table :deep(th) {
  color: #1867c0 !important;
  font-size: 0.8rem;
  text-transform: uppercase;
}

.no-plan-empty {
  opacity: 0.7;
}

.avatar-main {
  transition: transform 0.3s ease;
}

.avatar-main:hover {
  transform: scale(1.02);
}
</style>

<template>
  <v-container>


    <div class="d-flex align-center mb-6">
        <v-icon icon="mdi-account-circle" color="primary" size="32" class="mr-3"></v-icon>
        <h1 class="text-h4 font-weight-bold">{{ $t('profile.title') }}</h1>
    </div>

    <v-card class="rounded-xl overflow-hidden" elevation="3">
      <v-tabs
        v-model="activeTab"
        bg-color="primary"
        color="white"
        grow
        class="profile-tabs"
      >
        <v-tab value="personal" class="text-none">
          <v-icon start icon="mdi-account-circle" class="mr-2"></v-icon>
          {{ $t('profile.tabs.personal') }}
        </v-tab>
        <v-tab value="assinatura" class="text-none">
          <v-icon start icon="mdi-star-circle" class="mr-2"></v-icon>
          {{ $t('profile.tabs.subscription') }}
        </v-tab>
        <v-tab value="historico" class="text-none">
          <v-icon start icon="mdi-receipt" class="mr-2"></v-icon>
          {{ $t('profile.tabs.history') }}
        </v-tab>
      </v-tabs>

      <v-window v-model="activeTab">
        <div v-if="loadingUser && activeTab === 'personal'" class="text-center py-10 d-flex flex-column align-center">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
          {{ $t('profile.loading') }}
        </div>
          <v-window-item v-if="!loadingUser" value="personal">
          <v-row class="pa-6 pa-md-10">
            <v-col cols="12" md="4" class="text-center">
              <div class="avatar-wrapper mb-6">
                <v-avatar size="160" color="primary-lighten-4" class="elevation-4 avatar-main">
                  <v-img v-if="previewAvatar" :src="previewAvatar" cover></v-img>
                  <v-img v-else-if="user.avatar_url" :src="user.avatar_url" cover></v-img>
                  <v-img v-else-if="user.avatar" :src="authStore.getStorageUrl(user.avatar)" cover></v-img>
                  <span v-else class="text-h2 font-weight-bold text-primary">{{ getInitials(user.nome) }}</span>
                </v-avatar>
                <v-btn
                  icon="mdi-camera"
                  color="primary"
                  size="small"
                  class="avatar-edit-btn"
                  elevation="4"
                  @click="triggerFileInput"
                ></v-btn>
                <v-btn
                  v-if="user.avatar"
                  icon="mdi-delete"
                  color="error"
                  size="x-small"
                  class="avatar-delete-btn"
                  elevation="4"
                  @click="removeAvatar"
                ></v-btn>
                <input type="file" ref="fileInput" class="d-none" accept="image/*" @change="handleFileChange">
              </div>
              <v-chip
                :color="user.admin ? 'deep-purple' : 'primary'"
                variant="flat"
                class="font-weight-bold"
              >
                {{ user.admin ? $t('profile.roles.admin') : $t('profile.roles.client') }}
              </v-chip>
            </v-col>

            <v-col cols="12" md="8">
              <v-form @submit.prevent="saveProfile">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="user.nome"
                      :label="$t('profile.name_label')"
                      variant="outlined"
                      rounded="lg"
                      prepend-inner-icon="mdi-account"
                      :disabled="saving"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="user.email"
                      :label="$t('profile.email_label')"
                      variant="outlined"
                      rounded="lg"
                      prepend-inner-icon="mdi-email"
                      :disabled="saving"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="user.cpf"
                      :label="$t('profile.labels.cpf')"
                      variant="outlined"
                      rounded="lg"
                      prepend-inner-icon="mdi-card-account-details"
                      :rules="cpfRules"
                      @input="formatCPF"
                      maxlength="14"
                      :disabled="saving"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <DateInput 
                      v-model="user.data_nascimento" 
                      :label="$t('profile.labels.birthdate')" 
                      icon="mdi-calendar"
                      :disabled="saving"
                      :rules="ageRules"
                    />
                  </v-col>
                </v-row>
                <div class="d-flex justify-end mt-4">
                  <v-btn
                    type="submit"
                    color="primary"
                    size="large"
                    rounded="lg"
                    class="px-8 font-weight-bold"
                    :loading="saving"
                    :disabled="saving || uiStore.loading"
                  >
                    {{ $t('profile.btn_update') }}
                  </v-btn>
                </div>
              </v-form>
            </v-col>
          </v-row>
        </v-window-item>

        <v-window-item value="assinatura">
          <v-container class="pa-6 pa-md-10">
            <div v-if="loadingSub && activeTab === 'assinatura'" class="text-center py-10">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>
            
            <div v-else-if="!hasActiveOrValidSubscription && subscriptionData?.assinatura?.status !== 'pending'" class="text-center py-10 no-plan-empty">
              <v-icon icon="mdi-alert-circle-outline" size="64" color="grey"></v-icon>
              <h3 class="text-h5 mt-4">{{ $t('profile.subscription.no_active') }}</h3>
              <p class="text-medium-emphasis mb-6">{{ $t('profile.subscription.no_active_desc') }}</p>
              <v-btn color="primary" :to="{ name: 'Plans' }" size="large" rounded="xl">{{ $t('profile.subscription.view_plans') }}</v-btn>
            </div>

            <v-row v-else>
                <v-col cols="12" md="12" v-if="subscriptionData?.assinatura?.status === 'pending'">
                   <v-alert type="warning" variant="tonal" class="mb-4 rounded-xl" icon="mdi-clock-outline">
                      O seu pagamento está sendo processado ou está pendente. Assim que for aprovado, o plano será liberado automaticamente.
                   </v-alert>
                </v-col>
                <v-col cols="12" md="5">
                  <v-card class="plan-hero-card rounded-xl pa-6 text-white" elevation="6">
                    <div class="text-overline mb-2 opacity-80">{{ $t('profile.subscription.current') }}</div>
                    <div class="text-h4 font-weight-black mb-4">
                        {{ user.plano?.nome }}
                        <span class="text-subtitle-1 font-weight-bold ml-2 opacity-80" v-if="subscriptionData?.assinatura?.periodo">
                            ({{ subscriptionData.assinatura.periodo.nome }})
                        </span>
                    </div>
                    
                    <div class="d-flex align-center mb-6" v-if="subscriptionData?.assinatura">
                      <v-badge
                        :color="subscriptionData.assinatura.status === 'active' ? 'success' : 'warning'"
                        :content="subscriptionData.assinatura.status === 'active' ? $t('profile.active') : $t('profile.inactive')"
                        inline
                      ></v-badge>
                    </div>

                    <div class="subscription-timeline mb-6" v-if="subscriptionData?.assinatura">
                      <div class="d-flex justify-space-between text-caption mb-1">
                        <span>{{ $t('profile.subscription.expires_at') }}: {{ formatDate(subscriptionData.assinatura.termina_em) }}</span>
                        <span>{{ daysRemaining === 1 ? $t('profile.subscription.days_remaining_singular') : $t('profile.subscription.days_remaining', { count: daysRemaining }) }}</span>
                      </div>
                      <v-progress-linear
                        :model-value="progressPercentage"
                        color="white"
                        height="8"
                        rounded
                      ></v-progress-linear>
                    </div>

                    <v-btn block color="white" variant="flat" class="text-primary font-weight-bold" :to="{ name: 'Plans' }" rounded="lg">
                      {{ $t('profile.subscription.change') }}
                    </v-btn>
                  </v-card>
                </v-col>

                <v-col cols="12" md="7">
                  <v-card class="rounded-xl pa-6 border" elevation="0">
                    <h3 class="text-h6 font-weight-bold mb-6">{{ $t('profile.subscription.manage') }}</h3>
                    
                    <div class="management-item d-flex align-center justify-space-between mb-8">
                      <div>
                        <div class="font-weight-bold">{{ $t('profile.subscription.auto_renewal') }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ $t('profile.subscription.auto_renewal_desc') }}</div>
                      </div>
                      <v-switch
                        :model-value="subscriptionData?.assinatura?.renovacao_automatica ? true : false"
                        color="primary"
                        hide-details
                        inset
                        @change="ativarAutoRenovacao"
                        :disabled="loadingSub || uiStore.loading"
                      ></v-switch>
                    </div>

                    <v-divider class="mb-8"></v-divider>

                    <v-row>
                      <v-col cols="12" sm="6">
                        <v-btn
                          block
                          variant="outlined"
                          color="primary"
                          class="rounded-lg font-weight-bold"
                          @click="payAhead"
                          :disabled="loadingSub || saving || uiStore.loading"
                        >
                          {{ $t('profile.subscription.pay_ahead') }}
                        </v-btn>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-btn
                          block
                          variant="text"
                          color="error"
                          class="rounded-lg font-weight-bold"
                          v-if="subscriptionData.assinatura && subscriptionData.assinatura.status === 'active'"
                          @click="confirmCancel = true"
                          :disabled="loadingSub || saving || uiStore.loading"
                        >
                          {{ $t('profile.subscription.cancel') }}
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
            </v-row>
          </v-container>
        </v-window-item>

        <v-window-item value="historico">
          <v-container class="pa-6 pa-md-10">
            <h3 class="text-h6 font-weight-bold mb-6">{{ $t('profile.subscription.recent_payments') }}</h3>
            <div v-if="loadingSub && activeTab === 'historico'" class="text-center py-10">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>

            <v-table v-else-if="subscriptionData?.historico && subscriptionData.historico.length > 0 && !loadingSub" class="billing-table">
              <thead>
                <tr>
                  <th class="text-left font-weight-bold">{{ $t('transactions.table.date') }}</th>
                  <th class="text-left font-weight-bold">{{ $t('admin.item') || 'Item' }}</th>
                  <th class="text-left font-weight-bold">{{ $t('transactions.table.amount') }}</th>
                  <th class="text-left font-weight-bold">{{ $t('admin.status') || 'Status' }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in subscriptionData.historico" :key="item.id">
                  <td class="text-body-2">{{ formatDate(item.pago_em || item.created_at) }}</td>
                  <td>
                    <div class="d-flex align-center">
                      <v-icon icon="mdi-package-variant" size="small" class="mr-2" color="primary"></v-icon>
                      <span class="text-body-2">
                        {{ item.assinatura?.plano?.nome || item.item_nome || '-' }}
                        <span v-if="item.assinatura?.periodo" class="text-caption opacity-70 ml-1">
                          ({{ item.assinatura.periodo.nome }})
                        </span>
                      </span>
                    </div>
                  </td>
                  <td class="font-weight-bold text-body-2">{{ formatPrice(item.valor_centavos / 100) }}</td>
                  <td>
                    <v-chip
                      :color="getStatusColor(item.status)"
                      size="x-small"
                      class="text-uppercase font-weight-bold"
                      variant="tonal"
                    >
                      {{ getStatusText(item.status) }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </v-table>
            
            <div v-else-if="!loadingSub" class="text-center py-12 billing-empty opacity-60">
              <v-icon icon="mdi-receipt-text-minus-outline" size="64" class="mb-4"></v-icon>
              <h3 class="text-h6 font-weight-bold">{{ $t('profile.subscription.no_history') }}</h3>
              <p class="text-body-2">{{ $t('profile.subscription.no_history_desc') }}</p>
            </div>
            
          </v-container>
        </v-window-item>
      </v-window>
    </v-card>
    <ModalCancelarAssinatura v-model="confirmCancel" @cancelled="fetchSubscription" />
    <ModalRemoverAvatar v-model="confirmRemoveAvatarDialog" :user="user" @removed="user.avatar = null" />
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import ModalCancelarAssinatura from '../components/Modals/Profile/ModalCancelarAssinatura.vue'
import ModalRemoverAvatar from '../components/Modals/Profile/ModalRemoverAvatar.vue'
import DateInput from '../components/Common/DateInput.vue'

const { t } = useI18n()

const authStore = useAuthStore()
const uiStore = useUiStore()
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
const confirmRemoveAvatarDialog = ref(false)

const cpfRules = [
  v => !!v || t('validation.required'),
  v => validateCPF(v) || t('validation.cpf_invalid')
]

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
        } else {
            console.error('Error fetching subscription:', response.status)
        }
    } catch (e) {
        console.error(e)
    } finally {
        loadingSub.value = false
    }
}

const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'paid':
        case 'pago':
        case 'approved':
        case 'active':
            return 'success'
        case 'pending':
        case 'pendente':
            return 'warning'
        case 'failed':
        case 'falhou':
        case 'cancelled':
        case 'cancelado':
            return 'error'
        default:
            return 'grey'
    }
}

const getStatusText = (status) => {
    if (!status) return '-'
    const s = status.toLowerCase()
    if (s === 'paid' || s === 'pago' || s === 'approved') return t('profile.subscription.paid') || 'PAGO'
    if (s === 'pending' || s === 'pendente') return 'PENDENTE'
    if (s === 'failed' || s === 'falhou') return t('profile.subscription.failed') || 'FALHOU'
    return status.toUpperCase()
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
    
    // Consideramos ativa se o status for active ou authorized (MP)
    if (s.status === 'active' || s.status === 'authorized') return true
    
    // Se estiver pendente, também queremos mostrar o card (embora com aviso)
    if (s.status === 'pending') return true

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
            
            previewAvatar.value = null 
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

// getStorageUrl removed as it is now in authStore

const formatDate = (dateString) => {
    if (!dateString) return ''
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    return new Date(dateString).toLocaleDateString(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value)
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
    if (!v || typeof v !== 'string' || !v.includes('-')) return true
    const [year, month, day] = v.split('-').map(Number)
    const birth = new Date(year, month - 1, day)
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
  border: 4px solid rgb(var(--v-theme-surface)) !important;
  z-index: 2;
}

.avatar-delete-btn {
  position: absolute;
  bottom: 5px;
  left: 5px;
  border: 2px solid rgb(var(--v-theme-surface)) !important;
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
  border-left: 6px solid rgb(var(--v-theme-primary));
}

.billing-table :deep(th) {
  color: rgb(var(--v-theme-primary)) !important;
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

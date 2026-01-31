<template>
  <v-container class="py-8">
    <div class="mb-8 pl-4 border-left-lg">
      <h1 class="text-h4 font-weight-black mb-1">Meu Perfil</h1>
      <p class="text-subtitle-1 text-medium-emphasis">Gerencie suas informações, plano e histórico de pagamentos.</p>
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
          Dados Pessoais
        </v-tab>
        <v-tab value="subscription" class="text-none">
          <v-icon start icon="mdi-star-circle" class="mr-2"></v-icon>
          Minha Assinatura
        </v-tab>
        <v-tab value="billing" class="text-none">
          <v-icon start icon="mdi-receipt" class="mr-2"></v-icon>
          Histórico Financeiro
        </v-tab>
      </v-tabs>

      <v-window v-model="activeTab">
        <v-window-item value="personal">
          <v-row class="pa-6 pa-md-10">
            <v-col cols="12" md="4" class="text-center">
              <div class="avatar-wrapper mb-6">
                <v-avatar size="160" color="primary-lighten-4" class="elevation-4 avatar-main">
                  <v-img v-if="user.avatar || previewAvatar" :src="previewAvatar || `http://localhost:8000/storage/${user.avatar}`" cover></v-img>
                  <span v-else class="text-h2 font-weight-bold text-primary">{{ getInitials(user.name) }}</span>
                </v-avatar>
                <v-btn
                  icon="mdi-camera"
                  color="primary"
                  size="small"
                  class="avatar-edit-btn"
                  elevation="4"
                  @click="triggerFileInput"
                ></v-btn>
                <input type="file" ref="fileInput" class="d-none" accept="image/*" @change="handleFileChange">
              </div>
              <v-chip
                :color="user.role === 'admin' ? 'deep-purple' : 'primary'"
                variant="flat"
                class="font-weight-bold m-10"
              >
                {{ user.role === 'admin' ? 'ADMINISTRADOR' : 'CLIENTE' }}
              </v-chip>
            </v-col>

            <v-col cols="12" md="8">
              <v-form @submit.prevent="saveProfile">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="user.name"
                      label="Nome Completo"
                      variant="outlined"
                      bg-color="grey-lighten-4"
                      rounded="lg"
                      prepend-inner-icon="mdi-account"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="user.email"
                      label="Endereço de E-mail"
                      variant="outlined"
                      bg-color="grey-lighten-4"
                      rounded="lg"
                      prepend-inner-icon="mdi-email"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="user.cpf"
                      label="CPF"
                      variant="outlined"
                      bg-color="grey-lighten-4"
                      rounded="lg"
                      prepend-inner-icon="mdi-card-account-details"
                      :rules="cpfRules"
                      @input="formatCPF"
                      maxlength="14"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="user.birth_date"
                      label="Data de Nascimento"
                      type="date"
                      variant="outlined"
                      bg-color="grey-lighten-4"
                      rounded="lg"
                      prepend-inner-icon="mdi-calendar"
                    ></v-text-field>
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
                  >
                    Salvar Alterações
                  </v-btn>
                </div>
              </v-form>
            </v-col>
          </v-row>
        </v-window-item>

        <v-window-item value="subscription" class="bg-grey-lighten-5">
          <v-container class="pa-6 pa-md-10">
            <div v-if="loadingSub" class="text-center py-10">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>
            
            <div v-else-if="!subscriptionData.subscription" class="text-center py-10 no-plan-empty">
              <v-icon icon="mdi-alert-circle-outline" size="64" color="grey"></v-icon>
              <h3 class="text-h5 mt-4">Nenhum plano ativo</h3>
              <p class="text-medium-emphasis mb-6">Você ainda não assinou nenhum de nossos planos financeiros.</p>
              <v-btn color="primary" :to="{ name: 'Plans' }" size="large" rounded="xl">Ver Planos Agora</v-btn>
            </div>

            <div v-else>
              <v-row>
                <v-col cols="12" md="5">
                  <v-card class="plan-hero-card rounded-xl pa-6 text-white" elevation="6">
                    <div class="text-overline mb-2 opacity-80">PLANO ATUAL</div>
                    <div class="text-h4 font-weight-black mb-4">{{ user.plan?.name }}</div>
                    
                    <div class="d-flex align-center mb-6">
                      <v-badge
                        :color="subscriptionData.subscription.status === 'active' ? 'success' : 'warning'"
                        :content="subscriptionData.subscription.status === 'active' ? 'Ativo' : 'Cancelado'"
                        inline
                      ></v-badge>
                    </div>

                    <div class="subscription-timeline mb-6">
                      <div class="d-flex justify-space-between text-caption mb-1">
                        <span>Vence em: {{ formatDate(subscriptionData.subscription.ends_at) }}</span>
                        <span>{{ daysRemaining }} dias restantes</span>
                      </div>
                      <v-progress-linear
                        :model-value="progressPercentage"
                        color="white"
                        height="8"
                        rounded
                      ></v-progress-linear>
                    </div>

                    <v-btn block color="white" variant="flat" class="text-primary font-weight-bold" :to="{ name: 'Plans' }" rounded="lg">
                      Mudar de Plano
                    </v-btn>
                  </v-card>
                </v-col>

                <v-col cols="12" md="7">
                  <v-card class="rounded-xl pa-6 border" elevation="0">
                    <h3 class="text-h6 font-weight-bold mb-6">Gerenciar Assinatura</h3>
                    
                    <div class="management-item d-flex align-center justify-space-between mb-8">
                      <div>
                        <div class="font-weight-bold">Renovação Automática</div>
                        <div class="text-body-2 text-medium-emphasis">Cobraremos o valor do seu plano automaticamente ao final do período.</div>
                      </div>
                      <v-switch
                        :model-value="!!subscriptionData.subscription.auto_renew"
                        color="primary"
                        hide-details
                        inset
                        @change="toggleAutoRenew"
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
                        >
                          Pagar Antecipado
                        </v-btn>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-btn
                          block
                          variant="text"
                          color="error"
                          class="rounded-lg font-weight-bold"
                          v-if="subscriptionData.subscription.status === 'active'"
                          @click="confirmCancel = true"
                        >
                          Cancelar Assinatura
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
              </v-row>
            </div>
          </v-container>
        </v-window-item>

        <v-window-item value="billing">
          <v-container class="pa-6 pa-md-10">
            <h3 class="text-h6 font-weight-bold mb-6">Últimos Pagamentos</h3>
            
            <v-table v-if="subscriptionData.history && subscriptionData.history.length > 0" class="billing-table">
              <thead>
                <tr>
                  <th class="text-left font-weight-bold">Data</th>
                  <th class="text-left font-weight-bold">Método</th>
                  <th class="text-left font-weight-bold">Status</th>
                  <th class="text-right font-weight-bold">Valor</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in subscriptionData.history" :key="item.id">
                  <td class="text-body-2">{{ formatDate(item.paid_at) }}</td>
                  <td class="text-body-2">
                    <v-icon :icon="item.payment_method === 'pix' ? 'mdi-cellphone-nfc' : 'mdi-credit-card'" size="small" class="mr-2"></v-icon>
                    {{ item.payment_method?.toUpperCase() }}
                  </td>
                  <td>
                    <v-chip size="x-small" :color="item.status === 'paid' ? 'success' : 'error'" class="font-weight-bold">
                      {{ item.status === 'paid' ? 'PAGO' : 'FALHOU' }}
                    </v-chip>
                  </td>
                  <td class="text-right font-weight-bold">R$ {{ (item.amount_cents / 100).toFixed(2) }}</td>
                </tr>
              </tbody>
            </v-table>
            
            <div v-else class="text-center py-10 text-medium-emphasis">
              Nenhum histórico de faturamento encontrado.
            </div>
          </v-container>
        </v-window-item>
      </v-window>
    </v-card>

 
    <v-dialog v-model="confirmCancel" max-width="400">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-bold">Confirmar Cancelamento?</v-card-title>
        <v-card-text>
          Você continuará tendo acesso aos benefícios até o final do período ativo em <strong>{{ formatDate(subscriptionData.subscription?.ends_at) }}</strong>. Nenhuma nova cobrança será feita.
        </v-card-text>
        <v-card-actions class="justify-end gap-2">
          <v-btn variant="text" @click="confirmCancel = false">Manter</v-btn>
          <v-btn color="error" variant="flat" class="rounded-lg" :loading="cancelling" @click="cancelSubscription">Confirmar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const activeTab = ref('personal')
const user = ref({
    name: '',
    email: '',
    role: '',
    plan: null,
    avatar: null,
    cpf: '',
    birth_date: ''
})

const subscriptionData = ref({
    subscription: null,
    history: []
})

const loadingSub = ref(true)
const saving = ref(false)
const confirmCancel = ref(false)
const cancelling = ref(false)

onMounted(async () => {
   await fetchUser()
   await fetchSubscription()
})

const fetchUser = async () => {
    try {
        const response = await authStore.apiFetch('/user')
        const data = await response.json()
        user.value = data
        
        // Format CPF for display if exists
        if (user.value.cpf) {
          formatCPF({ target: { value: user.value.cpf } })
        }
        
        // Ensure birth_date is YYYY-MM-DD for the date input
        if (user.value.birth_date && typeof user.value.birth_date === 'string') {
          user.value.birth_date = user.value.birth_date.substring(0, 10)
        } else {
          user.value.birth_date = '' // Ensure it's not null/undefined for the input
        }
    } catch (e) {
        console.error(e)
    }
}

const fetchSubscription = async () => {
    try {
        loadingSub.value = true
        const response = await authStore.apiFetch('/subscriptions')
        if (response.ok) {
            subscriptionData.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loadingSub.value = false
    }
}

const toggleAutoRenew = async () => {
    try {
        const response = await authStore.apiFetch('/subscriptions/toggle-auto-renew', { method: 'POST' })
        if (response.ok) {
            const data = await response.json()
            subscriptionData.value.subscription.auto_renew = data.auto_renew
            toast.success(data.message)
        }
    } catch (e) {
        toast.error('Erro ao atualizar renovação')
    }
}

const cancelSubscription = async () => {
    try {
        cancelling.value = true
        const response = await authStore.apiFetch('/subscriptions/cancel', { method: 'POST' })
        if (response.ok) {
            toast.success('Assinatura cancelada com sucesso.')
            await fetchSubscription()
            confirmCancel.value = false
        }
    } catch (e) {
        toast.error('Erro ao cancelar')
    } finally {
        cancelling.value = false
    }
}

const payAhead = () => {
    router.push({ name: 'Plans' })
}

const progressPercentage = computed(() => {
    if (!subscriptionData.value.subscription) return 0
    const start = new Date(subscriptionData.value.subscription.starts_at).getTime()
    const end = new Date(subscriptionData.value.subscription.ends_at).getTime()
    const now = new Date().getTime()
    const total = end - start
    const elapsed = now - start
    return Math.min(100, Math.max(0, (elapsed / total) * 100))
})

const daysRemaining = computed(() => {
    if (!subscriptionData.value.subscription) return 0
    const end = new Date(subscriptionData.value.subscription.ends_at).getTime()
    const now = new Date().getTime()
    const diff = end - now
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
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
        selectedFile.value = file
        previewAvatar.value = URL.createObjectURL(file)
    }
}

const saveProfile = async () => {
    saving.value = true
    try {
        const formData = new FormData()
        formData.append('name', user.value.name)
        formData.append('email', user.value.email)
        if (user.value.cpf && typeof user.value.cpf === 'string') {
            formData.append('cpf', user.value.cpf.replace(/\D/g, ''))
        }
        if (user.value.birth_date) {
            formData.append('birth_date', user.value.birth_date)
        }
        formData.append('_method', 'PUT') 
        
        if (selectedFile.value) {
            formData.append('avatar', selectedFile.value)
        }

        const response = await authStore.apiFetch('/user', {
            method: 'POST', 
            body: formData
        })
        
        if (response.ok) {
            toast.success('Perfil atualizado!')
            const updated = await response.json()
            authStore.user = updated
            user.value = { ...updated }
            
            // Re-format after update
            if (user.value.cpf) formatCPF({ target: { value: user.value.cpf } })
            if (user.value.birth_date && typeof user.value.birth_date === 'string') {
              user.value.birth_date = user.value.birth_date.substring(0, 10)
            } else {
              user.value.birth_date = ''
            }
            
            selectedFile.value = null 
        } else {
             const errorData = await response.json().catch(() => ({}))
             if (errorData.errors) {
                 // Show the first validation error message
                 const firstErrorKey = Object.keys(errorData.errors)[0]
                 toast.warning(errorData.errors[firstErrorKey][0])
             } else {
                 toast.error(errorData.message || 'Erro ao atualizar perfil')
             }
        }
    } catch (e) {
        toast.error('Erro de conexão')
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

const cpfRules = [
  v => !v || validateCPF(v) || 'CPF inválido'
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
  border: 4px solid white !important;
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

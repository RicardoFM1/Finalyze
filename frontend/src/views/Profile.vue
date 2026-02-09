<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="4">
        <v-card class="text-center pa-6">
          <div class="position-relative d-inline-block">
            <v-avatar color="primary" size="120" class="mb-4">
              <v-img
                v-if="user.avatar || previewAvatar"
                :src="previewAvatar || `http://localhost:8000/storage/${user.avatar}`"
                cover
              />
              <span v-else class="text-h3 text-white">
                {{ getInitials(user.name) }}
              </span>
            </v-avatar>

            <v-btn
              icon="mdi-camera"
              size="small"
              color="white"
              class="position-absolute"
              style="bottom: 10px; right: 0"
              @click="triggerFileInput"
            />
          </div>

          <input
            type="file"
            ref="fileInput"
            class="d-none"
            accept="image/*"
            @change="handleFileChange"
          />

          <h2 class="text-h5 font-weight-bold">
            {{ getInitials(user.name) }}
          </h2>

          <p class="text-subtitle-1 text-medium-emphasis">
            {{ user.email }}
          </p>

          <v-chip
            :color="user.role === 'admin' ? 'purple' : 'info'"
            class="mt-2"
          >
            {{ user.role === 'admin'
              ? 'Admin'
              : (user.plan ? user.plan.name : $t('profile.free')) }}
          </v-chip>
        </v-card>
      </v-col>

      <v-col cols="12" md="8">
        <v-card :title="$t('profile.title')" class="mb-4">
          <v-card-text>
            <v-form @submit.prevent="saveProfile">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="user.name"
                    :label="$t('profile.name_label')"
                    variant="outlined"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="user.email"
                    :label="$t('profile.email_label')"
                    variant="outlined"
                  />
                </v-col>
              </v-row>
              <v-btn type="submit" color="primary" :loading="saving">
                {{ $t('profile.btn_update') }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>

        <v-card :title="$t('profile.signature')">
          <v-card-text>
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-h6">
                  {{ $t('profile.current_plan', {
                    plan: user.plan ? user.plan.name : $t('profile.free')
                  }) }}
                </div>

                <div class="text-subtitle-2 text-medium-emphasis">
                  {{ $t('profile.status', {
                    status: user.subscription_status === 'active'
                      ? $t('profile.active')
                      : $t('profile.inactive')
                  }) }}
                </div>
              </div>

              <v-btn variant="outlined" color="primary" to="/planos">
                {{ $t('profile.btn_upgrade') }}
              </v-btn>
              <Calendar @select-date="openFromCalendar" />
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
            </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
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
    name: '',
    email: '',
    role: '',
    plan: null,
    avatar: null
})

const subscriptionData = ref({
    subscription: null,
    history: []
})

const loadingSub = ref(true)
const saving = ref(false)
const confirmCancel = ref(false)
const cancelling = ref(false)
const open =ref(false)

onMounted(async () => {
   await fetchUser()
   await fetchSubscription()
})

const fetchUser = async () => {
    try {
        const response = await authStore.apiFetch('/user')
        const data = await response.json()
        user.value = data
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
    router.push('/planos')
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
        formData.append('_method', 'PUT') 
        
        if (selectedFile.value) {
            formData.append('avatar', selectedFile.value)
        }

        const response = await authStore.apiFetch('/user', {
            method: 'POST', 
            body: formData
        })
        
        if (response.ok) {
            toast.success(t('profile.toast_success'))
            const updated = await response.json()
            authStore.user = updated
            selectedFile.value = null 
        } else {
             toast.error(t('profile.toast_error'))
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

const resendBillingEmail = async () => {
  try {
    const response = await authStore.apiFetch(
      '/emails/resend-billing',
      { method: 'POST' }
    )

    if (response.ok) {
      toast.success('Email enviado com sucesso.')
    }
  } catch (e) {
    toast.error('Erro ao reenviar cobrança.')
  }
}



function openFromCalendar(date) {
  const clickedDate = startOfDay(new Date(date))
  const today = startOfDay(new Date())

  if (isAfter(clickedDate, today)) {
    selectedDate.value = clickedDate
    showReminderModal.value = true
    console.log(showReminderModal.value)
  } else {
    toast.warning('Só é possível criar lembretes para datas futuras')
  }
}

function sendInvite(payload) {
  console.log('Teste Email enviado', payload.email)
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

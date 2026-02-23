<template>
  <ModalBase :title="$t('modals.titles.share')" v-model="internalValue" maxWidth="600px">
    <div class="pa-0">
      <div class="px-6 pt-6 pb-2">
        <p class="text-body-2 text-medium-emphasis mb-6">
          {{ $t('share.subtitle') }}
        </p>

        <!-- Invite Section -->
        <v-card variant="tonal" color="primary" class="pa-6 rounded-xl border-dashed mb-10" elevation="0">
          <v-form @submit.prevent="inviteUser">
            <div class="d-flex flex-column flex-sm-row gap-4 align-sm-center">
                <v-text-field
                    v-model="inviteEmail"
                    :label="$t('share.email_label')"
                    placeholder="exemplo@email.com"
                    variant="solo"
                    density="comfortable"
                    hide-details
                    rounded="lg"
                    prepend-inner-icon="mdi-email-outline"
                    :loading="loading"
                    flat
                ></v-text-field>
                <v-btn 
                    color="primary" 
                    height="50" 
                    class="px-8 rounded-lg font-weight-bold w-100 w-sm-auto" 
                    @click="inviteUser"
                    :loading="loading"
                    elevation="2"
                >
                    {{ $t('share.invite_btn') }}
                </v-btn>
            </div>
          </v-form>
        </v-card>
      </div>

      <v-divider></v-divider>

      <div class="px-6 py-8 bg-surface-variant-light">
        <!-- Collaborators List -->
        <h3 class="text-overline font-weight-black mb-4 d-flex align-center opacity-70">
          <v-icon icon="mdi-account-multiple-outline" size="18" class="mr-2"></v-icon>
          {{ $t('share.people_access') }}
        </h3>
        
        <v-list class="bg-transparent pa-0">
          <v-list-item class="bg-surface rounded-xl mb-4 border" elevation="1">
              <template v-slot:prepend>
                  <v-avatar color="primary" class="mr-3" size="44">
                      <v-img v-if="authStore.user?.avatar" :src="authStore.getStorageUrl(authStore.user.avatar)" cover></v-img>
                      <span v-else class="text-caption font-weight-bold">{{ getInitials(authStore.user?.nome) }}</span>
                  </v-avatar>
              </template>
              <v-list-item-title class="font-weight-bold">{{ authStore.user?.nome }} ({{ $t('share.you') }})</v-list-item-title>
              <v-list-item-subtitle class="text-caption">{{ $t('share.owner') }}</v-list-item-subtitle>
          </v-list-item>

          <v-list-item v-for="share in mySharedAccounts" :key="share.id" class="bg-surface rounded-xl mb-4 border shadow-sm" elevation="1">
              <template v-slot:prepend>
                  <v-avatar color="secondary" class="mr-3" size="44">
                      <v-img v-if="share.guest?.avatar" :src="authStore.getStorageUrl(share.guest.avatar)" cover></v-img>
                      <v-icon v-else icon="mdi-account-guest"></v-icon>
                  </v-avatar>
              </template>
              <v-list-item-title class="font-weight-bold text-truncate">{{ share.guest?.nome || share.email_convidado }}</v-list-item-title>
              <v-list-item-subtitle class="text-caption">
                <v-chip size="x-small" :color="share.status === 'pending' ? 'warning' : 'success'" variant="tonal" class="mr-2 mt-1">
                  {{ share.status === 'pending' ? $t('share.status_pending') : $t('share.status_accepted') }}
                </v-chip>
                {{ share.guest?.email || share.email_convidado }}
              </v-list-item-subtitle>
              <template v-slot:append>
                  <v-btn 
                    icon="mdi-delete-outline" 
                    variant="text" 
                    color="error" 
                    size="small"
                    @click="confirmRemoveShare(share)"
                  ></v-btn>
              </template>
          </v-list-item>
        </v-list>
      </div>

      <!-- Social Share (Bottom) -->
      <v-divider></v-divider>
      <div class="pa-8 bg-surface text-center">
        <p class="text-caption text-medium-emphasis mb-4 uppercase tracking-widest font-weight-bold">Convidar via redes sociais</p>
        <div class="d-flex justify-center gap-6">
          <v-btn icon="mdi-whatsapp" color="success" variant="tonal" size="large" @click="shareSocial('whatsapp')"></v-btn>
          <v-btn icon="mdi-facebook" color="info" variant="tonal" size="large" @click="shareSocial('facebook')"></v-btn>
          <v-btn icon="mdi-twitter" color="primary" variant="tonal" size="large" @click="shareSocial('twitter')"></v-btn>
          <v-btn icon="mdi-content-copy" color="secondary" variant="tonal" size="large" @click="copyLink"></v-btn>
        </div>
      </div>
    </div>
  </ModalBase>

  <!-- Confirmation Modal for Removing Access -->
  <ModalBase 
    v-model="showConfirmDelete" 
    :title="$t('share.confirm_remove_title') || 'Remover Acesso'" 
    maxWidth="400px"
  >
    <div class="text-center pa-4">
        <v-avatar color="error-lighten-4" size="70" class="mb-4">
            <v-icon icon="mdi-account-remove-outline" color="error" size="40"></v-icon>
        </v-avatar>
        <p class="text-body-1 font-weight-medium mb-2">
            {{ $t('share.confirm_remove', { email: selectedShareToDelete?.email_convidado }) }}
        </p>
        <p class="text-caption text-medium-emphasis">
            {{ $t('share.confirm_remove_desc') || 'Esta pessoa não terá mais acesso às suas informações financeiras.' }}
        </p>
    </div>

    <template #actions>
        <div class="d-flex w-100 px-4 pb-4 gap-4">
            <v-btn 
                variant="outlined" 
                class="flex-grow-1 text-none font-weight-bold" 
                rounded="lg"
                height="44"
                @click="showConfirmDelete = false"
            >
                {{ $t('common.cancel') }}
            </v-btn>
            <v-btn 
                color="error" 
                variant="elevated" 
                class="flex-grow-1 text-none font-weight-bold" 
                elevation="2"
                rounded="lg"
                height="44"
                :loading="deleting"
                @click="handleConfirmedDelete"
            >
                {{ $t('share.remove_btn') || 'Remover' }}
            </v-btn>
        </div>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../../stores/auth'
import ModalBase from './modalBase.vue'
import { toast } from 'vue3-toastify'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const authStore = useAuthStore()
const inviteEmail = ref('')
const loading = ref(false)
const deleting = ref(false)
const mySharedAccounts = ref([])

const showConfirmDelete = ref(false)
const selectedShareToDelete = ref(null)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

onMounted(() => {
    if (authStore.isAuthenticated) fetchMyShares()
})

watch(internalValue, (val) => {
    if (val) fetchMyShares()
})

const fetchMyShares = async () => {
    try {
        const response = await authStore.apiFetch('/colaboracoes')
        if (response.ok) {
            const data = await response.json()
            mySharedAccounts.value = data.my_shares
        }
    } catch (e) { console.error(e) }
}

const inviteUser = async () => {
  if (!inviteEmail.value) return
  loading.value = true
  try {
    const response = await authStore.apiFetch('/colaboracoes', {
      method: 'POST',
      body: JSON.stringify({ email: inviteEmail.value })
    })
    
    if (response.ok) {
      toast.success(t('share.toast_invite_sent'))
      inviteEmail.value = ''
      fetchMyShares()
    } else {
        const data = await response.json()
        toast.error(data.message || t('share.toast_invite_error'))
    }
  } catch (e) {
    console.error(e)
    toast.error(t('share.toast_invite_error'))
  } finally {
    loading.value = false
  }
}

const confirmRemoveShare = (share) => {
    selectedShareToDelete.value = share
    showConfirmDelete.value = true
}

const handleConfirmedDelete = async () => {
    if (!selectedShareToDelete.value) return
    
    deleting.value = true
    try {
        await removeShare(selectedShareToDelete.value.id)
        showConfirmDelete.value = false
    } finally {
        deleting.value = false
        selectedShareToDelete.value = null
    }
}

const removeShare = async (id) => {
    const oldShares = [...mySharedAccounts.value]
    mySharedAccounts.value = mySharedAccounts.value.filter(s => s.id !== id)
    
    try {
        const response = await authStore.apiFetch(`/colaboracoes/${id}`, { method: 'DELETE' })
        if (response.ok) {
            toast.success(t('share.toast_remove_success'))
        } else {
            const data = await response.json().catch(() => ({}))
            throw new Error(data.message || 'Falha ao remover')
        }
    } catch (e) { 
        console.error(e)
        mySharedAccounts.value = oldShares
        toast.error(e.message || t('share.toast_remove_error'))
    } finally {
        fetchMyShares()
    }
}

const getInitials = (name) => {
    if (!name) return 'U'
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const shareSocial = (platform) => {
  const url = window.location.origin
  const text = 'Confira o Finalyze Finance!'
  let link = ''

  if (platform === 'whatsapp') link = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
  else if (platform === 'facebook') link = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
  else if (platform === 'twitter') link = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`
  
  window.open(link, '_blank')
}

const copyLink = () => {
  navigator.clipboard.writeText(window.location.origin)
  toast.success(t('share.toast_copy_success'))
}
</script>

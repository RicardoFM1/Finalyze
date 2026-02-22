<template>
  <ModalBase :title="$t('modals.titles.share')" v-model="internalValue" maxWidth="600px">
    <div class="pa-4">
      <p class="text-body-2 text-medium-emphasis mb-6">
        {{ $t('share.subtitle') }}
      </p>

      <!-- Invite Section -->
      <v-form @submit.prevent="inviteUser" class="mb-8">
        <div class="d-flex flex-column flex-sm-row gap-2 align-sm-center">
            <v-text-field
                v-model="inviteEmail"
                :label="$t('share.email_label')"
                placeholder="exemplo@email.com"
                variant="outlined"
                density="comfortable"
                hide-details
                rounded="lg"
                prepend-inner-icon="mdi-email-outline"
                :loading="loading"
            ></v-text-field>
            <v-btn 
                color="primary" 
                height="48" 
                class="px-6 rounded-lg font-weight-bold w-100 w-sm-auto" 
                @click="inviteUser"
                :loading="loading"
                elevation="2"
            >
                {{ $t('share.invite_btn') }}
            </v-btn>
        </div>
      </v-form>

      <v-divider class="mb-6"></v-divider>

      <!-- Collaborators List -->
      <h3 class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center">
        <v-icon icon="mdi-account-multiple-outline" class="mr-2"></v-icon>
        {{ $t('share.people_access') }}
      </h3>
      
      <v-list class="bg-transparent pa-0">
        <v-list-item class="bg-surface rounded-xl mb-3 border" elevation="0">
            <template v-slot:prepend>
                <v-avatar color="primary" class="mr-3">
                    <v-img v-if="authStore.user?.avatar" :src="authStore.getStorageUrl(authStore.user.avatar)" cover></v-img>
                    <span v-else class="text-caption font-weight-bold">{{ getInitials(authStore.user?.nome) }}</span>
                </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold">{{ authStore.user?.nome }} ({{ $t('share.you') }})</v-list-item-title>
            <v-list-item-subtitle>{{ $t('share.owner') }}</v-list-item-subtitle>
        </v-list-item>

        <v-list-item v-for="share in mySharedAccounts" :key="share.id" class="bg-surface rounded-xl mb-3 border shadow-sm" elevation="0">
            <template v-slot:prepend>
                <v-avatar color="secondary" class="mr-3">
                    <v-img v-if="share.guest?.avatar" :src="authStore.getStorageUrl(share.guest.avatar)" cover></v-img>
                    <v-icon v-else icon="mdi-account-guest"></v-icon>
                </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold text-truncate">{{ share.guest?.nome || share.email_convidado }}</v-list-item-title>
            <v-list-item-subtitle>{{ share.status === 'pending' ? $t('share.status_pending') : $t('share.status_accepted') }}</v-list-item-subtitle>
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

      <!-- Social Share (Bottom) -->
      <v-divider class="my-6"></v-divider>
      <div class="d-flex justify-center gap-4">
        <v-btn icon="mdi-whatsapp" color="success" variant="tonal" @click="shareSocial('whatsapp')"></v-btn>
        <v-btn icon="mdi-facebook" color="info" variant="tonal" @click="shareSocial('facebook')"></v-btn>
        <v-btn icon="mdi-twitter" color="primary" variant="tonal" @click="shareSocial('twitter')"></v-btn>
        <v-btn icon="mdi-content-copy" color="secondary" variant="tonal" @click="copyLink"></v-btn>
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
        <v-spacer></v-spacer>
        <v-btn 
            variant="text" 
            class="px-4 text-none" 
            @click="showConfirmDelete = false"
        >
            {{ $t('common.cancel') }}
        </v-btn>
        <v-btn 
            color="error" 
            variant="elevated" 
            class="px-6 text-none font-weight-bold ml-2" 
            elevation="1"
            rounded="lg"
            :loading="deleting"
            @click="handleConfirmedDelete"
        >
            {{ $t('share.remove_btn') || 'Remover' }}
        </v-btn>
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

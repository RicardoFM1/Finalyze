<template>
  <ModalBase title="Colaboração e Compartilhamento" v-model="internalValue" maxWidth="600px">
    <div class="pa-4">
      <p class="text-body-2 text-medium-emphasis mb-6">
        Convide outras pessoas para visualizar e editar seus dados financeiros, metas e agenda. Elas terão acesso total à sua conta.
      </p>

      <!-- Invite Section -->
      <v-form @submit.prevent="inviteUser" class="mb-8">
        <div class="d-flex flex-column flex-sm-row gap-2 align-sm-center">
            <v-text-field
                v-model="inviteEmail"
                label="E-mail do colaborador"
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
                Convidar
            </v-btn>
        </div>
      </v-form>

      <v-divider class="mb-6"></v-divider>

      <!-- Collaborators List -->
      <h3 class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center">
        <v-icon icon="mdi-account-multiple-outline" class="mr-2"></v-icon>
        Pessoas com acesso
      </h3>
      
      <v-list class="bg-transparent pa-0">
        <v-list-item class="bg-surface rounded-xl mb-3 border" elevation="0">
            <template v-slot:prepend>
                <v-avatar color="primary" class="mr-3">
                    <span class="text-caption font-weight-bold">{{ getInitials(authStore.user?.nome) }}</span>
                </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold">{{ authStore.user?.nome }} (Você)</v-list-item-title>
            <v-list-item-subtitle>Proprietário</v-list-item-subtitle>
        </v-list-item>

        <v-list-item v-for="share in mySharedAccounts" :key="share.id" class="bg-surface rounded-xl mb-3 border shadow-sm" elevation="0">
            <template v-slot:prepend>
                <v-avatar color="secondary" class="mr-3">
                    <v-icon icon="mdi-account-guest"></v-icon>
                </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold text-truncate">{{ share.guest_email }}</v-list-item-title>
            <v-list-item-subtitle>{{ share.status === 'pending' ? 'Pendente' : 'Convidado' }}</v-list-item-subtitle>
            <template v-slot:append>
                <v-btn 
                  icon="mdi-delete-outline" 
                  variant="text" 
                  color="error" 
                  size="small"
                  @click="removeShare(share.id)"
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
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../../stores/auth'
import ModalBase from './modalBase.vue'
import { toast } from 'vue3-toastify'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const authStore = useAuthStore()
const inviteEmail = ref('')
const loading = ref(false)
const mySharedAccounts = ref([])

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
        const response = await authStore.apiFetch('/shared-accounts')
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
    const response = await authStore.apiFetch('/shared-accounts', {
      method: 'POST',
      body: JSON.stringify({ email: inviteEmail.value })
    })
    
    if (response.ok) {
      toast.success('Convite de colaboração enviado!')
      inviteEmail.value = ''
      fetchMyShares()
    } else {
        const data = await response.json()
        toast.error(data.message || 'Erro ao enviar convite')
    }
  } catch (e) {
    console.error(e)
    toast.error('Ocorreu um erro ao processar o convite')
  } finally {
    loading.value = false
  }
}

const removeShare = async (id) => {
    const oldShares = [...mySharedAccounts.value]
    mySharedAccounts.value = mySharedAccounts.value.filter(s => s.id !== id)
    
    try {
        const response = await authStore.apiFetch(`/shared-accounts/${id}`, { method: 'DELETE' })
        if (response.ok) {
            toast.success('Acesso removido com sucesso')
        } else {
            throw new Error('Falha ao remover')
        }
    } catch (e) { 
        console.error(e)
        mySharedAccounts.value = oldShares
        toast.error('Erro ao remover acesso')
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
  toast.success('Link copiado para a área de transferência!')
}
</script>

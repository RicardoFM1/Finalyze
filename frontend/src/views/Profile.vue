<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="4">
        <v-card class="text-center pa-6">
          <div class="position-relative d-inline-block">
             <v-avatar color="primary" size="120" class="mb-4">
                <v-img v-if="user.avatar || previewAvatar" :src="previewAvatar || `http://localhost:8000/storage/${user.avatar}`" cover></v-img>
                <span v-else class="text-h3 text-white">{{ getInitials(user.name) }}</span>
             </v-avatar>
             <v-btn icon="mdi-camera" size="small" color="white" class="position-absolute" style="bottom: 10px; right: 0" @click="triggerFileInput"></v-btn>
          </div>
          <input type="file" ref="fileInput" class="d-none" accept="image/*" @change="handleFileChange">
          
          <h2 class="text-h5 font-weight-bold">{{ user.name }}</h2>
          <p class="text-subtitle-1 text-medium-emphasis">{{ user.email }}</p>
          <v-chip :color="user.role === 'admin' ? 'purple' : 'info'" class="mt-2">{{ user.role === 'admin' ? 'Administrador' : (user.plan ? user.plan.name : 'Sem Plano') }}</v-chip>
        </v-card>
      </v-col>
      <v-col cols="12" md="8">
        <v-card title="Detalhes do Perfil" class="mb-4">
            <v-card-text>
                <v-form @submit.prevent="saveProfile">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="user.name" label="Nome Completo" variant="outlined"></v-text-field>
                        </v-col>
                         <v-col cols="12" md="6">
                            <v-text-field v-model="user.email" label="E-mail" variant="outlined"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-btn type="submit" color="primary" :loading="saving">Salvar Alterações</v-btn>
                </v-form>
            </v-card-text>
        </v-card>

        <v-card title="Assinatura">
            <v-card-text>
                <div class="d-flex align-center justify-space-between">
                    <div>
                        <div class="text-h6">Plano Atual: {{ user.plan ? user.plan.name : 'Gratuito' }}</div>
                        <div class="text-subtitle-2 text-medium-emphasis">Status: {{ user.subscription_status === 'active' ? 'Ativo' : 'Inativo' }}</div>
                    </div>
                    <v-btn variant="outlined" color="primary" to="/planos">Upgrade / Mudar Plano</v-btn>
                </div>
            </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const user = ref({
    name: '',
    email: '',
    role: '',
    plan: null
})

onMounted(async () => {
   fetchUser()
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

const fileInput = ref(null)
const previewAvatar = ref(null)
const selectedFile = ref(null)
const saving = ref(false)

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
        formData.append('_method', 'PUT') // Method spoofing for FormData
        
        if (selectedFile.value) {
            formData.append('avatar', selectedFile.value)
        }

        const response = await authStore.apiFetch('/user', {
            method: 'POST', // Use POST with _method=PUT for FormData
            body: formData
        })
        
        if (response.ok) {
            toast.success('Perfil atualizado!')
            // Update store
            const updated = await response.json()
            authStore.user = updated
            localStorage.setItem('user', JSON.stringify(updated))
            selectedFile.value = null // Clear selection
        } else {
             toast.error('Erro ao atualizar')
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
</script>

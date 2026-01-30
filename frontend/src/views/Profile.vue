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
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
            toast.success(t('profile.toast_success'))
            // Update store
            const updated = await response.json()
            authStore.user = updated
           
            selectedFile.value = null // Clear selection
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
</script>

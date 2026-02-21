<template>
  <v-dialog v-model="internalValue" max-width="440px" persistent>
    <v-card class="rounded-xl pa-2" elevation="8">
      <v-card-title class="d-flex align-center gap-3 pt-4 pb-2 px-5">
        <v-avatar color="secondary" size="38" class="mr-2">
          <v-icon icon="mdi-share-variant-outline" color="white" size="20"></v-icon>
        </v-avatar>
        <div class="text-subtitle-1 font-weight-bold">{{ $t('metas.share.title') }}</div>
      </v-card-title>

      <v-divider opacity="0.1" class="mx-4"></v-divider>

      <v-card-text class="px-5 pt-4 pb-2">
        <p class="text-body-2 text-medium-emphasis mb-4">{{ $t('metas.share.description') }}</p>

        <v-text-field
          v-model="form.email"
          :label="$t('metas.share.email_label')"
          type="email"
          variant="outlined"
          rounded="lg"
          density="comfortable"
          prepend-inner-icon="mdi-email-outline"
          :error="!!error"
          :error-messages="error"
          :disabled="loading"
          @input="error = null"
          hide-details="auto"
        ></v-text-field>
      </v-card-text>

      <v-card-actions class="px-5 pb-4 gap-2">
        <v-spacer></v-spacer>
        <v-btn variant="text" rounded="lg" @click="close" :disabled="loading">
          {{ $t('common.cancel') }}
        </v-btn>
        <v-btn
          color="secondary"
          variant="flat"
          rounded="lg"
          prepend-icon="mdi-send-outline"
          :loading="loading"
          :disabled="!isValidEmail || loading"
          @click="submitInvite"
        >
          {{ $t('metas.share.send') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const authStore = useAuthStore()

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'invite'])

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const form = reactive({ email: '' })
const loading = ref(false)
const error = ref(null)

const isValidEmail = computed(() =>
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)
)

async function submitInvite() {
  if (!isValidEmail.value) return

  loading.value = true
  error.value = null

  try {
    const response = await authStore.apiFetch('/convites/enviar', {
      method: 'POST',
      body: JSON.stringify({ email_destino: form.email })
    })

    const data = await response.json().catch(() => ({}))
    if (!response.ok) {
      const validationMessage = data?.errors?.email_destino?.[0]
      throw new Error(validationMessage || data.message || t('metas.share.error_send'))
    }

    toast.success(t('metas.share.sent_success'))
    emit('invite', { ...data, email_destino: data?.email_destino || form.email })
    form.email = ''
    internalValue.value = false
  } catch (err) {
    error.value = err.message || t('metas.share.error_send')
  } finally {
    loading.value = false
  }
}

function close() {
  error.value = null
  form.email = ''
  internalValue.value = false
}
</script>

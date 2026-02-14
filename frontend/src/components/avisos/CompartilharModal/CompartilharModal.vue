<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card style="max-height: 90vh; display: flex; flex-direction: column;">
      <v-card-title>
        Compartilhar
      </v-card-title>

      <v-card-text>
        <v-text-field
          v-model="form.email"
          label="Email"
          type="email"
          :error="!!error"
          :error-messages="error"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="close">
          Cancelar
        </v-btn>
        <v-btn
          color="primary"
          :loading="loading"
          :disabled="!isValidEmail || loading"
          @click="submitInvite"
        >
          Enviar convite
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { useAuthStore } from '../../../stores/auth'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'invite'])
const authStore = useAuthStore()

const dialog = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

const form = reactive({
  email: ''
})

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
      throw new Error(data.message || 'Nao foi possivel enviar o convite.')
    }

    emit('invite', data)
    form.email = ''
    dialog.value = false
  } catch (err) {
    error.value = err.message || 'Erro ao enviar convite.'
  } finally {
    loading.value = false
  }
}

function close() {
  error.value = null
  dialog.value = false
}
</script>

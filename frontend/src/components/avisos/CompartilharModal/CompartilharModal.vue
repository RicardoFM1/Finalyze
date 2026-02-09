<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card style="max-height: 90vh; display: flex; flex-direction: column;">

      <v-card-title>
        Compartilhar
      </v-card-title>

      <v-card-text>
        <v-text-field
          label="Email"
          v-model="form.email"
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

/* props */
const props = defineProps({
  modelValue: Boolean
})

/* emits */
const emit = defineEmits(['update:modelValue', 'invite'])

/* dialog v-model */
const dialog = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v)
})

/* form */
const form = reactive({
  email: ''
})

/* states */
const loading = ref(false)
const error = ref(null)
const success = ref(false)

/* validation */
const isValidEmail = computed(() =>
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)
)

/* fake service */
function inviteUser(email) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (email.includes('erro')) {
        reject({ message: 'Usuário não encontrado' })
      } else {
        resolve({ status: 'ok' })
      }
    }, 1200)
  })
}

/* submit */
async function submitInvite() {
  if (!isValidEmail.value) return

  loading.value = true
  error.value = null

  try {
    await inviteUser(form.email)

    emit('invite', { email: form.email })
    success.value = true

    form.email = ''
    dialog.value = false
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

/* close */
function close() {
  dialog.value = false
}
</script>

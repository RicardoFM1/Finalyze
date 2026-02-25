<template>
  <ModalBase :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" :title="step === 1 ? $t('auth.forgot_password.title') : $t('auth.forgot_password.reset_title')" maxWidth="450px">
    <div class="pa-6">
      <!-- Step 1: Request Code -->
      <template v-if="step === 1">
        <p class="text-body-2 text-medium-emphasis mb-6">
          {{ $t('auth.forgot_password.desc') || 'Insira seu e-mail para receber um código de recuperação de senha.' }}
        </p>
        <v-form @submit.prevent="handleRequestCode" v-model="formValid">
          <v-text-field
            v-model="email"
            :label="$t('login.email_label')"
            placeholder="exemplo@email.com"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-email-outline"
            :rules="[v => !!v || $t('validation.required'), v => /.+@.+\..+/.test(v) || $t('validation.email')]"
            :disabled="loading"
          ></v-text-field>
          <v-btn
            block
            color="primary"
            size="large"
            class="rounded-lg mt-4 font-weight-bold"
            type="submit"
            :loading="loading"
            :disabled="!formValid"
          >
            {{ $t('auth.forgot_password.btn_send') || 'Enviar Código' }}
          </v-btn>
        </v-form>
      </template>

      <!-- Step 2: Reset Password -->
      <template v-else>
        <p class="text-body-2 text-medium-emphasis mb-6">
          {{ $t('auth.forgot_password.reset_desc') || 'Insira o código enviado para seu e-mail e sua nova senha.' }}
        </p>
        <v-form @submit.prevent="handleResetPassword" v-model="resetFormValid">
          <v-text-field
            v-model="code"
            :label="$t('auth.verification_code_label') || 'Código de 6 dígitos'"
            placeholder="000000"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-numeric"
            maxlength="6"
            :rules="[v => !!v || $t('validation.required'), v => v.length === 6 || $t('auth.invalid_code')]"
            :disabled="loading"
          ></v-text-field>

          <v-text-field
            v-model="password"
            :label="$t('login.password_label')"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-lock-outline"
            :type="showPass ? 'text' : 'password'"
            :append-inner-icon="showPass ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showPass = !showPass"
            :rules="[v => !!v || $t('validation.required'), v => v.length >= 6 || $t('register.rules.password_min')]"
            :disabled="loading"
            class="mt-4"
          ></v-text-field>

          <v-text-field
            v-model="passwordConfirmation"
            :label="$t('register.password_confirm_label')"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-lock-check-outline"
            :type="showConfirmPass ? 'text' : 'password'"
            :append-inner-icon="showConfirmPass ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showConfirmPass = !showConfirmPass"
            :rules="[v => !!v || $t('validation.required'), v => v === password || $t('validation.match_password')]"
            :disabled="loading"
            class="mt-4"
          ></v-text-field>

          <v-btn
            block
            color="primary"
            size="large"
            class="rounded-lg mt-6 font-weight-bold"
            type="submit"
            :loading="loading"
            :disabled="!resetFormValid"
          >
            {{ $t('auth.forgot_password.btn_reset') || 'Alterar Senha' }}
          </v-btn>

          <v-btn
            block
            variant="text"
            color="medium-emphasis"
            class="mt-2 text-none"
            @click="step = 1"
            :disabled="loading"
          >
            {{ $t('common.back') || 'Voltar' }}
          </v-btn>
        </v-form>
      </template>
    </div>
  </ModalBase>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../Modals/modalBase.vue'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const authStore = useAuthStore()
const step = ref(1)
const loading = ref(false)
const formValid = ref(false)
const resetFormValid = ref(false)

const email = ref('')
const code = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPass = ref(false)
const showConfirmPass = ref(false)

const handleRequestCode = async () => {
  loading.value = true
  try {
    await authStore.forgotPassword(email.value)
    toast.success('Se o e-mail estiver cadastrado, o código foi enviado!')
    step.value = 2
  } catch (e) {
    toast.error(e.message)
  } finally {
    loading.value = false
  }
}

const handleResetPassword = async () => {
  loading.value = true
  try {
    await authStore.resetPassword({
      email: email.value,
      codigo: code.value,
      senha: password.value,
      senha_confirmation: passwordConfirmation.value
    })
    toast.success('Senha alterada com sucesso! Agora você pode fazer login.')
    emit('update:modelValue', false)
    // Limpar campos
    step.value = 1
    code.value = ''
    password.value = ''
    passwordConfirmation.value = ''
  } catch (e) {
    toast.error(e.message)
  } finally {
    loading.value = false
  }
}
</script>

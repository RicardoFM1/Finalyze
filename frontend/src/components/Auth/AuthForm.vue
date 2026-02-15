<template>
  <v-form @submit.prevent="$emit('submit')" v-model="formValid" class="mt-4">
    <v-row dense>
      <v-col v-if="mode === 'register'" cols="12">
        <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('register.name_label') }}</label>
        <v-text-field
          v-model="modelValue.nome"
          placeholder="Seu nome"
          prepend-inner-icon="mdi-account-outline"
          variant="outlined"
          color="primary"
          density="comfortable"
          class="rounded-lg"
          :disabled="loading"
          :rules="[v => !!v || $t('validation.required')]"
          :error-messages="errors.nome"
          hide-details="auto"
        ></v-text-field>
      </v-col>

      <v-col cols="12" :class="mode === 'register' ? 'mt-4' : 'mb-6'">
        <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('login.email_label') }}</label>
        <v-text-field
          v-model="modelValue.email"
          placeholder="exemplo@email.com"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
          color="primary"
          type="email"
          density="comfortable"
          class="rounded-lg"
          :disabled="loading"
          :rules="[
            v => !!v || $t('login.rules.email_required'),
            v => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v) || $t('validation.email')
          ]"
          :error-messages="errors.email"
          hide-details="auto"
        ></v-text-field>
      </v-col>

      <v-col cols="12" :md="mode === 'register' ? 6 : 12" :class="mode === 'register' ? 'mt-4' : 'mb-4'">
        <div class="d-flex justify-space-between align-center mb-2">
          <label class="text-caption font-weight-bold text-medium-emphasis ms-1">{{ $t('login.password_label') }}</label>
    
        </div>
        <v-text-field
          v-model="modelValue.senha"
          placeholder="********"
          prepend-inner-icon="mdi-lock-outline"
          variant="outlined"
          color="primary"
          density="comfortable"
          class="rounded-lg"
          :type="showPass ? 'text' : 'password'"
          :append-inner-icon="showPass ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
          @click:append-inner="showPass = !showPass"
          :disabled="loading"
          :rules="mode === 'register' ? passwordRules : [v => !!v || $t('login.rules.password_required')]"
          :error-messages="errors.senha"
          hide-details="auto"
          @paste.prevent
        ></v-text-field>
      </v-col>

      <template v-if="mode === 'register'">
        <v-col cols="12" md="6" class="mt-4">
          <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('register.password_confirm_label') }}</label>
          <v-text-field
            v-model="modelValue.senha_confirmation"
            placeholder="********"
            prepend-inner-icon="mdi-lock-check-outline"
            variant="outlined"
            color="primary"
            density="comfortable"
            class="rounded-lg"
            :disabled="loading"
            :type="showConfirmPass ? 'text' : 'password'"
            :append-inner-icon="showConfirmPass ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
            @click:append-inner="showConfirmPass = !showConfirmPass"
            :rules="[v => !!v || $t('register.rules.confirm_required'), v => v === modelValue.senha || $t('validation.match_password')]"
            :error-messages="errors.senha_confirmation"
            hide-details="auto"
            @paste.prevent
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6" class="mt-4">
          <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('profile.labels.cpf') }}</label>
          <v-text-field
            v-model="modelValue.cpf"
            placeholder="000.000.000-00"
            prepend-inner-icon="mdi-card-account-details-outline"
            variant="outlined"
            color="primary"
            density="comfortable"
            class="rounded-lg"
            @input="$emit('update:cpf', $event)"
            maxlength="14"
            :disabled="loading"
            :rules="[v => !!v || $t('validation.required'), validateCPF]"
            :error-messages="errors.cpf"
            hide-details="auto"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6" class="mt-4">
          <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('profile.labels.birthdate') }}</label>
          <DateInput
            v-model="modelValue.data_nascimento"
            icon="mdi-calendar-outline"
            :disabled="loading"
            class="rounded-lg"
            :rules="[v => !!v || $t('validation.required'), validateAge]"
          />
          <div v-if="errors.data_nascimento" class="v-messages text-error text-caption mt-1">{{ errors.data_nascimento }}</div>
        </v-col>
      </template>
    </v-row>

    <v-alert v-if="error" type="error" variant="tonal" class="mt-6 rounded-lg" density="compact" closable>{{ error }}</v-alert>

    <v-btn
      color="primary"
      block
      size="x-large"
      class="mt-8 rounded-xl font-weight-bold py-4 text-none elevation-8"
      type="submit"
      :loading="loading"
      :disabled="buttonDisabled"
      :append-icon="mode === 'login' ? 'mdi-chevron-right' : 'mdi-account-check'"
    >
      {{ mode === 'login' ? $t('login.btn_login') : $t('register.btn_submit') }}
    </v-btn>

    <v-btn
      v-if="!hideNav"
      variant="tonal"
      block
      :disabled="loading"
      size="large"
      class="mt-4 rounded-xl font-weight-medium text-none"
      :to="{ name: mode === 'login' ? 'Register' : 'Login' }"
    >
      {{ mode === 'login' ? $t('login.register_link') : $t('register.login_link') }}
    </v-btn>
  </v-form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import DateInput from '../Common/DateInput.vue'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  mode: {
    type: String,
    default: 'login'
  },
  loading: Boolean,
  error: String,
  errors: {
    type: Object,
    default: () => ({})
  },
  validateAge: Function,
  validateCPF: Function,
  passwordRules: Array,
  hideNav: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'update:modelValue', 'update:cpf', 'update:valid'])

const formValid = ref(false)
const showPass = ref(false)
const showConfirmPass = ref(false)

const buttonDisabled = computed(() => {
  if (props.loading) return true
  if (!formValid.value) return true
  
  if (props.mode === 'login') {
    return !props.modelValue.email || !props.modelValue.senha
  } else {
    return !props.modelValue.nome || !props.modelValue.email || !props.modelValue.senha || 
           !props.modelValue.senha_confirmation || !props.modelValue.cpf || !props.modelValue.data_nascimento ||
           props.modelValue.senha !== props.modelValue.senha_confirmation
  }
})
</script>

<style scoped>
.form-group :deep(.v-field__outline) {
  --v-field-border-opacity: 0.2;
  transition: all 0.2s;
}

.form-group :deep(.v-field--focused .v-field__outline) {
  --v-field-border-opacity: 1;
}
</style>

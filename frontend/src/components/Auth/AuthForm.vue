<template>
  <v-form @submit.prevent="$emit('submit')" v-model="formValid" class="mt-4">
    <v-row dense>
      <v-col v-if="mode === 'register'" cols="12">
        <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('register.name_label') }}</label>
        <v-text-field
          v-model="modelValue.nome"
          :placeholder="$t('auth.placeholders.name')"
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
          :placeholder="$t('auth.placeholders.email')"
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
          :placeholder="$t('auth.placeholders.password')"
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
            :placeholder="$t('auth.placeholders.password')"
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
            :placeholder="$t('auth.placeholders.cpf')"
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
            :placeholder="$t('auth.placeholders.birthdate')"
            :disabled="loading"
            class="rounded-lg"
            :rules="[v => !!v || $t('validation.required'), validateAge]"
            hide-details="auto"
          />
        </v-col>

        <v-col cols="12" class="mt-4">
          <v-checkbox
            v-model="modelValue.aceita_termos"
            color="primary"
            hide-details
            density="compact"
          >
            <template v-slot:label>
                <span class="text-caption">
                   {{ $t('auth.i_agree_to') || 'Eu aceito os' }}
                   <span class="text-primary font-weight-bold cursor-pointer hover-underline" @click.stop="showTermsModal = true">{{ $t('auth.terms_and_conditions') || 'termos e condições' }}</span>
                </span>
            </template>
          </v-checkbox>

          <v-checkbox
            v-model="modelValue.aceita_notificacoes"
            color="primary"
            hide-details
            density="compact"
            class="mt-n2"
          >
            <template v-slot:label>
               <span class="text-caption">
                  {{ $t('auth.i_agree_to_notifications') || 'Aceito receber notificações por e-mail sobre novidades e promoções.' }}
               </span>
            </template>
          </v-checkbox>
        </v-col>
      </template>
    </v-row>

    <v-alert v-if="error" type="error" variant="tonal" class="mt-6 rounded-lg" density="compact" closable>{{ error }}</v-alert>

    <p v-if="mode === 'login'" class="text-caption text-medium-emphasis mt-6 text-center">
      {{ $t('auth.login_terms_notice') || 'Ao entrar, você concorda com nossos' }}
      <span class="text-primary font-weight-bold cursor-pointer hover-underline" @click="showTermsModal = true">{{ $t('auth.terms_and_conditions') || 'termos e condições' }}</span>.
    </p>

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

  <!-- Terms and Conditions Modal -->
  <ModalBase v-model="showTermsModal" :title="$t('auth.terms_title') || 'Termos e Condições'" maxWidth="500px">
    <div class="pa-6">
      <div class="bg-primary-lighten-5 pa-6 rounded-xl mb-6 text-center border">
        <v-icon icon="mdi-shield-check-outline" color="primary" size="48" class="mb-3"></v-icon>
        <p class="text-subtitle-1 font-weight-black text-primary mb-0">{{ $t('auth.terms_protection_title') }}</p>
      </div>
      <p class="text-body-2 text-medium-emphasis line-height-relaxed">
        {{ $t('auth.terms_text') }}
      </p>
    </div>
    <template #actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" variant="elevated" class="rounded-lg px-6" @click="showTermsModal = false">{{ $t('auth.terms_btn_close') }}</v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import ModalBase from '../Modals/modalBase.vue'
import DateInput from '../Common/DateInput.vue'

const { t } = useI18n()

const showTermsModal = ref(false)

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
           props.modelValue.senha !== props.modelValue.senha_confirmation || !props.modelValue.aceita_termos
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

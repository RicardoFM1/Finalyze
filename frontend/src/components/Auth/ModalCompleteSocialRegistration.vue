<template>
  <div>
    <ModalBase :model-value="modelValue" @update:model-value="v => $emit('update:modelValue', v)" :persistent="persistent" maxWidth="500px" :title="$t('auth.complete_registration.title') || 'Completar Cadastro'">
      <div class="pa-6">
        <p class="text-body-2 text-medium-emphasis mb-6">
          {{ $t('auth.complete_registration.desc') || 'Para sua segurança e conformidade, precisamos de mais alguns dados.' }}
        </p>

        <v-form @submit.prevent="handleSubmit" v-model="formValid">
          <v-row dense>
            <v-col cols="12">
              <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('profile.labels.cpf') }}</label>
              <v-text-field
                v-model="form.cpf"
                :placeholder="$t('auth.placeholders.cpf')"
                prepend-inner-icon="mdi-card-account-details-outline"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="rounded-lg"
                maxlength="14"
                :disabled="loading"
                :rules="[v => !!v || $t('validation.required'), v => validateCPF(v, t)]"
                hide-details="auto"
                @input="handleCpfInput"
              ></v-text-field>
            </v-col>

            <v-col cols="12" class="mt-4">
              <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('profile.labels.birthdate') }}</label>
              <DateInput
                v-model="form.data_nascimento"
                icon="mdi-calendar-outline"
                :label="$t('auth.placeholders.birthdate')"
                :disabled="loading"
                class="rounded-lg"
                :rules="[v => !!v || $t('validation.required'), v => validateAge(v, t)]"
                hide-details="auto"
              />
            </v-col>

            <v-col cols="12" class="mt-4">
              <v-checkbox
                v-model="form.aceita_termos"
                color="primary"
                hide-details
                density="compact"
                :rules="[v => !!v || ($t('register.rules.required', { field: $t('auth.terms_and_conditions') }) || 'Aceite os termos para continuar')]"
              >
                <template v-slot:label>
                  <span class="text-caption">
                    {{ $t('auth.i_agree_to') || 'Eu aceito os' }}
                    <span class="text-primary font-weight-bold cursor-pointer hover-underline" @click.stop="showTerms = true">{{ $t('auth.terms_and_conditions') || 'termos e condições' }}</span>
                  </span>
                </template>
              </v-checkbox>
            </v-col>
          </v-row>

          <v-btn
            block
            color="primary"
            size="x-large"
            class="rounded-xl font-weight-bold mt-8"
            type="submit"
            :loading="loading"
            :disabled="!formValid"
          >
            {{ $t('common.continue') || 'Continuar' }}
          </v-btn>
        </v-form>
      </div>
    </ModalBase>

    <ModalBase v-model="showTerms" :title="$t('auth.terms_title') || 'Termos e Condições'">
        <div class="pa-4 text-body-2">
            <p>{{ $t('auth.terms_text') }}</p>
        </div>
        <template #actions>
            <v-btn block color="primary" variant="tonal" class="rounded-lg" @click="showTerms = false">
                {{ $t('auth.terms_btn_close') || 'Fechar' }}
            </v-btn>
        </template>
    </ModalBase>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { toast } from 'vue3-toastify'
import { useI18n } from 'vue-i18n'
import ModalBase from '../Modals/modalBase.vue'
import DateInput from '../Common/DateInput.vue'
import { validateAge, validateCPF } from '../../utils/validation'

const props = defineProps({
  modelValue: Boolean,
  usuarioId: [Number, String],
  email: String,
  onboardingToken: String,
  persistent: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'complete'])

const { t } = useI18n()
const authStore = useAuthStore()
const loading = ref(false)
const formValid = ref(false)
const showTerms = ref(false)

const form = ref({
  cpf: '',
  data_nascimento: null,
  aceita_termos: false,
  aceita_notificacoes: true
})

const handleCpfInput = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 11) value = value.substring(0, 11)
  if (value.length > 9) {
    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  } else if (value.length > 6) {
    value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3')
  } else if (value.length > 3) {
    value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2')
  }
  form.value.cpf = value
}

const handleSubmit = async () => {
  loading.value = true
  try {
    const cleanCpf = form.value.cpf.replace(/\D/g, '')
    const result = await authStore.completarCadastroSocial({
      usuario_id: props.usuarioId,
      onboarding_token: props.onboardingToken,
      ...form.value,
      cpf: cleanCpf
    })
    toast.success(t('toasts.success_update'))
    emit('complete', result)
  } catch (e) {
    toast.error(e.message)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
</style>

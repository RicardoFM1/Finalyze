<template>
  <ModalBase :model-value="modelValue" persistent maxWidth="500px" :title="$t('auth.complete_registration.title') || 'Completar Cadastro'">
    <div class="pa-6">
      <p class="text-body-2 text-medium-emphasis mb-6">
        {{ $t('auth.complete_registration.desc') || 'Para continuar sua segurança, precisamos de mais alguns dados.' }}
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
              :rules="[v => !!v || $t('validation.required'), validateCPF]"
              hide-details="auto"
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="mt-4">
            <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">{{ $t('profile.labels.birthdate') }}</label>
            <DateInput
              v-model="form.data_nascimento"
              icon="mdi-calendar-outline"
              :placeholder="$t('auth.placeholders.birthdate')"
              :disabled="loading"
              class="rounded-lg"
              :rules="[v => !!v || $t('validation.required'), validateAge]"
              hide-details="auto"
            />
          </v-col>

          <v-col cols="12" class="mt-4">
            <v-divider class="mb-6"></v-divider>
            <label class="text-caption font-weight-bold text-medium-emphasis mb-2 d-block ms-1">
                {{ $t('auth.verification_code_label') || 'Código de Verificação' }}
            </label>
            <p class="text-caption text-medium-emphasis mb-3">
                {{ $t('auth.verify_subtitle') || 'Enviamos um código para seu e-mail:' }} <strong>{{ email }}</strong>
            </p>
            <div class="otp-container d-flex justify-center">
                <v-otp-input
                v-model="form.codigo"
                length="6"
                type="number"
                variant="outlined"
                :disabled="loading"
                height="60"
                ></v-otp-input>
            </div>
          </v-col>

          <v-col cols="12" class="mt-4">
            <v-checkbox
              v-model="form.aceita_termos"
              color="primary"
              hide-details
              density="compact"
              :rules="[v => !!v || 'Aceite os termos para continuar']"
            >
              <template v-slot:label>
                  <span class="text-caption">
                     {{ $t('auth.i_agree_to') || 'Eu aceito os' }}
                     <span class="text-primary font-weight-bold cursor-pointer hover-underline" @click.stop="">{{ $t('auth.terms_and_conditions') || 'termos e condições' }}</span>
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
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../Modals/modalBase.vue'
import DateInput from '../Common/DateInput.vue'

const props = defineProps({
  modelValue: Boolean,
  usuarioId: [Number, String],
  email: String
})

const emit = defineEmits(['update:modelValue', 'complete'])

const authStore = useAuthStore()
const loading = ref(false)
const formValid = ref(false)

const form = ref({
  cpf: '',
  data_nascimento: null,
  codigo: '',
  aceita_termos: false,
  aceita_notificacoes: true
})

const validateCPF = (v) => {
    if (!v) return true;
    const cpf = v.replace(/[^\d]/g, '');
    if (cpf.length !== 11) return 'CPF inválido';
    // Adicionar lógica de validação matemática se quiser, ou deixar pro backend
    return true;
}

const validateAge = (v) => {
    if (!v) return true;
    const birth = new Date(v);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    return age >= 18 || 'Você deve ter pelo menos 18 anos';
}

const handleSubmit = async () => {
    loading.value = true
    try {
        const result = await authStore.completarCadastroSocial({
            usuario_id: props.usuarioId,
            ...form.value
        })
        toast.success('Dados atualizados com sucesso!')
        emit('complete', result)
    } catch (e) {
        toast.error(e.message)
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.otp-container :deep(.v-otp-input__content) {
  gap: 8px;
  padding: 0;
}

.otp-container :deep(.v-field) {
  border-radius: 12px;
  background-color: rgba(var(--v-theme-surface), 0.5);
}

@media (max-width: 600px) {
  .otp-container :deep(.v-otp-input__content) {
    gap: 4px;
  }
  .otp-container :deep(.v-field) {
    --v-otp-input-width: 40px;
  }
}
</style>

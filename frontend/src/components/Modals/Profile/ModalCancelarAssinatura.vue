<template>
  <ModalBase v-model="internalValue" :title="$t('modals.cancel_subscription.title')" maxWidth="500px">
     <div class="text-center pa-4">
        <v-icon color="error" size="64" class="mb-4">mdi-alert-circle-outline</v-icon>
        <p class="text-body-1 mb-6 font-weight-medium">
          {{ $t('modals.cancel_subscription.description') }}
        </p>
        
        <v-alert v-if="dataExpiracao" variant="tonal" color="warning" class="text-left rounded-xl mb-6 bg-warning-lighten-5">
            <template v-slot:prepend>
                <v-icon icon="mdi-calendar-clock" color="warning"></v-icon>
            </template>
            <div>
               <div class="text-caption font-weight-bold opacity-70">{{ $t('profile.subscription.expires_at') }}</div>
               <div class="text-subtitle-1 font-weight-black">{{ dataExpiracaoFormatada }}</div>
            </div>
        </v-alert>
      </div>

    <template #actions>
      <div class="d-flex w-100 flex-column flex-sm-row justify-center gap-6 pa-6 pt-0">
         <v-btn 
            variant="outlined" 
            height="50"
            color="primary"
            class="rounded-xl font-weight-bold flex-grow-1 elevation-0" 
            @click="internalValue = false"
         >
            {{ $t('modals.cancel_subscription.keep') }}
         </v-btn>
         <v-btn 
            color="error" 
            variant="flat" 
            height="50"
            class="rounded-xl font-weight-bold flex-grow-1 shadow-error" 
            :loading="loading" 
            @click="confirmCancel"
         >
            {{ $t('modals.cancel_subscription.confirm') }}
         </v-btn>
      </div>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
  modelValue: Boolean,
  dataExpiracao: String
})

const emit = defineEmits(['update:modelValue', 'cancelled'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const dataExpiracaoFormatada = computed(() => {
  if (!props.dataExpiracao) return ''
  const dateLocale = locale.value === 'pt' ? 'pt-BR' : 'en-US'
  return new Date(props.dataExpiracao).toLocaleDateString(dateLocale)
})

const confirmCancel = async () => {
  loading.value = true
  try {
    const response = await authStore.apiFetch('/assinaturas/cancelar', { method: 'POST' })
    if (response.ok) {
      toast.success(t('toasts.success_update'))
      internalValue.value = false
      emit('cancelled')
    }
  } catch (e) {
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

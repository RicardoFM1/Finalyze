<template>
  <ModalBase v-model="internalValue" :title="$t('modals.cancel_subscription.title')" maxWidth="450px">
    <p class="text-body-1">
      {{ $t('modals.cancel_subscription.description') }} <strong>{{ dataExpiracaoFormatada }}</strong>.
    </p>
    <template #actions>
      <div class="d-flex w-100 flex-column flex-sm-row justify-end gap-2">
         <v-btn variant="text" class="mb-2 mb-sm-0" @click="internalValue = false">{{ $t('modals.cancel_subscription.keep') }}</v-btn>
         <v-btn color="error" variant="flat" class="rounded-lg ml-sm-2" :loading="loading" @click="confirmCancel">{{ $t('modals.cancel_subscription.confirm') }}</v-btn>
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

const { t } = useI18n()

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
  return new Date(props.dataExpiracao).toLocaleDateString('pt-BR')
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

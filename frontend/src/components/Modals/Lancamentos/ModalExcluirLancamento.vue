<template>
  <ModalBase :title="$t('transactions.delete_title')" v-model="internalValue" maxWidth="550px">
    <div class="text-center">
      <v-icon color="error" size="56" class="mb-4">
        mdi-alert-circle-outline
      </v-icon>
      <p class="text-body-2 text-grey-darken-1 mb-4">
        {{ $t('transactions.delete_confirm') }}
        <br />
        <strong>{{ $t('transactions.delete_warning') }}</strong>
      </p>
    </div>
    <template #actions>
      <v-btn
        variant="outlined"
        class="flex-grow-1"
        @click="internalValue = false"
        :disabled="loading"
        rounded="lg"
      >
        {{ $t('common.cancel') }}
      </v-btn>
      <v-btn
        color="error"
        class="flex-grow-1 ml-2"
        :loading="loading"
        @click="excluir"
        rounded="lg"
        elevation="2"
      >
        {{ $t('common.delete') }}
      </v-btn>
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
  lancamentoId: [Number, String]
})

const emit = defineEmits(['update:modelValue', 'deleted'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const excluir = async () => {
  if (!props.lancamentoId) return

  const id = props.lancamentoId

  internalValue.value = false

  toast.success(t('toasts.success_delete'))

  emit('deleted', id)

  try {
    const response = await authStore.apiFetch(`/lancamentos/${id}`, {
      method: 'DELETE'
    })

    if (!response.ok) {
      throw new Error('Erro ao deletar')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
    
  }
}
</script>

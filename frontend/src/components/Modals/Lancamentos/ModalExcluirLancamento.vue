<template>
  <ModalBase :title="$t('admin.deleteTitle')" v-model="internalValue" maxWidth="550px">
    <div class="text-center">
      <v-icon color="error" size="56" class="mb-4">
        mdi-alert-circle-outline
      </v-icon>
      <p class="text-body-2 text-grey-darken-1 mb-4">
        {{ $t('admin.deleteConfirm') }}?
        <br />
        <strong>{{ $t('profile.warnings.update_error') }}</strong>
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

  loading.value = true
  try {
    const response = await authStore.apiFetch(`/lancamentos/${props.lancamentoId}`, {
      method: 'DELETE'
    })

    if (response.ok) {
      toast.success(t('toasts.success_delete'))
      internalValue.value = false
      emit('deleted')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

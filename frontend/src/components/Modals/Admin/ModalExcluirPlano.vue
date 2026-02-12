<template>
  <ModalBase v-model="internalValue" title="Confirmar Exclusão" maxWidth="400px">
    <p class="text-body-1">Tem certeza que deseja excluir o plano <strong>{{ plano?.nome }}</strong>?</p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false" rounded="lg">Cancelar</v-btn>
      <v-btn color="error" variant="flat" rounded="lg" @click="confirmDelete" :loading="loading" class="ml-2">Excluir</v-btn>
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
  plano: Object
})

const emit = defineEmits(['update:modelValue', 'deleted'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const confirmDelete = async () => {
  if (!props.plano?.id) return

  loading.value = true
  try {
    const response = await authStore.apiFetch(`/planos/${props.plano.id}`, {
      method: 'DELETE'
    })
    if (response.ok) {
      toast.success(t('toasts.success_delete'))
      internalValue.value = false
      emit('deleted')
    } else {
      const data = await response.json()
      toast.error(data.message || 'Erro ao excluir')
    }
  } catch (e) {
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

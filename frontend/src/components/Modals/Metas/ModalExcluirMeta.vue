<template>
  <ModalBase v-model="internalValue" :title="$t('metas.actions.inactivate_title')" maxWidth="400px">
    <p class="text-body-1">{{ $t('metas.actions.inactivate_confirm', { title: meta?.titulo }) }}</p>
    <p class="text-caption text-medium-emphasis mt-2">{{ $t('metas.actions.inactivate_desc') }}</p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="flat" rounded="lg" @click="confirmDelete" :loading="loading" class="ml-2">Desativar</v-btn>
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
  meta: Object
})

const emit = defineEmits(['update:modelValue', 'deleted'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const confirmDelete = async () => {
  if (!props.meta?.id) return

  const id = props.meta.id
  const isAnotacao = !props.meta.tipo || props.meta.tipo === 'pessoal'
  
  // Close immediately for perceived speed
  internalValue.value = false
  toast.success(t('toasts.success_inactivate'))
  emit('deleted', { id, isAnotacao })

  try {
    const endpoint = isAnotacao ? `/anotacoes/${id}` : `/metas/${id}`
    const response = await authStore.apiFetch(endpoint, {
      method: 'DELETE'
    })
    if (!response.ok) {
        throw new Error('Erro ao desativar')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  }
}
</script>

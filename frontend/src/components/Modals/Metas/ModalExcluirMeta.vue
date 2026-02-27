<template>
  <ModalBase v-model="internalValue" :title="$t('metas.actions.inactivate_title')" maxWidth="400px">
    <p class="text-body-1">{{ $t('metas.actions.inactivate_confirm', { title: meta?.titulo }) }}</p>
    <p class="text-caption text-medium-emphasis mt-2">{{ $t('metas.actions.inactivate_desc') }}</p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="flat" rounded="lg" @click="confirmDelete" :loading="loading" class="ml-2">{{ $t('metas.actions.inactivate_btn') }}</v-btn>
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
  meta: Object,
  
  resourceType: {
    type: String,
    default: 'metas',
    validator: (v) => ['metas', 'lembretes'].includes(v)
  }
})

const emit = defineEmits(['update:modelValue', 'deleted', 'rollback'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const confirmDelete = async () => {
  if (!props.meta?.id) return

  const id = props.meta.id
  const oldStatus = props.meta.status
  const resource = props.resourceType
  const endpoint = `/${resource}/${id}`

  internalValue.value = false
  toast.success(t('toasts.success_inactivate'))
  emit('deleted', { id, oldStatus })

  try {
    const response = await authStore.apiFetch(endpoint, { method: 'DELETE' })
    if (!response.ok) {
      const body = await response.json().catch(() => ({}))
      throw new Error(body.message || 'Erro ao desativar')
    }
  } catch (e) {
    console.error(`[ModalExcluirMeta] DELETE ${endpoint} falhou:`, e.message)
    toast.error(t('toasts.error_generic'))
    emit('rollback', { id, oldStatus })
  }
}
</script>

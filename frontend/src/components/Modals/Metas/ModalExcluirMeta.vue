<template>
  <ModalBase v-model="internalValue" :title="$t('admin.deleteTitle')" maxWidth="400px">
    <p class="text-body-1">{{ $t('admin.deleteConfirm') }} "{{ meta?.titulo }}"?</p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="elevated" rounded="lg" @click="confirmDelete" :loading="loading" class="ml-2">{{ $t('common.delete') }}</v-btn>
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

  loading.value = true
  try {
    const isAnotacao = !props.meta.tipo || props.meta.tipo === 'pessoal'
    const endpoint = isAnotacao ? `/anotacoes/${props.meta.id}` : `/metas/${props.meta.id}`
    
    const response = await authStore.apiFetch(endpoint, {
      method: 'DELETE'
    })
    if (response.ok) {
      toast.success(isAnotacao ? t('toasts.success_update') : t('toasts.success_update'))
      internalValue.value = false
      emit('deleted')
    }
  } catch (e) {
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

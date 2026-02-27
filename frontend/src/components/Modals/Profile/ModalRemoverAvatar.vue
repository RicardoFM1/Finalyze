<template>
  <ModalBase v-model="internalValue" :title="$t('modals.remove_avatar.title')" maxWidth="400px">
    <p class="text-body-1">
      {{ $t('modals.remove_avatar.description') }}
    </p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="flat" class="rounded-lg ml-2" :loading="loading" @click="confirmRemove">{{ $t('modals.remove_avatar.confirm') }}</v-btn>
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
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'removed'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const confirmRemove = async () => {
  
  internalValue.value = false
  toast.success(t('toasts.success_update'))
  emit('removed')

  try {
    const response = await authStore.apiFetch('/usuario/avatar', {
      method: 'DELETE'
    })
    
    if (response.ok) {
      const data = await response.json()
      authStore.user = data.usuario
    } else {
      throw new Error('Erro ao remover avatar')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))

  }
}
</script>

<template>
  <ModalBase v-model="internalValue" title="Remover foto?" maxWidth="400px">
    <p class="text-body-1">
      Tem certeza que deseja remover sua foto de perfil? Ela será excluída permanentemente.
    </p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">Cancelar</v-btn>
      <v-btn color="error" variant="flat" class="rounded-lg ml-2" :loading="loading" @click="confirmRemove">Remover</v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'

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
  loading.value = true
  try {
    const response = await authStore.apiFetch('/usuario/avatar', {
      method: 'DELETE'
    })
    
    if (response.ok) {
      toast.success('Avatar removido!')
      const data = await response.json()
      authStore.user = data.usuario
      emit('removed', data.usuario)
      internalValue.value = false
    } else {
      toast.error('Erro ao remover avatar')
    }
  } catch (e) {
    toast.error('Erro de conexão')
  } finally {
    loading.value = false
  }
}
</script>

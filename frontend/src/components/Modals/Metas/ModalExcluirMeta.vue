<template>
  <ModalBase v-model="internalValue" title="Excluir Meta?" maxWidth="400px">
    <p class="text-body-1">Esta ação não pode ser desfeita. Deseja realmente excluir "{{ meta?.titulo }}"?</p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">Cancelar</v-btn>
      <v-btn color="error" variant="elevated" rounded="lg" @click="confirmDelete" :loading="loading" class="ml-2">Excluir</v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'

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
      toast.success(isAnotacao ? 'Anotação removida' : 'Meta removida')
      internalValue.value = false
      emit('deleted')
    }
  } catch (e) {
    toast.error('Erro ao deletar')
  } finally {
    loading.value = false
  }
}
</script>

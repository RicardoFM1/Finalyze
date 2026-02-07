<template>
  <ModalBase v-model="internalValue" title="Confirmar Cancelamento?" maxWidth="450px">
    <p class="text-body-1">
      Você continuará tendo acesso aos benefícios até o final do período ativo em <strong>{{ dataExpiracaoFormatada }}</strong>. Nenhuma nova cobrança será feita.
    </p>
    <template #actions>
      <v-btn variant="text" @click="internalValue = false">Manter</v-btn>
      <v-btn color="error" variant="flat" class="rounded-lg ml-2" :loading="loading" @click="confirmCancel">Confirmar</v-btn>
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
      toast.success('Assinatura cancelada com sucesso.')
      internalValue.value = false
      emit('cancelled')
    }
  } catch (e) {
    toast.error('Erro ao cancelar')
  } finally {
    loading.value = false
  }
}
</script>

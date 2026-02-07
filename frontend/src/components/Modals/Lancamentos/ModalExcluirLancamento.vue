<template>
  <ModalBase title="Confirmar exclusão" v-model="internalValue" maxWidth="550px">
    <div class="text-center">
      <v-icon color="error" size="56" class="mb-4">
        mdi-alert-circle-outline
      </v-icon>
      <p class="text-body-2 text-grey-darken-1 mb-4">
        Tem certeza que deseja excluir este lançamento?
        <br />
        <strong>Essa ação poderá ser desfeita apenas contatando o suporte.</strong>
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
        Cancelar
      </v-btn>
      <v-btn
        color="error"
        class="flex-grow-1 ml-2"
        :loading="loading"
        @click="excluir"
        rounded="lg"
        elevation="2"
      >
        Excluir
      </v-btn>
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
      toast.success('Lançamento excluído!')
      internalValue.value = false
      emit('deleted')
    }
  } catch (e) {
    console.error(e)
    toast.error('Erro ao excluir lançamento.')
  } finally {
    loading.value = false
  }
}
</script>

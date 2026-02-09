<template>
  <ModalBase title="Adicionar Lançamento" v-model="internalValue" maxWidth="550px">
    <v-form @submit.prevent="salvarLancamento">
      <v-row dense>
        <v-col cols="12">
          <v-btn-toggle v-model="form.tipo" mandatory color="primary" class="w-100 mb-4 rounded-lg" border>
            <v-btn value="receita" class="flex-grow-1" prepend-icon="mdi-cash-plus">Receita</v-btn>
            <v-btn value="despesa" class="flex-grow-1" prepend-icon="mdi-cash-minus">Despesa</v-btn>
          </v-btn-toggle>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.valor" label="Valor" prefix="R$" type="number" step="0.01" variant="outlined" rounded="lg" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.data" label="Data" type="date" variant="outlined" rounded="lg" required></v-text-field>
        </v-col>
       <v-col cols="12">
                            <v-autocomplete
                                v-model="form.categoria"
                                :items="categorias"
                                item-title="title"
                                item-value="title"
                                label="Categoria"
                                variant="outlined"
                                rounded="lg"
                                required
                                placeholder="Selecione ou digite para filtrar"
                                no-data-text="Nenhuma categoria encontrada"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item 
                                        v-bind="props" 
                                        :prepend-icon="item.raw.icon"
                                        :title="item.raw.title"
                                    ></v-list-item>
                                </template>

                                <template v-slot:prepend-inner>
                                    <v-icon 
                                        v-if="form.categoria" 
                                        :icon="categorias.find(c => c.title === form.categoria)?.icon || 'mdi-tag'" 
                                        class="mr-2 text-medium-emphasis"
                                    ></v-icon>
                                </template>
                            </v-autocomplete>
                        </v-col>
        <v-col cols="12">
          <v-textarea v-model="form.descricao" label="Descrição" variant="outlined" rounded="lg" rows="2"></v-textarea>
        </v-col>
      </v-row>
    </v-form>
    <template #actions>
      <v-btn 
        color="primary" 
        block 
        variant="flat" 
        size="large" 
        rounded="lg" 
        :loading="loading" 
        elevation="3"
        @click="salvarLancamento"
      >
        Salvar Lançamento
      </v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'saved'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

import { categorias } from '../../../constants/categorias'

const form = ref({
  tipo: 'despesa',
  valor: '',
  categoria: '',
  data: new Date().toISOString().substr(0, 10),
  descricao: ''
})


watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    form.value = {
      tipo: 'despesa',
      valor: '',
      categoria: '',
      data: new Date().toISOString().substr(0, 10),
      descricao: ''
    }
  }
})

const salvarLancamento = async () => {
  loading.value = true
  try {
    const valor = Number(form.value.valor)
    if (isNaN(valor) || valor <= 0) {
      toast.warning('Por favor, informe um valor válido.')
      loading.value = false
      return
    }

    const response = await authStore.apiFetch('/lancamentos', {
      method: 'POST',
      body: JSON.stringify({
        ...form.value,
        valor: valor
      })
    })

    if (response.ok) {
      toast.success('Lançamento adicionado!')
      internalValue.value = false
      emit('saved')
    } else {
      const data = await response.json()
      toast.error(data.message || 'Erro ao salvar lançamento.')
    }
  } catch (e) {
    console.error(e)
    toast.error('Erro ao salvar lançamento.')
  } finally {
    loading.value = false
  }
}
</script>

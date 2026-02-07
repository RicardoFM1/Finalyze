<template>
  <ModalBase :title="form.id ? 'Editar Meta' : 'Nova Meta'" v-model="internalValue" maxWidth="550px">
    <v-form ref="metaForm" @submit.prevent="saveMeta">
      <v-btn-toggle 
        v-model="form.tipo"
        class="w-100 mb-4 rounded-lg"
        mandatory 
        color="primary" 
        border
      >
        <v-btn value="financeira" class="flex-grow-1" prepend-icon="mdi-cash-plus">Financeira</v-btn>
        <v-btn value="pessoal" class="flex-grow-1" prepend-icon="mdi-account-outline">Pessoal</v-btn>
      </v-btn-toggle>

      <v-text-field
        v-model="form.titulo"
        label="Título"
        variant="outlined"
        placeholder="Ex: Reserva de Emergência"
        class="mb-2"
        rounded="lg"
        required
      ></v-text-field>

      <v-text-field
        v-model="form.descricao"
        label="Descrição / Subtítulo"
        variant="outlined"
        placeholder="Pequeno detalhe ou frase de impacto"
        class="mb-2"
        rounded="lg"
      ></v-text-field>

      <template v-if="form.tipo === 'financeira'">
        <v-row dense>
          <v-col cols="6">
            <v-text-field
              v-model="form.valor_atual"
              label="Valor Atual"
              prefix="R$"
              type="number"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.valor_objetivo"
              label="Objetivo"
              prefix="R$"
              type="number"
              variant="outlined"
              rounded="lg"
              required
            ></v-text-field>
          </v-col>
        </v-row>
      </template>

      <template v-else>
        <v-row dense>
          <v-col cols="6">
            <v-text-field
              v-model="form.atual_quantidade"
              label="Progresso Atual"
              type="number"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.meta_quantidade"
              label="Meta Total"
              type="number"
              variant="outlined"
              rounded="lg"
              required
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col cols="6">
            <v-text-field
              v-model="form.unidade"
              label="Unidade"
              placeholder="livros, km, etc"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.icone"
              label="Ícone/Emoji"
              placeholder="📚, 🎯, 🏃"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
        </v-row>
      </template>

      <v-row dense>
        <v-col cols="6">
          <v-text-field
            v-model="form.prazo"
            label="Prazo"
            type="date"
            variant="outlined"
            rounded="lg"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-text-field
            v-model="form.cor"
            label="Cor (Hex)"
            type="color"
            variant="outlined"
            rounded="lg"
          ></v-text-field>
        </v-col>
      </v-row>

      <v-btn 
        type="submit" 
        color="primary" 
        block 
        size="large" 
        rounded="lg" 
        class="mt-4" 
        variant="flat"
        :loading="loading" 
        elevation="2"
      >
        Salvar Meta
      </v-btn>
    </v-form>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'

const props = defineProps({
  modelValue: Boolean,
  meta: Object,
  initialTipo: {
    type: String,
    default: 'financeira'
  }
})

const emit = defineEmits(['update:modelValue', 'saved'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const form = ref({
  id: null,
  tipo: 'financeira',
  titulo: '',
  descricao: '',
  valor_objetivo: null,
  valor_atual: 0,
  meta_quantidade: null,
  atual_quantidade: 0,
  unidade: '',
  prazo: null,
  cor: '#1867C0',
  icone: ''
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    if (props.meta) {
      form.value = { 
        ...props.meta,
        prazo: props.meta.prazo ? new Date(props.meta.prazo).toISOString().substr(0, 10) : null
      }
    } else {
      form.value = {
        id: null,
        tipo: props.initialTipo,
        titulo: '',
        descricao: '',
        valor_objetivo: null,
        valor_atual: 0,
        meta_quantidade: null,
        atual_quantidade: 0,
        unidade: props.initialTipo === 'financeira' ? 'BRL' : '',
        prazo: null,
        cor: props.initialTipo === 'financeira' ? '#4CAF50' : '#7E57C2',
        icone: props.initialTipo === 'pessoal' ? '🎯' : ''
      }
    }
  }
}, { immediate: true })

const saveMeta = async () => {
  loading.value = true
  try {
    const isEdit = !!form.value.id
    const response = await authStore.apiFetch(isEdit ? `/metas/${form.value.id}` : '/metas', {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(form.value)
    })

    if (response.ok) {
      toast.success(isEdit ? 'Meta atualizada!' : 'Meta criada com sucesso!')
      internalValue.value = false
      emit('saved')
    } else {
      const data = await response.json()
      toast.error(data.message || 'Erro ao salvar meta')
    }
  } catch (e) {
    toast.error('Erro de conexão')
  } finally {
    loading.value = false
  }
}
</script>

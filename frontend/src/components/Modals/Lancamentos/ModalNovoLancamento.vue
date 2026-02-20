<template>
  <ModalBase :title="$t('modals.titles.add_transaction')" v-model="internalValue" maxWidth="550px">
    <v-form @submit.prevent="salvarLancamento">
      <v-row dense>
        <v-col cols="12">
          <v-btn-toggle v-model="form.tipo" mandatory color="primary" class="w-100 mb-4 rounded-lg" border>
            <v-btn value="receita" class="flex-grow-1" prepend-icon="mdi-cash-plus">{{ $t('transactions.type.income') }}</v-btn>
            <v-btn value="despesa" class="flex-grow-1" prepend-icon="mdi-cash-minus">{{ $t('transactions.type.expense') }}</v-btn>
          </v-btn-toggle>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.valor" :label="$t('modals.labels.value')" prefix="R$" type="number" step="0.01" variant="outlined" rounded="lg" required :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <DateInput v-model="form.data" :label="$t('modals.labels.date')" required :disabled="loading" />
        </v-col>
       <v-col cols="12">
                            <v-autocomplete
                                v-model="form.categoria"
                                :items="categorias"
                                item-title="title"
                                item-value="title"
                                :label="$t('modals.labels.category')"
                                variant="outlined"
                                rounded="lg"
                                required
                                :disabled="loading"
                                :placeholder="$t('modals.placeholders.select_category')"
                                :no-data-text="$t('transactions.no_data')"
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
          <v-textarea v-model="form.descricao" :label="$t('modals.labels.description')" variant="outlined" rounded="lg" rows="2" :disabled="loading"></v-textarea>
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
        :loading="loading || uiStore.loading" 
        :disabled="loading || uiStore.loading"
        elevation="3"
        @click="salvarLancamento"
      >
        {{ $t('common.save') }}
      </v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { useUiStore } from '../../../stores/ui'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'
import DateInput from '../../Common/DateInput.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'saved'])

const authStore = useAuthStore()
const uiStore = useUiStore()
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
  data: new Date().toLocaleDateString('en-CA'),
  descricao: ''
})


watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    form.value = {
      tipo: 'despesa',
      valor: '',
      categoria: '',
      data: new Date().toLocaleDateString('en-CA'),
      descricao: ''
    }
  }
})

const salvarLancamento = async () => {
  loading.value = true
  try {
    const valor = Number(form.value.valor)
    if (isNaN(valor) || valor <= 0) {
      toast.warning(t('validation.invalid_value'))
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
      toast.success(t('toasts.success_add'))
      internalValue.value = false
      emit('saved')
    } else {
      const data = await response.json()
      if (data.message && data.message.toLowerCase().includes('description') && data.message.includes('255')) {
        toast.error(t('validation.description_too_long'))
      } else {
        toast.error(data.message || t('toasts.error_generic'))
      }
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

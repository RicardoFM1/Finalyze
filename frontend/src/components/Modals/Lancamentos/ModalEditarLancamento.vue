<template>
  <ModalBase :title="$t('modals.titles.edit_transaction')" v-model="internalValue" maxWidth="550px">
    <v-form @submit.prevent="editar">
      <v-row dense>
        <v-col cols="12">
          <v-btn-toggle v-model="localForm.tipo" mandatory color="primary" class="w-100 mb-4 rounded-lg" border>
            <v-btn value="receita" class="flex-grow-1" prepend-icon="mdi-cash-plus">{{ $t('transactions.type.income') }}</v-btn>
            <v-btn value="despesa" class="flex-grow-1" prepend-icon="mdi-cash-minus">{{ $t('transactions.type.expense') }}</v-btn>
          </v-btn-toggle>
        </v-col>
        <v-col cols="12" md="6">
          <CurrencyInput v-model="localForm.valor" :label="$t('modals.labels.value')" :prefix="currencySymbol" variant="outlined" rounded="lg" required />
        </v-col>
        <v-col cols="12" md="6">
          <DateInput v-model="localForm.data" :label="$t('modals.labels.date')" required />
        </v-col>
        <v-col cols="12">
                            <v-autocomplete
                                v-model="localForm.categoria"
                                :items="categoriasTraduzidas"
                                item-title="displayTitle"
                                item-value="originalValue"
                                :label="$t('modals.labels.category')"
                                variant="outlined"
                                rounded="lg"
                                required
                                :placeholder="$t('modals.placeholders.select_category')"
                                :no-data-text="$t('transactions.no_data')"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item 
                                        v-bind="props" 
                                        :prepend-icon="item.raw.icon"
                                        :title="item.raw.displayTitle"
                                        class="py-2"
                                    ></v-list-item>
                                </template>

                                <template v-slot:prepend-inner>
                                    <v-icon 
                                        v-if="localForm.categoria" 
                                        :icon="categoriasTraduzidas.find(c => c.originalValue === localForm.categoria)?.icon || 'mdi-tag'" 
                                        class="mr-2 text-medium-emphasis"
                                    ></v-icon>
                                </template>
                            </v-autocomplete>
                        </v-col>
        <v-col cols="12" md="12">
          <v-select
            v-model="localForm.forma_pagamento"
            :items="formasPagamento"
            item-title="title"
            item-value="value"
            :label="$t('transactions.payment_methods.title')"
            variant="outlined"
            rounded="lg"
            prepend-inner-icon="mdi-credit-card-outline"
          ></v-select>
        </v-col>
        <v-col cols="12">
          <v-textarea v-model="localForm.descricao" :label="$t('modals.labels.description')" variant="outlined" rounded="lg" rows="2"></v-textarea>
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
        @click="editar"
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
import CurrencyInput from '../../Common/CurrencyInput.vue'
import { categorias } from '../../../constants/categorias'
import { useI18n } from 'vue-i18n'
import { useMoney } from '@/composables/useMoney'

const { t } = useI18n()
const { currencySymbol } = useMoney()

const props = defineProps({
  modelValue: Boolean,
  lancamento: Object
})

const emit = defineEmits(['update:modelValue', 'updated'])

const authStore = useAuthStore()
const uiStore = useUiStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const categoriasTraduzidas = computed(() => {
  return categorias.map(c => ({
    displayTitle: t('categories.' + c.title),
    originalValue: c.title,
    icon: c.icon
  }))
})

const formasPagamento = computed(() => [
  { title: t('transactions.payment_methods.money'), value: 'money' },
  { title: t('transactions.payment_methods.credit_card'), value: 'credit_card' },
  { title: t('transactions.payment_methods.debit_card'), value: 'debit_card' },
  { title: t('transactions.payment_methods.pix'), value: 'pix' },
  { title: t('transactions.payment_methods.transfer'), value: 'transfer' },
  { title: t('transactions.payment_methods.boleto'), value: 'boleto' },
  { title: t('transactions.payment_methods.other'), value: 'other' }
])

const localForm = ref({
  tipo: 'despesa',
  valor: '',
  categoria: '',
  forma_pagamento: 'other',
  data: '',
  descricao: ''
})

watch(() => props.lancamento, (newVal) => {
  if (newVal) {
    localForm.value = {
      tipo: newVal.tipo,
      valor: newVal.valor,
      categoria: newVal.categoria,
      forma_pagamento: newVal.forma_pagamento || 'other',
      data: newVal.data ? new Date(newVal.data).toLocaleDateString('en-CA') : '',
      descricao: newVal.descricao
    }
  }
}, { immediate: true })

const editar = async () => {
  if (!props.lancamento?.id) return
  
  const valor = Number(localForm.value.valor)
  if (isNaN(valor) || valor <= 0) {
    toast.warning(t('validation.invalid_value'))
    return
  }

  const optimisticItem = {
    ...props.lancamento,
    ...localForm.value,
    valor: valor
  }

  internalValue.value = false
  toast.success(t('toasts.success_update'))
  emit('updated', optimisticItem)

  try {
    const response = await authStore.apiFetch(`/lancamentos/${props.lancamento.id}`, {
      method: 'PUT',
      body: JSON.stringify({
        ...localForm.value,
        valor: valor
      })
    })

    if (!response.ok) {
        throw new Error('Erro ao editar')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  }
}
</script>

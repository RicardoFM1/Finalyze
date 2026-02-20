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
          <v-text-field v-model="localForm.valor" :label="$t('modals.labels.value')" :prefix="currencyPrefix" type="number" step="0.01" variant="outlined" rounded="lg" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <DateInput v-model="localForm.data" :label="$t('modals.labels.date')" required />
        </v-col>
        <v-col cols="12">
                            <v-autocomplete
                                v-model="localForm.categoria"
                                :items="categorias"
                                item-title="title"
                                item-value="title"
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
                                        :title="item.raw.title"
                                    ></v-list-item>
                                </template>

                                <template v-slot:prepend-inner>
                                    <v-icon 
                                        v-if="localForm.categoria" 
                                        :icon="categorias.find(c => c.title === localForm.categoria)?.icon || 'mdi-tag'" 
                                        class="mr-2 text-medium-emphasis"
                                    ></v-icon>
                                </template>
                            </v-autocomplete>
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
        :loading="loading" 
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
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'
import DateInput from '../../Common/DateInput.vue'
import { categorias } from '../../../constants/categorias'
import { useI18n } from 'vue-i18n'
import { useCurrency } from '../../../composables/useCurrency'

const { t } = useI18n()
const { currencyPrefix, currency, convert } = useCurrency()

const props = defineProps({
  modelValue: Boolean,
  lancamento: Object
})

const emit = defineEmits(['update:modelValue', 'updated'])

const authStore = useAuthStore()
const loading = ref(false)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const localForm = ref({
  tipo: 'despesa',
  valor: '',
  categoria: '',
  data: '',
  descricao: ''
})

watch(() => props.lancamento, (newVal) => {
  if (newVal) {
    localForm.value = {
      tipo: newVal.tipo,
      valor: convert(newVal.valor, 'BRL', currency.value),
      categoria: newVal.categoria,
      data: newVal.data ? new Date(newVal.data).toISOString().slice(0, 10) : '',
      descricao: newVal.descricao
    }
  }
}, { immediate: true })

const editar = async () => {
  if (!props.lancamento?.id) return
  
  loading.value = true
  try {
    const valor = Number(localForm.value.valor)
    if (isNaN(valor) || valor <= 0) {
      toast.warning(t('validation.invalid_value'))
      loading.value = false
      return
    }

    const valorEmBRL = convert(valor, currency.value, 'BRL')

    const response = await authStore.apiFetch(`/lancamentos/${props.lancamento.id}`, {
      method: 'PUT',
      body: JSON.stringify({
        ...localForm.value,
        valor: valorEmBRL
      })
    })

    if (response.ok) {
      toast.success(t('toasts.success_update'))
      internalValue.value = false
      emit('updated')
    }
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}
</script>

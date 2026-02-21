<template>
  <v-select
    v-model="selectedCurrency"
    :items="AVAILABLE_CURRENCIES"
    item-title="label"
    item-value="code"
    :label="$t('landing.select_currency')"
    variant="outlined"
    density="compact"
    hide-details
    style="max-width: 200px;"
    prepend-inner-icon="mdi-currency-usd"
  ></v-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useMoney, AVAILABLE_CURRENCIES } from '@/composables/useMoney'
import { useCurrencyStore } from '@/stores/currency'

const currencyStore = useCurrencyStore()
const { currentCurrency } = useMoney()

const selectedCurrency = ref(currentCurrency.value)

watch(selectedCurrency, (newCurrency) => {
  currencyStore.setCurrency(newCurrency)
})

watch(currentCurrency, (val) => {
  selectedCurrency.value = val
})

onMounted(() => {
  currencyStore.loadCurrency()
  selectedCurrency.value = currentCurrency.value
})
</script>

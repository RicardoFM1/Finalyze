<template>
  <v-select
    v-model="selectedCoin"
    :items="coins"
    item-title="label"
    item-value="value"
    :label="$t('landing.select_currency')"
    variant="outlined"
    density="compact"
    hide-details
    style="max-width: 200px;"
  />
</template>

<script setup>
import { ref, watch } from 'vue'
import { useCurrencyStore } from '../../stores/currency'

const currencyStore = useCurrencyStore()

const coins = [
  { label: 'Real Brasileiro (BRL)', value: 'BRL' },
  { label: 'Dólar Americano (USD)', value: 'USD' },
]

const selectedCoin = ref(currencyStore.currency)

watch(selectedCoin, (newCoin) => {
  currencyStore.setCurrency(newCoin)
})

watch(
  () => currencyStore.currency,
  (newCurrency) => {
    selectedCoin.value = newCurrency
  }
)
</script>

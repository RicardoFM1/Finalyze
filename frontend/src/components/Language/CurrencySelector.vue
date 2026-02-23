<template>
  <v-select
    v-model="selectedCurrency"
    :items="currencyItems"
    item-title="title"
    item-value="value"
    :label="$t('landing.select_currency')"
    variant="outlined"
    density="compact"
    hide-details
    style="min-width: 120px; max-width: 160px;"
    prepend-inner-icon="mdi-currency-usd"
    class="rounded-lg"
  >
    <template v-slot:item="{ props, item }">
      <v-list-item v-bind="props">
        <template v-slot:prepend>
          <span class="mr-2">{{ item.raw.flag }}</span>
        </template>
      </v-list-item>
    </template>
  </v-select>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { useMoney, AVAILABLE_CURRENCIES } from '../../composables/useMoney'
import { useCurrencyStore } from '@/stores/currency'

const currencyStore = useCurrencyStore()
const { currentCurrency } = useMoney()

const currencyItems = computed(() => 
  Object.values(AVAILABLE_CURRENCIES).map(c => ({
    title: c.code,
    value: c.code,
    flag: c.flag
  }))
)

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

<style scoped>
</style>

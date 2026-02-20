<template>
  <div class="coin-selector-container">
    <!-- Desktop: Select compacto -->
    <v-select
      v-if="mdAndUp"
      v-model="selectedCoin"
      :items="coins"
      item-title="label"
      item-value="value"
      variant="outlined"
      density="compact"
      hide-details
      class="compact-select"
    >
      <template v-slot:selection="{ item }">
        <div class="d-flex align-center">
          <v-icon size="18" class="mr-2">mdi-cash-multiple</v-icon>
          <span>{{ item.value }}</span>
        </div>
      </template>
    </v-select>

    <!-- Mobile: Ícone com Menu -->
    <v-menu v-else location="bottom end" transition="scale-transition">
      <template v-slot:activator="{ props }">
        <v-btn
          icon
          variant="tonal"
          color="white"
          size="small"
          v-bind="props"
          class="mobile-coin-btn"
        >
          <span class="currency-label">{{ selectedCoin }}</span>
        </v-btn>
      </template>
      <v-list class="rounded-lg shadow-lg">
        <v-list-item
          v-for="coin in coins"
          :key="coin.value"
          @click="selectedCoin = coin.value"
          :active="selectedCoin === coin.value"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>{{ coin.value === 'BRL' ? 'mdi-currency-brl' : 'mdi-currency-usd' }}</v-icon>
          </template>
          <v-list-item-title>{{ coin.label }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useCurrencyStore } from '../../stores/currency'
import { useDisplay } from 'vuetify'

const { mdAndUp } = useDisplay()
const currencyStore = useCurrencyStore()

const coins = [
  { label: 'Real Brasileiro (BRL)', value: 'BRL' },
  { label: 'Dolar Americano (USD)', value: 'USD' },
  { label: 'Euro (EUR)', value: 'EUR' },
  { label: 'Libra Esterlina (GBP)', value: 'GBP' }
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

<style scoped>
.coin-selector-container {
  display: flex;
  align-items: center;
}

.compact-select {
  width: 100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
}

:deep(.compact-select .v-field) {
  border-radius: 8px !important;
  color: white !important;
}

:deep(.compact-select .v-field__outline) {
  --v-field-border-opacity: 0.3 !important;
}

.mobile-coin-btn {
  background: rgba(255, 255, 255, 0.15) !important;
  font-weight: 800;
  width: 40px !important;
  height: 40px !important;
}

.currency-label {
  font-size: 10px;
  font-weight: 900;
}
</style>

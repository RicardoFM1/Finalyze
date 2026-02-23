<template>
  <div class="coin-selector-container">
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
        <div class="d-flex align-center gap-3">
          <img :src="item.raw.flag" class="coin-flag-img shadow-sm" />
          <span class="coin-code">{{ item.value }}</span>
        </div>
      </template>

      <template v-slot:item="{ item, props: itemProps }">
        <v-list-item v-bind="itemProps" :title="undefined" density="compact">
          <div class="d-flex align-center gap-4 py-1">
            <img :src="item.raw.flag" class="coin-flag-img mr-1" />
            <div>
              <div class="text-body-2 font-weight-bold">{{ item.value }}</div>
              <div class="text-caption opacity-60">{{ item.raw.name }}</div>
            </div>
          </div>
        </v-list-item>
      </template>
    </v-select>

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
          <div class="d-flex align-center gap-1">
            <img v-if="selectedCoinObject?.flag" :src="selectedCoinObject.flag" class="coin-flag-img" />
            <span class="currency-label ml-1">{{ selectedCoin }}</span>
          </div>
        </v-btn>
      </template>

      <v-list class="rounded-lg shadow-lg" width="180">
        <v-list-item
          v-for="coin in coins"
          :key="coin.value"
          @click="selectedCoin = coin.value"
          :active="selectedCoin === coin.value"
          color="primary"
          density="compact"
        >
          <template v-slot:prepend>
            <img :src="coin.flag" class="coin-flag-img mr-2" />
          </template>

          <v-list-item-title class="text-body-2">
            {{ coin.value }}
          </v-list-item-title>

          <v-list-item-subtitle class="text-caption">
            {{ coin.name }}
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useCurrencyStore } from '../../stores/currency'
import { useDisplay } from 'vuetify'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const { mdAndUp } = useDisplay()
const currencyStore = useCurrencyStore()

const coins = computed(() => [
  {
    label: 'BRL',
    value: 'BRL',
    name: t('common.currencies.brl'),
    flag: 'https://flagcdn.com/w40/br.png'
  },
  {
    label: 'USD',
    value: 'USD',
    name: t('common.currencies.usd'),
    flag: 'https://flagcdn.com/w40/us.png'
  },
  {
    label: 'EUR',
    value: 'EUR',
    name: t('common.currencies.eur'),
    flag: 'https://flagcdn.com/w40/eu.png'
  },
  {
    label: 'GBP',
    value: 'GBP',
    name: t('common.currencies.gbp'),
    flag: 'https://flagcdn.com/w40/gb.png'
  }
])

const selectedCoin = ref(currencyStore.currency)

const selectedCoinObject = computed(() =>
  coins.value.find(c => c.value === selectedCoin.value)
)

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
  width: 120px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
}

:deep(.compact-select .v-field) {
  border-radius: 8px !important;
  color: white !important;
  font-size: 12px !important;
  min-height: 34px !important;
}

:deep(.compact-select .v-field__input) {
  font-size: 12px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  min-height: 34px !important;
}

:deep(.compact-select .v-field__outline) {
  --v-field-border-opacity: 0.3 !important;
}

:deep(.compact-select .v-field__append-inner .v-icon) {
  font-size: 16px !important;
  opacity: 0.7;
}

.coin-flag-img {
  width: 18px;
  height: 18px;
  object-fit: cover;
  border-radius: 4px;
  box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.25);
}

.coin-code {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.3px;
  color: white;
}

.mobile-coin-btn {
  background: rgba(255, 255, 255, 0.15) !important;
  font-weight: 800;
  width: 44px !important;
  height: 44px !important;
}

.currency-label {
  font-size: 9px;
  font-weight: 900;
}
</style>
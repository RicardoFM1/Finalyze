<script setup>
import { useUiStore } from './stores/ui'
import { useCurrencyStore } from './stores/currency'
import { onMounted } from 'vue'

const ui = useUiStore()
const currencyStore = useCurrencyStore()

onMounted(() => {
  currencyStore.loadCurrency()
})
</script>

<template>
  <v-app>
   
    <router-view />

    <v-overlay
      v-model="ui.loading"
      class="align-center justify-center"
      persistent
      absolute
    >
      <v-progress-circular indeterminate color="primary" size="64" />
    </v-overlay>
  </v-app>
</template>

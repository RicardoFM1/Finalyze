<script setup>
import MainLayout from './components/MainLayout.vue'
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
  <v-app :theme="ui.theme">
    <MainLayout />

    <v-overlay v-model="ui.loading" class="align-center justify-center" persistent absolute>
      <v-progress-circular  indeterminate color="primary" size="64"/>
    </v-overlay>

  </v-app>
</template>





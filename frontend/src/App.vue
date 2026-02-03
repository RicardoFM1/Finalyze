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
  <v-app>
    <MainLayout />


    <v-overlay v-model="ui.loading" class="align-center justify-center" persistent absolute>
      <v-progress-circular  indeterminate color="primary" size="64"/>
    </v-overlay>

  </v-app>
</template>



<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>

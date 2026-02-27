<script setup>
import MainLayout from './components/MainLayout.vue'
import { useUiStore } from './stores/ui'
import { useCurrencyStore } from './stores/currency'
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
const ui = useUiStore()
const { t } = useI18n()
const currencyStore = useCurrencyStore()

onMounted(() => {
  currencyStore.loadCurrency()

})

</script>

<template>
  <v-app :theme="ui.theme">
    <MainLayout />

    <v-overlay v-model="ui.loading" class="align-center justify-center" persistent>
      <div class="d-flex flex-column align-center">
        <v-progress-circular  indeterminate color="primary" size="64" width="6" class="mb-4"/>
        <h2 class="text-h5 font-weight-bold gradient-text">{{ t('landing.loading_system') }}</h2>
      </div>
    </v-overlay>

  </v-app>
</template>


<template>
  <v-container>
    <div class="d-flex align-center mb-6">
      <v-icon icon="mdi-cog" color="primary" size="32" class="mr-3"></v-icon>
      <h1 class="text-h4 font-weight-bold">{{ $t('sidebar.settings') || 'Configurações' }}</h1>
    </div>

    <v-row>
      <v-col cols="12" md="6">
        <v-card class="rounded-xl pa-6" elevation="1">
          <div class="d-flex align-center mb-6">
            <v-icon icon="mdi-translate" color="primary" class="mr-3"></v-icon>
            <h3 class="text-h6 font-weight-bold">{{ $t('profile.settings.language') }}</h3>
          </div>

          <div class="management-item d-flex flex-column mb-6">
            <div class="mb-4">
              <div class="font-weight-bold">{{ $t('profile.settings.language') }}</div>
              <div class="text-body-2 text-medium-emphasis">{{ $t('profile.settings.language_desc') }}</div>
            </div>
            <LanguageSelector class="w-100" style="max-width: 100% !important" />
          </div>

          <v-divider class="my-4 opacity-30" />

          <div class="management-item d-flex flex-column">
            <div class="mb-4">
              <div class="font-weight-bold">{{ $t('landing.select_currency') }}</div>
              <div class="text-body-2 text-medium-emphasis">{{ $t('settings.currency_desc') || 'Formato de exibição dos valores monetários' }}</div>
            </div>
            <CurrencySelector class="w-100" style="max-width: 100% !important" />
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import LanguageSelector from '../components/Language/LanguageSelector.vue'
import CurrencySelector from '../components/Language/CurrencySelector.vue'
</script>

<style scoped>
.management-item {
  min-height: 64px;
}
</style>

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
            <v-icon icon="mdi-palette-outline" color="primary" class="mr-3"></v-icon>
            <h3 class="text-h6 font-weight-bold">{{ $t('settings.appearance') || 'Aparência' }}</h3>
          </div>
          
          <div class="management-item d-flex align-center justify-space-between mb-2">
            <div>
              <div class="font-weight-bold">{{ $t('settings.theme') || 'Tema do Sistema' }}</div>
              <div class="text-body-2 text-medium-emphasis">
                {{ uiStore.theme === 'light' ? 'Modo Claro Ativo' : 'Modo Escuro Ativo' }}
              </div>
            </div>
            <v-btn-toggle
              v-model="uiStore.theme"
              mandatory
              color="primary"
              variant="tonal"
              rounded="lg"
              @update:model-value="saveTheme"
            >
              <v-btn value="light">
                <v-icon icon="mdi-white-balance-sunny"></v-icon>
              </v-btn>
              <v-btn value="dark">
                <v-icon icon="mdi-moon-waning-crescent"></v-icon>
              </v-btn>
            </v-btn-toggle>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="rounded-xl pa-6" elevation="1">
          <div class="d-flex align-center mb-6">
            <v-icon icon="mdi-translate" color="primary" class="mr-3"></v-icon>
            <h3 class="text-h6 font-weight-bold">{{ $t('profile.settings.language') }}</h3>
          </div>

          <div class="management-item d-flex align-center justify-space-between mb-2">
            <div>
              <div class="font-weight-bold">{{ $t('profile.settings.language') }}</div>
              <div class="text-body-2 text-medium-emphasis">{{ $t('profile.settings.language_desc') }}</div>
            </div>
            <LanguageSelector />
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useUiStore } from '../stores/ui'
import LanguageSelector from '../components/Language/LanguageSelector.vue'

const uiStore = useUiStore()

const saveTheme = (val) => {
  localStorage.setItem('theme', val)
}
</script>

<style scoped>
.management-item {
  min-height: 64px;
}
</style>

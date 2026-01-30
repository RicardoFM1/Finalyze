<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6" class="text-center text-md-left">
        <h1 class="text-h2 font-weight-bold mb-4 text-primary">{{ $t('landing.hero_title') }}</h1>
        <p class="text-h5 text-medium-emphasis mb-6">
          {{ $t('landing.hero_subtitle') }}
        </p>
        <div class="d-flex gap-4 justify-center justify-md-start">
            <v-btn v-if="authStore.user?.plan_id == null" color="primary" size="x-large" to="/planos" elevation="4">{{ $t('landing.btn_plans') }}</v-btn>
            <v-btn v-if="!authStore.isAuthenticated" variant="outlined" size="x-large" class="ml-4" to="/login">{{ $t('landing.btn_login') }}</v-btn>
            <v-btn v-else-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" variant="outlined" size="x-large" class="ml-4" to="/painel">{{ $t('landing.btn_dashboard') }}</v-btn>
            <v-btn v-if="authStore.user?.role === 'admin'" variant="outlined" size="x-large" class="ml-4" to="/admin">{{ $t('landing.btn_admin') }}</v-btn>
        </div>
      </v-col>
      
      <v-col cols="12" md="6" class="d-flex justify-center">
        <v-icon size="300" color="primary" icon="mdi-finance"></v-icon>
      </v-col>
    </v-row>

    <v-row class="mt-16">
      <v-col cols="12" class="text-center mb-8">
        <h2 class="text-h3 font-weight-bold">{{ $t('landing.why_title') }}</h2>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-chart-line</v-icon>
            <v-card-title class="text-h5 font-weight-bold">{{ $t('landing.features.analysis_title') }}</v-card-title>
            <v-card-text>{{ $t('landing.features.analysis_text') }}</v-card-text>
          </v-card-item>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-lock-check</v-icon>
            <v-card-title class="text-h5 font-weight-bold">{{ $t('landing.features.security_title') }}</v-card-title>
            <v-card-text>{{ $t('landing.features.security_text') }}</v-card-text>
          </v-card-item>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-bullseye-arrow</v-icon>
            <v-card-title class="text-h5 font-weight-bold">{{ $t('landing.features.goals_title') }}</v-card-title>
            <v-card-text>{{ $t('landing.features.goals_text') }}</v-card-text>
          </v-card-item>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
const authStore = useAuthStore()
</script>

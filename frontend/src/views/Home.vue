<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6" class="text-center text-md-left">
        <h1 class="text-h5 text-md-h2 font-weight-bold mb-3 mb-md-4 text-primary">Domine suas Finanças com Finalyze</h1>
        <p class="text-body-2 text-md-h5 text-medium-emphasis mb-4 mb-md-6">
          Acompanhe despesas, monitore receitas e alcance seus objetivos financeiros com nossa plataforma intuitiva.
          Junte-se a milhares de usuários que assumiram o controle do seu dinheiro.
        </p>
        <div class="d-flex gap-2 gap-md-4 justify-center justify-md-start">
            <v-btn color="primary" size="large" class="py-2 py-md-4 text-body-2 text-md-body-1" to="/planos" elevation="4">Ver Planos</v-btn>
            <v-btn v-if="!authStore.isAuthenticated" variant="outlined" size="large" class="py-2 py-md-4 text-body-2 text-md-body-1" to="/login">Entrar</v-btn>
            <v-btn v-else-if="authStore.user?.role !== 'admin'" variant="outlined" size="large" class="py-2 py-md-4 text-body-2 text-md-body-1 ml-4" to="/painel">Ir para Painel</v-btn>
            <v-btn v-else variant="outlined" size="large" class="py-2 py-md-4 text-body-2 text-md-body-1 ml-4" to="/admin">Painel Admin</v-btn>
        </div>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-center">
        <v-icon
  size="160"
  size-md="300"
  color="primary"
  icon="mdi-finance"
/>
      </v-col>
    </v-row>

    <v-row class="mt-16">
      <v-col cols="12" class="text-center mb-8">
        <h2 class="text-h3 font-weight-bold">Por que Finalyze?</h2>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-chart-line</v-icon>
            <v-card-title class="text-h5 font-weight-bold">Análise Inteligente</v-card-title>
            <v-card-text>Visualize seus hábitos de consumo com gráficos e relatórios detalhados.</v-card-text>
          </v-card-item>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-lock-check</v-icon>
            <v-card-title class="text-h5 font-weight-bold">SegurançaTotal</v-card-title>
            <v-card-text>Seus dados financeiros são criptografados e protegidos com segurança de nível bancário.</v-card-text>
          </v-card-item>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="h-100 pa-4" variant="tonal">
          <v-card-item>
            <v-icon size="48" color="primary" class="mb-4">mdi-bullseye-arrow</v-icon>
            <v-card-title class="text-h5 font-weight-bold">Metas Financeiras</v-card-title>
            <v-card-text>Defina metas financeiras e acompanhe seu progresso automaticamente.</v-card-text>
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





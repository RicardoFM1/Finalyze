<template>
  <v-container v-if="authStore.hasFeature('Relatórios')">
    <v-row class="mb-6 pt-4" align="center">
      <v-col cols="12" md="6">
        <h1 class="text-h3 font-weight-bold mb-1 gradient-text">{{ $t('reports.title') }}</h1>
        <p class="text-h6 text-medium-emphasis font-weight-medium">{{ $t('reports.subtitle') }}</p>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-md-end">
        <v-select
          v-model="mesesRange"
          :items="rangeOptions"
          :label="$t('reports.view_period')"
          variant="solo-filled"
          density="comfortable"
          rounded="xl"
          hide-details
          class="history-selector"
          @update:model-value="fetchData"
        ></v-select>
      </v-col>
    </v-row>

    <v-card class="rounded-xl glass-card border-card pa-6" elevation="8">
        <v-card-text v-if="loaded" style="height: 450px; position: relative;">
            <Bar :data="chartData" :options="chartOptions" />
        </v-card-text>
        <v-card-text v-else class="h-100 d-flex flex-column align-center justify-center py-12" style="min-height: 400px;">
             <v-progress-circular indeterminate size="64" width="6" color="primary" class="mb-4"></v-progress-circular>
             <div class="text-h6 text-medium-emphasis">{{ $t('reports.loading') }}</div>
        </v-card-text>
    </v-card>
  </v-container>
  <v-container v-else class="text-center py-12">
    <v-icon icon="mdi-lock" size="64" color="medium-emphasis" class="mb-4"></v-icon>
    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('reports.restricted_access') }}</h2>
    <p class="text-h6 text-medium-emphasis mb-6">{{ $t('reports.restricted_subtitle') }}</p>
    <v-btn color="primary" size="large" rounded="xl" :to="{ name: 'Plans' }">{{ $t('reports.view_plans') }}</v-btn>
  </v-container>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const { t } = useI18n()
const authStore = useAuthStore()
const loaded = ref(false)
const mesesRange = ref(6)

const rangeOptions = computed(() => [
  { title: t('reports.last_3'), value: 3 },
  { title: t('reports.last_6'), value: 6 },
  { title: t('reports.last_12'), value: 12 },
  { title: t('reports.last_24'), value: 24 },
])

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: { family: 'Inter', size: 12 }
            }
        },
        tooltip: {
            padding: 12,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: 'rgba(0,0,0,0.1)',
            borderWidth: 1,
            bodyFont: { family: 'Inter' }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.05)' }
        },
        x: {
            grid: { display: false }
        }
    }
})

const rawData = ref({ labels: [], datasets: [] })

const chartData = computed(() => {
    if (!rawData.value.datasets) return { labels: [], datasets: [] }
    return {
        ...rawData.value,
        datasets: rawData.value.datasets.map(dataset => ({
            ...dataset,
            label: dataset.label === 'Receitas' ? t('transactions.type.income') : (dataset.label === 'Despesas' ? t('transactions.type.expense') : dataset.label)
        }))
    }
})

const fetchData = async () => {
    if (!authStore.hasFeature('Relatórios')) return
    loaded.value = false
    try {
        const response = await authStore.apiFetch(`/relatorios/mensal?meses=${mesesRange.value}`)
        const data = await response.json()
        
        rawData.value = data
        loaded.value = true
    } catch (e) {
        console.error(e)
    }
}

onMounted(fetchData)
</script>

<style scoped>
.gradient-text {
  background: linear-gradient(90deg, rgb(var(--v-theme-primary)), #11998E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.05) !important;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.07) !important;
}

.history-selector {
  max-width: 250px;
}
</style>

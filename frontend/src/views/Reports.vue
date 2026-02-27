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

    <v-row>
        <!-- Gráfico de Evolução (Área) -->
        <v-col cols="12" lg="8">
            <v-card class="rounded-xl glass-card border-card pa-6 h-100" elevation="8">
                <v-card-title class="px-0 pb-4 font-weight-bold d-flex align-center">
                    <v-icon icon="mdi-chart-areaspline" color="primary" class="mr-2"></v-icon>
                    {{ $t('reports.evolution_title') || 'Fluxo de Caixa' }}
                </v-card-title>
                <v-card-text v-if="loaded" style="height: 400px; position: relative;" class="px-0">
                    <Line :data="areaChartData" :options="areaChartOptions" />
                </v-card-text>
                <v-card-text v-else class="h-100 d-flex flex-column align-center justify-center py-12">
                    <v-progress-circular indeterminate size="64" width="6" color="primary" class="mb-4"></v-progress-circular>
                    <div class="text-h6 text-medium-emphasis">{{ $t('reports.loading') }}</div>
                </v-card-text>
            </v-card>
        </v-col>

        <!-- Gráfico de Categorias (Rosquinha) -->
        <v-col cols="12" lg="4">
            <v-card class="rounded-xl glass-card border-card pa-6 h-100" elevation="8">
                <v-card-title class="px-0 pb-4 font-weight-bold d-flex align-center">
                    <v-icon icon="mdi-chart-pie" color="secondary" class="mr-2"></v-icon>
                    {{ $t('reports.categories_title') || 'Distribuição de Gastos' }}
                </v-card-title>
                <v-card-text v-if="loaded" style="height: 300px; position: relative;" class="d-flex flex-column align-center justify-center">
                    <div style="width: 100%; height: 250px;">
                        <Doughnut v-if="categoryData.datasets[0].data.length" :data="categoryData" :options="categoryOptions" />
                        <div v-else class="d-flex flex-column align-center justify-center h-100 opacity-30">
                            <v-icon icon="mdi-chart-donut" size="48"></v-icon>
                            <p>{{ $t('reports.no_expenses') || 'Sem despesas no período' }}</p>
                        </div>
                    </div>
                </v-card-text>
                <div v-if="loaded && apiResponse?.categories?.length" class="mt-4">
                    <div v-for="(cat, i) in apiResponse.categories.slice(0, 4)" :key="cat.categoria" class="d-flex align-center justify-space-between mb-2">
                        <div class="d-flex align-center overflow-hidden">
                            <div class="category-dot mr-2" :style="{ backgroundColor: getCategoryColor(i) }"></div>
                            <span class="text-caption text-truncate">{{ $t('categories.' + cat.categoria) }}</span>
                        </div>
                        <span class="text-caption font-weight-bold ml-2">{{ currencySymbol }} {{ formatNumber(cat.total) }}</span>
                    </div>
                </div>
            </v-card>
        </v-col>

        <!-- Gráfico de Balanço Líquido -->
        <v-col cols="12">
            <v-card class="rounded-xl glass-card border-card pa-6" elevation="8">
                <v-card-title class="px-0 pb-4 font-weight-bold d-flex align-center">
                    <v-icon icon="mdi-finance" color="success" class="mr-2"></v-icon>
                    {{ $t('reports.net_balance_title') || 'Balanço Mensal' }}
                </v-card-title>
                <v-card-text v-if="loaded" style="height: 250px; position: relative;" class="px-0">
                    <Bar :data="balanceChartData" :options="balanceChartOptions" />
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
  </v-container>
  <v-container v-else class="text-center py-12">
    <v-icon icon="mdi-lock" size="64" color="medium-emphasis" class="mb-4"></v-icon>
    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('reports.restricted_access') }}</h2>
    <p class="text-h6 text-medium-emphasis mb-6">{{ $t('reports.restricted_subtitle') }}</p>
    <v-btn color="primary" size="large" rounded="xl" :to="{ name: 'Plans' }">{{ $t('reports.view_plans') }}</v-btn>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Line, Doughnut, Bar } from 'vue-chartjs'
import { 
    Chart as ChartJS, 
    Title, Tooltip, Legend, 
    PointElement, LineElement, 
    BarElement, CategoryScale, LinearScale, 
    ArcElement, Filler 
} from 'chart.js'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'
import { useMoney } from '../composables/useMoney'

ChartJS.register(
    Title, Tooltip, Legend, 
    PointElement, LineElement, 
    BarElement, CategoryScale, LinearScale, 
    ArcElement, Filler
)

const { t } = useI18n()
const { formatNumber, currencySymbol } = useMoney()
const authStore = useAuthStore()
const loaded = ref(false)
const mesesRange = ref(6)
const apiResponse = ref(null)

const rangeOptions = computed(() => [
  { title: t('reports.last_3'), value: 3 },
  { title: t('reports.last_6'), value: 6 },
  { title: t('reports.last_12'), value: 12 },
  { title: t('reports.last_24'), value: 24 },
])

const areaChartData = computed(() => {
    if (!apiResponse.value) return { labels: [], datasets: [] }
    return {
        labels: apiResponse.value.labels,
        datasets: [
            {
                label: t('transactions.type.income'),
                data: apiResponse.value.datasets.income,
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4CAF50'
            },
            {
                label: t('transactions.type.expense'),
                data: apiResponse.value.datasets.expense,
                borderColor: '#F44336',
                backgroundColor: 'rgba(244, 67, 54, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#F44336'
            }
        ]
    }
})

const areaChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, font: { family: 'Inter', size: 12 } } },
        tooltip: {
            mode: 'index',
            intersect: false,
            padding: 12,
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: 'rgba(0,0,0,0.1)',
            borderWidth: 1,
            callbacks: {
                label: (context) => ` ${context.dataset.label}: ${currencySymbol.value} ${formatNumber(context.raw)}`
            }
        }
    },
    scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: (v) => `${currencySymbol.value}${formatNumber(v)}` } },
        x: { grid: { display: false } }
    }
}

const balanceChartData = computed(() => {
    if (!apiResponse.value) return { labels: [], datasets: [] }
    return {
        labels: apiResponse.value.labels,
        datasets: [{
            label: t('features.net') || 'Saldo',
            data: apiResponse.value.datasets.balance,
            backgroundColor: apiResponse.value.datasets.balance.map(v => v >= 0 ? 'rgba(76, 175, 80, 0.6)' : 'rgba(244, 67, 54, 0.6)'),
            borderRadius: 6
        }]
    }
})

const balanceChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => ` ${t('features.net')}: ${currencySymbol.value} ${formatNumber(context.raw)}`
            }
        }
    },
    scales: {
        y: { grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
    }
}

const categoryColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF']
const getCategoryColor = (i) => categoryColors[i % categoryColors.length]

const categoryData = computed(() => {
    if (!apiResponse.value?.categories) return { labels: [], datasets: [] }
    const cats = apiResponse.value.categories
    return {
        labels: cats.map(c => t('categories.' + c.categoria)),
        datasets: [{
            data: cats.map(c => c.total),
            backgroundColor: categoryColors,
            borderWidth: 0,
            hoverOffset: 15
        }]
    }
})

const categoryOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => ` ${context.label}: ${currencySymbol.value} ${formatNumber(context.raw)}`
            }
        }
    },
    cutout: '70%'
}

const fetchData = async () => {
    if (!authStore.hasFeature('Relatórios')) return
    loaded.value = false
    try {
        const response = await authStore.apiFetch(`/relatorios/mensal?meses=${mesesRange.value}`)
        apiResponse.value = await response.json()
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

.category-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
</style>

<template>
  <v-container class="dashboard-wrapper">
    <v-row class="mb-4 pt-4" align="center">
      <v-col cols="12">
        <div class="d-flex align-center">
            <v-avatar color="primary" size="64" class="mr-4 elevation-4 d-none d-sm-flex">
                <v-icon icon="mdi-view-dashboard-outline" color="white" size="32"></v-icon>
            </v-avatar>
            <div class="w-100">
                <h1 class="text-h4 text-sm-h4 text-md-h3 font-weight-bold mb-1 gradient-text text-truncate">{{ $t('landing.home_welcome') }}, {{ authStore.user?.nome || $t('common.welcome_fallback') }}!</h1>
                <p class="text-subtitle-1 text-md-h6 text-medium-emphasis font-weight-medium">{{ $t('landing.home_subtitle') }}</p>
            </div>
        </div>
      </v-col>
    </v-row>

    <FilterLancamentos
      v-model="filterStore.filters"
      :categorias="categorias"
      @apply="fetchSummary"
      @clear="limparFiltro"
      class="mb-8"
      macro
    />

    <v-row class="mb-8 px-2">
        <v-col v-if="loading" cols="12">
            <v-row>
                <v-col v-for="i in 3" :key="i" cols="12" md="4">
                    <v-skeleton-loader type="card" class="rounded-xl"></v-skeleton-loader>
                </v-col>
            </v-row>
        </v-col>

        <template v-else>
          <v-col cols="12" sm="12" md="12" lg="4">
            <v-card class="summary-card glass-card receita-gradient rounded-xl overflow-hidden" elevation="0">
              <v-card-item class="pa-6">
                <div class="d-flex justify-space-between align-center mb-6">
                    <div class="icon-circle success-circle">
                      <v-icon icon="mdi-trending-up" color="success" size="24"></v-icon>
                    </div>
                    <span class="text-overline font-weight-bold opacity-70">{{ $t('features.RE') }}</span>
                </div>
                <div class="value-container font-weight-bold mb-1" :style="{ fontSize: dynamicFontSize(resumo.receita) }">
                  <span class="currency-symbol">{{ $t('common.currency') }}</span>
                  <span class="amount-value">{{ formatNumber(resumo.receita) }}</span>
                </div>
                <div class="text-subtitle-2 opacity-80 font-weight-medium">{{ $t('features.total_income_month') }}</div>
              </v-card-item>
              <div class="card-blur-bg"></div>
            </v-card>
          </v-col>
          <v-col cols="12" sm="12" md="12" lg="4">
            <v-card class="summary-card glass-card despesa-gradient rounded-xl overflow-hidden" elevation="0">
              <v-card-item class="pa-6">
                <div class="d-flex justify-space-between align-center mb-6">
                    <div class="icon-circle error-circle">
                      <v-icon icon="mdi-trending-down" color="error" size="24"></v-icon>
                    </div>
                    <span class="text-overline font-weight-bold opacity-70">{{ $t('features.DS') }}</span>
                </div>
                <div class="value-container font-weight-bold mb-1" :style="{ fontSize: dynamicFontSize(resumo.despesa) }">
                  <span class="currency-symbol">{{ $t('common.currency') }}</span>
                  <span class="amount-value">{{ formatNumber(resumo.despesa) }}</span>
                </div>
                <div class="text-subtitle-2 opacity-80 font-weight-medium">{{ $t('features.total_expense_month') }}</div>
              </v-card-item>
              <div class="card-blur-bg"></div>
            </v-card>
          </v-col>
          <v-col cols="12" sm="12" md="12" lg="4">
            <v-card class="summary-card glass-card saldo-gradient rounded-xl overflow-hidden" elevation="0">
              <v-card-item class="pa-6">
                <div class="d-flex justify-space-between align-center mb-6">
                    <div class="icon-circle info-circle">
                      <v-icon icon="mdi-wallet-outline" color="primary" size="24"></v-icon>
                    </div>
                    <span class="text-overline font-weight-bold opacity-70">{{ $t('features.balance') }} ({{ $t('features.net') }})</span>
                </div>
                <div class="value-container font-weight-bold mb-1" :style="{ fontSize: dynamicFontSize(resumo.saldo) }">
                  <span class="currency-symbol">{{ $t('common.currency') }}</span>
                  <span class="amount-value">{{ formatNumber(resumo.saldo) }}</span>
                </div>
                <div class="text-subtitle-2 opacity-80 font-weight-medium">{{ $t('features.net_worth_today') }}</div>
              </v-card-item>
              <div class="card-blur-bg"></div>
            </v-card>
          </v-col>
      </template>
    </v-row>

    <v-row>
      <v-col cols="12" md="8">
        <v-card class="rounded-xl mb-8 glass-card border-card overflow-hidden" elevation="4">
          <v-card-title class="font-weight-bold pa-6 d-flex align-center">
            <v-icon icon="mdi-chart-donut" color="primary" class="mr-2"></v-icon>
            {{ $t('features.financial_health') }}
            <v-spacer></v-spacer>
            <div class="text-caption text-medium-emphasis font-weight-medium">{{ $t('features.resource_distribution') }}</div>
          </v-card-title>
          <v-card-text class="pa-6">
            <v-row class="align-center">
              <v-col cols="12" md="5" class="d-flex justify-center">
                <div style="max-width: 250px; position: relative;">
                  <Doughnut v-if="!loading" :data="chartData" :options="chartOptions" :key="JSON.stringify(chartData.datasets[0].data)" />
                  <div class="chart-center-text" v-if="!loading">
                    <div class="text-h5 font-weight-bold">{{ getMarginPercentage }}%</div>
                    <div class="text-caption">{{ $t('features.margin') }}</div>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="7">
                <div class="mb-4 d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <div class="dot receita-dot mr-2"></div>
                    <span class="text-body-2">{{ $t('features.incomes') }}</span>
                  </div>
                  <span class="text-body-2 font-weight-bold">{{ $t('common.currency') }} {{ formatNumber(resumo.receita) }}</span>
                </div>
                <v-divider class="mb-4"></v-divider>
                <div class="mb-4 d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <div class="dot despesa-dot mr-2"></div>
                    <span class="text-body-2">{{ $t('features.expenses') }}</span>
                  </div>
                  <span class="text-body-2 font-weight-bold">{{ $t('common.currency') }} {{ formatNumber(resumo.despesa) }}</span>
                </div>
                <v-divider class="mb-4"></v-divider>
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <div class="dot saldo-dot mr-2"></div>
                    <span class="text-body-2">{{ $t('features.net') }}</span>
                  </div>
                  <span class="text-body-2 font-weight-bold" :class="resumo.saldo >= 0 ? 'text-success' : 'text-error'">
                    {{ $t('common.currency') }} {{ formatNumber(resumo.saldo) }}
                  </span>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card class="rounded-xl recent-activity overflow-hidden glass-card border-card" elevation="4">
           <v-toolbar color="transparent" density="comfortable" class="px-4 py-2">
               <v-toolbar-title class="font-weight-bold">
                    {{ filterStore.filters.data || filterStore.filters.descricao || filterStore.filters.categoria || (filterStore.filters.tipo && filterStore.filters.tipo !== 'todos') || filterStore.filters.valor ? 'Movimentações no Período' : $t('features.recent_transactions') }}
                </v-toolbar-title>
               <v-spacer></v-spacer>
               <v-btn variant="tonal" size="small" color="primary" class="rounded-lg" :to="{ name: 'Lancamentos' }">{{ $t('features.view_history') }}</v-btn>
           </v-toolbar>
           <v-list lines="two" class="pa-4 bg-transparent">
              <v-list-item v-for="item in resumo.atividades_recentes" :key="item.id" 
                class="rounded-xl mb-3 hover-item border-item"
                :title="item.descricao || item.categoria" 
                :subtitle="`${item.tipo === 'receita' ? $t('transactions.type.income') : $t('transactions.type.expense')} • ${item.categoria}`" 
              >
                <template v-slot:prepend>
                    <v-avatar :color="item.tipo === 'receita' ? 'success-lighten-5' : 'error-lighten-5'" rounded="lg" size="48">
                        <v-icon :icon="item.tipo === 'receita' ? 'mdi-plus' : 'mdi-minus'" :color="item.tipo === 'receita' ? 'success' : 'error'"></v-icon>
                    </v-avatar>
                </template>
                <template v-slot:append>
                    <div class="d-flex align-center">
                        <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="text-h6 font-weight-bold mr-4">
                            {{ item.tipo === 'receita' ? '+' : '-' }} {{ $t('common.currency') }} {{ formatNumber(item.valor) }}
                        </span>
                        <div class="d-flex gap-1">
                            <v-btn icon="mdi-pencil-outline" size="x-small" variant="text" color="primary" @click.stop="abrirEditar(item)"></v-btn>
                            <v-btn icon="mdi-delete-outline" size="x-small" variant="text" color="error" @click.stop="abrirExcluir(item)"></v-btn>
                        </div>
                    </div>
                </template>
              </v-list-item>
              <div v-if="!resumo.atividades_recentes?.length" class="text-center pa-10 text-medium-emphasis">
                  <v-icon icon="mdi-history" size="64" class="mb-4 opacity-10"></v-icon>
                  <p class="text-h6 font-weight-medium">{{ $t('features.Ne') }}</p>
              </div>
           </v-list>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="rounded-xl mb-6 glass-card border-card quick-actions-gradient" elevation="4">
            <v-card-title class="font-weight-bold pa-6 pb-2 text-white">{{ $t('features.quick_access') }}</v-card-title>
            <v-card-text class="pa-6 pt-2">
                <v-btn v-if="authStore.hasFeature('Lançamentos')" block color="white" min-height="56" class="mb-4 rounded-xl text-primary font-weight-bold py-3" prepend-icon="mdi-plus-circle" @click="dialog = true" elevation="2">
                  {{ $t('features.launch_now') }}
                </v-btn>
                <v-row dense>
                    <v-col cols="12" sm="12" lg="6" v-if="authStore.hasFeature('Relatórios')">
                        <v-btn block min-height="48" variant="tonal" color="white" class="rounded-xl font-weight-bold mb-2 py-2" prepend-icon="mdi-poll" :to="{ name: 'Reports' }">Relatórios</v-btn>
                    </v-col>
                    <v-col cols="12" sm="12" lg="6" v-if="authStore.hasFeature('Metas')">
                        <v-btn block min-height="48" variant="tonal" color="white" class="rounded-xl font-weight-bold py-2" prepend-icon="mdi-target" :to="{ name: 'Metas' }">Metas</v-btn>
                    </v-col>
                </v-row>
                <div v-if="!authStore.hasFeature('Lançamentos') && !authStore.hasFeature('Relatórios') && !authStore.hasFeature('Metas')" class="text-white opacity-70 text-center py-4">
                  Seu plano não possui acesso rápido disponível.
                </div>
            </v-card-text>
        </v-card>

        <v-card v-if="authStore.hasFeature('Metas') && metasSummary.length" class="rounded-xl glass-card border-card metas-preview" elevation="4">
            <v-card-title class="font-weight-bold pa-6 pb-2 d-flex align-center">
                <v-icon icon="mdi-target" color="primary" class="mr-2"></v-icon>
                {{ $t('features.goals_progress') }}
            </v-card-title>
            <v-card-text class="pa-6 pt-2">
                <div v-for="meta in metasSummary" :key="meta.id" class="mb-6">
                    <div class="d-flex justify-space-between text-body-2 mb-2 font-weight-medium">
                        <span class="text-truncate mr-2">{{ meta.titulo }}</span>
                        <span v-if="meta.status === 'concluido'" class="text-success font-weight-bold">Concluído!</span>
                        <span v-else class="text-primary">{{ calculatePercentage(meta) }}%</span>
                    </div>
                    <v-progress-linear
                        :model-value="calculatePercentage(meta)"
                        :color="meta.status === 'concluido' ? 'success' : (meta.cor || 'primary')"
                        height="10"
                        rounded
                        striped
                    ></v-progress-linear>
                </div>
                <v-btn block variant="tonal" color="primary" class="rounded-xl mt-2 font-weight-bold" :to="{ name: 'Metas' }">{{ $t('features.explore_goals') }}</v-btn>
            </v-card-text>
        </v-card>

        <v-card v-else-if="authStore.hasFeature('Metas')" class="rounded-xl glass-card border-card pa-6 text-center" elevation="4">
          <v-icon icon="mdi-flag-plus-outline" size="48" color="primary" class="mb-4 opacity-30"></v-icon>
          <div class="text-h6 font-weight-bold mb-2">{{ $t('features.define_goals') }}</div>
          <p class="text-body-2 text-medium-emphasis mb-4">{{ $t('features.goals_subtitle') }}</p>
          <v-btn color="primary" variant="flat" class="rounded-xl" :to="{ name: 'Metas' }">{{ $t('features.start_goals') }}</v-btn>
        </v-card>
      </v-col>
    </v-row>

    <ModalNovoLancamento v-model="dialog" @saved="fetchSummary" />
    <ModalEditarLancamento v-model="dialogEditar" :lancamento="itemAEditar" @updated="fetchSummary" />
    <ModalExcluirLancamento v-model="dialogExcluir" :lancamentoId="lancamentoIdExcluir" @deleted="fetchSummary" />
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useFilterStore } from '../stores/filters'
import { toast } from 'vue3-toastify'
import ModalNovoLancamento from '../components/Modals/Lancamentos/ModalNovoLancamento.vue'
import ModalEditarLancamento from '../components/Modals/Lancamentos/ModalEditarLancamento.vue'
import ModalExcluirLancamento from '../components/Modals/Lancamentos/ModalExcluirLancamento.vue'
import FilterLancamentos from '../components/Filters/Filter.vue'
import DateInput from '../components/Common/DateInput.vue'
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)
import { useI18n } from 'vue-i18n'
import { watch } from 'vue'
const { t } = useI18n()


import { categorias as categoriasConstantes } from '../constants/categorias'

const authStore = useAuthStore()
const filterStore = useFilterStore()
const dialog = ref(false)
const loading = ref(true)
const metasSummary = ref([])

const categorias = computed(() => {
  try {
    return categoriasConstantes.map(c => c.title)
  } catch (e) {
    return []
  }
})

const itemAEditar = ref(null)
const lancamentoIdExcluir = ref(null)
const dialogEditar = ref(false)
const dialogExcluir = ref(false)

const limparFiltro = () => {
    filterStore.clearFilters()
    fetchSummary()
}

const getGreeting = computed(() => {
    const hour = new Date().getHours()
    if (hour >= 5 && hour < 12) return 'Bom dia'
    if (hour >= 12 && hour < 18) return 'Boa tarde'
    return 'Boa noite'
})

const dynamicFontSize = (val) => {
    if (val === undefined || val === null) return '2rem'
    const strVal = formatNumber(val)
    const len = strVal.length
    
    // Mais inteligente: reduz mais cedo se estiver apertado
    if (len > 15) return '1.5rem'
    if (len > 10) return '2rem'
    return '2.5rem'
}

const getMarginPercentage = computed(() => {
    if (resumo.value.receita === 0) return 0
    return Math.max(0, Math.round((resumo.value.saldo / resumo.value.receita) * 100))
})

const formatNumber = (val) => Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })

const chartData = computed(() => ({
    labels: [t('features.incomes'), t('features.expenses'), t('features.net')],
    datasets: [{
        data: [resumo.value.receita, resumo.value.despesa, resumo.value.saldo > 0 ? resumo.value.saldo : 0],
        backgroundColor: ['#38ef7d', '#ff4b2b', '#0083b0'],
        borderWidth: 0,
        hoverOffset: 10
    }]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => ` ${t('common.currency')} ${formatNumber(context.raw)}`
            }
        }
    },
    cutout: '80%'
}

const resumo = ref({
    receita: 0,
    despesa: 0,
    saldo: 0,
    atividades_recentes: []
})

onMounted(async () => {
    fetchSummary()
    fetchMetas()
})

const fetchMetas = async () => {
    if (!authStore.hasFeature('Metas')) return
    try {
        const response = await authStore.apiFetch('/metas')
        if (response.ok) {
            const data = await response.json()
            metasSummary.value = data
                .filter(m => m.status !== 'inativo')
                .slice(0, 3)
        }
    } catch (e) { console.error(e) }
}

const calculatePercentage = (meta) => {
    if (meta.tipo === 'financeira') {
        if (!meta.valor_objetivo) return 0
        return Math.min(100, Math.round((meta.valor_atual / meta.valor_objetivo) * 100))
    }
    if (!meta.meta_quantidade) return 0
    return Math.min(100, Math.round((meta.atual_quantidade / meta.meta_quantidade) * 100))
}


const fetchSummary = async () => {
    loading.value = true
    try {
        const params = new URLSearchParams()
        const f = filterStore.filters
        if (f.data) {
            if (f.data.includes(' to ')) {
                const [inicio, fim] = f.data.split(' to ')
                params.append('data_inicio', inicio)
                params.append('data_fim', fim)
            } else {
                params.append('data', f.data)
            }
        }
        if (f.descricao) params.append('descricao', f.descricao)
        if (f.categoria) params.append('categoria', f.categoria)
        if (f.tipo && f.tipo !== 'todos') params.append('tipo', f.tipo)
        if (f.valor) params.append('valor', f.valor)

        const url = Array.from(params).length > 0 ? `/painel/resumo?${params.toString()}` : '/painel/resumo'
        const response = await authStore.apiFetch(url)
        if (response.ok) {
            resumo.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return '0,00'

  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(Number(value))
}

</script>

<style scoped>

.dashboard-wrapper {
    background: linear-gradient(180deg, rgba(var(--v-theme-primary), 0.05) 0%, transparent 100%);
    min-height: 100vh;
}

.gradient-text {
    background: linear-gradient(90deg, rgb(var(--v-theme-primary)), #5CBBF6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.glass-icon {
    background: rgba(24, 103, 192, 0.1) !important;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(24, 103, 192, 0.2);
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.05) !important;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.07) !important;
}

.summary-card {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    cursor: default;
    border: none !important;
}

.summary-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
}

@media (max-width: 600px) {
  .dashboard-wrapper {
      padding: 12px !important;
  }
  .text-h3 {
      font-size: 1.75rem !important;
  }
  .gradient-text {
      font-size: 1.5rem !important;
  }
  .summary-card {
      margin-bottom: 8px;
  }
  .summary-card:hover {
      transform: none;
  }
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--v-theme-surface), 0.9);
    box-shadow: 0 8px 16px rgba(0,0,0,0.05);
}

.receita-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    color: white;
}

.despesa-gradient {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%) !important;
    color: white;
}

.saldo-gradient {
    background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%) !important;
    color: white;
}

.card-blur-bg {
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(20px);
}

.hover-item {
    transition: all 0.2s ease;
    border: 1px solid transparent !important;
}

.hover-item:hover {
    background: rgba(var(--v-theme-primary), 0.05) !important;
    border-color: rgba(var(--v-theme-primary), 0.1) !important;
    transform: translateX(5px);
}

.border-card {
    border: 1px solid rgba(var(--v-border-color), 0.05) !important;
}

.quick-actions-gradient {
    background: linear-gradient(135deg, #1867C0 0%, #1A237E 100%) !important;
}

.chart-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.receita-dot { background: #38ef7d; }
.despesa-dot { background: #ff4b2b; }
.saldo-dot { background: #0083b0; }

.opacity-70 { opacity: 0.7; }
.opacity-80 { opacity: 0.8; }
.opacity-10 { opacity: 0.1; }
.opacity-30 { opacity: 0.3; }

.value-container {
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.currency-symbol {
    font-size: 0.5em;
    margin-right: 4px;
    opacity: 0.85;
}

.amount-value {
    word-break: break-all;
}

.filter-card {
  background: rgba(var(--v-theme-surface), 0.7) !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(var(--v-border-color), 0.1) !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
}

:deep(.v-field) {
  border-radius: 12px !important;
}

.dashboard-date-filter {
    max-width: none;
}
</style>

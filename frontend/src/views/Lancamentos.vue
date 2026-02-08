<template>
  <v-container class="lancamentos-wrapper">
    <!-- Header Moderno -->
    <v-row class="mb-4 pt-4" align="center">
      <v-col cols="12" md="6">
        <div class="d-flex align-center">
            <v-avatar color="#2962FF" size="64" class="mr-4 elevation-4 glass-icon">
                <v-icon icon="mdi-swap-horizontal" color="white" size="32"></v-icon>
            </v-avatar>
            <div>
                <h1 class="text-h3 font-weight-bold mb-1 gradient-text">Lançamentos</h1>
                <p class="text-h6 text-medium-emphasis font-weight-medium">Gestão completa das suas movimentações.</p>
            </div>
        </div>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-md-end">
        <v-btn color="primary" size="large" class="rounded-xl px-8" prepend-icon="mdi-plus" @click="abrirNovo" elevation="4">
          Novo Lançamento
        </v-btn>
      </v-col>
    </v-row>

    <!-- Cards de Resumo Rápido -->
    <v-row class="mb-8 px-2">
      <v-col cols="12" md="4" v-for="(stat, i) in summaryStats" :key="i">
        <v-card class="stat-card glass-card rounded-xl pa-6 border-card" elevation="4">
          <div class="d-flex align-center justify-space-between mb-4">
            <div class="icon-circle-small" :class="stat.colorClass">
              <v-icon :icon="stat.icon" color="white" size="20"></v-icon>
            </div>
            <span class="text-overline font-weight-bold opacity-70">{{ stat.label }}</span>
          </div>
          <div class="text-h4 font-weight-bold" :class="stat.textClass">
            R$ {{ formatNumber(stat.value) }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Tabela de Lançamentos Modernizada -->
    <v-card class="rounded-xl glass-card border-card overflow-hidden" elevation="8">
      <v-toolbar color="transparent" density="comfortable" class="px-4 py-2">
        <v-toolbar-title class="font-weight-bold">Histórico de Movimentações</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>

      <v-divider></v-divider>

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        :search="search"
        item-value="id"
        @update:options="loadItems"
        class="bg-transparent"
        hover
        no-data-text="Nenhum lançamento encontrado"
        loading-text="Carregando lançamentos..."
        items-per-page-text="Itens por página"
        :items-per-page-options="[5, 10, 25, 50]"
      >
        <template v-slot:item.tipo="{ item }">
          <v-chip
            :color="item.tipo === 'receita' ? 'success' : 'error'"
            variant="tonal"
            size="small"
            class="font-weight-bold text-uppercase"
          >
            {{ item.tipo === 'receita' ? 'Receita' : 'Despesa' }}
          </v-chip>
        </template>

        <template v-slot:item.valor="{ item }">
          <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="font-weight-bold">
            {{ item.tipo === 'receita' ? '+' : '-' }} R$ {{ formatNumber(item.valor) }}
          </span>
        </template>

        <template v-slot:item.data="{ item }">
          {{ formatDate(item.data) }}
        </template>

        <template v-slot:item.acoes="{ item }">
          <div class="d-flex gap-1 justify-end">
            <v-btn icon="mdi-pencil-outline" size="small" variant="text" color="primary" @click="abrirEditar(item)"></v-btn>
            <v-btn icon="mdi-delete-outline" size="small" variant="text" color="error" @click="abrirExcluir(item)"></v-btn>
          </div>
        </template>
      </v-data-table-server>
    </v-card>
  </v-container>

  <ModalNovoLancamento v-model="dialogNovo" @saved="buscarLancamentos" />
  <ModalEditarLancamento v-model="dialogEditar" :lancamento="itemAEditar" @updated="buscarLancamentos" />
  <ModalExcluirLancamento v-model="dialogExcluir" :lancamentoId="lancamentoIdExcluir" @deleted="buscarLancamentos" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import ModalNovoLancamento from '../components/Modals/Lancamentos/ModalNovoLancamento.vue'
import ModalEditarLancamento from '../components/Modals/Lancamentos/ModalEditarLancamento.vue'
import ModalExcluirLancamento from '../components/Modals/Lancamentos/ModalExcluirLancamento.vue'

const authStore = useAuthStore()
const loading = ref(false)
const search = ref('')
const serverItems = ref([])
const totalItems = ref(0)
const itemsPerPage = ref(10)
const itemAEditar = ref(null)
const lancamentoIdExcluir = ref(null)
const dialogNovo = ref(false)
const dialogEditar = ref(false)
const dialogExcluir = ref(false)

const headers = [
  { title: 'Data', key: 'data', align: 'start', sortable: false },
  { title: 'Descrição', key: 'descricao', align: 'start', sortable: false },
  { title: 'Categoria', key: 'categoria', align: 'start', sortable: false },
  { title: 'Tipo', key: 'tipo', align: 'center', sortable: false },
  { title: 'Valor', key: 'valor', align: 'end', sortable: false },
  { title: 'Ações', key: 'acoes', align: 'end', sortable: false },
]

const formatNumber = (val) => Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const formatDate = (date) => new Date(date).toLocaleDateString('pt-BR')

// Estatísticas de resumo (podemos manter o fetch global apenas para isso ou buscar do primeiro request)
const stats = ref({ receitas: 0, despesas: 0 })
const summaryStats = computed(() => [
  { label: 'Total Recebido', value: stats.value.receitas, icon: 'mdi-arrow-up', colorClass: 'receita-gradient', textClass: 'text-success' },
  { label: 'Total Gasto', value: stats.value.despesas, icon: 'mdi-arrow-down', colorClass: 'despesa-gradient', textClass: 'text-error' },
  { label: 'Saldo líquido', value: stats.value.receitas - stats.value.despesas, icon: 'mdi-bank', colorClass: 'saldo-gradient', textClass: '' }
])

const loadItems = async ({ page, itemsPerPage, sortBy, search }) => {
  loading.value = true
  try {
    const query = new URLSearchParams({
      page,
      per_page: itemsPerPage,
      search: search || ''
    }).toString()

    const response = await authStore.apiFetch(`/lancamentos?${query}`)
    if (response.ok) {
        const data = await response.json()
        serverItems.value = data.data
        totalItems.value = data.total
        
        // Atualiza stats de resumo (opcional: o backend poderia retornar isso no meta da paginação)
        // Por ora, vamos fazer um fetch separado para o resumo se necessário, ou usar os dados da primeira página
        // Mas o ideal é um endpoint de stats. Vou manter o fetch global simplificado para o resumo se houver lag.
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const buscarLancamentos = () => {
  // Dispara o update do v-data-table-server
}

const buscarStats = async () => {
  try {
    const response = await authStore.apiFetch('/painel/resumo')
    if (response.ok) {
       const data = await response.json()
       stats.value.receitas = data.receita
       stats.value.despesas = data.despesa
    }
  } catch (e) { console.error(e) }
}

const abrirNovo = () => { dialogNovo.value = true }
const abrirEditar = (item) => { itemAEditar.value = item; dialogEditar.value = true }
const abrirExcluir = (item) => { lancamentoIdExcluir.value = item.id; dialogExcluir.value = true }

onMounted(() => {
  buscarStats()
})
</script>

<style scoped>
.lancamentos-wrapper {
  background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%);
  min-height: 100vh;
}

.gradient-text {
  background: linear-gradient(90deg, #1867C0, #11998E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.glass-icon {
  background: rgba(24, 103, 192, 0.1) !important;
  border: 1px solid rgba(24, 103, 192, 0.2);
}

.glass-card {
  background: rgba(255, 255, 255, 0.95) !important;
  border: 1px solid rgba(255, 255, 255, 0.5) !important;
}

.border-card {
  border: 1px solid rgba(0,0,0,0.05) !important;
}

.icon-circle-small {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.receita-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.despesa-gradient { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
.saldo-gradient { background: linear-gradient(135deg, #1867C0 0%, #1A237E 100%); }

.search-field {
  max-width: 300px;
}

.gap-1 {
  gap: 8px;
}

.opacity-70 { opacity: 0.7; }
</style>

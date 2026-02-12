<template>
  <v-container class="lancamentos-wrapper">
    <!-- Header Moderno -->
    <v-row class="mb-4 pt-4" align="center">
      <v-col cols="12" md="6">
        <div class="d-flex align-center">
            <v-avatar color="primary" size="64" class="mr-4 elevation-4">
                <v-icon icon="mdi-swap-horizontal" color="white" size="32"></v-icon>
            </v-avatar>
            <div>
                <h1 class="text-h3 font-weight-bold mb-1 gradient-text">{{ $t('transactions.title') }}</h1>
                <p class="text-h6 text-medium-emphasis font-weight-medium">{{ $t('transactions.subtitle') }}</p>
            </div>
        </div>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-md-end">
        <v-btn color="primary" size="large" class="rounded-xl px-8" prepend-icon="mdi-plus" @click="abrirNovo" elevation="4">
          {{ $t('transactions.new') }}
        </v-btn>
      </v-col>
    </v-row>

    <FilterLancamentos
  v-model="filters"
  :categorias="categorias"
  @apply="aplicarFiltros"
  @clear="limparFiltros"
/>


    <v-card class="rounded-xl glass-card border-card overflow-hidden" elevation="8">
      <v-toolbar color="transparent" density="comfortable" class="px-4 py-2">
        <v-toolbar-title class="font-weight-bold">{{ $t('transactions.history') }}</v-toolbar-title>
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
        :no-data-text="$t('transactions.no_data')"
        :loading-text="$t('transactions.loading')"
        :items-per-page-text="$t('common.items_per_page')"
        :items-per-page-options="[5, 10, 25, 50]"
      >
        <template v-slot:item.tipo="{ item }">
          <v-chip
            :color="item.tipo === 'receita' ? 'success' : 'error'"
            variant="tonal"
            size="small"
            class="font-weight-bold text-uppercase"
          >
            {{ item.tipo === 'receita' ? $t('transactions.type.income') : $t('transactions.type.expense') }}
          </v-chip>
        </template>

        <template v-slot:item.valor="{ item }">
          <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="font-weight-bold">
            {{ item.tipo === 'receita' ? '+' : '-' }} {{ $t('common.currency') }} {{ formatNumber(item.valor) }}
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
import { useI18n } from 'vue-i18n'
import { categorias as categoriasConstantes } from '../constants/categorias'

const { t } = useI18n()
const authStore = useAuthStore()
import ModalNovoLancamento from '../components/Modals/Lancamentos/ModalNovoLancamento.vue'
import ModalEditarLancamento from '../components/Modals/Lancamentos/ModalEditarLancamento.vue'
import ModalExcluirLancamento from '../components/Modals/Lancamentos/ModalExcluirLancamento.vue'
import FilterLancamentos from '../components/Filters/Filter.vue'


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

const filters = ref({
  data: '',
  descricao: '',
  categoria: '',
  tipo: 'todos',
  valor: ''
})




const headers = computed(() => [
  { title: t('transactions.table.date'), key: 'data', align: 'start', sortable: true },
  { title: t('transactions.table.description'), key: 'descricao', align: 'start', sortable: true },
  { title: t('transactions.table.category'), key: 'categoria', align: 'start', sortable: true },
  { title: t('transactions.table.type'), key: 'tipo', align: 'center', sortable: true },
  { title: t('transactions.table.amount'), key: 'valor', align: 'end', sortable: true },
  { title: t('admin.actions'), key: 'acoes', align: 'end', sortable: false },
])

const formatNumber = (val) => {
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    return Number(val).toLocaleString(locale, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
const formatDate = (date) => {
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    return new Date(date).toLocaleDateString(locale, { timeZone: 'UTC' })
}



const categorias = computed(() => {
  // Usar categorias predefinidas do arquivo de constantes (títulos)
  try {
    return categoriasConstantes.map(c => c.title)
  } catch (e) {
    // Fallback: extrair categorias dos itens carregados
    const set = new Set()
    serverItems.value.forEach(l => l.categoria && set.add(l.categoria))
    return Array.from(set)
  }
})

const loadItems = async ({ page, itemsPerPage, sortBy, search: tableSearch }) => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page,
      per_page: itemsPerPage,
    })

    if (sortBy && sortBy.length) {
      params.append('sort_by', sortBy[0].key)
      params.append('sort_order', sortBy[0].order)
    }

    if (tableSearch) params.append('search', tableSearch)
    if (filters.value.data) params.append('data', filters.value.data)
    if (filters.value.categoria) params.append('categoria', filters.value.categoria)
    if (filters.value.tipo && filters.value.tipo !== 'todos') params.append('tipo', filters.value.tipo)
    if (filters.value.valor) params.append('valor', filters.value.valor)
    if (filters.value.descricao) params.append('descricao', filters.value.descricao)

    const response = await authStore.apiFetch(`/lancamentos?${params.toString()}`)
    if (response.ok) {
        const data = await response.json()
        serverItems.value = data.data
        totalItems.value = data.total
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const aplicarFiltros = () => {
  loadItems({
    page: 1,
    itemsPerPage: itemsPerPage.value,
    sortBy: []
  })
}


const limparFiltros = () => {
  filters.value = {
    data: '',
    descricao: '',
    categoria: '',
    tipo: 'todos',
    valor: ''
  }

  loadItems({
    page: 1,
    itemsPerPage: itemsPerPage.value,
    sortBy: []
  })
}




const buscarLancamentos = () => {
    // Força o reload do v-data-table-server
    search.value = (search.value || '') + ' '
    setTimeout(() => { search.value = search.value.trim() }, 10)
}

const abrirNovo = () => { dialogNovo.value = true }
const abrirEditar = (item) => { itemAEditar.value = item; dialogEditar.value = true }
const abrirExcluir = (item) => { lancamentoIdExcluir.value = item.id; dialogExcluir.value = true }


</script>

<style scoped>
.lancamentos-wrapper {
  background: linear-gradient(180deg, rgba(var(--v-theme-primary), 0.05) 0%, transparent 100%);
  min-height: 100vh;
}

.gradient-text {
  background: linear-gradient(90deg, rgb(var(--v-theme-primary)), #11998E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.glass-icon {
  background: rgba(24, 103, 192, 0.1) !important;
  border: 1px solid rgba(24, 103, 192, 0.2);
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.05) !important;
}

.border-card {
  border: 1px solid rgba(var(--v-border-color), 0.05) !important;
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

<style scoped>
.filter-bar {
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-border-color), 0.1);
}

.filter-bar .v-field {
  font-size: 14px;
}

.filter-bar .v-icon {
  opacity: 0.75;
}

.filter-bar .v-btn {
  border-radius: 50%;
}


</style>

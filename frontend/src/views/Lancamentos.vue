<template>
  <v-container class="lancamentos-wrapper">
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
      <v-col cols="12" md="6">
        <div class="d-flex flex-wrap justify-md-end align-center gap-4">
          <v-btn variant="flat" color="secondary" size="large" class="rounded-xl px-6 flex-grow-1 flex-sm-grow-0" prepend-icon="mdi-file-import" @click="$refs.fileInput.click()" elevation="2" :disabled="loading">
            {{ $t('actions.import_excel') }}
          </v-btn>
          <v-btn color="primary" size="large" class="rounded-xl px-8 flex-grow-1 flex-sm-grow-0" prepend-icon="mdi-plus" @click="abrirNovo" elevation="4" :disabled="loading">
            {{ $t('transactions.new') }}
          </v-btn>
          <input type="file" ref="fileInput" class="d-none" accept=".xlsx, .xls, .csv" @change="handleImport" />
        </div>
      </v-col>
    </v-row>
    
    <div class="d-flex flex-wrap align-center gap-3 mb-8 pa-4 rounded-xl export-area shadow-sm">
      <span class="text-caption font-weight-black opacity-60 w-100 mb-n1 ml-2 text-uppercase letter-spacing-1">{{ $t('actions.export_page') }}</span>
      <Planilhas :dados="serverItems" :disabled="loading" @exportar="exportarExcel" />
      <PdfExport :dados="serverItems" :disabled="loading" @exportar="exportarPdf" />
    </div>
    


    <FilterLancamentos
      v-model="filterStore.filters"
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
        v-model:options="options"
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

        <template v-slot:item.forma_pagamento="{ item }">
          <div class="d-flex align-center justify-center gap-1 opacity-80">
            <v-icon size="16" :icon="getPaymentMethodIcon(item.forma_pagamento)"></v-icon>
            <span class="text-caption font-weight-medium">
              {{ $t('transactions.payment_methods.' + (item.forma_pagamento || 'other')) }}
            </span>
          </div>
        </template>

        <template v-slot:item.categoria="{ item }">
          {{ $t('categories.' + item.categoria) }}
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

  <ModalNovoLancamento v-model="dialogNovo" @saved="onLancamentoSalvo" />
  <ModalEditarLancamento v-model="dialogEditar" :lancamento="itemAEditar" @updated="onLancamentoEditado" />
  <ModalExcluirLancamento v-model="dialogExcluir" :lancamentoId="lancamentoIdExcluir" @deleted="onLancamentoExcluido" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useFilterStore } from '../stores/filters'
import { useI18n } from 'vue-i18n'
import { categorias as categoriasConstantes } from '../constants/categorias'
import * as XLSX from "xlsx"

const { t } = useI18n()
const authStore = useAuthStore()
const filterStore = useFilterStore()
import ModalNovoLancamento from '../components/Modals/Lancamentos/ModalNovoLancamento.vue'
import ModalEditarLancamento from '../components/Modals/Lancamentos/ModalEditarLancamento.vue'
import ModalExcluirLancamento from '../components/Modals/Lancamentos/ModalExcluirLancamento.vue'
import FilterLancamentos from '../components/Filters/Filter.vue'
import Planilhas from '../components/Exportacoes/planilhas.vue'
import PdfExport from '../components/Exportacoes/pdf.vue'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

const fileInput = ref(null)

const handleImport = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    const reader = new FileReader()
    reader.onload = async (e) => {
        const data = new Uint8Array(e.target.result)
        const workbook = XLSX.read(data, { type: 'array' })
        const sheetName = workbook.SheetNames[0]
        const worksheet = workbook.Sheets[sheetName]
        const json = XLSX.utils.sheet_to_json(worksheet)

        if (json.length === 0) {
            alert('Arquivo vazio!')
            return
        }

        loading.value = true
        let count = 0
        for (const row of json) {
            // Tenta mapear colunas comuns: descricao, valor, categoria, data, tipo, forma_pagamento
            const rawForma = row['Forma de Pagamento'] || row.Forma || row['Payment Method'] || row.forma_pagamento || 'other'
            const formaMap = {
                'dinheiro': 'money', 'money': 'money', 'cash': 'money',
                'cartao de credito': 'credit_card', 'cartão de crédito': 'credit_card', 'credit card': 'credit_card', 'credito': 'credit_card',
                'cartao de debito': 'debit_card', 'cartão de débito': 'debit_card', 'debit card': 'debit_card', 'debito': 'debit_card',
                'pix': 'pix',
                'transferencia': 'transfer', 'transferência': 'transfer', 'transfer': 'transfer', 'ted': 'transfer', 'doc': 'transfer',
                'boleto': 'boleto',
                'outros': 'other', 'other': 'other'
            }
            
            const normalizedForma = rawForma.toString().toLowerCase().trim()
            const finalForma = formaMap[normalizedForma] || 'other'

            const payload = {
                descricao: row.Descrição || row.descricao || row.Description || 'Importado',
                valor: row.Valor || row.valor || row.Value || 0,
                categoria: row.Categoria || row.categoria || row.Category || 'Importado',
                data: row.Data || row.data || row.Date || new Date().toISOString().split('T')[0],
                tipo: (row.Tipo || row.tipo || row.Type || 'despesa').toLowerCase().includes('receita') ? 'receita' : 'despesa',
                forma_pagamento: finalForma
            }

            try {
                await authStore.apiFetch('/lancamentos', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                })
                count++
            } catch (err) {
                console.error('Erro ao importar linha:', row, err)
            }
        }

        alert(`Sucesso! ${count} lançamentos importados.`)
        buscarLancamentos()
        loading.value = false
    }
    reader.readAsArrayBuffer(file)
    event.target.value = '' // Clear input
}

const totais = ref({ receita: 0, despesa: 0 })

const exportarExcel = () => {
    loading.value = true
    try {
        const dataToExport = serverItems.value.map(item => ({
            [t('transactions.table.date')]: formatDate(item.data),
            [t('transactions.table.description')]: item.descricao,
            [t('transactions.table.category')]: t('categories.' + item.categoria),
            [t('transactions.table.type')]: item.tipo === 'receita' ? t('transactions.type.income') : t('transactions.type.expense'),
            [t('transactions.payment_methods.title')]: t('transactions.payment_methods.' + (item.forma_pagamento || 'other')),
            [t('transactions.table.amount')]: item.valor
        }))
        const worksheet = XLSX.utils.json_to_sheet(dataToExport)
        const workbook = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(workbook, worksheet, t('sidebar.transactions'))
        XLSX.writeFile(workbook, `finalyze_${t('sidebar.transactions').toLowerCase()}.xlsx`)
    } catch (e) {
        toast.error(t('toasts.error_generic'))
    } finally {
        loading.value = false
    }
}

const exportarPdf = () => {
    if (!serverItems.value || serverItems.value.length === 0) {
        toast.info(t('transactions.no_data'))
        return
    }

    loading.value = true
    setTimeout(() => {
        try {
            const doc = new jsPDF()
            const head = [[
                t('transactions.table.date'), 
                t('transactions.table.description'), 
                t('transactions.table.category'), 
                t('transactions.type.type') || t('transactions.table.type'), 
                t('transactions.payment_methods.title'),
                t('transactions.table.amount')
            ]]
            const data = serverItems.value.map(item => [
                formatDate(item.data),
                item.descricao || '',
                t('categories.' + item.categoria) || item.categoria || '',
                item.tipo === 'receita' ? t('transactions.type.income') : t('transactions.type.expense'),
                t('transactions.payment_methods.' + (item.forma_pagamento || 'other')),
                formatNumber(item.valor)
            ])

            doc.text(`${t('reports.title')} - Finalyze`, 14, 15)
            autoTable(doc, {
                head: head,
                body: data,
                startY: 20,
                theme: 'striped',
                headStyles: { fillColor: [24, 103, 192] },
                styles: { font: 'Inter' }
            })
            doc.save(`finalyze_${t('sidebar.transactions').toLowerCase()}.pdf`)
        } catch (e) {
            toast.error(t('toasts.error_generic'))
        } finally {
            loading.value = false
        }
    }, 100)
}

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




const headers = computed(() => [
  { title: t('transactions.table.date'), key: 'data', align: 'start', sortable: true },
  { title: t('transactions.table.description'), key: 'descricao', align: 'start', sortable: true },
  { title: t('transactions.table.category'), key: 'categoria', align: 'start', sortable: true },
  { title: t('transactions.table.type'), key: 'tipo', align: 'center', sortable: true },
  { title: t('transactions.table.payment_method'), key: 'forma_pagamento', align: 'center', sortable: true },
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

const getPaymentMethodIcon = (method) => {
  const icons = {
    money: 'mdi-cash-multiple',
    credit_card: 'mdi-credit-card',
    debit_card: 'mdi-credit-card-outline',
    pix: 'mdi-cellphone-check',
    transfer: 'mdi-bank-transfer',
    boleto: 'mdi-barcode-scan',
    other: 'mdi-dots-horizontal-circle-outline'
  }
  return icons[method] || icons.other
}



const categorias = computed(() => {
  try {
    return categoriasConstantes.map(c => c.title)
  } catch (e) {
    const set = new Set()
    serverItems.value.forEach(l => l.categoria && set.add(l.categoria))
    return Array.from(set)
  }
})

const loadItems = async ({ page, itemsPerPage, sortBy, search: tableSearch, isSilent = false }) => {
  if (!isSilent) loading.value = true
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
    
    if (f.categoria) params.append('categoria', f.categoria)
    if (f.tipo && f.tipo !== 'todos') params.append('tipo', f.tipo)
    if (f.valor) params.append('valor', f.valor)
    if (f.descricao) params.append('descricao', f.descricao)

    const response = await authStore.apiFetch(`/lancamentos?${params.toString()}`)
    if (response.ok) {
        const data = await response.json()
        serverItems.value = data.data
        totalItems.value = data.total
        if (data.totais) {
          totais.value = data.totais
        }
    }
  } catch (e) {
    console.error(e)
  } finally {
    if (!isSilent) loading.value = false
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
    filterStore.clearFilters()

  loadItems({
    page: 1,
    itemsPerPage: itemsPerPage.value,
    sortBy: []
  })
}




const buscarLancamentos = (isSilent = false) => {
    // Refresh manual mantendo página e filtros
    loadItems({
        page: Math.ceil((totalItems.value || 0) / itemsPerPage.value) > 0 ? 1 : 1, // Reset para o caso de novos
        itemsPerPage: itemsPerPage.value,
        sortBy: [],
        search: search.value,
        isSilent: isSilent
    })
}

const options = ref({ page: 1, itemsPerPage: 10, sortBy: [] })

const onLancamentoSalvo = (optimisticItem) => {
    if (optimisticItem) {
        // Só adicionamos na lista local se estivermos na página 1 e sem filtros restritivos (ou filtros que batam com o item)
        const f = filterStore.filters
        const isPage1 = options.value.page === 1
        const matchesFilter = (!f.descricao || optimisticItem.descricao.toLowerCase().includes(f.descricao.toLowerCase())) &&
                              (!f.categoria || optimisticItem.categoria === f.categoria) &&
                              (!f.tipo || f.tipo === 'todos' || optimisticItem.tipo === f.tipo)

        if (isPage1 && matchesFilter) {
            const exists = serverItems.value.some(i => i.id === optimisticItem.id)
            if (!exists) {
                serverItems.value.unshift(optimisticItem)
                totalItems.value++
                
                if (serverItems.value.length > itemsPerPage.value) {
                    serverItems.value.pop()
                }
            }
        }
    }
    // Sincronização silenciosa em background
    buscarLancamentos(true)
}

const onLancamentoEditado = (updatedItem) => {
    if (updatedItem && serverItems.value) {
        const index = serverItems.value.findIndex(i => i.id === updatedItem.id)
        if (index !== -1) {
            const oldItem = serverItems.value[index]
            
            // Reverter valor antigo nos totais
            if (oldItem.tipo === 'receita') totais.value.receita -= Number(oldItem.valor)
            else totais.value.despesa -= Number(oldItem.valor)
            
            // Aplicar valor novo nos totais
            if (updatedItem.tipo === 'receita') totais.value.receita += Number(updatedItem.valor)
            else totais.value.despesa += Number(updatedItem.valor)
            
            // Atualizar o item na lista
            serverItems.value[index] = { ...updatedItem }
        }
    }
    buscarLancamentos(true)
}

const onLancamentoExcluido = (deletedId) => {
    if (deletedId && serverItems.value) {
        // Encontrar item para atualizar totais locais
        const item = serverItems.value.find(i => i.id === deletedId)
        if (item) {
            if (item.tipo === 'receita') {
                totais.value.receita -= Number(item.valor)
            } else {
                totais.value.despesa -= Number(item.valor)
            }
        }
        
        serverItems.value = serverItems.value.filter(i => i.id !== deletedId)
        totalItems.value = Math.max(0, totalItems.value - 1)
    }
    buscarLancamentos(true)
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

.export-area {
  background: rgba(var(--v-theme-surface), 0.6);
  border: 1px solid rgba(var(--v-border-color), 0.1);
  backdrop-filter: blur(8px);
}

.letter-spacing-1 {
  letter-spacing: 1px;
}

.gap-4 {
  gap: 16px;
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

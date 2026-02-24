<template>
  <v-container>
    <v-row align="center" class="mb-6 pt-4">
      <v-col cols="12">
        <div class="d-flex flex-column gap-6">
          <!-- Top Row: Main View Tabs & New Button -->
          <div class="d-flex flex-column flex-md-row justify-space-between align-md-center gap-4">
            <div class="d-flex align-center">
              <v-avatar color="primary" size="48" class="mr-3 elevation-2">
                <v-icon icon="mdi-bullseye-arrow" color="white"></v-icon>
              </v-avatar>
              <div>
                <h1 class="text-h4 font-weight-bold">{{ $t('metas.tabs.metas') }}</h1>
                <p class="text-subtitle-2 text-medium-emphasis">{{ $t('metas.financial_subtitle') }}</p>
              </div>
            </div>

            <v-btn 
              color="primary" 
              prepend-icon="mdi-plus" 
              rounded="xl" 
              size="large"
              class="font-weight-bold px-8 elevation-3 shadow-primary w-100 w-sm-auto"
              @click="openDialog('financeira')"
            >
              {{ $t('metas.new_goal') }}
            </v-btn>
          </div>

          <!-- Bottom Row: Status Filters -->
          <div class="filter-wrapper d-flex justify-center justify-md-start">
            <v-btn-toggle
              v-model="statusFilter"
              mandatory
              color="primary"
              variant="text"
              class="status-toggle-group gap-2 flex-wrap justify-center justify-md-start"
            >
              <v-btn value="andamento" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-progress-clock</v-icon>
                {{ $t('metas.filter.active') }}
              </v-btn>
              <v-btn value="concluido" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-check-circle</v-icon>
                {{ $t('metas.filter.completed') }}
              </v-btn>
              <v-btn value="inativo" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-pause-circle</v-icon>
                {{ $t('metas.filter.inactive') }}
              </v-btn>
              <v-btn value="all" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-format-list-bulleted</v-icon>
                {{ $t('metas.filter.all') }}
              </v-btn>
            </v-btn-toggle>
          </div>
        </div>
      </v-col>
    </v-row>

    <v-window v-model="activeTab">
      <v-window-item value="metas">
        <div v-if="loading" class="d-flex flex-column align-center py-12">
          <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="180" />
          <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="180" />
        </div>

        <v-row v-else-if="filteredMetas.length">
          <v-col v-for="meta in filteredMetas" :key="meta.id" cols="12" md="6" lg="4">
            <v-card class="rounded-xl goal-card finance-card" :class="{ 'completed-meta': meta.status === 'concluido' }" elevation="2">
              <v-card-text class="pa-6">
                <div class="d-flex align-center mb-4">
                  <v-avatar :color="meta.cor || 'primary-lighten-4'" size="36" class="mr-3">
                    <v-icon :icon="meta.icone || 'mdi-cash-multiple'" :color="meta.cor ? 'white' : 'primary'" size="20"></v-icon>
                  </v-avatar>
                  <span class="text-h6 font-weight-bold text-truncate">{{ meta.titulo }}</span>
                </div>

                <div class="d-flex justify-space-between align-baseline mb-2">
                  <div>
                    <span class="text-h5 font-weight-bold">{{ formatPrice(meta.valor_atual) }}</span>
                    <span class="text-medium-emphasis"> / {{ formatShortPrice(meta.valor_objetivo) }}</span>
                  </div>
                  <v-chip :color="getProgressColor(meta)" size="small" class="font-weight-bold">
                    {{ calculatePercentage(meta.valor_atual, meta.valor_objetivo) }}%
                  </v-chip>
                </div>

                <v-progress-linear
                  :model-value="calculatePercentage(meta.valor_atual, meta.valor_objetivo)"
                  height="8"
                  rounded
                  :color="getProgressColor(meta)"
                  class="mb-4"
                ></v-progress-linear>

                <div class="d-flex align-center text-caption" :class="meta.status === 'atrasado' ? 'text-error' : 'text-medium-emphasis'">
                  <v-icon v-if="meta.status === 'atrasado'" icon="mdi-alert" size="14" class="mr-1"></v-icon>
                  <v-icon v-else-if="meta.status === 'concluido'" icon="mdi-check-circle" size="14" color="success" class="mr-1"></v-icon>
                  <v-icon v-else icon="mdi-trending-up" size="14" color="success" class="mr-1"></v-icon>
                  <span class="text-truncate">{{ meta.descricao || getMetaSubtitle(meta) }}</span>
                </div>

                <v-chip v-if="meta.prazo" size="x-small" variant="tonal" class="mt-3" color="medium-emphasis">
                  <v-icon icon="mdi-calendar" size="10" class="mr-1"></v-icon>
                  {{ formatDate(meta.prazo) }}
                </v-chip>
              </v-card-text>

              <v-divider opacity="0.1"></v-divider>

              <v-card-actions class="pa-4 justify-end gap-2">
                <v-btn 
                  variant="tonal" 
                  size="small" 
                  rounded="lg" 
                  :color="meta.status === 'concluido' ? 'warning' : 'success'" 
                  @click="toggleStatusConcluido(meta)"
                  :prepend-icon="meta.status === 'concluido' ? 'mdi-refresh' : 'mdi-check'"
                >
                  {{ meta.status === 'concluido' ? $t('metas.actions.reopen') : $t('metas.completed') }}
                </v-btn>
                <v-btn 
                  v-if="meta.status === 'inativo'"
                  variant="tonal" 
                  size="small" 
                  rounded="lg" 
                  color="info" 
                  @click="reativarItem(meta)"
                  prepend-icon="mdi-restore"
                >
                  {{ $t('metas.actions.reactivate') }}
                </v-btn>
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="primary" @click="editMeta(meta)" icon="mdi-pencil-outline" />
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="error" @click="confirmDelete(meta)" icon="mdi-delete-outline" />
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
        <div v-else class="text-center py-12 text-medium-emphasis">
          <v-icon icon="mdi-bullseye-arrow" size="64" class="mb-4 opacity-20"></v-icon>
          <p>{{ statusFilter === 'concluido' ? $t('metas.no_goals_completed') : $t('metas.no_financial_goals') }}</p>
        </div>
      </v-window-item>
    </v-window>
    
    <ModalMeta v-model="dialog" :meta="itemAEditar" :initialTipo="initialTipo" @saved="onMetaSalva" />
    <ModalExcluirMeta
      v-model="deleteDialog"
      :meta="metaToDelete"
      resourceType="metas"
      @deleted="onMetaExcluida"
      @rollback="({ id, oldStatus }) => { const i = metas.findIndex(m => m.id === id); if(i !== -1) metas[i].status = oldStatus; fetchMetas(true) }"
    />
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import ModalMeta from '../components/Modals/Metas/ModalMeta.vue'
import ModalExcluirMeta from '../components/Modals/Metas/ModalExcluirMeta.vue'
import ModalLembrete from '../components/Modals/Metas/ModalLembrete.vue'
import CompartilharModal from '../components/avisos/CompartilharModal/CompartilharModal.vue'
import { useI18n } from 'vue-i18n'
import { useMoney } from '../composables/useMoney'

const { t } = useI18n()
const { fromBRL, currencySymbol, formatNumber: fmtNum, meta: currencyMeta } = useMoney()
const authStore = useAuthStore()

const metas = ref([])
const loading = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const metaToDelete = ref(null)
const itemAEditar = ref(null)
const initialTipo = ref('financeira')
const lembreteDialog = ref(false)
const metaParaLembrete = ref(null)
const compartilharDialog = ref(false)
// Lembretes carregados do backend, indexados por meta id
const lembretesPorMeta = ref({})

// UI State
const activeTab = ref('metas')
const statusFilter = ref('andamento')

onMounted(() => {
  fetchMetas()
  fetchLembretes()
})

const onMetaSalva = (optimisticItem) => {
    if (optimisticItem) {
        const isEdit = optimisticItem.id && !String(optimisticItem.id).startsWith('opt-')
        if (isEdit) {
            const index = metas.value.findIndex(m => m.id === optimisticItem.id)
            if (index !== -1) {
                metas.value[index] = { ...metas.value[index], ...optimisticItem }
            }
        } else {
            const matchesFilter = statusFilter.value === 'all' || 
                                  (statusFilter.value === 'andamento' && optimisticItem.status === 'andamento')
            if (matchesFilter) {
                metas.value.unshift(optimisticItem)
            }
        }
    }
    setTimeout(() => { fetchMetas(true) }, 1500)
}

const onMetaExcluida = ({ id, oldStatus }) => {
    const index = metas.value.findIndex(m => m.id === id)
    if (index !== -1) {
        metas.value[index].status = 'inativo'
    }
    setTimeout(() => { fetchMetas(true) }, 2000)
}

const fetchMetas = async (isSilent = false) => {
  if (!isSilent) loading.value = true
  try {
    const [resMetas] = await Promise.all([
      authStore.apiFetch('/metas')
    ])
    
    if (resMetas.ok) metas.value = await resMetas.json()
  } catch (e) {
    console.error(e)
  } finally {
    if (!isSilent) loading.value = false
  }
}

const filteredMetas = computed(() => {
  if (statusFilter.value === 'all') return metas.value
  if (statusFilter.value === 'andamento') {
    return metas.value.filter(m => (m.status === 'andamento' || m.status === 'atrasado') && m.status !== 'inativo')
  }
  return metas.value.filter(m => m.status === statusFilter.value)
})

const openDialog = (tipo = 'financeira') => {
  initialTipo.value = tipo
  itemAEditar.value = null
  dialog.value = true
}

const editMeta = (meta) => {
  itemAEditar.value = meta
  initialTipo.value = meta.tipo || 'financeira'
  dialog.value = true
}

const confirmDelete = (meta) => {
  metaToDelete.value = meta
  deleteDialog.value = true
}

const openLembreteDialog = (meta) => {
  metaParaLembrete.value = meta
  lembreteDialog.value = true
}

const fetchLembretes = async () => {
  try {
    const res = await authStore.apiFetch('/lembretes')
    if (!res.ok) return
    const lista = await res.json()
    // Indexa por titulo da meta para associar (lembretes não têm meta_id na Eduardo)
    // Usa a lista flat e deixa o ModalLembrete buscar pelo id do lembrete
    const mapa = {}
    lista.forEach(l => {
      // Associamos pelo meta_id se existir, ou mantemos lista flat
      const key = l.meta_id || null
      if (key) {
        mapa[key] = l
      }
    })
    lembretesPorMeta.value = mapa
  } catch (e) {
    console.error('Erro ao buscar lembretes:', e)
  }
}

const reminderParaMeta = (meta) => {
  if (!meta?.id) return null
  return lembretesPorMeta.value[meta.id] || null
}

const onLembreteSalvo = (lembrete) => {
  if (!lembrete?.id) return
  // Atualiza o mapa — usa meta_id se disponível
  const key = lembrete.meta_id
  if (key) {
    lembretesPorMeta.value = { ...lembretesPorMeta.value, [key]: lembrete }
  }
  fetchLembretes()
}

const onLembreteExcluido = (id) => {
  // Remove do mapa
  const mapa = { ...lembretesPorMeta.value }
  for (const key of Object.keys(mapa)) {
    if (mapa[key]?.id === id) delete mapa[key]
  }
  lembretesPorMeta.value = mapa
}

const hasActiveReminder = (meta) => {
  if (!meta?.id) return false
  const lembrete = lembretesPorMeta.value[meta.id]
  if (!lembrete) return false
  if (lembrete.status !== 'pendente') return false
  return new Date(lembrete.data_aviso) > new Date()
}

const openCompartilharDialog = () => {
  compartilharDialog.value = true
}

const onConviteEnviado = (convite) => {
  toast.success(t('metas.share.sent_success'))
}

const toggleStatusConcluido = async (item) => {
  const isAnotacao = false
  
  if (!isAnotacao && item.status !== 'concluido') {
    const p = calculatePercentage(item.valor_atual, item.valor_objetivo)
    if (p < 100) {
      toast.error(t('metas.goal_incomplete_error') || 'A meta só pode ser finalizada quando atingir 100% de progresso.')
      return
    }
  }

  const endpoint = isAnotacao ? `/anotacoes/${item.id}` : `/metas/${item.id}`
  const oldStatus = item.status
  const newStatus = item.status === 'concluido' ? 'andamento' : 'concluido'
  
  // Optimistic update
  item.status = newStatus
  toast.success(newStatus === 'concluido' ? t('toasts.success_update') : t('toasts.success_restore'))

  try {
    const response = await authStore.apiFetch(endpoint, {
      method: 'PATCH',
      body: JSON.stringify({ status: newStatus })
    })
    if (!response.ok) {
        throw new Error('Erro ao atualizar status')
    }
  } catch (e) { 
      // Rollback
      item.status = oldStatus
      console.error(e) 
  } finally {
      setTimeout(() => { fetchMetas(true) }, 1500)
  }
}

const reativarItem = async (item) => {
  const isAnotacao = false
  const endpoint = isAnotacao ? `/anotacoes/${item.id}/reativar` : `/metas/${item.id}/reativar`
  const oldStatus = item.status

  // Optimistic update
  item.status = 'andamento'
  toast.success(t('metas.actions.reactivate_success'))
  
  try {
    const response = await authStore.apiFetch(endpoint, { method: 'POST' })
    if (!response.ok) {
        throw new Error('Erro ao reativar')
    }
  } catch (e) { 
      item.status = oldStatus
      console.error(e) 
  } finally {
      setTimeout(() => { fetchMetas(true) }, 1500)
  }
}

// Helpers
const formatPrice = (value) => {
    const converted = fromBRL(value || 0)
    return new Intl.NumberFormat(currencyMeta.value.locale, {
        style: 'currency',
        currency: currencyMeta.value.code
    }).format(converted)
}
const formatShortPrice = (value) => {
  // Convert from BRL to selected currency first, then abbreviate
  const converted = fromBRL(value || 0)
  if (converted >= 1000000) return currencySymbol.value + (converted / 1000000).toFixed(1).replace(/\.0$/, '') + 'M'
  if (converted >= 1000) return currencySymbol.value + (converted / 1000).toFixed(1).replace(/\.0$/, '') + 'k'
  return formatPrice(value)
}
const formatDate = (date) => {
    if (!date) return ''
    // parse YYYY-MM-DD as local time to avoid UTC offset (-1 day bug)
    if (typeof date === 'string' && date.match(/^\d{4}-\d{2}-\d{2}/)) {
        const [y, mo, d] = date.split('T')[0].split('-').map(Number)
        return new Date(y, mo - 1, d).toLocaleDateString(currencyMeta.value.locale)
    }
    return new Date(date).toLocaleDateString(currencyMeta.value.locale)
}
const calculatePercentage = (current, total) => {
  if (!total || total === 0) return 0
  const p = Math.round((current / total) * 100)
  return Math.max(0, Math.min(100, p))
}

const getProgressColor = (meta) => {
  if (meta.status === 'concluido') return '#38ef7d'
  if (meta.status === 'atrasado') return '#ff4b2b'
  if (meta.status === 'pausado') return '#9e9e9e'
  const p = calculatePercentage(meta.valor_atual || meta.atual_quantidade, meta.valor_objetivo || meta.meta_quantidade)
  if (p >= 100) return '#38ef7d'
  if (p >= 70) return '#0083b0'
  if (p >= 30) return '#ffb347'
  return '#ff4b2b'
}

const getMetaSubtitle = (meta) => {
  if (meta.tipo === 'financeira') {
    if (meta.status === 'atrasado') return t('metas.goal_late')
    const diff = meta.valor_objetivo - meta.valor_atual
    return diff > 0 ? t('metas.goal_remaining', { amount: formatPrice(diff) }) : t('metas.goal_reached')
  }
  return ''
}
</script>

<style scoped>
.goal-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(var(--v-border-color), 0.1);
}

.goal-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.1) !important;
}

.completed-meta {
  opacity: 0.85;
  filter: grayscale(0.2);
}

.finance-card {
  background: linear-gradient(135deg, rgba(var(--v-theme-surface), 1) 0%, rgba(var(--v-theme-surface), 0.8) 100%);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(var(--v-border-color), 0.05);
}

.finance-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
  border-color: rgba(var(--v-theme-primary), 0.2);
}

.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }
.gap-6 { gap: 24px; }

.status-toggle-group {
    background: transparent !important;
}

.filter-pill {
    background: rgba(var(--v-theme-surface), 0.5) !important;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(var(--v-border-color), 0.1) !important;
    text-transform: none !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    transition: all 0.2s ease;
    opacity: 0.8;
}

.filter-pill.v-btn--active {
    background: rgb(var(--v-theme-primary)) !important;
    color: white !important;
    opacity: 1;
    border-color: transparent !important;
    box-shadow: 0 4px 10px rgba(var(--v-theme-primary), 0.3) !important;
}

.shadow-primary {
  box-shadow: 0 8px 20px rgba(var(--v-theme-primary), 0.3) !important;
}
</style>

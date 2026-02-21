<template>
  <v-container>
    <v-row align="center" class="mb-6 pt-4">
      <v-col cols="12">
        <div class="d-flex flex-column gap-6">
          <!-- Top Row: Main View Tabs & New Button -->
          <div class="d-flex flex-column flex-md-row justify-space-between align-md-center gap-4">
            <div class="tabs-container rounded-xl pa-1 elevation-1 glass-card d-inline-flex">
              <v-tabs v-model="activeTab" color="primary" hide-slider class="pill-tabs" density="comfortable">
                <v-tab value="metas" rounded="lg" class="px-6">
                  <v-icon start icon="mdi-bullseye-arrow" class="mr-2"></v-icon>
                  {{ $t('metas.tabs.metas') }}
                </v-tab>
                <v-tab value="notepad" rounded="lg" class="px-6">
                  <v-icon start icon="mdi-notebook-outline" class="mr-2"></v-icon>
                  {{ $t('metas.tabs.notepad') }}
                </v-tab>
              </v-tabs>
            </div>

            <v-btn 
              color="primary" 
              :prepend-icon="activeTab === 'metas' ? 'mdi-plus' : 'mdi-note-plus-outline'" 
              rounded="xl" 
              size="large"
              class="font-weight-bold px-8 elevation-3 shadow-primary w-100 w-sm-auto"
              @click="openDialog(activeTab === 'metas' ? 'financeira' : 'pessoal')"
            >
              {{ activeTab === 'metas' ? $t('metas.new_goal') : $t('metas.new_note') }}
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
                  {{ meta.status === 'concluido' ? $t('metas.actions.reopen') : $t('metas.actions.complete') }}
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
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="primary" @click="editMeta(meta)" icon="mdi-pencil-outline"></v-btn>
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="error" @click="confirmDelete(meta)" icon="mdi-delete-outline"></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
        <div v-else class="text-center py-12 text-medium-emphasis">
          <v-icon icon="mdi-bullseye-arrow" size="64" class="mb-4 opacity-20"></v-icon>
          <p>{{ statusFilter === 'concluido' ? $t('metas.no_goals_completed') : $t('metas.no_financial_goals') }}</p>
        </div>
      </v-window-item>

      <v-window-item value="notepad">
        <div v-if="loading" class="d-flex flex-column align-center py-12">
          <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="120" />
          <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="120" />
        </div>

        <v-row v-else-if="filteredNotes.length">
          <v-col v-for="note in filteredNotes" :key="note.id" cols="12" sm="6" md="4" lg="3">
            <v-card 
              class="rounded-xl goal-card notepad-card overflow-hidden" 
              :class="{ 'completed-note': note.status === 'concluido' }"
              elevation="2"
            >
              <div class="notepad-header" :style="`background: ${note.cor || '#FFEB3B'}`"></div>
              <v-card-text class="pa-5 notepad-content">
                <div class="d-flex align-start mb-2">
                  <span v-if="note.icone" class="mr-2 notepad-emoji">{{ note.icone }}</span>
                  <span class="text-h6 font-weight-bold notepad-title text-truncate">{{ note.titulo }}</span>
                </div>

                <div class="notepad-text mb-4">
                   {{ note.descricao || $t('metas.no_notes_content') }}
                </div>

                <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mt-auto">
                   <span v-if="note.prazo" class="d-flex align-center">
                      <v-icon icon="mdi-calendar-clock" size="14" class="mr-1"></v-icon>
                      {{ formatDate(note.prazo) }}
                   </span>
                   <v-chip v-if="note.status === 'concluido'" size="x-small" color="success" variant="tonal" class="rounded-lg">{{ $t('metas.completed') }}</v-chip>
                </div>
              </v-card-text>

              <v-divider opacity="0.1"></v-divider>
              <v-card-actions class="pa-2 px-4 justify-end gap-1">
                <v-btn 
                  :icon="note.status === 'concluido' ? 'mdi-refresh' : 'mdi-check'" 
                  size="small" 
                  variant="text" 
                  :color="note.status === 'concluido' ? 'warning' : 'success'" 
                  @click="toggleStatusConcluido(note)"
                ></v-btn>
                <v-btn 
                  v-if="note.status === 'inativo'"
                  variant="text" 
                  size="small" 
                  color="info" 
                  @click="reativarItem(note)"
                  icon="mdi-restore"
                ></v-btn>
                <v-btn v-if="note.status !== 'inativo'" icon="mdi-pencil-outline" size="small" variant="text" color="primary" @click="editMeta(note)"></v-btn>
                <v-btn v-if="note.status !== 'inativo'" icon="mdi-delete-outline" size="small" variant="text" color="error" @click="confirmDelete(note)"></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
        <div v-else class="text-center py-12 text-medium-emphasis">
          <v-icon icon="mdi-notebook-outline" size="64" class="mb-4 opacity-20"></v-icon>
          <p>{{ statusFilter === 'concluido' ? $t('metas.no_notes_completed') : $t('metas.no_notes_available') }}</p>
        </div>
      </v-window-item>
    </v-window>
    
    <ModalMeta v-model="dialog" :meta="itemAEditar" :initialTipo="initialTipo" @saved="onMetaSalva" />
    <ModalExcluirMeta v-model="deleteDialog" :meta="metaToDelete" @deleted="onMetaExcluida" />
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import ModalMeta from '../components/Modals/Metas/ModalMeta.vue'
import ModalExcluirMeta from '../components/Modals/Metas/ModalExcluirMeta.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const authStore = useAuthStore()

const metas = ref([])
const anotacoes = ref([])
const loading = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const metaToDelete = ref(null)
const itemAEditar = ref(null)
const initialTipo = ref('financeira')

// UI State
const activeTab = ref('metas')
const statusFilter = ref('andamento')

onMounted(() => {
  fetchMetas()
})

const onMetaSalva = (optimisticItem, isAnotacao) => {
    if (optimisticItem) {
        // Se for uma edição (tem ID real), atualizamos o item existente
        const isEdit = optimisticItem.id && !String(optimisticItem.id).startsWith('opt-')
        
        if (isEdit) {
            const list = isAnotacao ? anotacoes : metas
            const index = list.value.findIndex(m => m.id === optimisticItem.id)
            if (index !== -1) {
                list.value[index] = { ...list.value[index], ...optimisticItem }
            }
        } else {
            // Se for novo, adicionamos no topo
            const matchesFilter = statusFilter.value === 'all' || 
                                  (statusFilter.value === 'andamento' && optimisticItem.status === 'andamento')
            
            if (matchesFilter) {
                if (isAnotacao) {
                    anotacoes.value.unshift(optimisticItem)
                } else {
                    metas.value.unshift(optimisticItem)
                }
            }
        }
    }
    
    // Sincronização em background - com delay para garantir que o backend commitou
    setTimeout(() => {
        fetchMetas(true)
    }, 800)
}

const onMetaExcluida = ({ id, isAnotacao }) => {
    if (isAnotacao) {
        anotacoes.value = anotacoes.value.filter(a => a.id !== id)
    } else {
        metas.value = metas.value.filter(m => m.id !== id)
    }
    setTimeout(() => {
        fetchMetas(true)
    }, 800)
}

const fetchMetas = async (isSilent = false) => {
  if (!isSilent) loading.value = true
  try {
    const [resMetas, resAnotacoes] = await Promise.all([
      authStore.apiFetch('/metas'),
      authStore.apiFetch('/anotacoes')
    ])
    
    if (resMetas.ok) metas.value = await resMetas.json()
    if (resAnotacoes.ok) anotacoes.value = await resAnotacoes.json()
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

const filteredNotes = computed(() => {
  if (statusFilter.value === 'all') return anotacoes.value
  if (statusFilter.value === 'andamento') {
    return anotacoes.value.filter(n => (n.status === 'andamento' || n.status === 'atrasado') && n.status !== 'inativo')
  }
  return anotacoes.value.filter(n => n.status === statusFilter.value)
})

const openDialog = (tipo = 'financeira') => {
  initialTipo.value = tipo
  itemAEditar.value = null
  dialog.value = true
}

const editMeta = (meta) => {
  itemAEditar.value = meta
  initialTipo.value = meta.tipo || 'pessoal'
  dialog.value = true
}

const confirmDelete = (meta) => {
  metaToDelete.value = meta
  deleteDialog.value = true
}

const toggleStatusConcluido = async (item) => {
  const isAnotacao = !item.tipo || item.tipo === 'pessoal'
  
  if (!isAnotacao && item.status !== 'concluido') {
    const p = calculatePercentage(item.valor_atual, item.valor_objetivo)
    if (p < 100) {
      toast.error('A meta só pode ser finalizada quando atingir 100% de progresso.')
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
      setTimeout(() => { fetchMetas(true) }, 800)
  }
}

const reativarItem = async (item) => {
  const isAnotacao = !item.tipo || item.tipo === 'pessoal'
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
      setTimeout(() => { fetchMetas(true) }, 800)
  }
}

// Helpers
const formatPrice = (value) => {
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    const currency = t('common.currency') === 'R$' ? 'BRL' : 'USD'
    return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(value)
}
const formatShortPrice = (value) => {
  if (value >= 1000) return (value / 1000) + 'k'
  return value
}
const formatDate = (date) => {
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    return new Date(date).toLocaleDateString(locale)
}
const calculatePercentage = (current, total) => {
  if (!total || total === 0) return 0
  const p = Math.round((current / total) * 100)
  return p > 100 ? 100 : p
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

.notepad-card {
  background: #FFF9C4;
  position: relative;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.notepad-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
}

:root[data-v-theme="dark"] .notepad-card,
.v-theme--dark .notepad-card {
  background: #33301a;
}

.completed-note {
  opacity: 0.7;
}

.notepad-header {
  height: 12px;
  width: 100%;
}

.notepad-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.notepad-emoji {
  font-size: 1.5rem;
}

.notepad-title {
  color: inherit;
  font-family: 'Inter', sans-serif;
}

.notepad-text {
  color: inherit;
  opacity: 0.9;
  font-size: 0.95rem;
  line-height: 1.5;
  white-space: pre-wrap;
  font-family: 'Inter', sans-serif;
}

.gap-1 {
  gap: 8px;
}
.pill-tabs :deep(.v-tab--selected) {
  background: white !important;
  color: rgb(var(--v-theme-primary)) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.v-theme--dark .pill-tabs :deep(.v-tab--selected) {
    background: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
}

.pill-tabs :deep(.v-tab) {
  text-transform: none;
  font-weight: 700;
  letter-spacing: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  min-width: 120px !important;
  opacity: 0.7;
}

.pill-tabs :deep(.v-tab--selected) {
    opacity: 1;
}

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

.filter-scroll-wrapper::-webkit-scrollbar {
    display: none;
}

.filter-scroll-wrapper {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.7) !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(var(--v-border-color), 0.05) !important;
}

.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }
.gap-6 { gap: 24px; }

.shadow-primary {
  box-shadow: 0 8px 20px rgba(var(--v-theme-primary), 0.3) !important;
}

@media (max-width: 600px) {
  .pill-tabs :deep(.v-tab) {
    min-width: 100px !important;
    padding: 0 12px !important;
    font-size: 0.85rem !important;
  }
}
</style>

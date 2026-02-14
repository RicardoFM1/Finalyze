<template>
  <v-container>
    <v-row align="center" no-gutters class="mb-6">
      <v-col cols="12" md="6">
        <v-tabs v-model="activeTab" color="primary" align-tabs="start">
          <v-tab value="metas">
            <v-icon start icon="mdi-bullseye-arrow"></v-icon>
            {{ $t('metas.tabs.metas') }}
          </v-tab>
          <v-tab value="notepad">
            <v-icon start icon="mdi-notebook-outline"></v-icon>
            {{ $t('metas.tabs.notepad') }}
          </v-tab>
        </v-tabs>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-md-end mt-4 mt-md-0">
            <v-btn-toggle v-model="statusFilter" mandatory color="primary" rounded="xl" class="mr-4" density="comfortable" variant="tonal">
              <v-btn value="andamento" class="px-4">{{ $t('metas.filter.active') }}</v-btn>
              <v-btn value="concluido" class="px-4">{{ $t('metas.filter.completed') }}</v-btn>
              <v-btn value="inativo" class="px-4">{{ $t('metas.filter.inactive') }}</v-btn>
              <v-btn value="all" class="px-4">{{ $t('metas.filter.all') }}</v-btn>
            </v-btn-toggle>
        
        <v-btn 
          color="primary" 
          :prepend-icon="activeTab === 'metas' ? 'mdi-plus' : 'mdi-note-plus-outline'" 
          rounded="lg" 
          class="ml-4"
          @click="openDialog(activeTab === 'metas' ? 'financeira' : 'pessoal')"
        >
          {{ activeTab === 'metas' ? $t('metas.new_goal') : $t('metas.new_note') }}
        </v-btn>
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
                  Reativar
                </v-btn>
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="primary" @click="editMeta(meta)" icon="mdi-pencil-outline"></v-btn>
                <v-btn v-if="meta.status !== 'inativo'" variant="tonal" size="small" rounded="lg" color="error" @click="confirmDelete(meta)" icon="mdi-delete-outline"></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
        <div v-else class="text-center py-12 text-medium-emphasis">
          <v-icon icon="mdi-bullseye-arrow" size="64" class="mb-4 opacity-20"></v-icon>
          <p>{{ statusFilter === 'concluido' ? 'Nenhuma meta concluída ainda.' : $t('metas.no_financial_goals') }}</p>
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
          <p>{{ statusFilter === 'concluido' ? 'Sem anotações concluídas.' : $t('metas.no_notes_available') }}</p>
        </div>
      </v-window-item>
    </v-window>
    
    <ModalMeta v-model="dialog" :meta="itemAEditar" :initialTipo="initialTipo" @saved="fetchMetas" />
    <ModalExcluirMeta v-model="deleteDialog" :meta="metaToDelete" @deleted="fetchMetas" />
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

const fetchMetas = async () => {
  loading.value = true
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
    loading.value = false
  }
}

const filteredMetas = computed(() => {
  if (statusFilter.value === 'all') return metas.value.filter(m => m.status !== 'inativo')
  if (statusFilter.value === 'andamento') {
    return metas.value.filter(m => m.status === 'andamento' || m.status === 'atrasado')
  }
  return metas.value.filter(m => m.status === statusFilter.value)
})

const filteredNotes = computed(() => {
  if (statusFilter.value === 'all') return anotacoes.value.filter(n => n.status !== 'inativo')
  if (statusFilter.value === 'andamento') {
    return anotacoes.value.filter(n => n.status === 'andamento' || n.status === 'atrasado')
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
  const newStatus = item.status === 'concluido' ? 'andamento' : 'concluido'
  
  try {
    const response = await authStore.apiFetch(endpoint, {
      method: 'PUT',
      body: JSON.stringify({ ...item, status: newStatus })
    })
    if (response.ok) fetchMetas()
  } catch (e) { console.error(e) }
}

const reativarItem = async (item) => {
  const isAnotacao = !item.tipo || item.tipo === 'pessoal'
  const endpoint = isAnotacao ? `/anotacoes/${item.id}/reativar` : `/metas/${item.id}/reativar`
  
  try {
    const response = await authStore.apiFetch(endpoint, { method: 'POST' })
    if (response.ok) {
      toast.success('Item reativado com sucesso!')
      fetchMetas()
    }
  } catch (e) { console.error(e) }
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
}

.notepad-card {
  background: #FFF9C4;
  position: relative;
  min-height: 200px;
  display: flex;
  flex-direction: column;
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
</style>

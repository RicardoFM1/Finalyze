<template>
  <v-container>
    <div v-if="!loading" class="d-flex align-center mb-8">
      <div>
        <h1 class="text-h4 font-weight-bold d-flex align-center">
          {{ $t('metas.financial_title') }}
          <v-tooltip :text="$t('metas.financial_tooltip')">
            <template v-slot:activator="{ props }">
              <v-icon v-bind="props" icon="mdi-information-outline" size="xs" color="medium-emphasis" class="ml-2"></v-icon>
            </template>
          </v-tooltip>
        </h1>
      </div>
      <v-spacer></v-spacer>
      <v-btn color="primary" prepend-icon="mdi-plus" rounded="lg" @click="openDialog('financeira')" elevation="2">
        {{ $t('metas.new_goal') }}
      </v-btn>
    </div>

    <div v-if="loading" class="d-flex flex-column align-center py-12">
      <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="180" />
      <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="180" />
      <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="180" />
    </div>


    <v-row v-if="!loading && financialMetas.length" class="mb-12">
      <v-col v-for="meta in financialMetas" :key="meta.id" cols="12" md="4">
        <v-card class="rounded-xl goal-card finance-card" elevation="4">
          <v-card-text class="pa-6">
            <div class="d-flex align-center mb-4">
              <v-avatar :color="meta.cor || 'primary-lighten-4'" size="36" class="mr-3">
                <v-icon :icon="meta.icone || 'mdi-cash-multiple'" :color="meta.cor ? 'white' : 'primary'" size="20"></v-icon>
              </v-avatar>
              <span class="text-h6 font-weight-bold">{{ meta.titulo }}</span>
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
              <v-icon v-else icon="mdi-trending-up" size="14" color="success" class="mr-1"></v-icon>
              <span>{{ meta.descricao || getMetaSubtitle(meta) }}</span>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4 justify-end gap-2">
            <v-btn variant="tonal" size="small" rounded="lg" color="primary" @click="editMeta(meta)" prepend-icon="mdi-pencil-outline">{{ $t('metas.edit') }}</v-btn>
            <v-btn variant="tonal" size="small" rounded="lg" color="error" @click="confirmDelete(meta)" prepend-icon="mdi-delete-outline">{{ $t('metas.delete') }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <div v-else-if="!loading && !financialMetas.length" class="text-center py-8 text-medium-emphasis mb-12">
      {{ $t('metas.no_financial_goals') }}
    </div>

    
    <div v-if="!loading" class="d-flex align-center mb-8 pt-4">
      <h1 class="text-h4 font-weight-bold d-flex align-center">
        {{ $t('metas.notepad_title') }}
        <v-tooltip :text="$t('metas.notepad_tooltip')">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" icon="mdi-information-outline" size="xs" color="medium-emphasis" class="ml-2"></v-icon>
          </template>
        </v-tooltip>
      </h1>
      <v-spacer></v-spacer>
      <v-btn color="secondary" prepend-icon="mdi-note-plus-outline" rounded="lg" @click="openDialog('pessoal')" elevation="2">
        {{ $t('metas.new_note') }}
      </v-btn>
    </div>

    <v-row v-if="!loading">
      <v-col v-for="meta in personalMetas" :key="meta.id" cols="12" sm="6" md="4" lg="3">
        <v-card class="rounded-xl goal-card notepad-card overflow-hidden" elevation="3">
          <div class="notepad-header" :style="`background: ${meta.cor || '#FFEB3B'}`"></div>
          <v-card-text class="pa-5 notepad-content">
            <div class="d-flex align-start mb-2">
              <span v-if="meta.icone" class="mr-2 notepad-emoji">{{ meta.icone }}</span>
              <span class="text-h6 font-weight-bold notepad-title text-truncate">{{ meta.titulo }}</span>
            </div>

            <div class="notepad-text mb-4">
               {{ meta.descricao || $t('metas.no_notes_content') }}
            </div>

            <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mt-auto">
               <span v-if="meta.prazo" class="d-flex align-center">
                  <v-icon icon="mdi-calendar-clock" size="14" class="mr-1"></v-icon>
                  {{ formatDate(meta.prazo) }}
               </span>
               <v-chip v-if="meta.status === 'concluido'" size="x-small" color="success" variant="tonal" class="rounded-lg">{{ $t('metas.completed') }}</v-chip>
            </div>
          </v-card-text>

          <v-divider></v-divider>
          <v-card-actions class="pa-2 px-4 justify-end gap-1">
            <v-btn icon="mdi-pencil-outline" size="small" variant="text" color="primary" @click="editMeta(meta)"></v-btn>
            <v-btn icon="mdi-delete-outline" size="small" variant="text" color="error" @click="confirmDelete(meta)"></v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col v-if="!personalMetas.length" cols="12" class="text-center py-8 text-medium-emphasis">
        {{ $t('metas.no_notes_available') }}
      </v-col>
    </v-row>

    
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

const financialMetas = computed(() => metas.value)
const personalMetas = computed(() => anotacoes.value)

const openDialog = (tipo = 'financeira') => {
  initialTipo.value = tipo
  itemAEditar.value = null
  dialog.value = true
}

const editMeta = (meta) => {
  itemAEditar.value = meta
  dialog.value = true
}


const confirmDelete = (meta) => {
  metaToDelete.value = meta
  deleteDialog.value = true
}

const toggleStatusConcluido = async (item) => {
  const isAnotacao = !item.tipo || item.tipo === 'pessoal'
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
  if (meta.status === 'atrasado') return 'error'
  if (meta.status === 'pausado') return 'grey'
  const p = calculatePercentage(meta.valor_atual || meta.atual_quantidade, meta.valor_objetivo || meta.meta_quantidade)
  if (p >= 100) return 'success'
  if (p >= 70) return 'primary'
  if (p >= 30) return 'warning'
  return 'error'
}

const getMetaSubtitle = (meta) => {
  if (meta.tipo === 'financeira') {
    if (meta.status === 'atrasado') return t('metas.goal_late')
    const diff = meta.valor_objetivo - meta.valor_atual
    return diff > 0 ? t('metas.goal_remaining', { amount: formatPrice(diff) }) : t('metas.goal_reached')
  }
  return ''
}

// Mock functions for menu
const viewHistory = (meta) => toast.info(t('metas.history_dev'))
const viewPlanning = (meta) => toast.info(t('metas.planning_dev'))

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

.finance-card {
  background: linear-gradient(135deg, rgba(var(--v-theme-surface), 1) 0%, rgba(var(--v-theme-surface), 0.8) 100%);
}

.notepad-card {
  background: #FFF9C4; /* Default Light Yellow */
  position: relative;
  min-height: 200px;
  display: flex;
  flex-direction: column;
}

:root[data-v-theme="dark"] .notepad-card,
.v-theme--dark .notepad-card {
  background: #33301a;
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

/* Glassmorphism subtle touch */
.v-dialog .v-card {
  backdrop-filter: blur(10px);
}
</style>

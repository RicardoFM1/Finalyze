<template>
  <v-container>
    <div v-if="!loading" class="d-flex align-center mb-8">
      <div>
        <h1 class="text-h4 font-weight-bold d-flex align-center">
          Metas Financeiras
          <v-tooltip text="Acompanhe seus objetivos de economia e gastos.">
            <template v-slot:activator="{ props }">
              <v-icon v-bind="props" icon="mdi-information-outline" size="xs" color="medium-emphasis" class="ml-2"></v-icon>
            </template>
          </v-tooltip>
        </h1>
      </div>
      <v-spacer></v-spacer>
      <v-btn color="primary" prepend-icon="mdi-plus" rounded="lg" @click="openDialog('financeira')" elevation="2">
        Nova Meta
      </v-btn>
    </div>

    <div v-if="loading" class="d-flex justify-center align-center py-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
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
            <v-btn variant="tonal" size="small" rounded="lg" color="primary" @click="editMeta(meta)" prepend-icon="mdi-pencil-outline">Editar</v-btn>
            <v-btn variant="tonal" size="small" rounded="lg" color="error" @click="confirmDelete(meta)" prepend-icon="mdi-delete-outline">Excluir</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <div v-else-if="!loading && !financialMetas.length" class="text-center py-8 text-medium-emphasis mb-12">
      Nenhuma meta financeira encontrada.
    </div>

    
    <div v-if="!loading" class="d-flex align-center mb-8 pt-4">
      <h1 class="text-h4 font-weight-bold d-flex align-center">
        Bloco de Notas
        <v-tooltip text="Suas anotações pessoais e lembretes.">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" icon="mdi-information-outline" size="xs" color="medium-emphasis" class="ml-2"></v-icon>
          </template>
        </v-tooltip>
      </h1>
      <v-spacer></v-spacer>
      <v-btn color="secondary" prepend-icon="mdi-note-plus-outline" rounded="lg" @click="openDialog('pessoal')" elevation="2">
        Nova Anotação
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
               {{ meta.descricao || 'Sem anotações...' }}
            </div>

            <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mt-auto">
               <span v-if="meta.prazo" class="d-flex align-center">
                  <v-icon icon="mdi-calendar-clock" size="14" class="mr-1"></v-icon>
                  {{ formatDate(meta.prazo) }}
               </span>
               <v-chip v-if="meta.status === 'concluido'" size="x-small" color="success" variant="tonal" class="rounded-lg">Concluído</v-chip>
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
        Nenhuma anotação disponível.
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
const formatPrice = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
const formatShortPrice = (value) => {
  if (value >= 1000) return (value / 1000) + 'k'
  return value
}
const formatDate = (date) => new Date(date).toLocaleDateString('pt-BR')
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
    if (meta.status === 'atrasado') return 'Meta Atrasada!'
    const diff = meta.valor_objetivo - meta.valor_atual
    return diff > 0 ? `Faltam ${formatPrice(diff)} para sua meta` : 'Meta batida! Parabéns!'
  }
  return ''
}

// Mock functions for menu
const viewHistory = (meta) => toast.info('Histórico em desenvolvimento')
const viewPlanning = (meta) => toast.info('Planejamento em desenvolvimento')

</script>

<style scoped>
.goal-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(0,0,0,0.05);
}

.goal-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.1) !important;
}

.finance-card {
  background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
}

.notepad-card {
  background: #FFF9C4; /* Light Yellow */
  position: relative;
  min-height: 200px;
  display: flex;
  flex-direction: column;
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
  color: #5D4037;
  font-family: 'Inter', sans-serif;
}

.notepad-text {
  color: #795548;
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
  background: rgba(255, 255, 255, 0.95);
}
</style>

<template>
  <v-container>
    <div class="d-flex align-center mb-8">
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


    <v-row class="mb-12">
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

          <v-card-actions class="pa-3 justify-space-between">
            <div class="d-flex gap-1">
              <v-menu>
                <template v-slot:activator="{ props }">
                   <v-btn variant="outlined" size="small" rounded="lg" v-bind="props" prepend-icon="mdi-chevron-down">Detalhes</v-btn>
                </template>
                <v-list>
                   <v-list-item @click="viewHistory(meta)">Histórico</v-list-item>
                   <v-list-item @click="viewPlanning(meta)">Planejamento</v-list-item>
                </v-list>
              </v-menu>
              
              <v-btn variant="outlined" size="small" rounded="lg" @click="editMeta(meta)">Editar</v-btn>
              <v-btn variant="outlined" size="small" rounded="lg" @click="toggleStatus(meta)">
                {{ meta.status === 'pausado' ? 'Retomar' : 'Pausar' }}
              </v-btn>
            </div>
            <v-btn icon="mdi-dots-horizontal" size="x-small" variant="text" color="medium-emphasis"></v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    
    <div class="d-flex align-center mb-8 pt-4">
      <h1 class="text-h4 font-weight-bold d-flex align-center">
        Metas Pessoais
        <v-tooltip text="Metas relacionadas a hábitos e produtividade.">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" icon="mdi-information-outline" size="xs" color="medium-emphasis" class="ml-2"></v-icon>
          </template>
        </v-tooltip>
      </h1>
      <v-spacer></v-spacer>
      
    </div>

    <v-row>
      <v-col v-for="meta in personalMetas" :key="meta.id" cols="12" sm="6" md="3">
        <v-card class="rounded-xl goal-card personal-card overflow-hidden" elevation="4">
          <div :style="`height: 8px; background: ${meta.cor || '#7E57C2'}`"></div>
          <v-card-text class="pa-5">
            <div class="d-flex align-center mb-3">
              <span class="mr-2" style="font-size: 1.5rem">{{ meta.icone || '🎯' }}</span>
              <span class="text-h6 font-weight-bold text-truncate">{{ meta.titulo }}</span>
            </div>

            <p class="text-medium-emphasis text-body-2 mb-4">{{ meta.descricao || 'Sem descrição' }}</p>

            <div class="d-flex align-center mb-2">
              <v-progress-linear
                :model-value="calculatePercentage(meta.atual_quantidade, meta.meta_quantidade)"
                height="10"
                rounded
                :color="meta.cor || 'primary'"
                class="mr-3"
              ></v-progress-linear>
              <span class="font-weight-bold">{{ calculatePercentage(meta.atual_quantidade, meta.meta_quantidade) }}%</span>
            </div>

            <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mt-4">
               <span v-if="meta.prazo">Até {{ formatDate(meta.prazo) }}</span>
               <v-chip v-if="meta.status === 'pausado'" size="x-small" variant="tonal" class="rounded-lg">Pausada</v-chip>
            </div>
          </v-card-text>

          <v-divider></v-divider>
          <v-card-actions class="pa-2 bg-grey-lighten-4 justify-center">
            <v-btn icon="mdi-pencil" size="x-small" variant="text" color="medium-emphasis" @click="editMeta(meta)"></v-btn>
            <v-btn icon="mdi-information-outline" size="x-small" variant="text" color="medium-emphasis"></v-btn>
            <v-btn icon="mdi-check-circle-outline" size="x-small" variant="text" color="medium-emphasis" @click="incrementProgress(meta)"></v-btn>
            <v-btn icon="mdi-delete-outline" size="x-small" variant="text" color="error" @click="confirmDelete(meta)"></v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    
    <v-dialog v-model="dialog" max-width="500px">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="font-weight-bold d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-icon :icon="form.id ? 'mdi-pencil' : 'mdi-plus'" class="mr-2" color="primary"></v-icon>
            {{ form.id ? 'Editar Meta' : 'Nova Meta' }}
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialog = false"></v-btn>
           
        </v-card-title>
        <v-card-text>
          <v-form ref="metaForm" @submit.prevent="saveMeta">
             <v-btn-toggle v-model="form.tipo"
                                label="Tipo de Meta"
                                class="w-100 mb-4 rounded-lg"
                                rounded="lg" 
                                variant="flat"
                                mandatory color="primary" border>
                                <v-btn value="financeira" class="flex-grow-1" prepend-icon="mdi-cash-plus">Financeira</v-btn>
                                <v-btn value="pessoal" class="flex-grow-1" prepend-icon="mdi-account-outline">Pessoal</v-btn>
                                
                              </v-btn-toggle>
            

            <v-text-field
              v-model="form.titulo"
              label="Título"
              variant="outlined"
              placeholder="Ex: Reserva de Emergência"
              class="mb-2"
              rounded="lg"
              required
            ></v-text-field>

            <v-text-field
              v-model="form.descricao"
              label="Descrição / Subtítulo"
              variant="outlined"
              placeholder="Pequeno detalhe ou frase de impacto"
              class="mb-2"
              rounded="lg"
            ></v-text-field>

            <template v-if="form.tipo === 'financeira'">
              <v-row dense>
                <v-col cols="6">
                   <v-text-field
                    v-model="form.valor_atual"
                    label="Valor Atual"
                    prefix="R$"
                    type="number"
                    variant="outlined"
                    rounded="lg"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                   <v-text-field
                    v-model="form.valor_objetivo"
                    label="Objetivo"
                    prefix="R$"
                    type="number"
                    variant="outlined"
                    rounded="lg"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>
            </template>

            <template v-else>
               <v-row dense>
                <v-col cols="6">
                   <v-text-field
                    v-model="form.atual_quantidade"
                    label="Progresso Atual"
                    type="number"
                    variant="outlined"
                    rounded="lg"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                   <v-text-field
                    v-model="form.meta_quantidade"
                    label="Meta Total"
                    type="number"
                    variant="outlined"
                    rounded="lg"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row dense>
                 <v-col cols="6">
                    <v-text-field
                      v-model="form.unidade"
                      label="Unidade"
                      placeholder="livros, km, etc"
                      variant="outlined"
                      rounded="lg"
                    ></v-text-field>
                 </v-col>
                 <v-col cols="6">
                    <v-text-field
                      v-model="form.icone"
                      label="Ícone/Emoji"
                      placeholder="📚, 🎯, 🏃"
                      variant="outlined"
                      rounded="lg"
                    ></v-text-field>
                 </v-col>
              </v-row>
            </template>

            <v-row dense>
              <v-col cols="6">
                <v-text-field
                  v-model="form.prazo"
                  label="Prazo"
                  type="date"
                  variant="outlined"
                  rounded="lg"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="form.cor"
                  label="Cor (Hex)"
                  type="color"
                  variant="outlined"
                  rounded="lg"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-btn type="submit" color="primary" block size="large" rounded="lg" class="mt-4" :loading="saving" elevation="2">
              Salvar Meta
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

  
    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h5 font-weight-bold">Excluir Meta?</v-card-title>
        <v-card-text>Esta ação não pode ser desfeita. Deseja realmente excluir "{{ metaToDelete?.titulo }}"?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Cancelar</v-btn>
          <v-btn color="error" variant="elevated" rounded="lg" @click="confirmDeleteAction" :loading="deleting">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const metas = ref([])
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const metaToDelete = ref(null)

const form = ref({
  id: null,
  tipo: 'financeira',
  titulo: '',
  descricao: '',
  valor_objetivo: null,
  valor_atual: 0,
  meta_quantidade: null,
  atual_quantidade: 0,
  unidade: '',
  prazo: null,
  cor: '#1867C0',
  icone: ''
})

onMounted(() => {
  fetchMetas()
})

const fetchMetas = async () => {
  loading.value = true
  try {
    const response = await authStore.apiFetch('/metas')
    if (response.ok) {
      metas.value = await response.json()
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const financialMetas = computed(() => metas.value.filter(m => m.tipo === 'financeira'))
const personalMetas = computed(() => metas.value.filter(m => m.tipo === 'pessoal'))

const openDialog = (tipo = 'financeira') => {
  form.value = {
    id: null,
    tipo,
    titulo: '',
    descricao: '',
    valor_objetivo: null,
    valor_atual: 0,
    meta_quantidade: null,
    atual_quantidade: 0,
    unidade: tipo === 'financeira' ? 'BRL' : '',
    prazo: null,
    cor: tipo === 'financeira' ? '#4CAF50' : '#7E57C2',
    icone: tipo === 'pessoal' ? '🎯' : ''
  }
  dialog.value = true
}

const editMeta = (meta) => {
  form.value = { 
    ...meta,
    prazo: meta.prazo ? new Date(meta.prazo).toISOString().substr(0, 10) : null
  }
  dialog.value = true
}

const saveMeta = async () => {
  saving.value = true
  try {
    const isEdit = !!form.value.id
    const response = await authStore.apiFetch(isEdit ? `/metas/${form.value.id}` : '/metas', {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(form.value)
    })

    if (response.ok) {
      toast.success(isEdit ? 'Meta atualizada!' : 'Meta criada com sucesso!')
      dialog.value = false
      fetchMetas()
    } else {
      const data = await response.json()
      toast.error(data.message || 'Erro ao salvar meta')
    }
  } catch (e) {
    toast.error('Erro de conexão')
  } finally {
    saving.value = false
  }
}

const confirmDelete = (meta) => {
  metaToDelete.value = meta
  deleteDialog.value = true
}

const confirmDeleteAction = async () => {
  deleting.value = true
  try {
    const response = await authStore.apiFetch(`/metas/${metaToDelete.value.id}`, {
      method: 'DELETE'
    })
    if (response.ok) {
      toast.success('Meta removida')
      deleteDialog.value = false
      fetchMetas()
    }
  } catch (e) {
    toast.error('Erro ao deletar')
  } finally {
    deleting.value = false
  }
}

const toggleStatus = async (meta) => {
  const newStatus = meta.status === 'pausado' ? 'andamento' : 'pausado'
  try {
    const response = await authStore.apiFetch(`/metas/${meta.id}`, {
      method: 'PUT',
      body: JSON.stringify({ ...meta, status: newStatus })
    })
    if (response.ok) fetchMetas()
  } catch (e) { console.error(e) }
}

const incrementProgress = async (meta) => {
  if (meta.tipo === 'pessoal') {
    const newVal = (meta.atual_quantidade || 0) + 1
    if (newVal > meta.meta_quantidade) return
    
    try {
      const response = await authStore.apiFetch(`/metas/${meta.id}`, {
        method: 'PUT',
        body: JSON.stringify({ ...meta, atual_quantidade: newVal, status: newVal === meta.meta_quantidade ? 'concluido' : meta.status })
      })
      if (response.ok) fetchMetas()
    } catch (e) { console.error(e) }
  }
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

.personal-card {
  background: white;
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

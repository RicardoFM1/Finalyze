<template>
  <v-container class="dashboard-wrapper">
    <!-- Header Modernizado -->
    <v-row class="mb-8">
      <v-col cols="12">
        <div class="d-flex align-center">
            <v-avatar color="primary" size="48" class="mr-4 elevation-3">
                <v-icon icon="mdi-view-dashboard-outline" color="white"></v-icon>
            </v-avatar>
            <div>
                <h1 class="text-h4 font-weight-bold mb-0">Olá, bem-vindo!</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Visão geral da sua saúde financeira hoje.</p>
            </div>
        </div>
      </v-col>
    </v-row>

    <!-- Cards de Resumo com Gradientes -->
    <v-row class="mb-8">
        <v-col v-if="loading" cols="12">
            <v-row>
                <v-col v-for="i in 3" :key="i" cols="12" md="4">
                    <v-skeleton-loader type="card" class="rounded-xl"></v-skeleton-loader>
                </v-col>
            </v-row>
        </v-col>
        
        <template v-else>
          <v-col cols="12" md="4">
            <v-card class="summary-card glass-card receita-gradient rounded-xl" elevation="6">
              <v-card-item>
                <div class="d-flex justify-space-between align-center mb-4">
                    <span class="text-overline font-weight-bold">Receitas</span>
                    <v-icon icon="mdi-arrow-up-circle" color="white"></v-icon>
                </div>
                <div class="text-h4 font-weight-bold">R$ {{ resumo.receita }}</div>
                <div class="text-caption mt-2 opacity-80">Sua receita total</div>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card class="summary-card glass-card despesa-gradient rounded-xl" elevation="6">
              <v-card-item>
                <div class="d-flex justify-space-between align-center mb-4">
                    <span class="text-overline font-weight-bold">Despesas</span>
                    <v-icon icon="mdi-arrow-down-circle" color="white"></v-icon>
                </div>
                <div class="text-h4 font-weight-bold">R$ {{ resumo.despesa }}</div>
                <div class="text-caption mt-2 opacity-80">Sua despesa total</div>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card class="summary-card glass-card saldo-gradient rounded-xl" elevation="6">
              <v-card-item>
                <div class="d-flex justify-space-between align-center mb-4">
                    <span class="text-overline font-weight-bold">Saldo Atual</span>
                    <v-icon icon="mdi-account-balance-wallet" color="white"></v-icon>
                </div>
                <div class="text-h4 font-weight-bold">R$ {{ resumo.saldo }}</div>
                <div class="text-caption mt-2 opacity-80">Saldo disponível para investimentos</div>
              </v-card-item>
            </v-card>
          </v-col>
      </template>
    </v-row>

    <v-row>
      <!-- Atividade Recente -->
      <v-col cols="12" md="8">
        <v-card class="rounded-xl recent-activity overflow-hidden" elevation="4">
           <v-toolbar color="transparent" density="comfortable" class="px-2">
               <v-toolbar-title class="font-weight-bold">Atividade Recente</v-toolbar-title>
               <v-spacer></v-spacer>
               <v-btn variant="text" size="small" color="primary" font-weight-bold :to="{ name: 'Lancamentos' }">Ver todos</v-btn>
           </v-toolbar>
           <v-list lines="two" class="pa-2">
              <v-list-item v-for="item in resumo.atividades_recentes" :key="item.id" 
                class="rounded-lg mb-1"
                :title="item.descricao || item.categoria" 
                :subtitle="`${item.tipo === 'receita' ? 'Receita' : 'Despesa'} • ${item.categoria}`" 
              >
                <template v-slot:prepend>
                    <v-avatar :color="item.tipo === 'receita' ? 'success-lighten-4' : 'error-lighten-4'" rounded="lg">
                        <v-icon :icon="item.tipo === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-minus'" :color="item.tipo === 'receita' ? 'success' : 'error'"></v-icon>
                    </v-avatar>
                </template>
                <template v-slot:append>
                    <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="text-h6 font-weight-bold">
                        {{ item.tipo === 'receita' ? '+' : '-' }} R$ {{ Number(item.valor).toFixed(2).replace('.', ',') }}
                    </span>
                </template>
              </v-list-item>
              <div v-if="!resumo.atividades_recentes?.length" class="text-center pa-10 text-medium-emphasis">
                  <v-icon icon="mdi-history" size="48" class="mb-4 opacity-20"></v-icon>
                  <p>Nenhuma atividade recente registrada.</p>
              </div>
           </v-list>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
     
        <v-card class="rounded-xl mb-6 quick-actions" elevation="4" color="grey-lighten-4">
            <v-card-title class="font-weight-bold pa-4 pb-0">Ações Rápidas</v-card-title>
            <v-card-text class="pa-4">
                <v-btn block color="primary" size="large" class="mb-4 rounded-lg" prepend-icon="mdi-plus" @click="dialog = true" elevation="2">Novo Lançamento</v-btn>
                <v-row dense>
                    <v-col cols="6">
                        <v-btn block variant="outlined" color="primary" class="rounded-lg" prepend-icon="mdi-chart-bar" :to="{ name: 'Reports' }">Relatórios</v-btn>
                    </v-col>
                    <v-col cols="6">
                        <v-btn block variant="outlined" color="primary" class="rounded-lg" prepend-icon="mdi-flag" :to="{ name: 'Metas' }">Metas</v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

       
        <v-card v-if="metasSummary.length" class="rounded-xl metas-preview" elevation="4">
            <v-card-title class="font-weight-bold pa-4 pb-0 d-flex align-center">
                Metas em Foco
                <v-spacer></v-spacer>
                <v-icon icon="mdi-flag-checkered" color="primary"></v-icon>
            </v-card-title>
            <v-card-text class="pa-4">
                <div v-for="meta in metasSummary" :key="meta.id" class="mb-4">
                    <div class="d-flex justify-space-between text-body-2 mb-1">
                        <span class="font-weight-bold">{{ meta.titulo }}</span>
                        <span>{{ calculatePercentage(meta) }}%</span>
                    </div>
                    <v-progress-linear
                        :model-value="calculatePercentage(meta)"
                        :color="meta.cor || 'primary'"
                        height="6"
                        rounded
                    ></v-progress-linear>
                </div>
                <v-btn block variant="text" size="small" color="primary" :to="{ name: 'Metas' }">Ver todas as metas</v-btn>
            </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Colocar no componente de modal -->
    <v-dialog v-model="dialog" max-width="500px">
        <v-card class="rounded-xl pa-4">
            <div class="d-flex align-center justify-space-between mb-4">
              <v-card-title class="pa-0 font-weight-bold">Adicionar Lançamento</v-card-title>
              <v-btn icon="mdi-close" variant="text" size="small" @click="dialog = false"></v-btn>
            </div>
              <v-card-text class="pa-0">
                <v-form @submit.prevent="salvarLancamento">
                    <v-row dense>
                        <v-col cols="12">
                            <v-btn-toggle v-model="form.tipo" mandatory color="primary" class="w-100 mb-4 rounded-lg" border>
                                <v-btn value="receita" class="flex-grow-1" prepend-icon="mdi-cash-plus">Receita</v-btn>
                                <v-btn value="despesa" class="flex-grow-1" prepend-icon="mdi-cash-minus">Despesa</v-btn>
                            </v-btn-toggle>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="form.valor" label="Valor" prefix="R$" type="number" step="0.01" variant="outlined" rounded="lg" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="form.data" label="Data" type="date" variant="outlined" rounded="lg" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="form.categoria" label="Categoria" variant="outlined" rounded="lg" required placeholder="Ex: Alimentação, Salário"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="form.descricao" label="Descrição" variant="outlined" rounded="lg" rows="2" placeholder="Opcional"></v-textarea>
                        </v-col>
                    </v-row>
                    <v-btn type="submit" color="primary" block size="large" rounded="lg" class="mt-4" :loading="saving" elevation="3">Salvar Lançamento</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const dialog = ref(false)
const saving = ref(false)
const loading = ref(true)
const metasSummary = ref([])

const resumo = ref({
    receita: 0,
    despesa: 0,
    saldo: 0,
    atividades_recentes: []
})

const form = ref({
    tipo: 'despesa',
    valor: '',
    categoria: '',
    data: new Date().toISOString().substr(0, 10),
    descricao: ''
})

onMounted(async () => {
    fetchSummary()
    fetchMetas()
})

const fetchSummary = async () => {
    loading.value = true
    try {
        const response = await authStore.apiFetch('/painel/resumo')
        if (response.ok) {
            resumo.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const fetchMetas = async () => {
    if (!authStore.hasFeature('Metas')) return
    try {
        const response = await authStore.apiFetch('/metas')
        if (response.ok) {
            const data = await response.json()
            metasSummary.value = data.slice(0, 3) // Pega as 3 primeiras
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

const salvarLancamento = async () => {
    saving.value = true
    try {
        const valor = Number(form.value.valor)
        if (isNaN(valor) || valor <= 0) {
            toast.warning('Por favor, informe um valor válido.')
            saving.value = false
            return
        }

        const response = await authStore.apiFetch('/lancamentos', {
            method: 'POST',
            body: JSON.stringify({
                ...form.value,
                valor: valor 
            })
        })

        if (response.ok) {
            toast.success('Lançamento adicionado!')
            dialog.value = false
            fetchSummary()
           
            form.value = {
                tipo: 'despesa',
                valor: '',
                categoria: '',
                data: new Date().toISOString().substr(0, 10),
                descricao: ''
            }
        } else {
            const data = await response.json().catch(() => ({}))
            toast.error(data.message || 'Erro ao salvar lançamento')
        }
    } catch (e) {
        toast.error('Erro de conexão')
    } finally {
        saving.value = false
    }
}
</script>

<style scoped>
.dashboard-wrapper {
    background-color: #f8f9fc;
}

.summary-card {
    border: none;
    color: white;
    position: relative;
    overflow: hidden;
}

.summary-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.receita-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.despesa-gradient {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
}

.saldo-gradient {
    background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
}

.glass-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.summary-card .opacity-80 {
    opacity: 0.85;
}

.recent-activity :deep(.v-list) {
    background: transparent;
}

.gap-2 {
    gap: 8px;
}

.v-btn-toggle .v-btn {
    text-transform: none;
    letter-spacing: normal;
}
</style>

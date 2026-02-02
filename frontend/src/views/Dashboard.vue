<template>
  <v-container>
    <v-row>
      <v-col cols="12" class="mb-4">
        <h1 class=" texto-painel">Painel</h1>
        <p class=" texto-subtitulo text-subtitle-1">Visão geral da sua saúde financeira</p>
      </v-col>
    </v-row>

    <v-row class="mb-6">
        
        <v-col v-if="loading" cols="12">
            <v-skeleton-loader type="article"></v-skeleton-loader>
        </v-col>
        
        <template v-else>
          <v-col cols="12" md="4">
            <v-card color="success" class="text-white" elevation="4">
              <v-card-item>
                <v-card-title class=" text-body-3 text-weight-bold">Receitas</v-card-title>
                <div class="  text-h5 font-weight-bold mt-2">R${{ formatCurrency(summary.income) }}</div>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card color="error" class="text-white" elevation="4">
              <v-card-item>
                <v-card-title class="text-body-3 text-weight-bold">Despesas</v-card-title>
                <div class="text-h5 font-weight-bold mt-2">R$ {{ formatCurrency(summary.expense) }}</div>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card color="info" class="text-white" elevation="4">
              <v-card-item>
                <v-card-title class="text-body-3 text-weight-bold">Saldo</v-card-title>
                <div class="text-h5 font-weight-bold mt-2">R$ {{ formatCurrency(summary.balance) }}</div>
              </v-card-item>
            </v-card>
          </v-col>
      </template>
    </v-row>

    <v-row>
      <v-col cols="12" md="8">
        <v-card title="Atividade Recente">
           <v-list lines="two">
              <v-list-item v-for="item in summary.atividades_recentes" :key="item.id" 
                :title="item.descricao || item.categoria" 
                :subtitle="`${item.tipo === 'receita' ? 'Receita' : 'Despesa'} - ${item.categoria}`" 
                :prepend-icon="item.tipo === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-minus'"
              >
                <template v-slot:append>
                    <span :class="item.type === 'income' ? 'text-success' : 'text-error'" class="font-weight-bold">
                       {{ item.type === 'income' ? '+' : '-' }} R$ {{ formatCurrency(item.amount) }}

                    </span>
                </template>
              </v-list-item>
              <div v-if="!summary.atividades_recentes?.length" class="text-center pa-4 text-medium-emphasis">
                  Nenhuma atividade recente.
              </div>
           </v-list>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card title="Ações Rápidas">
            <v-card-text>
                <v-btn block color="primary" class="mb-2 text-body-2 font-weight-bold" prepend-icon="mdi-plus" @click="dialog = true">Adicionar Lançamento</v-btn>
                <v-btn block variant="outlined" class="mb-2 text-body-2 font-weight-bold" to="/relatorios">Ver Relatórios</v-btn>
            </v-card-text>
        </v-card>
      </v-col>
    </v-row>


    
    <v-dialog v-model="dialog" max-width="500px">
        <v-card>
            <v-card-title>Novo Lançamento</v-card-title>
            <v-card-text>
                <v-form @submit.prevent="salvarLancamento">
                    <v-select v-model="form.tipo" :items="[{title: 'Receita', value: 'receita'}, {title: 'Despesa', value: 'despesa'}]" label="Tipo" required></v-select>
                    <v-text-field v-model="form.valor" label="Valor" prefix="R$" type="number" step="0.01" required></v-text-field>
                    <v-text-field v-model="form.categoria" label="Categoria" required></v-text-field>
                    <v-text-field v-model="form.data" label="Data" type="date" required></v-text-field>
                    <v-text-field v-model="form.descricao" label="Descrição"></v-text-field>
                    <v-btn type="submit" color="primary" block class="mt-4" :loading="saving">Salvar</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const dialog = ref(false)
const saving = ref(false)
const summary = ref({
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

const loading = ref(true)

onMounted(async () => {
    fetchSummary()
})

const fetchSummary = async () => {
    loading.value = true
    try {
        const response = await authStore.apiFetch('/dashboard/summary')
        if (response.ok) {
            summary.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
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
            toast.error('Erro ao salvar lançamento')
        }
    } catch (e) {
        toast.error('Erro de conexão')
    } finally {
        saving.value = false
    }
}



const formatCurrency = (value) => {
  if (value === null || value === undefined) return '0,00'

  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(Number(value))
}

</script>




<style scoped>
.texto-painel {
  display: inline-block; 

  background: linear-gradient(
    to right,
    #2f63ff 30%,
    #2900a5 100%
  );

  background-size: 100% 100%;

  background-clip: text;
  -webkit-background-clip: text;

  color: transparent;
  -webkit-text-fill-color: transparent;

  font-size: 48px;
  font-weight: 700;
}
.texto-subtitulo {
 

  background: linear-gradient(
    to right,
    #2f63ff 30%,
    #2900a5 100%
  );

  background-clip: text;
  -webkit-background-clip: text;

  color: transparent;
  -webkit-text-fill-color: transparent;


}


</style>
<template>
  <v-container>
    <v-row class="mb-4" align="center">
      <v-col>
        <h1 class="text-h4 font-weight-bold">Lançamentos</h1>
      </v-col>
     
    </v-row>

    <v-card elevation="2">
        <v-data-table :headers="headers" :items="lancamentos" :loading="loading">
            <template v-slot:item.data="{ item }">
                {{ new Date(item.data).toLocaleDateString('pt-BR') }}
            </template>
            <template v-slot:item.tipo="{ item }">
                <v-chip :color="item.tipo === 'receita' ? 'success' : 'error'" size="small" class="text-uppercase font-weight-bold">
                    {{ item.tipo === 'receita' ? 'Receita' : 'Despesa' }}
                </v-chip>
            </template>
            <template v-slot:item.valor="{ item }">
                <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="font-weight-bold">
                    {{ item.tipo === 'receita' ? '+' : '-' }} 
                    {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.valor) }}
                </span>
            </template >

          <template v-slot:item.acoes="{ item }">
  <v-menu>
    <template #activator="{ props }">
      <v-btn icon v-bind="props">
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </template>

    <v-list>
      <v-list-item @click="editar(item.raw)">
        <v-list-item-title>Editar</v-list-item-title>
      </v-list-item>

      <v-list-item @click="excluir(item.raw)">
        <v-list-item-title>Excluir</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>


        </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const loading = ref(false)
const headers = [
  { title: 'Data', key: 'data', align: 'start' },
  { title: 'Descrição', key: 'descricao', align: 'start' },
  { title: 'Categoria', key: 'categoria', align: 'start' },
  { title: 'Tipo', key: 'tipo', align: 'start' },
  { title: 'Valor', key: 'valor', align: 'end' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center'}
]

const lancamentos = ref([])

const buscarLancamentos = async () => {
    loading.value = true
    try {
        const response = await authStore.apiFetch('/lancamentos')
        if (response.ok) {
            lancamentos.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

onMounted(buscarLancamentos)
</script>

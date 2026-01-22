<template>
  <v-container>
    <v-row class="mb-4" align="center">
      <v-col>
        <h1 class="text-h4 font-weight-bold">Lançamentos</h1>
      </v-col>
      <v-col class="text-right">
        <v-btn color="primary" prepend-icon="mdi-plus" size="large">Novo Lançamento</v-btn>
      </v-col>
    </v-row>

    <v-card elevation="2">
        <v-data-table :headers="headers" :items="transactions" :loading="loading">
            <template v-slot:item.date="{ item }">
                {{ new Date(item.date).toLocaleDateString('pt-BR') }}
            </template>
            <template v-slot:item.type="{ item }">
                <v-chip :color="item.type === 'income' ? 'success' : 'error'" size="small" class="text-uppercase font-weight-bold">
                    {{ item.type === 'income' ? 'Receita' : 'Despesa' }}
                </v-chip>
            </template>
            <template v-slot:item.amount="{ item }">
                <span :class="item.type === 'income' ? 'text-success' : 'text-error'" class="font-weight-bold">
                    {{ item.type === 'income' ? '+' : '-' }} 
                    {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.amount) }}
                </span>
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
  { title: 'Data', key: 'date', align: 'start' },
  { title: 'Descrição', key: 'description', align: 'start' },
  { title: 'Categoria', key: 'category', align: 'start' },
  { title: 'Tipo', key: 'type', align: 'start' },
  { title: 'Valor', key: 'amount', align: 'end' },
]

const transactions = ref([])

const fetchTransactions = async () => {
    loading.value = true
    try {
        const response = await authStore.apiFetch('/transactions')
        if (response.ok) {
            transactions.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

onMounted(fetchTransactions)
</script>

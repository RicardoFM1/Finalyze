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
            <template v-slot:item.type="{ item }">
                <v-chip :color="item.type === 'income' ? 'success' : 'error'" size="small" class="text-uppercase font-weight-bold">
                    {{ item.type === 'income' ? 'Receita' : 'Despesa' }}
                </v-chip>
            </template>
            <template v-slot:item.amount="{ item }">
                <span :class="item.type === 'income' ? 'text-success' : 'text-error'" class="font-weight-bold">
                    {{ item.type === 'income' ? '+' : '-' }} {{ item.amount }}
                </span>
            </template>
        </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'

const loading = ref(false)
const headers = [
  { title: 'Data', key: 'date', align: 'start' },
  { title: 'Descrição', key: 'description', align: 'start' },
  { title: 'Categoria', key: 'category', align: 'start' },
  { title: 'Tipo', key: 'type', align: 'start' },
  { title: 'Valor', key: 'amount', align: 'end' },
  { title: 'Ações', key: 'actions', align: 'end', sortable: false },
]

const transactions = ref([
    { id: 1, date: '2023-10-01', description: 'Salário', category: 'Salário', type: 'income', amount: 'R$ 5.000,00' },
    { id: 2, date: '2023-10-05', description: 'Aluguel', category: 'Habitação', type: 'expense', amount: 'R$ 1.500,00' },
    { id: 3, date: '2023-10-10', description: 'Supermercado', category: 'Alimentação', type: 'expense', amount: 'R$ 800,00' },
])
</script>

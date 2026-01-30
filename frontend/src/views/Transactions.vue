<template>
  <v-container>
    <v-row class="mb-4" align="center">
      <v-col>
        <h1 class="text-h4 font-weight-bold">
          {{ $t('transactions.title') }}
        </h1>
      </v-col>

      <v-col class="text-right">
        <v-btn color="primary" prepend-icon="mdi-plus" size="large">
          {{ $t('transactions.new') }}
        </v-btn>
      </v-col>
    </v-row>

    <v-card elevation="2">
      <v-data-table
        :headers="headers"
        :items="transactions"
        :loading="loading"
      >
        <!-- Data -->
        <template #item.date="{ item }">
          {{ formatDate(item.date) }}
        </template>

        <!-- Tipo -->
        <template #item.type="{ item }">
          <v-chip
            :color="item.type === 'income' ? 'success' : 'error'"
            size="small"
            class="text-uppercase font-weight-bold"
          >
            {{
              item.type === 'income'
                ? $t('transactions.type.income')
                : $t('transactions.type.expense')
            }}
          </v-chip>
        </template>

        <!-- Valor -->
        <template #item.amount="{ item }">
          <span
            :class="item.type === 'income' ? 'text-success' : 'text-error'"
            class="font-weight-bold"
          >
            {{ item.type === 'income' ? '+' : '-' }}
            {{ formatCurrency(item.amount) }}
          </span>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const loading = ref(false)
const transactions = ref([])

const { t, locale } = useI18n()

/* Headers traduzidos e reativos */
const headers = computed(() => [
  { title: t('transactions.table.date'), key: 'date', align: 'start' },
  { title: t('transactions.table.description'), key: 'description', align: 'start' },
  { title: t('transactions.table.category'), key: 'category', align: 'start' },
  { title: t('transactions.table.type'), key: 'type', align: 'start' },
  { title: t('transactions.table.amount'), key: 'amount', align: 'end' }
])

/* Mapeamento de moeda por idioma */
const currencyByLocale = {
  'pt-BR': 'BRL',
  'en-US': 'USD'
}

/* Formatadores */
const formatDate = (date) => {
  return new Intl.DateTimeFormat(locale.value).format(new Date(date))
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat(locale.value, {
    style: 'currency',
    currency: currencyByLocale[locale.value] || 'USD'
  }).format(amount)
}

/* Fetch */
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

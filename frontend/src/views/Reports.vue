<template>
  <v-container>
    <h1 class="text-h4 font-weight-bold mb-4">Relatórios Financeiros</h1>
    <v-card class="mb-6 pa-4">
        <v-card-title>Receitas vs Despesas (Últimos 6 Meses)</v-card-title>
        <v-card-text v-if="loaded" style="height: 400px; position: relative;">
            <Bar :data="chartData" :options="chartOptions" />
        </v-card-text>
        <v-card-text v-else class="h-100 d-flex align-center justify-center" style="min-height: 300px;">
             <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { useAuthStore } from '../stores/auth'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const authStore = useAuthStore()
const loaded = ref(false)
const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false
})

onMounted(async () => {
    try {
        const response = await authStore.apiFetch('/reports/monthly')
        const data = await response.json()
        
        chartData.value = data
        loaded.value = true
    } catch (e) {
        console.error(e)
    }
})
</script>

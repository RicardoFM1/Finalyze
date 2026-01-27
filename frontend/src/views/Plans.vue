<template>
  <v-container>
    <div class="text-center mb-10">
      <h1 class="text-h3 font-weight-bold mb-2">Preços Simples e Transparentes</h1>
      <p class="text-subtitle-1 text-medium-emphasis">Escolha o plano que se adapta aos seus objetivos financeiros</p>
    </div>

    <v-row v-if="loading" justify="center" class="mt-8">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </v-row>

    <v-row v-else justify="center">
      <v-col v-for="plan in plans" :key="plan.id" cols="12" md="4">
        <PlanCard :plan="plan" @select="handleSelectPlan" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PlanCard from '../components/PlanCard.vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const router = useRouter()
const authStore = useAuthStore()
const plans = ref([])
const loading = ref(true)

onMounted(async () => {
  

  if (router.currentRoute.value.query.msg === 'no_plan') {
    toast.warning('Você precisa de um plano ativo para acessar essa área. Escolha um plano abaixo!')
  }
  
  try {
    const response = await authStore.apiFetch('/plans')
    const data = await response.json()
    plans.value = data
  } catch (error) {
    console.error('Erro ao buscar planos:', error)
  } finally {
    loading.value = false
  }

})

const handleSelectPlan = (plan) => {
  
  if (authStore.isAuthenticated) {
      router.push({ path: '/pagamento' })
  } else {
    
      router.push({ path: '/login', query: { redirect: 'pagamento', plan: plan.id } })
  }
}
</script>

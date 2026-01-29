<template>
  <v-container class="py-6 py-md-12">

    <div class="text-center mb-10">
  <h1 class="hero-animate text-h4 text-md-h3 font-weight-bold mb-2">
    Preços Simples e Transparentes
  </h1>
  <p class="subtitle-animate text-body-2 text-md-subtitle-1 text-medium-emphasis">
    Escolha o plano que se adapta aos seus objetivos financeiros
  </p>
</div>


    <v-row v-if="loading" justify="center" align="center" style="min-height: 40vh">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </v-row>

    <v-row v-else justify="center">
      <v-col
  v-for="plan in plans"
  :key="plan.id"
  cols="12"
  sm="10"
  md="4"
  class="d-flex justify-center"
>
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



<style scoped> 

.hero-animate {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeSlideUp 0.9s ease-out forwards;
}

.subtitle-animate {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeSlideUp 0.9s ease-out forwards;
  animation-delay: 0.15s;
}
@media (max-width: 600px) {
  .hero-animate {
    font-size: 1.6rem;
    line-height: 1.2;
  }

  .subtitle-animate {
    font-size: 0.95rem;
  }
}


@keyframes fadeSlideUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}


</style>
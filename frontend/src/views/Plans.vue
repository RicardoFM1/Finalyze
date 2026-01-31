<template>
  <v-container class="py-12 px-4 px-md-10 bg-surface">
    <div class="header-section text-center mb-16 px-4">
      <h1 class="hero-animate text-h4 text-md-h2 font-weight-black mb-4 primary--text">
        Invista no seu Futuro Financeiro
      </h1>
      <p class="subtitle-animate text-body-1 text-md-h6 text-medium-emphasis mx-auto" style="max-width: 700px">
        Escolha o plano perfeito para transformar seus hábitos e alcançar a liberdade que você merece. Transparentes, simples e focados em você.
      </p>
    </div>

    <v-row v-if="loading" justify="center" align="center" style="min-height: 40vh">
        <v-progress-circular indeterminate color="primary" size="80" width="8"></v-progress-circular>
    </v-row>

    <v-row v-else justify="center" class="plan-grid">
      <v-col
        v-for="(plan, index) in plans"
        :key="plan.id"
        cols="12"
        sm="10"
        md="4"
        lg="3"
        class="d-flex justify-center mb-8 px-md-4"
        :style="{ animationDelay: (index * 0.1) + 's' }"
      >
        <PlanCard :plan="plan" :is-featured="index === 1" :disabled="checkingPreference" @select="handleSelectPlan" />
      </v-col>
    </v-row>

    <v-dialog v-model="showPendingDialog" persistent max-width="500">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h5 font-weight-bold text-center">
            Pagamento Pendente Encontrado
        </v-card-title>
        <v-card-text class="text-body-1 text-center py-4">
          Você já iniciou o pagamento para o plano <strong>{{ pendingPlanName }}</strong>. 
          Deseja continuar de onde parou ou deseja cancelar esse pagamento para escolher um novo plano?
        </v-card-text>
        <v-card-actions class="justify-center gap-4">
          <v-btn
            variant="outlined"
            color="error"
            class="rounded-lg px-6"
            :loading="cancelling"
            @click="cancelSubscription"
          >
            Cancelar Anterior
          </v-btn>
          <v-btn
            variant="flat"
            color="primary"
            class="rounded-lg px-6"
            @click="continuePayment"
          >
            Continuar Pagamento
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
const checkingPreference = ref(false)
const showPendingDialog = ref(false)
const pendingPlanName = ref('')
const cancelling = ref(false)
const currentSubscription = ref(null)

onMounted(async () => {
  try {
    const plansResponse = await authStore.apiFetch('/plans')
    if (plansResponse.ok) {
        plans.value = await plansResponse.json()
    }
  } catch (error) {
    console.error('Erro ao buscar planos:', error)
  } finally {
    loading.value = false
  }

  if (authStore.isAuthenticated) {
    checkingPreference.value = true
    try {
        const prefResponse = await authStore.apiFetch('/checkout/preference')
        if (prefResponse.ok) {
            const data = await prefResponse.json()
            if (data.id && data.plan) {
                currentSubscription.value = data
                pendingPlanName.value = data.plan.name
                showPendingDialog.value = true
            }
        }
    } catch (e) {
        console.error('Erro ao buscar preferência:', e)
    } finally {
        checkingPreference.value = false
    }
  }

})

const handleSelectPlan = ({ plan, period }) => {
    router.push({ 
        name: 'Checkout', 
        query: { plan: plan.id, period: period.id } 
    })
}

const continuePayment = () => {
    router.push({ name: 'Checkout' })
}

const cancelSubscription = async () => {
    try {
        cancelling.value = true
        const response = await authStore.apiFetch('/checkout/cancel-subscription', {
            method: 'POST'
        })
        if (response.ok) {
            toast.success('Assinatura anterior cancelada.')
            showPendingDialog.value = false
            currentSubscription.value = null
        }
    } catch (e) {
        toast.error('Erro ao cancelar assinatura.')
    } finally {
        cancelling.value = false
    }
}
</script>



<style scoped> 
.bg-surface {
  background-color: transparent !important;
}

.header-section {
  position: relative;
  z-index: 1;
}

.hero-animate {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeSlideUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  background: linear-gradient(135deg, #1867C0 30%, #5CBBF6 90%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.subtitle-animate {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeSlideUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  animation-delay: 0.2s;
  line-height: 1.6;
}

.plan-grid > .v-col {
  opacity: 0;
  transform: translateY(40px);
  animation: fadeSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@media (max-width: 600px) {
  .hero-animate {
    font-size: 2rem !important;
    line-height: 1.1;
  }
}

@keyframes fadeSlideUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
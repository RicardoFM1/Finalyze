<template>
  <v-container>
    <div class="text-center mb-10">
      <h1 class="text-h3 font-weight-bold mb-2">{{ $t('plans.title') }}</h1>
      <p class="text-subtitle-1 text-medium-emphasis">{{ $t('plans.subtitle') }}</p>
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
        md="6"
        lg="4"
        class="d-flex justify-center px-md-4 "
        :style="{ animationDelay: (index * 0.1) + 's' }"
      >
        <PlanCard :plan="plan" :is-featured="index === 1" class="plan-card" :disabled="checkingPreference" @select="handleSelectPlan" />
      </v-col>
    </v-row>

    <ModalBase v-model="showPendingDialog" :title="$t('plans.pending_title')" maxWidth="500px" persistent>
        <p class="text-body-1 text-center mb-4">
          {{ $t('plans.pending_desc', { plan: pendingPlanName }) }}
        </p>
        <template #actions>
          <v-btn
            variant="outlined"
            color="error"
            class="rounded-lg px-6"
            :loading="cancelling"
            @click="cancelarPagamento"
          >
            {{ $t('plans.cancel_prev') }}
          </v-btn>
          <v-btn
            variant="flat"
            color="primary"
            class="rounded-lg px-6 ml-4"
            @click="continuePayment"
          >
            {{ $t('plans.continue') }}
          </v-btn>
        </template>
    </ModalBase>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PlanCard from '../components/PlanCard.vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../components/Modals/modalBase.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
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
    const plansResponse = await authStore.apiFetch('/planos')
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
        const prefResponse = await authStore.apiFetch('/checkout/preferencia')
        if (prefResponse.ok) {
            const data = await prefResponse.json()
            if (data.id && data.plano) {
                currentSubscription.value = data
                pendingPlanName.value = data.plano.nome
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

const cancelarPagamento = async () => {
    try {
        cancelling.value = true
        const response = await authStore.apiFetch('/checkout/cancelar_pagamento', {
            method: 'POST'
        })
        if (response.ok) {
            toast.success(t('plans.toast_cancel_success'))
            showPendingDialog.value = false
            currentSubscription.value = null
        }
    } catch (e) {
        toast.error(t('plans.toast_cancel_error'))
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

.plan-card {
  width: 100%;
  max-width: 420px;
}


.plan-card.featured {
  transform: scale(1.05);
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
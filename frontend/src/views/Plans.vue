<template>
  <v-container>
    <div class="text-center mb-10">
      <h1 class="text-h3 font-weight-bold mb-2">{{ $t('plans.title') }}</h1>
      <p class="text-subtitle-1 text-medium-emphasis">{{ $t('plans.subtitle') }}</p>
      
      <v-row justify="center" class="mt-4">
        <v-col cols="12" md="8">
          <v-alert
            color="info"
            variant="tonal"
            icon="mdi-swap-horizontal"
            class="rounded-lg text-left"
            density="comfortable"
          >
            <div class="text-subtitle-2 font-weight-bold mb-1">Dica de Upgrade</div>
            Ao mudar de plano, o tempo restante do seu plano atual é convertido em crédito e descontado automaticamente no valor do novo plano.
          </v-alert>
        </v-col>
      </v-row>
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
          <div class="d-flex w-100 flex-column flex-sm-row justify-end gap-2">
            <v-btn
                variant="outlined"
                color="error"
                class="mb-2 mb-sm-0"
                :loading="cancelling"
                :disabled="cancelling || continuing"
                @click="cancelarPagamento"
            >
                {{ $t('plans.cancel_prev') }}
            </v-btn>
            <v-btn
                variant="flat"
                color="primary"
                class="ml-0 ml-sm-4 px-6 py-2"
                min-height="44"
                :loading="continuing"
                :disabled="cancelling || continuing"
                @click="continuePayment"
            >
                {{ $t('plans.continue') }}
            </v-btn>
          </div>
        </template>
    </ModalBase>

    <!-- Modal para Upgrade Gratuito (Prorrata) -->
    <ModalBase v-model="showFreeUpgradeModal" title="Upgrade Especial!" maxWidth="500px">
        <div class="text-center pa-4">
            <v-icon color="success" size="64" class="mb-4">mdi-gift-outline</v-icon>
            <h3 class="text-h5 font-weight-bold mb-2">Você ganhou um Upgrade!</h3>
            <p class="text-body-1 text-medium-emphasis mb-4">
                Seus créditos do plano atual (R$ {{ selectedForUpgrade?.creditos }}) cobrem totalmente o valor do novo plano.
                Deseja migrar agora mesmo de forma gratuita?
            </p>
            
            <v-card variant="tonal" color="success" class="pa-3 rounded-lg mb-6">
                <div class="d-flex justify-space-between align-center">
                    <span class="text-subtitle-2">Novo Plano:</span>
                    <span class="font-weight-bold">{{ selectedForUpgrade?.plan.nome }} ({{ selectedForUpgrade?.period.nome }})</span>
                </div>
                <div class="d-flex justify-space-between align-center mt-1">
                    <span class="text-subtitle-2">Custo zero:</span>
                    <span class="text-success font-weight-bold">R$ 0,00</span>
                </div>
            </v-card>

            <div class="d-flex flex-column gap-2">
                <v-btn
                    block
                    color="success"
                    size="large"
                    class="rounded-pill"
                    :loading="upgrading"
                    @click="applyFreeUpgrade"
                >
                    Confirmar Upgrade Grátis
                </v-btn>
                <v-btn
                    block
                    variant="text"
                    color="grey"
                    @click="showFreeUpgradeModal = false"
                >
                    Talvez depois
                </v-btn>
            </div>
        </div>
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
const continuing = ref(false)
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

const handleSelectPlan = async ({ plan, period }) => {
    // If not authenticated or same plan/period, just go to checkout (or let guard handle)
    if (!authStore.isAuthenticated) {
        router.push({ name: 'Checkout', query: { plan: plan.id, period: period.id } })
        return
    }

    try {
        checkingPreference.value = true
        const response = await authStore.apiFetch(`/checkout/check-upgrade?plano_id=${plan.id}&periodo_id=${period.id}`)
        if (response.ok) {
            const data = await response.json()
            if (data.gratuito) {
                selectedForUpgrade.value = { plan, period, creditos: data.creditos }
                showFreeUpgradeModal.value = true
                return
            }
        }
    } catch (e) {
        console.error('Erro ao verificar upgrade:', e)
    } finally {
        checkingPreference.value = false
    }

    router.push({ 
        name: 'Checkout', 
        query: { plan: plan.id, period: period.id } 
    })
}

const showFreeUpgradeModal = ref(false)
const selectedForUpgrade = ref(null)
const upgrading = ref(false)

const applyFreeUpgrade = async () => {
    if (!selectedForUpgrade.value) return
    
    try {
        upgrading.value = true
        const response = await authStore.apiFetch('/checkout/apply-free-upgrade', {
            method: 'POST',
            body: JSON.stringify({
                plano_id: selectedForUpgrade.value.plan.id,
                periodo_id: selectedForUpgrade.value.period.id
            })
        })
        
        if (response.ok) {
            toast.success('Upgrade realizado com sucesso! Aproveite seu novo plano.')
            showFreeUpgradeModal.value = false
            await authStore.fetchUser()
            router.push({ name: 'Dashboard' })
        } else {
            const data = await response.json()
            toast.error(data.error || 'Erro ao processar upgrade.')
        }
    } catch (e) {
        toast.error('Erro de conexão.')
    } finally {
        upgrading.value = false
    }
}

const continuePayment = () => {
    continuing.value = true
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
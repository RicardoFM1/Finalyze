<template>
  <v-card 
    class="plan-card overflow-visible" 
    :class="{ 'featured-card': isFeatured }"
    :elevation="isFeatured ? 12 : 2"
    rounded="xl"
    :max-width="isFeatured ? 380 : 344"
  >
    <div v-if="isFeatured" class="popular-badge">
      <v-chip color="white" text-color="primary" size="small" variant="flat" class="font-weight-bold px-4">
        MAIS POPULAR
      </v-chip>
    </div>

    <v-card-item class="pt-8 pb-4 text-center">
      <v-card-title class="text-h5 font-weight-black mb-2 plan-name">{{ plan.name }}</v-card-title>
      <div class="price-container my-4">
        <span class="currency">R$</span>
        <span class="price-integer">{{ Math.floor(plan.price_cents / 100) }}</span>
        <span class="price-decimal">,{{ (plan.price_cents % 100).toString().padStart(2, '0') }}</span>
        <span class="interval text-medium-emphasis">/mês</span>
      </div>
      <v-card-subtitle class="description-text px-4" v-html="plan.description"></v-card-subtitle>
    </v-card-item>

    <v-card-text class="px-6">
      <v-divider class="mb-6 opacity-20"></v-divider>
      <v-list density="comfortable" class="bg-transparent pa-0">
        <v-list-item 
          v-for="(feature, i) in plan.features" 
          :key="i" 
          class="px-0 py-1"
          min-height="32"
        >
          <template v-slot:prepend>
            <v-icon color="success" icon="mdi-check-circle" size="18" class="mr-3"></v-icon>
          </template>
          <span class="text-body-2 font-weight-medium text-grey-darken-3">{{ feature }}</span>
        </v-list-item>
      </v-list>
    </v-card-text>

    <v-card-actions class="pa-6 pt-2">
      <v-btn
        :loading="loadingEscolher"
        :color="isCurrentPlan ? 'success' : (isFeatured ? 'primary' : 'primary')"
        variant="flat"
        block
        height="54"
        class="action-btn text-none text-subtitle-1 font-weight-bold rounded-lg"
        @click="clickEscolha"
      >
        <v-icon v-if="isCurrentPlan" start icon="mdi-refresh" class="mr-2"></v-icon>
        {{ buttonText }}
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
const router = useRouter()
const props = defineProps({
  plan: {
    type: Object,
    required: true
  },
  isFeatured: {
    type: Boolean,
    default: false
  }
})
defineEmits(['select'])

const loadingEscolher = ref(false)
const authStore = useAuthStore()
const preferenceId = ref(null)

const isCurrentPlan = computed(() => {
    return authStore.user?.plan_id === props.plan.id
})

const buttonText = computed(() => {
    if (isCurrentPlan.value) return 'Renovar / Estender'
    if (authStore.user?.plan_id) return 'Mudar para ' + props.plan.name
    return 'Escolher ' + props.plan.name
})
const clickEscolha = async () => {
try {
  loadingEscolher.value = true
        const response = await authStore.apiFetch('/checkout/preference', {
            method: 'POST',
            body: JSON.stringify({
                plan_id: props.plan.id
            })
        })
        const data = await response.json()
        preferenceId.value = data.id
        if (response.ok){
          router.push({ path: '/pagamento' })
        }
    } catch (e) {
        console.error('Erro ao criar preferência:', e)
    }finally{
      loadingEscolher.value = false 
    }
}

const formatPrice = (value) => {
    if (!value && value !== 0) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

<style scoped>
.plan-card {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

.plan-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
  border-color: rgba(24, 103, 192, 0.2);
}

.featured-card {
  border: 2px solid #1867C0;
  background: linear-gradient(to bottom, #ffffff, #f8faff);
  transform: scale(1.05);
}

.featured-card:hover {
  transform: translateY(-8px) scale(1.05) !important;
}

.popular-badge {
  position: absolute;
  top: -16px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}

.popular-badge .v-chip {
  background: linear-gradient(135deg, #1867C0, #5CBBF6) !important;
  box-shadow: 0 4px 10px rgba(24, 103, 192, 0.3);
}

.plan-name {
  color: #1a237e;
  letter-spacing: -0.5px;
}

.price-container {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 2px;
}

.currency {
  font-size: 1.25rem;
  font-weight: 700;
  align-self: flex-start;
  margin-top: 4px;
}

.price-integer {
  font-size: 3.5rem;
  font-weight: 900;
  line-height: 1;
  letter-spacing: -2px;
}

.price-decimal {
  font-size: 1.25rem;
  font-weight: 700;
}

.interval {
  font-size: 1rem;
  margin-left: 4px;
}

.description-text {
  font-size: 0.9rem;
  line-height: 1.4;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn {
  transition: all 0.2s ease;
}

.action-btn:not(:disabled):hover {
  box-shadow: 0 6px 15px rgba(24, 103, 192, 0.2);
  transform: scale(1.02);
}
</style>

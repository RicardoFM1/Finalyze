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
      <v-card-title class="text-h5 font-weight-black mb-2 plan-name">{{ plan.nome }}</v-card-title>
      
   
      <div v-if="plan.periodos.length" class="text-center my-3">
        <div class="text-caption text-medium-emphasis mb-2 font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem !important;">
          Período
        </div>
        
        <v-chip-group
          v-model="selectedPeriodId"
          mandatory
          selected-class="bg-primary text-white"
          class="justify-center"
          column
        >
          <v-chip
            v-for="periodo in plan.periodos"
            :key="periodo.id"
            :value="periodo.id"
            variant="outlined"
            filter
            class="ma-1 font-weight-bold"
            size="small"
            style="border-color: rgba(var(--v-theme-primary), 0.2);"
          >
            {{ periodo.nome }}
          </v-chip>
        </v-chip-group>
      </div>

      <div class="price-container my-4">
        <span class="currency">{{ priceParts.symbol }}</span>
        <span class="price-integer">{{ priceParts.integer }}</span>
        <span class="price-decimal">{{ priceParts.decimalSeparator }}{{ priceParts.decimal }}</span>
        <span class="interval text-medium-emphasis">/{{ selectedPeriodSlug }}</span>
      </div>
      
      <v-chip 
        v-if="currentDiscount > 0" 
        color="success" 
        variant="flat" 
        size="small" 
        class="mb-3 font-weight-bold"
      >
        <v-icon start size="small">mdi-tag</v-icon>
        {{ currentDiscount }}% de desconto
      </v-chip>
      
      <v-card-subtitle class="description-text px-4" v-html="plan.descricao"></v-card-subtitle>
    </v-card-item>

    <v-card-text class="px-6">
      <v-divider class="mb-6 opacity-20"></v-divider>
      <v-list density="comfortable" class="bg-transparent pa-0">
        <v-list-item 
          v-for="feature in plan.recursos" 
          :key="feature.id" 
          class="px-0 py-1"
          min-height="32"
        >
          <template v-slot:prepend>
            <v-icon color="success" icon="mdi-check-circle" size="18" class="mr-3"></v-icon>
          </template>
          <span class="text-body-2 font-weight-medium text-grey-darken-3">{{ feature.nome }}</span>
        </v-list-item>
        <v-list-item 
          v-if="plan.limite_lancamentos"
          class="px-0 py-1"
          min-height="32"
        >
          <template v-slot:prepend>
            <v-icon color="success" icon="mdi-check-circle" size="18" class="mr-3"></v-icon>
          </template>
          <span class="text-body-2 font-weight-medium feature-text">
            {{ plan.limite_lancamentos >= 100000 ? $t('plans.launch_unlimited') : $t('plans.launch_limits', { count: plan.limite_lancamentos }) }}
          </span>
        </v-list-item>
      </v-list>
    </v-card-text>

    <v-card-actions class="pa-6 pt-2">
      <v-btn
        :disabled="disabled"
        :loading="loadingEscolher"
        :color="isCurrentPlan ? 'success' : (isFeatured ? 'primary' : 'primary')"
        variant="flat"
        block
        min-height="54"
        class="action-btn text-none text-subtitle-1 font-weight-bold rounded-lg py-3"
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
import { useCurrency } from '../composables/useCurrency'
const router = useRouter()
const authStore = useAuthStore()
const { currency, locale, convert } = useCurrency()
const props = defineProps({
  plan: {
    type: Object,
    required: true
  },
  isFeatured: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
})
const selectedPeriodId = ref(props.plan.periodos?.[1]?.id || props.plan.periodos?.[0]?.id)
const selectedPeriod = computed(() => {
    return props.plan.periodos.find(p => p.id === selectedPeriodId.value)
})

const currentPrice = computed(() => {
    return selectedPeriod.value?.pivot?.valor_centavos || 0
})

const convertedPrice = computed(() => {
    return convert(currentPrice.value / 100, 'BRL', currency.value)
})

const priceParts = computed(() => {
    const formatter = new Intl.NumberFormat(locale.value, {
        style: 'currency',
        currency: currency.value
    })
    const parts = formatter.formatToParts(convertedPrice.value)
    const symbol = parts.find((part) => part.type === 'currency')?.value || currency.value
    const integer = parts.filter((part) => part.type === 'integer' || part.type === 'group').map((part) => part.value).join('')
    const decimalSeparator = parts.find((part) => part.type === 'decimal')?.value || ','
    const decimal = parts.find((part) => part.type === 'fraction')?.value || '00'
    return { symbol, integer, decimalSeparator, decimal }
})

const selectedPeriodSlug = computed(() => {
    const slug = selectedPeriod.value?.slug || 'mensal' || 'weekly'
    const periodText = {
        'semanal': 'sem',
        'mensal': 'mês',
        'trimestral': 'tri',
        'anual': 'ano'
    }
    return periodText[slug] || 'mês'
})

const currentDiscount = computed(() => {
    return selectedPeriod.value?.pivot?.percentual_desconto || 0
})

const isCurrentPlan = computed(() => {
    return authStore.user?.plano_id === props.plan.id
})

const buttonText = computed(() => {
    if (isCurrentPlan.value) return 'Renovar / Estender'
    if (authStore.user?.plano_id) return 'Mudar para ' + props.plan.nome
    return 'Escolher ' + props.plan.nome
})

const emit = defineEmits(['select'])

const clickEscolha = () => {
    emit('select', { 
        plan: props.plan, 
        period: selectedPeriod.value 
    })
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
  line-height: 1.5;
  min-height: 48px;
  display: block;
  text-align: center;
  white-space: normal;
}

.action-btn {
  transition: all 0.2s ease;
}

.action-btn:not(:disabled):hover {
  box-shadow: 0 6px 15px rgba(24, 103, 192, 0.2);
  transform: scale(1.02);
}
</style>

<template>
  <div class="payment-brick-wrapper">
    <div v-if="loading && !error" class="loading-state d-flex flex-column align-center justify-center pa-10 text-center">
      <v-progress-circular indeterminate color="primary" size="64" width="6"></v-progress-circular>
      <span class="mt-4 text-subtitle-1">Iniciando checkout seguro...</span>
    </div>

    <div id="paymentBrick_container" :style="{ visibility: loading ? 'hidden' : 'visible', minHeight: '300px' }"></div>

    <v-alert v-if="error" type="error" variant="tonal" class="mt-4">
      {{ error }}
      <v-btn block color="error" class="mt-4" @click="reload">Recarregar Página</v-btn>
    </v-alert>

    <div v-if="!loading && !error && !brickMounted" class="mt-4 text-center">
      <v-btn color="primary" @click="initMercadoPago">Tentar Carregar Novamente</v-btn>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const amount = ref(null)
const preferenceId = ref(null)
const planId = ref(null)
const loading = ref(true)
const error = ref(null)
const brickMounted = ref(false)
let brickController = null

const reload = () => window.location.reload()

const API_URL = import.meta.env.VITE_API_URL
if (!API_URL) console.error('[PaymentBrick] VITE_API_URL não configurada!')

const initMercadoPago = async () => {
  if (!preferenceId.value || brickMounted.value) return

  loading.value = true
  error.value = null

  try {
    // Espera o script do Mercado Pago carregar
    let attempts = 0
    while (!window.MercadoPago && attempts < 20) {
      await new Promise(resolve => setTimeout(resolve, 500))
      attempts++
    }
    if (!window.MercadoPago) {
      error.value = "Script do Mercado Pago não pôde ser carregado. Verifique extensões bloqueando."
      loading.value = false
      return
    }

    let publicKey = import.meta.env.VITE_MERCADO_PAGO_PUBLIC_KEY
    if (!publicKey || publicKey.length > 50) {
      console.warn('[Brick] Public Key inválida ou não configurada. Usando fallback de teste.')
      publicKey = 'APP_USR-5a0a3834-8c4c-47ea-a49d-3ee5f0d2ced8'
    }

    const mp = new window.MercadoPago(publicKey, { locale: 'pt-BR' })
    const bricksBuilder = mp.bricks()

    if (brickController) await brickController.unmount()

    brickController = await bricksBuilder.create('payment', 'paymentBrick_container', {
      initialization: { amount: amount.value, preferenceId: preferenceId.value },
      customization: {
        paymentMethods: {
          ticket: 'all',
          bankTransfer: 'all',
          creditCard: 'all',
          debitCard: 'all',
          mercadoPago: 'all'
        }
      },
      callbacks: {
        onReady: () => {
          loading.value = false
          brickMounted.value = true
        },
        onSubmit: ({ formData }) => {
  return new Promise(async (resolve, reject) => {
    if (!API_URL) {
      error.value = 'Backend não configurado. Verifique VITE_API_URL.'
      reject()
      return
    }

    // Garantir campos obrigatórios antes de enviar
    if (!formData.transaction_amount || !formData.payment_method_id) {
      error.value = 'Campos obrigatórios faltando'
      reject()
      return
    }

    try {
      const response = await fetch(`${API_URL}/checkout/process_payment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(formData)
      })

      let result = {}
      try { result = await response.json() } catch { result = {} }

      if (response.ok) {
        if (result.status === 'approved') window.location.href = '/painel?status=success'
        else if (result.status === 'in_process') window.location.href = '/painel?status=pending'
        else error.value = "Pagamento não aprovado. Verifique os dados e tente novamente."
        resolve()
      } else {
        error.value = result.error || 'Erro no processamento do pagamento'
        reject()
      }
    } catch (err) {
      console.error('Erro no processamento:', err)
      error.value = "Houve um erro técnico ao processar seu pagamento. Tente novamente mais tarde."
      reject()
    }
  })
},

        onError: (err) => {
          console.error('Erro no Brick:', err)
          error.value = "Erro na renderização do Mercado Pago: " + (err.message || JSON.stringify(err))
          loading.value = false
        }
      }
    })
  } catch (e) {
    console.error('Erro ao inicializar brick:', e)
    error.value = "Falha ao carregar o pagamento: " + e.message
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const response = await authStore.apiFetch('/checkout/preference')
    if (!response.ok) throw new Error('Preferência não encontrada')

    const data = await response.json()
    amount.value = data.plan.price_cents / 100
    planId.value = data.plan.id
    preferenceId.value = data.id
  } catch (e) {
    console.error('Erro ao carregar checkout:', e)
    router.push('/planos')
  }
})

onUnmounted(() => { if (brickController) brickController.unmount() })
watch(() => preferenceId.value, (newId) => { if (newId) initMercadoPago() })

</script>

<style scoped>
.payment-brick-wrapper {
  min-height: 400px;
}
#paymentBrick_container {
  width: 100%;
}
#paymentBrick_container svg {
  width: auto !important;
  height: auto !important;
}
</style>

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

    <v-dialog v-model="showQrDialog" persistent max-width="480">
      <v-card class="rounded-xl overflow-hidden" elevation="10">
        <div class="bg-primary px-6 py-4 text-center position-relative">
            <v-btn
              icon="mdi-close"
              variant="text"
              color="white"
              size="small"
              class="position-absolute"
              style="top: 8px; right: 8px;"
              @click="showQrDialog = false"
            ></v-btn>
            <span class="text-h6 text-white font-weight-bold">Pagamento via Pix</span>
            <div class="text-caption text-white opacity-80 mt-1">Escaneie ou copie o código abaixo</div>
        </div>

        <v-card-text class="pa-6 text-center">
          <div class="d-flex justify-center mb-6">
             <div class="rounded-lg border pa-2 bg-white elevation-2" v-if="qrCodeBase64">
                <img :src="'data:image/png;base64,' + qrCodeBase64" style="width: 200px; height: 200px; display: block;" />
             </div>
          </div>

          <div class="mb-4">
            <div class="d-flex justify-space-between text-caption font-weight-bold mb-1">
              <span>O código expira em:</span>
              <span>{{ formatTime(countdownPercentage) }}</span>
            </div>
            <v-progress-linear
              :model-value="(countdownPercentage / 600) * 100"
              color="primary"
              height="8"
              rounded
            ></v-progress-linear>
          </div>

          
          <div class="bg-grey-lighten-4 rounded-lg pa-4 mb-4 text-left">
             <div class="text-caption font-weight-bold text-grey-darken-1 mb-2">Pix Copia e Cola</div>
             
             <div class="d-flex align-center bg-white rounded border pa-2 pr-1">
                <div class="text-caption text-truncate flex-grow-1 text-grey-darken-3 px-2" style="max-width: 250px;">
                    {{ qrCodeCopy }}
                </div>
                <v-btn 
                    color="primary" 
                    variant="flat" 
                    size="small" 
                    class="rounded px-4"
                    @click="copyToClipboard"
                >
                    <v-icon start icon="mdi-content-copy" size="small"></v-icon>
                    Copiar
                </v-btn>
             </div>
          </div>

          <v-divider class="my-4"></v-divider>
          
          <div class="d-flex align-center justify-center text-body-2 text-grey-darken-2">
             <v-progress-circular indeterminate color="primary" size="20" width="2" class="mr-3"></v-progress-circular>
             Aguardando confirmação do pagamento...
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
    
    <v-snackbar v-model="showCopiedStart" color="success" timeout="2000" location="top">
        <div class="d-flex align-center">
            <v-icon start icon="mdi-check-circle"></v-icon>
            Código Pix copiado com sucesso!
        </div>
    </v-snackbar>

    <div v-if="!loading && !error && !brickMounted" class="mt-4 text-center">
      <v-btn color="primary" @click="initMercadoPago">Tentar Carregar Novamente</v-btn>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const router = useRouter()

const amount = ref(null)
const preferenceId = ref(null)
const planId = ref(null)
const loading = ref(true)
const error = ref(null)
const brickMounted = ref(false)
let brickController = null

const showQrDialog = ref(false)
const showCopiedStart = ref(false)
const qrCodeBase64 = ref(null)
const qrCodeCopy = ref(null)
const paymentId = ref(null)
let pollInterval = null

const countdownPercentage = ref(600) // 10 minutes
let countdownInterval = null

const startCountdown = () => {
    countdownPercentage.value = 600
    if (countdownInterval) clearInterval(countdownInterval)
    countdownInterval = setInterval(() => {
        if (countdownPercentage.value > 0) {
            countdownPercentage.value--
        } else {
            clearInterval(countdownInterval)
            showQrDialog.value = false
            error.value = "O código Pix expirou. Tente novamente."
        }
    }, 1000)
}

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

const reload = () => window.location.reload()

const API_URL = import.meta.env.VITE_API_URL
if (!API_URL) console.error('[PaymentBrick] VITE_API_URL não configurada!')

const copyToClipboard = () => {
    if (qrCodeCopy.value) {
        navigator.clipboard.writeText(qrCodeCopy.value)
        showCopiedStart.value = true
    }
}



const pollPaymentStatus = async () => {
    if (!paymentId.value) return
    
    try {
        const response = await authStore.apiFetch(`/checkout/status/${paymentId.value}`)
        if (response.ok) {
            const data = await response.json()
            if (data.status === 'approved') {
                clearInterval(pollInterval)
                showCopiedStart.value = false 
                
                showQrDialog.value = false
                
                toast.success('Pagamento aprovado!')
                setTimeout(() => {
                    window.location.href = '/'
                }, 2000)
            } else if (data.status === 'rejected' || data.status === 'cancelled') {
                 clearInterval(pollInterval)
                 showQrDialog.value = false
                 error.value = "Pagamento cancelado ou rejeitado."
            }
        }
    } catch (e) {
        console.error('Polling error', e)
    }
}

const initMercadoPago = async () => {
  if (!preferenceId.value || !amount.value || brickMounted.value) return

  loading.value = true
  error.value = null

  try {
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
      initialization: {
        amount: amount.value,
        preferenceId: preferenceId.value,
        payer: {
          email: authStore.user?.email || 'email@test.com'
        }
      },
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
            
            if (!formData.transaction_amount || !formData.payment_method_id) {
              error.value = 'Campos obrigatórios faltando'
              reject()
              return
            }
            


            try {
              const response = await authStore.apiFetch('/checkout/process_payment', {
                method: 'POST',
                body: JSON.stringify({
                    ...formData,
                    plano_id: planId.value,
                    periodo_id: props.periodId
                })
              })

              let result = {}
              try { result = await response.json() } catch { result = {} }

              if (response.ok) {
                if (result.status === 'approved') {
                  toast.success('Pagamento aprovado!')
                    setTimeout(() => {
                        window.location.href = '/'
                    }, 2000)
                } 
                else if ((result.status === 'in_process' || result.status === 'pending') && result.qr_code) {
                   qrCodeBase64.value = result.qr_code_base64
                   qrCodeCopy.value = result.qr_code
                   paymentId.value = result.id
                   showQrDialog.value = true
                   startCountdown()
                   if (pollInterval) clearInterval(pollInterval)
                   pollInterval = setInterval(pollPaymentStatus, 3000)

                   resolve()
                }
                else if (result.status === 'in_process' || result.status === 'pending') {
                    window.location.href = '/painel?status=pending'
                }
                else {
                    error.value = `Pagamento não aprovado (${result.status_detail || 'motivo desconhecido'}). Tente outro meio de pagamento.`
                    resolve()
                }
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

const props = defineProps({
  preferenceId: String,
  planId: [String, Number],
  periodId: [String, Number]
})

onMounted(async () => {
  // If props are provided, use them directly
  if (props.preferenceId && props.planId) {
    preferenceId.value = props.preferenceId
    planId.value = props.planId
    
    // Fetch plan details to get the price
    try {
      const response = await authStore.apiFetch(`/plans`)
      if (response.ok) {
        const plans = await response.json()
        const plan = plans.find(p => p.id == props.planId)
        if (plan && props.periodId) {
          const period = plan.periodos?.find(p => p.id == props.periodId)
          if (period) {
            amount.value = period.pivot.valor_centavos / 100
          } else {
             // Fallback: use first period if the specified one isn't found
             const fallback = plan.periodos?.[0]
             if (fallback) {
                amount.value = fallback.pivot.valor_centavos / 100
                console.warn('[PaymentBrick] Period not found, using fallback:', fallback.id)
             }
          }
        }
      }
    } catch (e) {
      console.error('Erro ao buscar detalhes do plano:', e)
      error.value = 'Erro ao carregar informações do plano'
    }
  } else {
    // Fallback: fetch preference if no props provided
    try {
      const response = await authStore.apiFetch('/checkout/preference')
      if (!response.ok) throw new Error('Preferência não encontrada')

      const data = await response.json()
      amount.value = data.valor_centavos / 100
      planId.value = data.plano.id
      periodId.value = data.periodo_id || (data.plano.periodos?.[0]?.id)
      preferenceId.value = data.id
    } catch (e) {
      console.error('Erro ao carregar checkout:', e)
      error.value = 'Houve um problema ao carregar as informações de pagamento. Por favor, tente novamente.'
      loading.value = false
    }
  }

  // Final check: if we have required info but amount is still null, try fallback
  if (preferenceId.value && !amount.value) {
      // Last resort: if we have planId but amount is still null, try to reload/retry
       console.warn('[PaymentBrick] preferenceId exists but amount is null. Retrying fetch...')
       // Potentially reload plan details here if needed, but for now just stop loading if it's stuck
       setTimeout(() => {
           if (!amount.value && loading.value) {
               loading.value = false
               error.value = 'Não foi possível determinar o valor do plano. Tente selecionar o plano novamente.'
           }
       }, 5000)
  }
})

onUnmounted(() => { 
    if (brickController) brickController.unmount()
    if (pollInterval) clearInterval(pollInterval)
    if (countdownInterval) clearInterval(countdownInterval)
})

watch([() => preferenceId.value, () => amount.value], ([newPrefId, newAmount]) => {
    if (newPrefId && newAmount && !brickMounted.value) {
        initMercadoPago()
    }
})

</script>

<style scoped>
.payment-brick-wrapper {
  min-height: 400px;
}
#paymentBrick_container {
  width: 100%;
}
</style>

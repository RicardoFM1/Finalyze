<template>
  <div class="payment-brick-wrapper">
    <div v-if="loading && !error" class="loading-state d-flex flex-column align-center justify-center pa-10 text-center">
      <v-progress-circular indeterminate color="primary" size="64" width="6"></v-progress-circular>
      <span class="mt-4 text-subtitle-1">Iniciando checkout seguro...</span>
    </div>

    <div class="mb-4" style="max-width: 400px; margin: 0 auto;">
      <v-text-field
        v-model="cpf"
        label="CPF / CNPJ do Pagador"
        placeholder="000.000.000-00"
        variant="outlined"
        density="comfortable"
        :rules="documentRules"
        maxlength="18"
        required
      ></v-text-field>
    </div>

    <div id="paymentBrick_container" :style="{ visibility: loading ? 'hidden' : 'visible', minHeight: '300px' }"></div>

    <v-alert v-if="error" type="error" variant="tonal" class="mt-4">
      {{ error }}
      <v-btn block color="error" class="mt-4" @click="reload">Recarregar Página</v-btn>
    </v-alert>

    <v-dialog v-model="showQrDialog" persistent max-width="480">
      <v-card class="rounded-xl overflow-hidden" elevation="10">
        <div class="bg-primary px-6 py-4 text-center">
            <span class="text-h6 text-white font-weight-bold">Pagamento via Pix</span>
            <div class="text-caption text-white opacity-80 mt-1">Escaneie ou copie o código abaixo</div>
        </div>

        <v-card-text class="pa-6 text-center">
          <div class="d-flex justify-center mb-6">
             <div class="rounded-lg border pa-2 bg-white elevation-2" v-if="qrCodeBase64">
                <img :src="'data:image/png;base64,' + qrCodeBase64" style="width: 200px; height: 200px; display: block;" />
             </div>
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
const cpf = ref('')
let brickController = null

const showQrDialog = ref(false)
const showCopiedStart = ref(false)
const qrCodeBase64 = ref(null)
const qrCodeCopy = ref(null)
const paymentId = ref(null)
let pollInterval = null

const reload = () => window.location.reload()

const API_URL = import.meta.env.VITE_API_URL
if (!API_URL) console.error('[PaymentBrick] VITE_API_URL não configurada!')

const validateCPF = (cpf) => {
  cpf = cpf.replace(/[^\d]+/g, '')
  if (cpf === '') return false
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false
  let add = 0
  for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i)
  let rev = 11 - (add % 11)
  if (rev === 10 || rev === 11) rev = 0
  if (rev !== parseInt(cpf.charAt(9))) return false
  add = 0
  for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i)
  rev = 11 - (add % 11)
  if (rev === 10 || rev === 11) rev = 0
  return rev === parseInt(cpf.charAt(10))
}

const copyToClipboard = () => {
    if (qrCodeCopy.value) {
        navigator.clipboard.writeText(qrCodeCopy.value)
        showCopiedStart.value = true
    }
}

const validateCNPJ = (cnpj) => {
  cnpj = cnpj.replace(/[^\d]+/g, '')
  if (cnpj === '') return false
  if (cnpj.length !== 14) return false
  if (/^(\d)\1{13}$/.test(cnpj)) return false
  let tamanho = cnpj.length - 2
  let numeros = cnpj.substring(0, tamanho)
  let digitos = cnpj.substring(tamanho)
  let soma = 0
  let pos = tamanho - 7
  for (let i = tamanho; i >= 1; i--) {
    soma += numeros.charAt(tamanho - i) * pos--
    if (pos < 2) pos = 9
  }
  let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
  if (resultado != digitos.charAt(0)) return false
  tamanho = tamanho + 1
  numeros = cnpj.substring(0, tamanho)
  soma = 0
  pos = tamanho - 7
  for (let i = tamanho; i >= 1; i--) {
    soma += numeros.charAt(tamanho - i) * pos--
    if (pos < 2) pos = 9
  }
  resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
  return resultado == digitos.charAt(1)
}

const documentRules = [
  v => !!v || 'CPF/CNPJ é obrigatório',
  v => {
    const clean = v ? v.replace(/\D/g, '') : ''
    if (clean.length > 14) return 'Máximo 14 dígitos para CNPJ'
    if (clean.length < 11) return 'Mínimo 11 dígitos'
    
    if (clean.length <= 11) return validateCPF(clean) || 'CPF inválido'
    return validateCNPJ(clean) || 'CNPJ inválido'
  }
]


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
  if (!preferenceId.value || brickMounted.value) return

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

            if (!formData.payer) formData.payer = {}
            if (!formData.payer.identification) formData.payer.identification = {}
            
            if (!formData.payer.identification.number && cpf.value) {
               const cleanDoc = cpf.value.replace(/\D/g, '')
               formData.payer.identification.number = cleanDoc
               formData.payer.identification.type = cleanDoc.length > 11 ? 'CNPJ' : 'CPF'
            }

 
            const docClean = cpf.value.replace(/\D/g, '')
            if (docClean.length <= 11 && !validateCPF(docClean)) { error.value = 'CPF inválido.'; reject(); return }
            if (docClean.length > 11 && !validateCNPJ(docClean)) { error.value = 'CNPJ inválido.'; reject(); return }

            if (!formData.transaction_amount || !formData.payment_method_id) {
              error.value = 'Campos obrigatórios faltando'
              reject()
              return
            }
            
            if ((formData.payment_method_id === 'pix' || formData.payment_method_id === 'bolbradesco') && !formData.payer.identification.number) {
                 error.value = 'Por favor, informe o CPF/CNPJ acima.'
                 window.scrollTo(0,0)
                 reject()
                 return
            }

            try {
              const response = await authStore.apiFetch('/checkout/process_payment', {
                method: 'POST',
                body: JSON.stringify({
                    ...formData,
                    plan_id: planId.value
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

onUnmounted(() => { 
    if (brickController) brickController.unmount()
    if (pollInterval) clearInterval(pollInterval)
})
watch(() => preferenceId.value, (newId) => { if (newId) initMercadoPago() })

</script>

<style scoped>
.payment-brick-wrapper {
  min-height: 400px;
}
#paymentBrick_container {
  width: 100%;
}
</style>

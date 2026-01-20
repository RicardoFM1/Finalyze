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

const props = defineProps({
  preferenceId: String,
  amount: {
    type: Number,
    default: 29.90
  }
})

const loading = ref(true)
const error = ref(null)
const brickMounted = ref(false)
let brickController = null

const reload = () => window.location.reload()

const initMercadoPago = async () => {
    if (!props.preferenceId) return
    
    loading.value = true
    error.value = null

    try {
        // Tentar encontrar o SDK por até 10 seg
        let attempts = 0
        while (!window.MercadoPago && attempts < 20) {
            await new Promise(resolve => setTimeout(resolve, 500))
            attempts++
        }

        if (!window.MercadoPago) {
            error.value = "Script do Mercado Pago não pôde ser carregado. Verifique se há extensões bloqueando."
            loading.value = false
            return
        }

        // NOTA: A Public Key deve ser a da sua conta real.
        let publicKey = import.meta.env.VITE_MERCADO_PAGO_PUBLIC_KEY;
        
        if (!publicKey || publicKey.length > 50) {
            console.warn('[Brick] Public Key inválida ou não configurada. Usando fallback de teste.');
            publicKey = 'APP_USR-5a0a3834-8c4c-47ea-a49d-3ee5f0d2ced8';
        }


        const mp = new window.MercadoPago(publicKey, { locale: 'pt-BR' });
        const bricksBuilder = mp.bricks();

        if (brickController) {
            await brickController.unmount();
        }

        brickController = await bricksBuilder.create('payment', 'paymentBrick_container', {
            initialization: {
                amount: props.amount,
                preferenceId: props.preferenceId,
            },
            customization: {
                paymentMethods: {
                    ticket: 'all',
                    bankTransfer: 'all',
                    creditCard: 'all',
                    debitCard: 'all',
                    mercadoPago: 'all',
                },
            },
            callbacks: {
                onReady: () => {
                    loading.value = false
                    brickMounted.value = true
                    console.log('Brick ready!')
                },
                onSubmit: ({ formData }) => {
                    return new Promise((resolve, reject) => {
                        fetch('http://localhost:8000/api/process_payment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}` // Fallback para token se necessário
                            },
                            body: JSON.stringify(formData),
                        })
                        .then((response) => response.json())
                        .then((result) => {
                            if (result.status === 'approved') {
                                window.location.href = '/painel?status=success';
                                resolve();
                            } else if (result.status === 'in_process') {
                                window.location.href = '/painel?status=pending';
                                resolve();
                            } else {
                                error.value = "Pagamento não aprovado. Verifique os dados e tente novamente.";
                                reject();
                            }
                        })
                        .catch((err) => {
                            console.error('Erro no processamento:', err)
                            error.value = "Houve um erro técnico ao processar seu pagamento. Tente novamente mais tarde."
                            reject()
                        })
                    })
                },
                onError: (err) => {
                    console.error('Erro no Brick:', err)
                    error.value = "Erro na renderização do Mercado Pago: " + (err.message || JSON.stringify(err))
                    loading.value = false
                }
            }
        });
    } catch (e) {
        console.error('Erro ao inicializar brick:', e)
        error.value = "Falha ao carregar o pagamento: " + e.message
        loading.value = false
    }
}

onMounted(initMercadoPago)
onUnmounted(() => { if (brickController) brickController.unmount() })
watch(() => props.preferenceId, (newId) => { if (newId) initMercadoPago() })
</script>

<style scoped>
.payment-brick-wrapper {
  min-height: 400px;
}
#paymentBrick_container {
  width: 100%;
}
</style>






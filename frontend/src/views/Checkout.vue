<template>
  <v-container class="fill-height justify-center">
    <v-card width="600" class="pa-4">
      
      <v-card-title>Complete sua Assinatura</v-card-title>
      <v-card-text>
        <p class="mb-4" v-if="planName">Você está assinando o plano <strong>{{ planName }}</strong>.</p>
        <PaymentBrick :preferenceId="preferenceId" v-if="preferenceId" />
        <v-alert v-else type="info" class="mt-4">
            <v-progress-circular indeterminate size="20" class="mr-2"></v-progress-circular>
            Gerando pagamento...
        </v-alert>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PaymentBrick from '../components/PaymentBrick.vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const preferenceId = ref(null)
const planName = ref('Selecionado')
const selectedPlan = ref(null)

onMounted(async () => {
    try {
        const response = await authStore.apiFetch('/checkout/preference')
        if (!response.ok) throw new Error('Preferência não encontrada')
        
        const data = await response.json()
        preferenceId.value = data.id
        planName.value = data.plan.name
        selectedPlan.value = data.plan
    } catch (e) {
        console.error('Erro ao carregar checkout:', e)
        router.push('/planos')
    }
})
</script>

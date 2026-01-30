<template>
  <v-container class="fill-height justify-center">
    <v-card width="600" class="pa-4">
      
      <v-card-title>{{ $t('checkout.title') }}</v-card-title>
      <v-card-text>
        <p class="mb-4" v-if="planName">{{ $t('checkout.subtitle', { planName }) }}</p>
        <PaymentBrick :preferenceId="preferenceId" v-if="preferenceId" />
        <v-alert v-else type="info" class="mt-4">
            <v-progress-circular indeterminate size="20" class="mr-2"></v-progress-circular>
            {{ $t('checkout.Gen_pagament') }}
        </v-alert>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PaymentBrick from '../components/PaymentBrick.vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const authStore = useAuthStore()
const preferenceId = ref(null)
const planName = ref('Selecionado')

onMounted(async () => {
    const planId = route.query.plan
    if (!planId) return

    // Create Preference on Backend
    try {
        const response = await authStore.apiFetch('/checkout/preference', {
            method: 'POST',
            body: JSON.stringify({
                items: [
                    {
                        title: `Subscription Plan ${planId}`,
                        quantity: 1,
                        unit_price: 29.90 // TODO: Fetch real price from plan ID
                    }
                ]
            })
        })
        const data = await response.json()
        preferenceId.value = data.id
    } catch (e) {
        console.error('Erro ao criar preferência:', e)
    }
})
</script>

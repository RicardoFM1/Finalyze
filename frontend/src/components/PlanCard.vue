<template>
  <v-card class="mx-auto" max-width="344" elevation="4">
    <v-card-item>
      <v-card-title class="text-h5 font-weight-bold">{{ plan.name }}</v-card-title>
      <v-card-subtitle>
        <span class="text-h4 font-weight-black">{{ formatPrice(plan.price_cents / 100) }}</span> / {{ plan.interval }}
      </v-card-subtitle>
    </v-card-item>

    <v-card-text>
      <div v-html="plan.description"></div>
      <v-divider class="my-3"></v-divider>
      <v-list density="compact">
        <v-list-item v-for="(feature, i) in plan.features" :key="i" prepend-icon="mdi-check" color="primary">
            {{ feature }}
        </v-list-item>
      </v-list>
    </v-card-text>

    <v-card-actions>
      <v-btn :loading="loadingEscolher" color="primary" block variant="flat" size="large" @click="clickEscolha">
        Escolher {{ plan.name }}
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
const router = useRouter()
const props = defineProps({
  plan: {
    type: Object,
    required: true
  }
})
defineEmits(['select'])

const loadingEscolher = ref(false)
const authStore = useAuthStore()
const preferenceId = ref(null)
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

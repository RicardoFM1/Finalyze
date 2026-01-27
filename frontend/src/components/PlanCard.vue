<template>
  <v-card class="mx-auto" max-width="344" elevation="4">
    <v-card-item>
      <v-card-title class="text-h5 font-weight-bold">{{ plan.name }}</v-card-title>
      <v-card-subtitle>
        <span class="text-h4 font-weight-black">{{ formatPrice(plan.price_cents) }}</span> / {{ plan.interval }}
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
      <v-btn color="primary" block variant="flat" size="large" @click="$emit('select', plan)">
        Escolher {{ plan.name }}
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
defineProps({
  plan: {
    type: Object,
    required: true
  }
})
defineEmits(['select'])

const formatPrice = (value) => {
    if (!value && value !== 0) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

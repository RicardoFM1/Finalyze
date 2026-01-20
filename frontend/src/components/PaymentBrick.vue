<template>
  <div id="payment-brick-container"></div>
  <v-progress-circular v-if="loading" indeterminate color="primary"></v-progress-circular>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
  preferenceId: String
})

const loading = ref(true)

onMounted(async () => {
      // render brick
      console.log('Rendering brick for preference', props.preferenceId)
      loading.value = false
})
</script>

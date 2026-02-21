<template>
  <v-btn @click="exportarPdf" color="error" variant="outlined" class="ml-2 mt-4" :disabled="loading">
    <v-icon left>mdi-file-pdf-box</v-icon>
    {{ $t('actions.export_pdf') }}
  </v-btn>
</template>

<script setup>
const props = defineProps({
  dados: Array,
  loading: Boolean
})

const emit = defineEmits(['exportar'])

const exportarPdf = () => {
  emit('exportar')
}
</script>

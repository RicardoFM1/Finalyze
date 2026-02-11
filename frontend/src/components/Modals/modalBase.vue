<template>
  <v-dialog v-model="dialog" :max-width="maxWidth" :persistent="persistent" class="modal-base-dialog">
    <v-card class="rounded-xl overflow-hidden elevation-12">
      <v-toolbar color="primary" density="comfortable">
        <v-toolbar-title class="font-weight-bold text-wrap" style="line-height: 1.2; overflow: visible; white-space: normal;">{{ title }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" variant="text" @click="close"></v-btn>
      </v-toolbar>
      
      <v-card-text class="pa-6 pb-2" style="max-height: 75vh; overflow-y: auto;">
        <slot></slot>
      </v-card-text>

      <v-card-actions v-if="$slots.actions" class="pa-6 pt-2">
        <slot name="actions"></slot>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
    required: true
  },
  title: {
    type: String,
    default: 'Título do Modal',
    required: true
  },
  maxWidth: {
    type: String,
    default: '500px'
  },
  persistent: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue'])

const dialog = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

const close = () => {
  emit('update:modelValue', false)
}
</script>

<style>
/* Sem efeito de Blur no fundo do modal para performance máxima */
.modal-base-dialog .v-overlay__scrim {
  background: rgba(0, 0, 0, 0.4) !important;
  opacity: 1 !important;
}

.modal-base-dialog .v-card {
  will-change: transform, opacity;
}

/* Force title to wrap and not truncate */
.modal-base-dialog .v-toolbar-title__placeholder {
  white-space: normal !important;
  overflow: visible !important;
  text-overflow: clip !important;
}

/* Allow toolbar to grow if text wraps */
.modal-base-dialog .v-toolbar {
  height: auto !important;
  min-height: 48px; /* standard density comfortable height */
  padding-top: 8px;
  padding-bottom: 8px;
}
</style>

<style scoped>
/* Estilização suave para o scroll interno */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: rgba(var(--v-border-color), 0.3);
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: rgba(var(--v-border-color), 0.5);
}
</style>

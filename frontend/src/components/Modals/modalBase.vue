<template>
  <v-dialog v-model="dialog" :max-width="maxWidth" :persistent="persistent" class="modal-base-dialog">
    <v-card class="rounded-xl overflow-hidden elevation-12">
      <!-- Custom header: avoids v-toolbar-title's internal overflow:hidden clipping on mobile -->
      <div class="modal-header d-flex align-start py-3 px-4">
        <span class="modal-header-title font-weight-bold text-white flex-grow-1">
          {{ title }}
        </span>
        <v-btn icon="mdi-close" variant="text" color="white" size="small" class="ml-2 flex-shrink-0" @click="close"></v-btn>
      </div>

      <v-card-text class="pa-6 pb-2" style="max-height: 75vh; overflow-y: auto;">
        <slot></slot>
      </v-card-text>

      <v-card-actions v-if="$slots.actions" class="pa-6 pt-2 d-flex flex-wrap">
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
.modal-base-dialog .v-overlay__scrim {
  background: rgba(0, 0, 0, 0.4) !important;
  opacity: 1 !important;
}

.modal-base-dialog .v-card {
  will-change: transform, opacity;
}
</style>

<style scoped>
.modal-header {
  background-color: rgb(var(--v-theme-primary));
  min-height: 56px;
}

.modal-header-title {
  font-size: 1.1rem;
  line-height: 1.4;
  white-space: normal;
  word-break: break-word;
  overflow-wrap: break-word;
  padding-top: 2px;
}

@media (min-width: 600px) {
  .modal-header-title {
    font-size: 1.25rem;
  }
}

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

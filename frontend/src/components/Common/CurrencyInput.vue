<template>
  <v-text-field
    v-model="displayValue"
    @keydown="handleKeydown"
    @paste.prevent="handlePaste"
    :label="label"
    :variant="variant"
    :rounded="rounded"
    :required="required"
    :disabled="disabled"
    :prefix="prefix"
    :placeholder="placeholder"
    inputmode="numeric"
    v-bind="$attrs"
    autocomplete="off"
  >
    <template v-if="hint" #details>
      <div class="text-caption opacity-70">{{ hint }}</div>
    </template>
  </v-text-field>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: 0
  },
  label: String,
  variant: { type: String, default: 'outlined' },
  rounded: { type: String, default: 'lg' },
  required: Boolean,
  disabled: Boolean,
  prefix: { type: String, default: 'R$' },
  placeholder: String,
  hint: String
})

const emit = defineEmits(['update:modelValue'])

const internalValue = ref(0)

const displayValue = computed({
  get: () => {
    const val = internalValue.value / 100
    return val.toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    })
  },
  set: (val) => {
    
    parseAndEmit(val)
  }
})

watch(() => props.modelValue, (newVal) => {
  const numVal = typeof newVal === 'number' ? newVal : parseFloat(String(newVal).replace(',', '.')) || 0
  const centavos = Math.round(numVal * 100)
  if (centavos !== internalValue.value) {
    internalValue.value = centavos
  }
}, { immediate: true })

const handleKeydown = (e) => {
  
  const controlKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', 'Enter']
  if (controlKeys.includes(e.key)) {
    if (e.key === 'Backspace') {
      e.preventDefault()
      internalValue.value = Math.floor(internalValue.value / 10)
      emitValue()
    } else if (e.key === 'Delete') {
      e.preventDefault()
      internalValue.value = 0
      emitValue()
    }
    return
  }

  if (!/^\d$/.test(e.key)) {
    e.preventDefault()
    return
  }

  e.preventDefault()

  if (internalValue.value >= 999999999999) return

  internalValue.value = (internalValue.value * 10) + parseInt(e.key)
  emitValue()
}

const handlePaste = (e) => {
  const data = e.clipboardData.getData('text')
  const numericData = data.replace(/\D/g, '')
  if (numericData) {
    internalValue.value = parseInt(numericData)
    emitValue()
  }
}

const parseAndEmit = (val) => {
  const numeric = String(val).replace(/\D/g, '')
  internalValue.value = parseInt(numeric) || 0
  emitValue()
}

const emitValue = () => {
  emit('update:modelValue', internalValue.value / 100)
}
</script>

<style scoped>
:deep(.v-field__input) {
  font-family: 'Roboto Mono', monospace;
  font-weight: 600;
  text-align: right;
  letter-spacing: 0.5px;
}
</style>

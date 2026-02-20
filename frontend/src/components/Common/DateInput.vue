<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    transition="scale-transition"
    offset="8"
    min-width="auto"
  >
    <template v-slot:activator="{ props }">
      <v-text-field
        v-model="formattedDate"
        :label="label"
        :prepend-inner-icon="icon || 'mdi-calendar'"
        readonly
        variant="outlined"
        rounded="lg"
        v-bind="props"
        :required="required"
        :disabled="disabled"
        :hide-details="hideDetails"
        :rules="rules"
        class="date-input-field"
      >
        <template v-if="modelValue" v-slot:append-inner>
          <v-btn
            v-if="clearable"
            icon="mdi-close-circle"
            variant="text"
            size="x-small"
            @click.stop="clearDate"
          ></v-btn>
        </template>
      </v-text-field>
    </template>
    
    <v-card class="date-picker-card rounded-xl border-none" elevation="12">
      <VDatePicker
        v-model="internalDate"
        :mode="mode"
        :is-dark="isDark"
        :color="color"
        @update:modelValue="onDateChange"
        borderless
        transparent
        expanded
        title-position="left"
        class="custom-v-date-picker"
      />
    </v-card>
  </v-menu>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  modelValue: [String, Date],
  label: String,
  icon: String,
  required: Boolean,
  disabled: Boolean,
  clearable: Boolean,
  hideDetails: Boolean,
  mode: {
    type: String,
    default: 'date'
  },
  color: {
    type: String,
    default: 'blue'
  },
  rules: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])
const uiStore = useUiStore()
const { locale } = useI18n()

const menu = ref(false)
const isDark = computed(() => uiStore.theme === 'dark')

const internalDate = ref(props.modelValue ? new Date(props.modelValue) : null)

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    internalDate.value = new Date(newVal)
  } else {
    internalDate.value = null
  }
})

const formattedDate = computed(() => {
  if (!internalDate.value) return ''
  const dateLocale = locale.value === 'en' ? 'en-US' : 'pt-BR'
  return new Intl.DateTimeFormat(dateLocale).format(new Date(internalDate.value))
})

const onDateChange = (date) => {
  if (date) {
    // We want to store as YYYY-MM-DD for consistency with backend
    const offset = date.getTimezoneOffset()
    const adjustedDate = new Date(date.getTime() - (offset * 60 * 1000))
    const isoString = adjustedDate.toISOString().split('T')[0]
    emit('update:modelValue', isoString)
  } else {
    emit('update:modelValue', null)
  }
  menu.value = false
}

const clearDate = () => {
  emit('update:modelValue', null)
  internalDate.value = null
}
</script>

<style scoped>
.date-picker-card {
  overflow: hidden;
  background: rgb(var(--v-theme-surface));
}

.date-input-field :deep(.v-field__input) {
  cursor: pointer;
}

.custom-v-date-picker {
  --vc-font-family: 'Inter', sans-serif;
}
</style>

<style>
/* Global overrides for VCalendar to look more premium */
.vc-container {
  border: none !important;
  font-family: 'Inter', sans-serif !important;
}

.vc-header {
  padding: 16px 16px 8px 16px !important;
}

.vc-title {
  font-weight: 700 !important;
  color: var(--v-theme-primary) !important;
  text-transform: capitalize !important;
}

.vc-nav-header {
    padding: 10px !important;
}

.vc-nav-title {
    font-weight: 700 !important;
}

.vc-weeks {
  padding: 8px 16px 16px 16px !important;
}

.vc-day-content:hover {
  background-color: rgba(var(--v-theme-primary), 0.1) !important;
}

.vc-highlight {
  background-color: rgb(var(--v-theme-primary)) !important;
}

.vc-blue {
    --vc-accent-50: rgba(var(--v-theme-primary), 0.1);
    --vc-accent-100: rgba(var(--v-theme-primary), 0.2);
    --vc-accent-200: rgba(var(--v-theme-primary), 0.3);
    --vc-accent-300: rgba(var(--v-theme-primary), 0.4);
    --vc-accent-400: rgba(var(--v-theme-primary), 0.5);
    --vc-accent-500: rgb(var(--v-theme-primary));
    --vc-accent-600: rgb(var(--v-theme-primary));
    --vc-accent-700: rgb(var(--v-theme-primary));
    --vc-accent-800: rgb(var(--v-theme-primary));
    --vc-accent-900: rgb(var(--v-theme-primary));
}
</style>

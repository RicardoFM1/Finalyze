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
        :prepend-inner-icon="icon ? icon : (mode === 'range' ? 'mdi-calendar-range' : 'mdi-calendar')"
        readonly
        :variant="variant"
        :density="density"
        rounded="lg"
        v-bind="props"
        :required="required"
        :disabled="disabled"
        :hide-details="hideDetails"
        :rules="rules"
        class="date-input-field"
      >
        <template v-if="internalDate" v-slot:append-inner>
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
        :mode="mode === 'range' ? 'date' : mode"
        :is-range="mode === 'range'"
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
  modelValue: [String, Date, Object],
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
  },
  variant: {
    type: String,
    default: 'outlined'
  },
  density: {
    type: String,
    default: 'default'
  }
})

const emit = defineEmits(['update:modelValue'])
const uiStore = useUiStore()
const { t } = useI18n()

const menu = ref(false)
const isDark = computed(() => uiStore.theme === 'dark')

const internalDate = ref(null)

const parseValue = (val) => {
    if (!val) return null
    if (props.mode === 'range') {
        if (typeof val === 'string' && val.includes(' to ')) {
            const [s, e] = val.split(' to ')
            return { start: new Date(s), end: new Date(e || s) }
        }
        return val // Assume already an object {start, end}
    }
    return new Date(val)
}

internalDate.value = parseValue(props.modelValue)

watch(() => props.modelValue, (newVal) => {
  internalDate.value = parseValue(newVal)
})

const formattedDate = computed(() => {
  if (!internalDate.value) return ''
  const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
  const formatter = new Intl.DateTimeFormat(locale)
  const separator = locale === 'pt-BR' ? ' até ' : ' to '

  if (props.mode === 'range' ) {
      const startObj = internalDate.value.start || internalDate.value
      const endObj = internalDate.value.end
      
      if (!startObj || isNaN(new Date(startObj).getTime())) return ''
      
      const start = formatter.format(new Date(startObj))
      if (!endObj) {
          const endLabel = locale === 'pt-BR' ? 'Selecionar fim' : 'Select end'
          return `${start}${separator}${endLabel}`
      }
      
      const end = formatter.format(new Date(endObj))
      return `${start}${separator}${end}`
  }
  
  const d = new Date(internalDate.value)
  return isNaN(d.getTime()) ? '' : formatter.format(d)
})

const formatDateISO = (date) => {
    if (!date) return ''
    const d = new Date(date)
    if (isNaN(d.getTime())) return ''
    
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${y}-${m}-${day}`
}

const onDateChange = (val) => {
  if (!val) {
      emit('update:modelValue', null)
      return
  }

  if (props.mode === 'range') {
      if (val && val.start && val.end) {
          const start = formatDateISO(val.start)
          const end = formatDateISO(val.end)
          emit('update:modelValue', `${start} to ${end}`)
          menu.value = false
      } else if (val && val.start) {
          internalDate.value = val
      }
  } else {
      emit('update:modelValue', formatDateISO(val))
      menu.value = false
  }
}

const clearDate = () => {
  emit('update:modelValue', null)
  internalDate.value = null
  menu.value = false
}
</script>

<style scoped>
.date-picker-card {
  overflow: hidden;
  background: rgb(var(--v-theme-surface));
}

.date-input-field :deep(.v-field__input) {
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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

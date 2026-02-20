<template>
  <div class="date-input-container">
    <VueDatePicker
      v-model="internalDate"
      :range="mode === 'range'"
      :dark="isDark"
      :locale="dpLocale"
      auto-apply
      :enable-time-picker="false"
      :enable-seconds="false"
      :teleport="true"
      @update:model-value="onDateChange"
      :placeholder="label"
      :disabled="disabled"
      @closed="menu = false"
      @open="menu = true"
    >
      <template #trigger>
        <v-text-field
          v-model="formattedDisplayDate"
          :label="label"
          :prepend-inner-icon="icon ? icon : (mode === 'range' ? 'mdi-calendar-range' : 'mdi-calendar')"
          readonly
          :variant="variant"
          :density="density"
          rounded="lg"
          :required="required"
          :disabled="disabled"
          :hide-details="hideDetails"
          :rules="rules"
          class="date-input-field"
          :focused="menu"
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
    </VueDatePicker>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useI18n } from 'vue-i18n'
import { VueDatePicker } from '@vuepic/vue-datepicker'
import { ptBR, enUS } from 'date-fns/locale'
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
  modelValue: [String, Date, Object, Array],
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
const locale = computed(() => t('common.currency') === 'R$' ? 'pt-BR' : 'en-US')
const dpLocale = computed(() => locale.value === 'pt-BR' ? ptBR : enUS)

const isValidDate = (d) => d instanceof Date && !isNaN(d.getTime())
const internalDate = ref(null)

const parseValue = (val) => {
  if (!val) return null
  
  if (props.mode === 'range') {
    if (typeof val === 'string' && val.includes(' to ')) {
      const [s, e] = val.split(' to ')
      const start = new Date(s)
      const end = e ? new Date(e) : null
      return [isValidDate(start) ? start : null, isValidDate(end) ? end : null]
    }
    if (Array.isArray(val)) return val
    return null
  }
  
  const d = new Date(val)
  return isValidDate(d) ? d : null
}

const formattedDisplayDate = computed(() => {
  if (!internalDate.value) return ''
  
  const formatter = new Intl.DateTimeFormat(locale.value, { day: '2-digit', month: '2-digit', year: 'numeric' })
  
  if (props.mode === 'range' && Array.isArray(internalDate.value)) {
    const [start, end] = internalDate.value
    if (!start) return ''
    const s = formatter.format(start)
    return end ? `${s} -> ${formatter.format(end)}` : `${s} -> ...`
  }
  
  return isValidDate(internalDate.value) ? formatter.format(internalDate.value) : ''
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
    if (Array.isArray(val) && val[0] && val[1]) {
      const start = formatDateISO(val[0])
      const end = formatDateISO(val[1])
      emit('update:modelValue', `${start} to ${end}`)
    }
  } else {
    emit('update:modelValue', formatDateISO(val))
  }
}

const clearDate = () => {
  internalDate.value = null
  emit('update:modelValue', null)
}

watch(() => props.modelValue, (newVal) => {
  const parsed = parseValue(newVal)
  if (JSON.stringify(parsed) !== JSON.stringify(internalDate.value)) {
    internalDate.value = parsed
  }
}, { immediate: true })
</script>

<style scoped>
.date-input-container {
  width: 100%;
}

.date-input-field :deep(.v-field__input) {
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

:deep(.dp__main) {
  font-family: 'Inter', sans-serif;
}

:deep(.dp__theme_dark) {
  --dp-background-color: rgb(var(--v-theme-surface));
  --dp-text-color: #ffffff;
  --dp-hover-color: rgba(255, 255, 255, 0.05);
  --dp-hover-text-color: #ffffff;
  --dp-hover-icon-color: #959595;
  --dp-primary-color: rgb(var(--v-theme-primary));
  --dp-primary-text-color: #ffffff;
  --dp-secondary-color: #a9a9a9;
  --dp-border-color: rgba(255, 255, 255, 0.1);
  --dp-menu-border-color: rgba(255, 255, 255, 0.1);
  --dp-border-color-hover: rgb(var(--v-theme-primary));
  --dp-disabled-color: #737373;
  --dp-scroll-bar-background: #212121;
  --dp-scroll-bar-color: #484848;
  --dp-success-color: #00701a;
  --dp-success-color-check: #43a047;
  --dp-loader-color: rgb(var(--v-theme-primary));
  --dp-range-between-dates-background-color: rgba(var(--v-theme-primary), 0.15);
  --dp-range-between-dates-text-color: #ffffff;
  --dp-range-between-border-color: transparent;
}

:deep(.dp__theme_light) {
  --dp-background-color: #ffffff;
  --dp-text-color: #212121;
  --dp-hover-color: #f3f3f3;
  --dp-hover-text-color: #212121;
  --dp-hover-icon-color: #959595;
  --dp-primary-color: #1867c0;
  --dp-primary-text-color: #ffffff;
  --dp-secondary-color: #c0c4cc;
  --dp-border-color: #ddd;
  --dp-menu-border-color: #ddd;
  --dp-border-color-hover: #1867c0;
  --dp-disabled-color: #f6f6f6;
  --dp-scroll-bar-background: #f3f3f3;
  --dp-scroll-bar-color: #959595;
  --dp-success-color: #76d275;
  --dp-success-color-check: #43a047;
  --dp-loader-color: #1867c0;
  --dp-range-between-dates-background-color: rgba(24, 103, 192, 0.1);
  --dp-range-between-dates-text-color: #212121;
  --dp-range-between-border-color: transparent;
}

:deep(.dp__menu) {
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.15);
  border: 1px solid rgba(var(--v-border-color), 0.1);
  padding: 8px;
}

:deep(.dp__arrow_top), :deep(.dp__arrow_bottom) {
  display: none;
}

:deep(.dp__calendar_header_item) {
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.75rem;
  opacity: 0.6;
}

:deep(.dp__cell_inner) {
  border-radius: 10px;
}

:deep(.dp__active_date), :deep(.dp__range_start), :deep(.dp__range_end) {
  background: var(--dp-primary-color);
  font-weight: bold;
}

:deep(.dp__today) {
  border: 1px solid var(--dp-primary-color);
}
</style>
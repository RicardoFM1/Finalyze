<template>
  <v-card class="filter-card mb-6" elevation="0" rounded="xl">

    <div class="filter-header px-6 pt-5 pb-2">
      <div class="d-flex align-center">
        <v-icon icon="mdi-tune-variant" color="primary" size="20" class="mr-2" />
        <span class="filter-title">{{ macro ? $t('filters.dashboard_title') : $t('filters.search_title') }}</span>
      </div>

      <div class="d-flex align-center">
        <v-btn
          variant="text"
          size="small"
          color="primary"
          @click="limpar"
          class="mr-2"
        >
          {{ $t('filters.clear') }}
        </v-btn>

        <v-btn
          color="primary"
          size="small"
          rounded="lg"
          @click="aplicar"
        >
          {{ $t('filters.apply') }}
        </v-btn>
      </div>
    </div>

    <v-divider class="my-3" />

    <div class="px-6 pb-6">
      <v-row dense align="center">
        <!-- Período -->
        <v-col cols="12" :md="macro ? 6 : 12" :lg="macro ? 5 : 3">
          <DateInput
            v-model="localFilters.data"
            :label="$t('filters.period')"
            hide-details
            clearable
            mode="range"
            :class="{ 'macro-date-input': macro }"
          />
        </v-col>

        <!-- Descrição -->
        <v-col v-if="!macro" cols="12" sm="6" md="4" lg="3">
          <v-text-field
            v-model="localFilters.descricao"
            :label="$t('filters.description')"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>

        <!-- Categoria -->
        <v-col cols="12" sm="6"  :md="macro ? 5 : 4" :lg="macro ? 5 : 2">
          <v-select
            v-model="localFilters.categoria"
            :items="formatCategorias"
            :label="$t('filters.category')"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
            clearable
            multiple
            chips
            closable-chips
            persistent-placeholder
            :placeholder="localFilters.categoria && localFilters.categoria.length ? '' : $t('common.all')"
            class="filter-select-multi"
          >
            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :prepend-icon="item.raw.icon" :title="item.raw.title" class="categoria-item"></v-list-item>
            </template>
            <template v-slot:chip="{ props, item }">
              <v-chip 
                v-bind="props" 
                :prepend-icon="item.raw.icon" 
                :text="item.raw.title" 
                size="medium"
                variant="elevated"
                class="categoria-chip"
              ></v-chip>
            </template>
          </v-select>
        </v-col>

        <!-- Tipo -->
        <v-col cols="12" sm="6" :md="macro ? 1 : 4" :lg="macro ? 2 : 2">
          <v-select
            v-model="localFilters.tipo"
            :items="tipos"
            :label="$t('filters.type')"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>

        <!-- Valor -->
        <v-col v-if="!macro" cols="12" sm="6" md="4" lg="2">
          <v-text-field
            v-model="localFilters.valor"
            :label="$t('filters.value')"
            type="number"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>
      </v-row>
    </div>

  </v-card>
</template>


<script setup>
import { computed, reactive, watch, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import DateInput from '../Common/DateInput.vue'
import { categorias as categoriasConstantes } from '../../constants/categorias'

const { t } = useI18n()

const props = defineProps({
  modelValue: Object,
  categorias: Array,
  macro: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'apply', 'clear'])

const expanded = ref(true)

const localFilters = reactive({ 
  data: props.modelValue?.data || '',
  descricao: props.modelValue?.descricao || '',
  categoria: Array.isArray(props.modelValue?.categoria) ? props.modelValue.categoria : (props.modelValue?.categoria ? [props.modelValue.categoria] : []),
  tipo: props.modelValue?.tipo || 'todos',
  valor: props.modelValue?.valor || ''
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    Object.assign(localFilters, newVal)
  }
}, { deep: true })

watch(localFilters, (val) => {
  emit('update:modelValue', { ...val })
}, { deep: true })

const tipos = computed(() => [
  { title: t('common.all'), value: 'todos' },
  { title: t('transactions.type.income'), value: 'receita' },
  { title: t('transactions.type.expense'), value: 'despesa' }
])

const formatCategorias = computed(() => {
  if (!props.categorias) return []
  return props.categorias.map(cat => {
    const original = categoriasConstantes.find(c => c.title === cat)
    return {
      title: t('categories.' + cat),
      value: cat,
      icon: original ? original.icon : 'mdi-tag'
    }
  })
})

const aplicar = () => emit('apply')
const limpar = () => emit('clear')
</script>

<style scoped>
.filter-card {
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-border-color), 0.15);
  transition: all 0.25s ease;
}

.filter-card:hover {
  border-color: rgba(var(--v-theme-primary), 0.4);
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-title {
  font-weight: 600;
  font-size: 14px;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  color: rgb(var(--v-theme-on-surface));
  opacity: 0.9;
}

:deep(.v-field) {
  border-radius: 14px !important;
  background: rgba(var(--v-theme-primary), 0.08) !important;
}

:deep(.v-field:hover) {
  background: rgba(var(--v-theme-primary), 0.12) !important;
}

:deep(.v-field--focused) {
  background: rgba(var(--v-theme-primary), 0.15) !important;
  box-shadow: 0 4px 14px rgba(var(--v-theme-primary), 0.2);
}

:deep(.v-label.v-field-label) {
  color: rgb(var(--v-theme-on-surface)) !important;
  opacity: 0.7;
}

:deep(.v-field__input) {
  color: rgb(var(--v-theme-on-surface)) !important;
}

.macro-date-input :deep(.v-field) {
  min-height: 56px !important;
}

.macro-date-input :deep(.v-field__input) {
  font-size: 1.1rem !important;
  font-weight: 600 !important;
}

.filter-select-multi :deep(.v-field) {

  font-size:16px;
}

.filter-select-multi :deep(.v-field__input) {
  padding-top: 20px !important;
  padding-bottom: 10px !important;
  gap: 6px;
}

.filter-select-multi :deep(.v-list) {
  max-height: 300px;
  overflow-y: auto;
}

.categoria-chip {
  background-color: rgba(var(--v-theme-primary), 0.12) !important;
  color: rgb(var(--v-theme-primary)) !important;
  font-weight: 500 !important;
  font-size: 0.85rem !important;
  padding: 4px 8px !important;
  border-radius: 8px !important;
  transition: all 0.2s ease !important;
}

.categoria-chip:hover {
  background-color: rgba(var(--v-theme-primary), 0.2) !important;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(var(--v-theme-primary), 0.15) !important;
}

.categoria-item :deep(.v-list-item__prepend) .v-icon {
  margin-inline-end: 12px !important;
}

.filter-select-multi :deep(.v-chip) {
  margin: 2px !important;
  height: auto !important;
}



</style>

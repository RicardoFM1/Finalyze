<template>
  <v-card class="filter-card mb-6" elevation="0" rounded="xl">

    <div class="filter-header px-6 pt-5 pb-2">
      <div class="d-flex align-center">
        <v-icon icon="mdi-tune-variant" color="primary" size="20" class="mr-2" />
        <span class="filter-title">Filtros</span>
      </div>

      <div class="d-flex align-center">
        <v-btn
          variant="text"
          size="small"
          color="primary"
          @click="limpar"
          class="mr-2"
        >
          Limpar
        </v-btn>

        <v-btn
          color="primary"
          size="small"
          rounded="lg"
          @click="aplicar"
        >
          Aplicar
        </v-btn>
      </div>
    </div>

    <v-divider class="my-3" />

    <div class="px-6 pb-6">
      <v-row>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="localFilters.descricao"
            label="Descrição"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>

        <v-col cols="12" md="2">
          <v-select
            v-model="localFilters.categoria"
            :items="formatCategorias"
            label="Categoria"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
            clearable
          />
        </v-col>

        <v-col cols="12" md="2">
          <v-select
            v-model="localFilters.tipo"
            :items="tipos"
            label="Tipo"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>

        <v-col cols="12" md="2">
          <v-text-field
            v-model="localFilters.valor"
            label="Valor"
            type="number"
            density="comfortable"
            variant="solo-filled"
            flat
            hide-details
          />
        </v-col>

        <v-col cols="12" md="3">
          <v-text-field
            v-model="localFilters.data"
            label="Data"
            type="date"
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

const { t } = useI18n()

const props = defineProps({
  modelValue: Object,
  categorias: Array
})

const emit = defineEmits(['update:modelValue', 'apply', 'clear'])

const expanded = ref(true)

const localFilters = reactive({ 
  data: props.modelValue?.data || '',
  descricao: props.modelValue?.descricao || '',
  categoria: props.modelValue?.categoria || '',
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
  return props.categorias.map(cat => ({
    title: cat,
    value: cat
  }))
})

const aplicar = () => emit('apply')
const limpar = () => emit('clear')
</script>

<style scoped>
.filter-card {
  background: white;
  border: 1px solid rgba(var(--v-border-color), 0.08);
  transition: all 0.25s ease;
}

.filter-card:hover {
  border-color: rgba(var(--v-theme-primary), 0.25);
  box-shadow: 0 8px 28px rgba(var(--v-theme-primary), 0.08);
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
  opacity: 0.8;
}

:deep(.v-field) {
  border-radius: 14px !important;
  background: rgba(var(--v-theme-primary), 0.03) !important;
}

:deep(.v-field:hover) {
  background: rgba(var(--v-theme-primary), 0.06) !important;
}

:deep(.v-field--focused) {
  background: rgba(var(--v-theme-primary), 0.08) !important;
  box-shadow: 0 4px 14px rgba(var(--v-theme-primary), 0.12);
}


</style>

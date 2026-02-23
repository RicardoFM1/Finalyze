<template>
  <ModalBase v-model="internalValue" :title="form.id ? $t('admin.editPlan') : $t('admin.newPlan')" maxWidth="800px" persistent>
    <v-form ref="planForm">
      <v-row>
        <v-col cols="12" md="8">
          <v-text-field v-model="form.nome" :label="$t('admin.form.name')" variant="outlined" density="comfortable"></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field v-model="form.limite_lancamentos" :label="$t('admin.form.maxTransactions')" type="number" variant="outlined" density="comfortable"></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-textarea v-model="form.descricao" :label="$t('admin.form.description')" variant="outlined" rows="2" density="comfortable"></v-textarea>
        </v-col>

        <v-divider class="my-4 w-100"></v-divider>

        <v-col cols="12">
          <div class="text-h6 mb-4 d-flex align-center">
            <v-icon icon="mdi-currency-usd" class="mr-2" color="primary"></v-icon>
            {{ $t('admin.active_prices') }}
          </div>
          
          <v-row align="center" class="mb-4 pa-2 bg-blue-lighten-5 rounded-lg">
            <v-col cols="12" md="6">
              <v-text-field 
                v-model="baseMonthlyPrice" 
                :label="$t('admin.form.price') + ' (' + $t('admin.intervals.month') + ')'" 
                :prefix="currencySymbol" 
                variant="solo" 
                type="number"
                @input="calculatePeriodPrices"
                hint="Usado para calcular automaticamente os outros períodos"
                persistent-hint
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="text-caption">
              Semanal: 25% | Trimestral: 3 meses -10% | Anual: 12 meses -20%
            </v-col>
          </v-row>

          <v-row v-for="p in form.periodos_config" :key="p.id" align="center" dense>
            <v-col cols="12" md="3">
              <v-checkbox v-model="p.active" :label="p.nome" density="compact" color="primary" hide-details></v-checkbox>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field 
                v-model="p.price" 
                :label="$t('admin.price')" 
                :prefix="currencySymbol" 
                variant="outlined" 
                density="compact" 
                type="number" 
                :disabled="!p.active"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field 
                v-model="p.discount" 
                :label="$t('admin.form.features')" 
                suffix="%" 
                variant="outlined" 
                density="compact" 
                type="number" 
                :disabled="!p.active"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="2" class="text-caption text-right">
              {{ p.active ? $t('profile.active') : $t('profile.inactive') }}
            </v-col>
          </v-row>
        </v-col>

        <v-divider class="my-4 w-100"></v-divider>

        <v-col cols="12">
          <div class="text-h6 mb-4 d-flex align-center">
            <v-icon icon="mdi-check-all" class="mr-2" color="success"></v-icon>
            {{ $t('admin.form.features') }}
          </div>
          <v-row dense>
            <v-col cols="12" sm="6" v-for="feat in dbFeatures" :key="feat.id">
              <v-checkbox 
                v-model="form.recursos" 
                :label="feat.nome" 
                :value="feat.id" 
                density="comfortable" 
                color="success"
                hide-details
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12">
           <v-switch v-model="form.ativo" :label="$t('admin.form.active')" color="primary"></v-switch>
        </v-col>
      </v-row>
    </v-form>

    <template #actions>
      <v-btn variant="text" rounded="lg" @click="internalValue = false" class="px-6">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="primary" variant="elevated" rounded="lg" @click="savePlan" :loading="loading" class="px-8">{{ $t('modals.manage_plan.save') }}</v-btn>
    </template>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'
import { useI18n } from 'vue-i18n'
import { useMoney } from '@/composables/useMoney'

const { t } = useI18n()
const { currencySymbol } = useMoney()

const props = defineProps({
  modelValue: Boolean,
  plano: Object,
  dbPeriods: Array,
  dbFeatures: Array
})

const emit = defineEmits(['update:modelValue', 'saved'])

const authStore = useAuthStore()
const loading = ref(false)
const baseMonthlyPrice = ref(0)

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const form = ref({
  id: null,
  nome: '',
  limite_lancamentos: 100,
  descricao: '',
  ativo: true,
  recursos: [],
  periodos_config: []
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    if (props.plano) {
      form.value = {
        id: props.plano.id,
        nome: props.plano.nome,
        limite_lancamentos: props.plano.limite_lancamentos,
        descricao: props.plano.descricao,
        ativo: !!props.plano.ativo,
        recursos: props.plano.recursos.map(f => f.id),
        periodos_config: props.dbPeriods.map(dbp => {
          const planPeriod = props.plano.periodos.find(p => p.id === dbp.id)
          return {
            id: dbp.id,
            nome: dbp.nome,
            slug: dbp.slug,
            active: !!planPeriod,
            price: planPeriod ? (planPeriod.pivot.valor_centavos / 100).toFixed(2) : 0,
            discount: planPeriod ? planPeriod.pivot.percentual_desconto : 0
          }
        })
      }
      const mensal = form.value.periodos_config.find(p => p.slug === 'mensal')
      baseMonthlyPrice.value = mensal?.active ? mensal.price : 0
    } else {
      form.value = {
        id: null,
        nome: '',
        limite_lancamentos: 100,
        descricao: '',
        ativo: true,
        recursos: [],
        periodos_config: props.dbPeriods.map(dbp => ({
          id: dbp.id,
          nome: dbp.nome,
          slug: dbp.slug,
          active: true,
          price: 0,
          discount: 100
        }))
      }
      baseMonthlyPrice.value = 0
    }
  }
})

const calculatePeriodPrices = () => {
  const base = parseFloat(baseMonthlyPrice.value) || 0
  form.value.periodos_config.forEach(p => {
    if (p.slug === 'semanal') {
      p.price = (base / 4).toFixed(2)
      p.discount = 0
    } else if (p.slug === 'mensal') {
      p.price = base.toFixed(2)
      p.discount = 0
    } else if (p.slug === 'trimestral') {
      p.price = (base * 3 * 0.9).toFixed(2) // 10% desconto
      p.discount = 10
    } else if (p.slug === 'anual') {
      p.price = (base * 12 * 0.8).toFixed(2) // 20% desconto
      p.discount = 20
    }
  })
}

const savePlan = async () => {
    if (form.value.recursos.length === 0) {
        toast.error(t('admin.form.error_missing_features')) 
        return
    }

    const activePeriods = form.value.periodos_config.filter(p => p.active)
    if (activePeriods.length === 0) {
        toast.error(t('toasts.error_generic'))
        return
    }

    loading.value = true
    try {
        const payload = {
            nome: form.value.nome,
            descricao: form.value.descricao,
            limite_lancamentos: form.value.limite_lancamentos,
            ativo: form.value.ativo,
            recursos: form.value.recursos,
            periodos: activePeriods.map(p => ({
                id: p.id,
                valor_centavos: Math.round(parseFloat(p.price) * 100),
                percentual_desconto: parseInt(p.discount)
            }))
        }

        const isEdit = !!form.value.id
        const response = await authStore.apiFetch(isEdit ? `/planos/${form.value.id}` : '/planos', {
            method: isEdit ? 'PUT' : 'POST',
            body: JSON.stringify(payload)
        })

        if (response.ok) {
            toast.success(isEdit ? t('toasts.success_update') : 'Plano criado com sucesso!')
            internalValue.value = false
            emit('saved')
        } else {
            const data = await response.json()
            toast.error(data.message || t('toasts.error_generic'))
        }
    } catch (e) {
        toast.error(t('toasts.error_generic'))
    } finally {
        loading.value = false
    }
}
</script>

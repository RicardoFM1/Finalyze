<template>
  <v-container>
    <h1 class="text-h4 font-weight-bold mb-4">
      {{ $t('admin.title') }}
    </h1>

    <v-card>
      <v-toolbar color="primary" density="compact">
        <v-toolbar-title>{{ $t('admin.subtitle') }}</v-toolbar-title>
        <v-spacer />
        <v-btn icon="mdi-plus" @click="openDialog()" />
      </v-toolbar>

      <v-table>
        <thead>
          <tr>
            <th class="text-left">{{ $t('admin.name') }}</th>
            <th class="text-left">{{ $t('admin.price') }}</th>
            <th class="text-left">{{ $t('admin.duration') }}</th>
            <th class="text-left">{{ $t('admin.actions') }}</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loadingPlans">
            <td colspan="4" class="text-center">
              <v-progress-linear indeterminate color="primary" />
            </td>
          </tr>

          <tr v-for="item in plans" :key="item.id">
            <td>{{ item.name }}</td>
            <td>{{ formatPrice(item.price) }}</td>
            <td>{{ $t(`admin.intervals.${item.interval}`) }}</td>
            <td>
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                color="info"
                @click="openDialog(item)"
              />
              <v-btn
                icon="mdi-delete"
                size="small"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
              />
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <!-- MODAL CREATE / EDIT -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">
          {{ form.id ? $t('admin.editPlan') : $t('admin.newPlan') }}
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.name"
                :label="$t('admin.form.name')"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.price"
                :label="$t('admin.form.price')"
                :prefix="currencyPrefix"
                type="number"
                step="0.01"
                />

            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.interval"
                :items="intervalOptions"
                :label="$t('admin.form.interval')"
              />
            </v-col>

            <v-col cols="12">
              <v-text-field
                v-model="form.max_transactions"
                type="number"
                :label="$t('admin.form.maxTransactions')"
              />
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="form.description"
                rows="2"
                :label="$t('admin.form.description')"
              />
            </v-col>

            <v-col cols="12">
              <v-label>{{ $t('admin.form.features') }}</v-label>
              <v-row dense>
                <v-col
                  cols="6"
                  v-for="feat in availableFeatures"
                  :key="feat"
                >
                  <v-checkbox
                    v-model="form.features"
                    :label="$t(`features.${feat}`)"
                    :value="feat"
                    density="compact"
                    hide-details
                  />
                </v-col>
              </v-row>
            </v-col>

            <v-col cols="12">
              <v-switch
                v-model="form.is_active"
                color="success"
                hide-details
                :label="$t('admin.form.active')"
              />
            </v-col>
          </v-row>

          <v-overlay :model-value="saving" class="align-center justify-center">
            <v-progress-circular indeterminate color="white" />
          </v-overlay>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="closeDialog">
            {{ $t('common.cancel') }}
          </v-btn>
          <v-btn
            variant="text"
            :loading="loadingSalvar"
            @click="savePlan"
          >
            {{ $t('common.save') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DELETE -->
    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card>
        <v-card-title class="text-h5">
          {{ $t('admin.deleteTitle') }}
        </v-card-title>

        <v-card-text>
          {{ $t('admin.deleteConfirm') }}
          <strong>{{ planToDelete?.name }}</strong>?
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="deleteDialog = false">
            {{ $t('common.cancel') }}
          </v-btn>
          <v-btn color="error" variant="text" @click="deletePlan">
            {{ $t('common.delete') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import { useCurrencyStore } from '../stores/currency'

const authStore = useAuthStore()
const currencyStore = useCurrencyStore()

const plans = ref([])
const dialog = ref(false)
const deleteDialog = ref(false)
const planToDelete = ref(null)

const loadingPlans = ref(false)
const loadingSalvar = ref(false)
const saving = ref(false)

const intervalOptions = ['month', 'year']

const availableFeatures = [
  'dashboard',
  'transactions',
  'reports',
  'csv_export',
  'ai_support'
]

const form = ref({
  id: null,
  name: '',
  price: 0,
  interval: 'month',
  max_transactions: 100,
  description: '',
  features: [],
  is_active: true
})

onMounted(fetchPlans)

async function fetchPlans () {
  loadingPlans.value = true
  try {
    const res = await authStore.apiFetch('/plans')
    plans.value = await res.json()
  } finally {
    loadingPlans.value = false
  }
}

function formatPrice (value) {
  const currency =  currencyStore.currency

  const localMap = {
    BRL: 'pt-BR',
    USD: 'en-US'
  }

  return new Intl.NumberFormat(localMap[currency], {
    style: 'currency',
    currency,
  }).format(value)
  }

  const currencyPrefix = computed(()=>{
    return currencyStore.currency === 'BRL' ? 'R$ ' : '$ '
  })

function openDialog (item = null) {
  form.value = item
    ? { ...item, features: item.features || [], is_active: !!item.is_active }
    : { ...form.value, id: null }
  dialog.value = true
}

function closeDialog () {
  dialog.value = false
}

async function savePlan () {
  const isEdit = !!form.value.id
  loadingSalvar.value = true

  const res = await authStore.apiFetch(
    isEdit ? `/plans/${form.value.id}` : '/plans',
    {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(form.value)
    }
  )

  if (res.ok) {
    toast.success(isEdit ? 'Updated successfully' : 'Created successfully')
    fetchPlans()
    closeDialog()
  }

  loadingSalvar.value = false
}

function confirmDelete (item) {
  planToDelete.value = item
  deleteDialog.value = true
}

async function deletePlan () {
  await authStore.apiFetch(`/plans/${planToDelete.value.id}`, {
    method: 'DELETE'
  })
  fetchPlans()
  deleteDialog.value = false
}
</script>

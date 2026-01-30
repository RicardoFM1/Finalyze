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
<<<<<<< HEAD
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
=======
      <v-progress-linear v-if="loadingPlans" indeterminate color="primary" />
    </td>
            <thead>
                <tr>
                    <th class="text-left">Nome</th>
                    <th class="text-left">Preço</th>
                    <th class="text-left">Intervalo</th>
                    <th class="text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in plans" :key="item.id">
                    <td>{{ item.name }}</td>
                    <td>{{ formatPrice(item.price_cents / 100) }}</td>
                    <td>{{ item.interval }}</td>
                    <td>
                        <v-btn icon="mdi-pencil" size="small" variant="text" color="info" @click="openDialog(item)"></v-btn>
                        <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="confirmDelete(item)"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>
>>>>>>> origin/Ricardo
    </v-card>

    <!-- MODAL CREATE / EDIT -->
    <v-dialog v-model="dialog" max-width="500px">
<<<<<<< HEAD
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
                :prefix="'R$ '"
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
=======
        <v-card>
            <v-card-title>
                <span class="text-h5">{{ form.id ? 'Editar Plano' : 'Novo Plano' }}</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="form.name" label="Nome do Plano"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field 
                                v-model="form.price_cents" 
                                label="Preço" 
                                prefix="R$"
                                type="number" 
                                step="0.01"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select v-model="form.interval" :items="['mês', 'ano']" label="Intervalo"></v-select>
                        </v-col>
                         <v-col cols="12">
                            <v-text-field v-model="form.max_transactions" label="Max Lançamentos" type="number"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="form.description" label="Descrição" rows="2"></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-label>Funcionalidades</v-label>
                            <v-row dense>
                                <v-col cols="6" v-for="feat in availableFeatures" :key="feat">
                                    <v-checkbox v-model="form.features" :label="feat" :value="feat" density="compact" hide-details></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <v-switch v-model="form.is_active" label="Plano Ativo" color="success" hide-details></v-switch>
                        </v-col>
                    </v-row>
                    <v-overlay :model-value="saving" class="align-center justify-center">
                        <v-progress-circular indeterminate color="white"></v-progress-circular>
                    </v-overlay>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog">Cancelar</v-btn>
                <v-btn :loading="loadingSalvar" color="blue-darken-1" variant="text" @click="savePlan">Salvar</v-btn>
            </v-card-actions>
        </v-card>
>>>>>>> origin/Ricardo
    </v-dialog>

    <!-- DELETE -->
    <v-dialog v-model="deleteDialog" max-width="400px">
<<<<<<< HEAD
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
=======
        <v-card>
            <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
            <v-card-text>Tem certeza que deseja excluir o plano <strong>{{ planToDelete?.name }}</strong>?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="deleteDialog = false">Cancelar</v-btn>
                <v-btn :loading="loadingSalvar" color="error" variant="text" @click="deletePlan">Excluir</v-btn>
            </v-card-actions>
        </v-card>
>>>>>>> origin/Ricardo
    </v-dialog>
  </v-container>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()

const plans = ref([])
const dialog = ref(false)
<<<<<<< HEAD
=======
const form = ref({
    id: null,
    name: '',
    price_cents: 0,
    interval: 'mês',
    max_transactions: 100,
    description: '',
    features: []
})
const loadingSalvar = ref(false)
const saving = ref(false)
const availableFeatures = [
    'Painel Financeiro',
    'Lançamentos',
    'Relatórios Gráficos',
    'Exportação CSV',
    'Chat IA de Suporte'
]

onMounted(async () => {
   fetchPlans()
})
const loadingPlans = ref(false)
const fetchPlans = async () => {
    try {
        loadingPlans.value = true
        const response = await authStore.apiFetch('/admin/plans')
        plans.value = await response.json()
    } catch (e) {
        console.error(e)
    }finally {
        loadingPlans.value = false
    }   
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}

const openDialog = (item = null) => {
    if (item) {
        form.value = { 
            ...item, 
            price_cents: item.price_cents / 100,
            features: item.features || [], 
            is_active: !!item.is_active 
        } 
    } else {
        form.value = { id: null, name: '', price_cents: 0, interval: 'mês', max_transactions: 100, description: '', features: [], is_active: true }
    }
    dialog.value = true
}

const closeDialog = () => {
    dialog.value = false
}

const savePlan = async () => {
    if (!form.value.features || form.value.features.length === 0) {
        toast.error('O plano deve ter pelo menos uma funcionalidade selecionada.')
        return
    }
    const isEdit = !!form.value.id
    const endpoint = isEdit ? `/plans/${form.value.id}` : '/plans'
    const method = isEdit ? 'PUT' : 'POST'
    loadingSalvar.value = true
    try {
      
        const payload = {
            ...form.value,
            price_cents: Math.round(form.value.price_cents * 100)
        }

        const response = await authStore.apiFetch(endpoint, {
            method: method,
            body: JSON.stringify(payload)
        })
        
        if (response.ok) {
            closeDialog()
            toast.success(`Plano ${isEdit ? 'atualizado' : 'criado'} com sucesso!`)
            fetchPlans()
        } else {
            const data = await response.json()
            toast.error(data.message || 'Erro ao salvar plano')
            loadingSalvar.value = false
        }
    } catch (e) {
        console.error(e)
    }finally {
        loadingSalvar.value = false
        fetchPlans()
    }
}

>>>>>>> origin/Ricardo
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

<<<<<<< HEAD
function formatPrice (value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

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
=======
const deletePlan = async () => {
    if (!planToDelete.value) return 
    
   loadingSalvar.value = true
    try {
        const response = await authStore.apiFetch(`/plans/${planToDelete.value.id}`, {
            method: 'DELETE'
        })
        fetchPlans()
        deleteDialog.value = false
        planToDelete.value = null
        if(response.ok){
            toast.success("Plano deletado com sucesso")
        } else {
            const data = await response.json().catch(() => ({}))
            toast.error(data.message || "Erro ao tentar deletar o plano")
        }
    } catch (e) {
        console.log(e)
        toast.error("Erro na comunicação com o servidor")
    }finally {
        loadingSalvar.value = false
>>>>>>> origin/Ricardo
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

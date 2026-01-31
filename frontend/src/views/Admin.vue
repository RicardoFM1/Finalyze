<template>
  <v-container>
    <div class="d-flex align-center mb-6">
        <v-icon icon="mdi-shield-crown" color="primary" size="32" class="mr-3"></v-icon>
        <h1 class="text-h4 font-weight-bold">Gestão de Planos</h1>
        <v-spacer></v-spacer>
        <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" elevation="2">Novo Plano</v-btn>
    </div>

    <v-card class="rounded-xl overflow-hidden" elevation="4">
        <v-table hover>
            <thead>
                <tr class="bg-grey-lighten-4">
                    <th class="text-left font-weight-bold">Plano</th>
                    <th class="text-left font-weight-bold">Status</th>
                    <th class="text-left font-weight-bold">Preços Ativos</th>
                    <th class="text-left font-weight-bold">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loadingPlans">
                    <td colspan="4" class="text-center py-10">
                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </td>
                </tr>
                <tr v-else v-for="item in plans" :key="item.id">
                    <td>
                        <div class="font-weight-bold text-body-1">{{ item.name }}</div>
                        <div class="text-caption text-medium-emphasis" style="white-space: pre-wrap; word-break: break-word; max-width: 350px; line-height: 1.4;">{{ item.description }}</div>
                    </td>
                    <td>
                        <v-chip :color="item.is_active ? 'success' : 'grey'" size="small" variant="flat">
                            {{ item.is_active ? 'Ativo' : 'Inativo' }}
                        </v-chip>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            <v-chip v-for="p in item.periods" :key="p.id" size="x-small" variant="outlined" color="primary">
                                {{ p.name }}: {{ formatPrice(p.pivot.price_cents / 100) }}
                            </v-chip>
                        </div>
                    </td>
                    <td>
                        <v-btn icon="mdi-pencil" size="small" variant="text" color="info" @click="openDialog(item)"></v-btn>
                        <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="confirmDelete(item)"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>
    </v-card>

    <!-- Dialog de Criação/Edição -->
    <v-dialog v-model="dialog" max-width="800px" persistent>
        <v-card class="rounded-xl overflow-hidden">
            <v-toolbar color="primary" density="comfortable">
                <v-toolbar-title>{{ form.id ? 'Editar Plano' : 'Criar Novo Plano' }}</v-toolbar-title>
                <v-btn icon="mdi-close" @click="closeDialog"></v-btn>
            </v-toolbar>
            
            <v-card-text class="pa-6" style="max-height: 70vh; overflow-y: auto;">
                <v-form ref="planForm">
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-text-field v-model="form.name" label="Nome do Plano" variant="outlined" density="comfortable"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field v-model="form.max_transactions" label="Limite Lançamentos" type="number" variant="outlined" density="comfortable"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="form.description" label="Descrição (HTML suportado)" variant="outlined" rows="2" density="comfortable"></v-textarea>
                        </v-col>

                        <v-divider class="my-4 w-100"></v-divider>

                        <v-col cols="12">
                            <div class="text-h6 mb-4 d-flex align-center">
                                <v-icon icon="mdi-currency-usd" class="mr-2" color="primary"></v-icon>
                                Configuração de Preços
                            </div>
                            
                            <v-row align="center" class="mb-4 pa-2 bg-blue-lighten-5 rounded-lg">
                                <v-col cols="12" md="6">
                                    <v-text-field 
                                        v-model="baseMonthlyPrice" 
                                        label="Preço Base (Mensal)" 
                                        prefix="R$" 
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

                            <v-row v-for="p in form.periods_config" :key="p.id" align="center" dense>
                                <v-col cols="12" md="3">
                                    <v-checkbox v-model="p.active" :label="p.name" density="compact" color="primary" hide-details></v-checkbox>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field 
                                        v-model="p.price" 
                                        label="Preço Final" 
                                        prefix="R$" 
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
                                        label="Desconto (%)" 
                                        suffix="%" 
                                        variant="outlined" 
                                        density="compact" 
                                        type="number" 
                                        :disabled="!p.active"
                                        hide-details
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" md="2" class="text-caption text-right">
                                    {{ p.active ? 'Ativo' : 'Inativo' }}
                                </v-col>
                            </v-row>
                        </v-col>

                        <v-divider class="my-4 w-100"></v-divider>

                        <v-col cols="12">
                            <div class="text-h6 mb-4 d-flex align-center">
                                <v-icon icon="mdi-check-all" class="mr-2" color="success"></v-icon>
                                Benefícios do Plano
                            </div>
                            <v-row dense>
                                <v-col cols="12" sm="6" v-for="feat in dbFeatures" :key="feat.id">
                                    <v-checkbox 
                                        v-model="form.features_ids" 
                                        :label="feat.name" 
                                        :value="feat.id" 
                                        density="compact" 
                                        color="success"
                                        hide-details
                                    ></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-col>

                        <v-col cols="12">
                             <v-switch v-model="form.is_active" label="Tornar este plano disponível no site" color="primary"></v-switch>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-card-actions class="pa-6 pt-0">
                <v-spacer></v-spacer>
                <v-btn variant="text" rounded="lg" @click="closeDialog" class="px-6">Cancelar</v-btn>
                <v-btn color="primary" variant="elevated" rounded="lg" @click="savePlan" :loading="loadingSalvar" class="px-8">Salvar Plano</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card class="rounded-xl pa-4">
            <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
            <v-card-text>Tem certeza que deseja excluir o plano <strong>{{ planToDelete?.name }}</strong>?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="deleteDialog = false">Cancelar</v-btn>
                <v-btn color="error" variant="flat" rounded="lg" @click="deletePlan" :loading="loadingSalvar">Excluir</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const plans = ref([])
const dbPeriods = ref([])
const dbFeatures = ref([])
const dialog = ref(false)
const loadingPlans = ref(false)
const loadingSalvar = ref(false)
const baseMonthlyPrice = ref(0)

const form = ref({
    id: null,
    name: '',
    max_transactions: 100,
    description: '',
    is_active: true,
    features_ids: [],
    periods_config: []
})

onMounted(async () => {
    fetchBaseData()
    fetchPlans()
})

const fetchBaseData = async () => {
    try {
        const periodsRes = await authStore.apiFetch('/admin/periods')
        dbPeriods.value = await periodsRes.json()
        
        const featuresRes = await authStore.apiFetch('/admin/features')
        dbFeatures.value = await featuresRes.json()
    } catch (e) {
        console.error(e)
    }
}

const fetchPlans = async () => {
    try {
        loadingPlans.value = true
        const response = await authStore.apiFetch('/admin/plans')
        plans.value = await response.json()
    } catch (e) {
        console.error(e)
    } finally {
        loadingPlans.value = false
    }   
}

const calculatePeriodPrices = () => {
    const base = parseFloat(baseMonthlyPrice.value) || 0
    form.value.periods_config.forEach(p => {
        if (p.slug === 'weekly') {
            p.price = (base / 4).toFixed(2)
            p.discount = 0
        } else if (p.slug === 'monthly') {
            p.price = base.toFixed(2)
            p.discount = 0
        } else if (p.slug === 'quarterly') {
            p.price = (base * 3 * 0.9).toFixed(2) // 10% discount
            p.discount = 10
        } else if (p.slug === 'yearly') {
            p.price = (base * 12 * 0.8).toFixed(2) // 20% discount
            p.discount = 20
        }
    })
}

const openDialog = (item = null) => {
    if (item) {
        form.value = {
            id: item.id,
            name: item.name,
            max_transactions: item.max_transactions,
            description: item.description,
            is_active: !!item.is_active,
            features_ids: item.features.map(f => f.id),
            periods_config: dbPeriods.value.map(dbp => {
                const planPeriod = item.periods.find(p => p.id === dbp.id)
                return {
                    id: dbp.id,
                    name: dbp.name,
                    slug: dbp.slug,
                    active: !!planPeriod,
                    price: planPeriod ? (planPeriod.pivot.price_cents / 100).toFixed(2) : 0,
                    discount: planPeriod ? planPeriod.pivot.discount_percentage : 0
                }
            })
        }
        const monthly = form.value.periods_config.find(p => p.slug === 'monthly')
        baseMonthlyPrice.value = monthly?.active ? monthly.price : 0
    } else {
        form.value = {
            id: null,
            name: '',
            max_transactions: 100,
            description: '',
            is_active: true,
            features_ids: [],
            periods_config: dbPeriods.value.map(dbp => ({
                id: dbp.id,
                name: dbp.name,
                slug: dbp.slug,
                active: true,
                price: 0,
                discount: 0
            }))
        }
        baseMonthlyPrice.value = 0
    }
    dialog.value = true
}

const closeDialog = () => {
    dialog.value = false
}

const savePlan = async () => {
    if (form.value.features_ids.length === 0) {
        toast.error('Escolha pelo menos uma funcionalidade.')
        return
    }

    const activePeriods = form.value.periods_config.filter(p => p.active)
    if (activePeriods.length === 0) {
        toast.error('O plano deve ter pelo menos um período de cobrança ativo.')
        return
    }

    loadingSalvar.value = true
    try {
        const payload = {
            name: form.value.name,
            description: form.value.description,
            max_transactions: form.value.max_transactions,
            is_active: form.value.is_active,
            features: form.value.features_ids,
            periods: activePeriods.map(p => ({
                id: p.id,
                price_cents: Math.round(parseFloat(p.price) * 100),
                discount_percentage: parseInt(p.discount)
            }))
        }

        const isEdit = !!form.value.id
        const response = await authStore.apiFetch(isEdit ? `/plans/${form.value.id}` : '/plans', {
            method: isEdit ? 'PUT' : 'POST',
            body: JSON.stringify(payload)
        })

        if (response.ok) {
            toast.success('Plano salvo com sucesso!')
            closeDialog()
            fetchPlans()
        } else {
            const data = await response.json()
            toast.error(data.message || 'Erro ao salvar plano')
        }
    } catch (e) {
        toast.error('Erro de conexão')
    } finally {
        loadingSalvar.value = false
    }
}

const deleteDialog = ref(false)
const planToDelete = ref(null)

const confirmDelete = (item) => {
    planToDelete.value = item
    deleteDialog.value = true
}

const deletePlan = async () => {
    loadingSalvar.value = true
    try {
        const response = await authStore.apiFetch(`/plans/${planToDelete.value.id}`, {
            method: 'DELETE'
        })
        if (response.ok) {
            toast.success('Plano excluído.')
            deleteDialog.value = false
            fetchPlans()
        } else {
            const data = await response.json()
            toast.error(data.message || 'Erro ao excluir')
        }
    } finally {
        loadingSalvar.value = false
    }
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

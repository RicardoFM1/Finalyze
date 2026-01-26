<template>
  <v-container>
    <h1 class="text-h4 font-weight-bold mb-4">Painel Administrativo</h1>
    <v-card>
        <v-toolbar color="primary" density="compact">
            <v-toolbar-title>Gerenciar Planos</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon="mdi-plus" @click="openDialog()"></v-btn>
        </v-toolbar>
        <v-table>
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
                    <td>{{ formatPrice(item.price) }}</td>
                    <td>{{ item.interval }}</td>
                    <td>
                        <v-btn icon="mdi-pencil" size="small" variant="text" color="info" @click="openDialog(item)"></v-btn>
                        <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="confirmDelete(item)"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>
    </v-card>

    <v-dialog v-model="dialog" max-width="500px">
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
                                v-model="form.price" 
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
                <v-btn color="blue-darken-1" variant="text" @click="savePlan">Salvar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card>
            <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
            <v-card-text>Tem certeza que deseja excluir o plano <strong>{{ planToDelete?.name }}</strong>?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="deleteDialog = false">Cancelar</v-btn>
                <v-btn color="error" variant="text" @click="deletePlan">Excluir</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const plans = ref([])
const dialog = ref(false)
const form = ref({
    id: null,
    name: '',
    price: 0,
    interval: 'mês',
    max_transactions: 100,
    description: '',
    features: []
})

const saving = ref(false)
const availableFeatures = [
    'Transações Ilimitadas',
    'Relatórios Detalhados',
    'Suporte Prioritário',
    'Exportação de Dados',
    'Múltiplas Categorias',
    'Planejamento de Metas',
    'Acesso Mobile'
]

onMounted(async () => {
   fetchPlans()
})

const fetchPlans = async () => {
    try {
        const response = await authStore.apiFetch('/plans')
        plans.value = await response.json()
    } catch (e) {
        console.error(e)
    }
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}

const openDialog = (item = null) => {
    if (item) {
        form.value = { ...item, features: item.features || [], is_active: !!item.is_active } // Ensure array and boolean
    } else {
        form.value = { id: null, name: '', price: 0, interval: 'mês', max_transactions: 100, description: '', features: [], is_active: true }
    }
    dialog.value = true
}

const closeDialog = () => {
    dialog.value = false
}

const savePlan = async () => {
    const isEdit = !!form.value.id
    const endpoint = isEdit ? `/plans/${form.value.id}` : '/plans'
    const method = isEdit ? 'PUT' : 'POST'

    try {
        const response = await authStore.apiFetch(endpoint, {
            method: method,
            body: JSON.stringify(form.value)
        })
        
        if (response.ok) {
            closeDialog()
            fetchPlans()
        } else {
            alert('Erro ao salvar plano')
        }
    } catch (e) {
        console.error(e)
    }
}

const deleteDialog = ref(false)
const planToDelete = ref(null)

const confirmDelete = (item) => {
    planToDelete.value = item
    deleteDialog.value = true
}

const deletePlan = async () => {
    if (!planToDelete.value) return 
    
    // Optimistic UI or wait? Let's wait.
    try {
        await authStore.apiFetch(`/plans/${planToDelete.value.id}`, {
            method: 'DELETE'
        })
        fetchPlans()
        deleteDialog.value = false
        planToDelete.value = null
    } catch (e) {
        console.error(e)
    }
}
</script>

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
            <td colspan="4" class="text-center">
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
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card>
            <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
            <v-card-text>Tem certeza que deseja excluir o plano <strong>{{ planToDelete?.name }}</strong>?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="deleteDialog = false">Cancelar</v-btn>
                <v-btn :loading="loadingSalvar" color="error" variant="text" @click="deletePlan">Excluir</v-btn>
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
const dialog = ref(false)
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

const deleteDialog = ref(false)
const planToDelete = ref(null)

const confirmDelete = (item) => {
    planToDelete.value = item
    deleteDialog.value = true
}

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
    }
}
</script>

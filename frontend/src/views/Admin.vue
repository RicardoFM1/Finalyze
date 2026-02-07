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
                        <div class="font-weight-bold text-body-1">{{ item.nome }}</div>
                        <div class="text-caption text-medium-emphasis" style="white-space: pre-wrap; word-break: break-word; max-width: 350px; line-height: 1.4;">{{ item.descricao }}</div>
                    </td>
                    <td>
                        <v-chip :color="item.ativo ? 'success' : 'grey'" size="small" variant="flat">
                            {{ item.ativo ? 'Ativo' : 'Inativo' }}
                        </v-chip>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            <v-chip v-for="p in item.periodos" :key="p.id" size="x-small" variant="outlined" color="primary">
                                {{ p.nome }}: {{ formatPrice(p.pivot.valor_centavos / 100) }}
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

    <ModalPlano 
      v-model="dialog" 
      :plano="itemAEditar" 
      :dbPeriods="dbPeriods" 
      :dbFeatures="dbFeatures" 
      @saved="fetchPlans" 
    />

    <ModalExcluirPlano 
      v-model="deleteDialog" 
      :plano="planToDelete" 
      @deleted="fetchPlans" 
    />
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import ModalPlano from '../components/Modals/Admin/ModalPlano.vue'
import ModalExcluirPlano from '../components/Modals/Admin/ModalExcluirPlano.vue'

const authStore = useAuthStore()
const plans = ref([])
const dbPeriods = ref([])
const dbFeatures = ref([])
const dialog = ref(false)
const deleteDialog = ref(false)
const loadingPlans = ref(false)
const itemAEditar = ref(null)
const planToDelete = ref(null)


onMounted(async () => {
    fetchBaseData()
    fetchPlans()
})

const fetchBaseData = async () => {
    try {
        const periodsRes = await authStore.apiFetch('/admin/periodos')
        dbPeriods.value = await periodsRes.json()
        
        const featuresRes = await authStore.apiFetch('/admin/recursos')
        dbFeatures.value = await featuresRes.json()
    } catch (e) {
        console.error(e)
    }
}

const fetchPlans = async () => {
    try {
        loadingPlans.value = true
        const response = await authStore.apiFetch('/admin/planos')
        plans.value = await response.json()
    } catch (e) {
        console.error(e)
    } finally {
        loadingPlans.value = false
    }   
}


const openDialog = (item = null) => {
    itemAEditar.value = item
    dialog.value = true
}

const confirmDelete = (item) => {
    planToDelete.value = item
    deleteDialog.value = true
}

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

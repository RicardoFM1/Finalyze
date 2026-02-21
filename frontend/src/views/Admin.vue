<template>
  <v-container>
    <div class="d-flex align-center mb-6">
        <v-icon icon="mdi-shield-crown" color="primary" size="32" class="mr-3"></v-icon>
        <h1 class="text-h4 font-weight-bold">{{ $t('admin.header') }}</h1>
        <v-spacer></v-spacer>
        <v-btn color="primary" :disabled="loadingPlans" prepend-icon="mdi-plus" @click="openDialog()" elevation="2">{{ $t('admin.new_plan_btn') }}</v-btn>
    </div>

    <v-card class="rounded-xl overflow-hidden" elevation="4">
        <v-table hover>
            <thead>
                <tr class="bg-grey-lighten-4">
                    <th class="text-left font-weight-bold">{{ $t('admin.name') }}</th>
                    <th class="text-left font-weight-bold">{{ $t('admin.status') }}</th>
                    <th class="text-left font-weight-bold">{{ $t('admin.features') }}</th>
                    <th class="text-left font-weight-bold">{{ $t('admin.active_prices') }}</th>
                    <th class="text-left font-weight-bold">{{ $t('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loadingPlans">
                    <td colspan="5" class="text-center py-10">
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
                            {{ item.ativo ? $t('profile.active') : $t('profile.inactive') }}
                        </v-chip>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1 align-center">
                            <v-chip v-for="rec in item.recursos.slice(0, 2)" :key="rec.id" size="x-small" color="secondary" variant="outlined">
                                {{ rec.nome }}
                            </v-chip>
                            
                            <v-tooltip v-if="item.recursos.length > 2" location="top" open-on-click>
                                <template v-slot:activator="{ props }">
                                    <v-chip v-bind="props" size="x-small" color="secondary" variant="flat" class="cursor-pointer">
                                        +{{ item.recursos.length - 2 }}
                                    </v-chip>
                                </template>
                                <div class="d-flex flex-column pa-2">
                                    <span class="text-caption font-weight-bold mb-1">{{ $t('admin.all_features') }}</span>
                                    <span v-for="rec in item.recursos" :key="rec.id" class="text-caption">• {{ rec.nome }}</span>
                                </div>
                            </v-tooltip>
                            <span v-if="!item.recursos || item.recursos.length === 0" class="text-caption text-disabled">-</span>
                        </div>
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
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import ModalPlano from '../components/Modals/Admin/ModalPlano.vue'
import ModalExcluirPlano from '../components/Modals/Admin/ModalExcluirPlano.vue'
import { useI18n } from 'vue-i18n'
import { useMoney } from '@/composables/useMoney'

const { t } = useI18n()
const { formatPrice } = useMoney()

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

// formatPrice is from useMoney composable (imported above)

</script>

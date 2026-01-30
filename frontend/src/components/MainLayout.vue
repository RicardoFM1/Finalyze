<template>
  <v-layout>
    <v-app-bar color="primary" elevation="2">
      <v-toolbar-title class="font-weight-bold" style="cursor: pointer" @click="$router.push('/')">
        <v-icon icon="mdi-chart-pie" class="mr-2"></v-icon>
        Finalyze
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <div class="d-flex align-center">
        <template v-if="!authStore.isAuthenticated">
          <LanguageSelector />
            <v-btn to="/planos" variant="text" color="white" class="mx-1 text-none font-weight-medium">{{ $t('landing.btn_plans') }}</v-btn>
            <v-btn to="/login" variant="elevated" color="white" class="ml-2 mr-2 text-none font-weight-bold text-primary">
            {{ $t('landing.btn_login') }}
            </v-btn>
        </template>
        
      </div>
    </v-app-bar>



    <v-navigation-drawer v-if="authStore.isAuthenticated" permanent elevation="2">
        <v-list>
            <v-list-item 
                v-if="authStore.user"
                :prepend-avatar="authStore.user.avatar ? 'http://localhost:8000/storage/' + authStore.user.avatar : undefined"
                :prepend-icon="!authStore.user.avatar ? 'mdi-account-circle' : undefined"
                :title="authStore.user.name"
                :subtitle="authStore.user.email"
            ></v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list density="compact" nav>
           <v-list-item prepend-icon="mdi-home" title="Página inicial" to="/"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-view-dashboard" title="Painel" to="/painel"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-bank-transfer" title="Lançamentos" to="/lancamentos"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-chart-bar" title="Relatórios" to="/relatorios"></v-list-item>
            <v-list-item prepend-icon="mdi-account" title="Perfil" to="/perfil"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin'" prepend-icon="mdi-shield-crown" title="Admin" to="/admin"></v-list-item>
            <v-list-item v-if="authStore.user?.plan_id === null" prepend-icon="mdi-tag-text-outline" title="Planos" to="/planos"></v-list-item>
            <v-list-item  color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" title="Sair"></v-list-item>
        </v-list>

        
    </v-navigation-drawer>

    <!-- Diálogo de Confirmação de Logout -->
    <v-dialog v-model="confirmLogout" max-width="400" persistent transition="dialog-bottom-transition">
        <v-card class="rounded-xl pa-4" elevation="10" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9);">
            <div class="text-center mb-4">
                <v-avatar color="error-lighten-4" size="70" class="mb-2">
                    <v-icon icon="mdi-logout-variant" color="error" size="40"></v-icon>
                </v-avatar>
                <h3 class="text-h5 font-weight-bold">Deseja sair?</h3>
                <p class="text-body-1 text-medium-emphasis">Você precisará fazer login novamente para acessar seus dados.</p>
            </div>
            
            <v-card-actions class="justify-center gap-2">
                <v-btn 
                    variant="text" 
                    class="px-6 text-none font-weight-bold" 
                    rounded="lg"
                    @click="confirmLogout = false"
                >
                    Cancelar
                </v-btn>
                <v-btn 
                    color="error" 
                    variant="elevated" 
                    class="px-8 text-none font-weight-bold" 
                    elevation="2"
                    rounded="lg"
                    @click="handleLogout"
                >
                    Sair Agora
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-layout>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { ref} from 'vue'
import LanguageSelector from './Language/LanguageSelector.vue'

const authStore = useAuthStore()
const router = useRouter()
const confirmLogout = ref(false)


import { onMounted } from 'vue'
onMounted(async () => {
    if (authStore.isAuthenticated) {
        await authStore.fetchUser()
    }
    console.log(authStore.user)
})

const handleLogout = () => {
    confirmLogout.value = false
    authStore.logout()
    router.push('/login')
}
</script>

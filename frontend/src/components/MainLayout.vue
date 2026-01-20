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
            <v-btn to="/planos" variant="text" color="white" class="mx-1 text-none font-weight-medium">Planos</v-btn>
            <v-btn to="/login" variant="elevated" color="white" class="ml-2 text-none font-weight-bold text-primary">
            Entrar
            </v-btn>
        </template>
        <template v-else>
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="text" color="white" prepend-icon="mdi-account-circle">
                        {{ authStore.user?.name || 'Conta' }}
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item to="/perfil" prepend-icon="mdi-account" title="Perfil"></v-list-item>
                    <v-divider></v-divider>
                    <v-list-item @click="handleLogout" prepend-icon="mdi-logout" title="Sair" color="error"></v-list-item>
                </v-list>
            </v-menu>
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
            <v-list-item prepend-icon="mdi-view-dashboard" title="Painel" to="/painel"></v-list-item>
            <v-list-item prepend-icon="mdi-bank-transfer" title="Lançamentos" to="/lancamentos"></v-list-item>
            <v-list-item prepend-icon="mdi-chart-bar" title="Relatórios" to="/relatorios"></v-list-item>
            <v-list-item prepend-icon="mdi-account" title="Perfil" to="/perfil"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin'" prepend-icon="mdi-shield-crown" title="Admin" to="/admin"></v-list-item>
             <v-list-item prepend-icon="mdi-tag-text-outline" title="Planos" to="/planos"></v-list-item>
        </v-list>

        <template v-slot:append>
          <div class="pa-2">
            <v-btn block color="error" variant="text" @click="handleLogout" prepend-icon="mdi-logout">
              Sair
            </v-btn>
          </div>
        </template>
    </v-navigation-drawer>

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-layout>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

// Fetch fresh user data on mount to ensure role is correct
import { onMounted } from 'vue'
onMounted(() => {
    if (authStore.isAuthenticated) {
        authStore.fetchUser()
    }
})

const handleLogout = () => {
    authStore.logout()
    router.push('/login')
}
</script>

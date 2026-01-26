<template>
  <v-layout>
    <v-app-bar color="primary" elevation="2">
  <v-app-bar-nav-icon
  v-if="authStore.isAuthenticated"
  @click="toggleDrawer"
  class="mr-2"
  variant="text"
/>


  <v-toolbar-title
    class="font-weight-bold app-title"
    style="cursor: pointer"
    @click="$router.push('/')"
  >
    <v-icon icon="mdi-chart-pie" class="mr-2" />
    Finalyze
  </v-toolbar-title>

  <v-spacer />

  <div class="d-flex align-center d-none d-md-flex">
    <template v-if="!authStore.isAuthenticated">
      <v-btn to="/planos" variant="text" color="white" class="mx-1">
        Planos
      </v-btn>
      <v-btn
        to="/login"
        variant="elevated"
        color="white"
        class="ml-2 font-weight-bold text-primary"
      >
        Entrar
      </v-btn>
    </template>
  </div>
</v-app-bar>


    <v-navigation-drawer
  v-if="authStore.isAuthenticated"
  v-model="drawer"
  :rail="isDesktop && rail"
  :permanent="isDesktop"
  :temporary="!isDesktop"
  class="animated-drawer"
  elevation="6"
>


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
           <v-list-item prepend-icon="mdi-home" title="Página inicial"to="/" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-view-dashboard" title="Painel" to="/painel"  @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-bank-transfer" title="Lançamentos" to="/lancamentos"  @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin' || authStore.user?.plan_id != null" prepend-icon="mdi-chart-bar" title="Relatórios" to="/relatorios"  @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-account" title="Perfil" to="/perfil" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.role === 'admin'" prepend-icon="mdi-shield-crown" title="Admin" to="/admin"  @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.plan_id === null" prepend-icon="mdi-tag-text-outline" title="Planos" to="/planos"  @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item  color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" title="Sair" ></v-list-item>
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
import { ref, computed } from 'vue'
import { useDisplay } from 'vuetify'
import { watch } from 'vue'


const authStore = useAuthStore()
const router = useRouter()
const confirmLogout = ref(false)

const drawer = ref(false)
const rail = ref(false)
const { mdAndUp } = useDisplay()
const isDesktop = computed(() => mdAndUp.value)

const toggleDrawer = () => {
  if (isDesktop.value) {
    rail.value = !rail.value
    drawer.value = true
  } else {
    rail.value = false
    drawer.value = !drawer.value
  }
}



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
onMounted(() => {
  if (isDesktop.value) {
    drawer.value = true
    rail.value = false
  }
})

watch(isDesktop, (desktop) => {
  if (desktop) {
    drawer.value = true
    rail.value = false
  } else {
    drawer.value = false
    rail.value = false
  }
})
</script>

<style scoped>

.animated-drawer {
  transition:
    width 0.45s cubic-bezier(0.4, 0, 0.2, 1),
    transform 0.45s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.45s ease,
    background-color 0.45s ease;
}

.v-navigation-drawer--rail {
  box-shadow: 0 0 0 rgba(0,0,0,0);
  background-color: rgba(24, 103, 192, 0.05);
}

.app-title {
  max-width: none !important;
  overflow: visible !important;
  white-space: nowrap;
}

@media (max-width: 600px) {
  .app-title {
    font-size: 1rem;
  }
}


</style>
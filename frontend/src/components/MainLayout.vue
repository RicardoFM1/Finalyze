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
    @click="$router.push({ name: 'Home' })"
  >
    <v-icon icon="mdi-chart-pie" class="mr-2" />
    Finalyze
  </v-toolbar-title>

  <v-spacer />

  <div class="d-flex align-center">
    <template v-if="!authStore.isAuthenticated">
        <Coinselector />
        <LanguageSelector />
        <v-btn :to="{ name: 'Plans' }" variant="text" color="white" class="mx-1 text-none font-weight-medium">
          {{ $t('landing.btn_plans') }}
        </v-btn>

        <v-btn
          :to="{ name: 'Login' }"
          variant="elevated"
          color="white"
          class="ml-2 mr-2 font-weight-bold text-primary text-none"
        >
          {{ $t('landing.btn_login') }}
        </v-btn>
    </template>
  </div>
</v-app-bar>


    <v-navigation-drawer
  v-if="authStore.isAuthenticated && uiAuthStore.loading === false"
  v-model="drawer"
  :rail="isDesktop && rail"
  :permanent="isDesktop"
  :temporary="!isDesktop"
  class="animated-drawer"
  elevation="6"
>
        <v-list>
            <v-list-item v-if="authStore.user">
                <template v-slot:prepend>
                    <v-avatar color="primary-lighten-4" size="40">
                        <v-img
                            v-if="authStore.user.avatar"
                            :src="'http://localhost:8000/storage/' + authStore.user.avatar"
                            cover
                        ></v-img>
                        <span v-else class="text-caption font-weight-bold text-primary">
                            {{ getInitials(authStore.user.nome) }}
                        </span>
                    </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold">{{ authStore.user.nome }}</v-list-item-title>
                <v-list-item-subtitle>{{ authStore.user.email }}</v-list-item-subtitle>
            </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list density="compact" nav>
           <v-list-item prepend-icon="mdi-home" title="Página inicial" :to="{ name: 'Home' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Painel Financeiro')" prepend-icon="mdi-view-dashboard" title="Painel" :to="{ name: 'Dashboard' }"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Lançamentos')" prepend-icon="mdi-bank-transfer" title="Lançamentos" :to="{ name: 'Lancamentos' }"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Metas')" prepend-icon="mdi-flag-checkered" title="Metas" :to="{ name: 'Metas' }"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Relatórios Gráficos')" prepend-icon="mdi-chart-bar" title="Relatórios" :to="{ name: 'Reports' }"></v-list-item>
            <v-list-item prepend-icon="mdi-account" title="Perfil" :to="{ name: 'Profile' }"></v-list-item>
            <v-list-item v-if="authStore.user?.admin === true" prepend-icon="mdi-shield-crown" title="Admin" :to="{ name: 'Admin' }"></v-list-item>
            <v-list-item prepend-icon="mdi-tag-text-outline" title="Planos" :to="{ name: 'Plans' }"></v-list-item>
            <v-list-item  color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" title="Sair"></v-list-item>
        </v-list>

        
    </v-navigation-drawer>

    <ModalBase v-model="confirmLogout" :title="$t('features.DS')" maxWidth="400px" persistent>
        <div class="text-center mb-4">
            <v-avatar color="error-lighten-4" size="70" class="mb-2">
                <v-icon icon="mdi-logout-variant" color="error" size="40"></v-icon>
            </v-avatar>
            <p class="text-body-1 text-medium-emphasis">{{ $t('features.VDS') }}</p>
        </div>
        
        <template #actions>
            <v-btn 
                variant="text" 
                class="px-6 text-none font-weight-bold" 
                rounded="lg"
                @click="confirmLogout = false"
            >
                {{ $t('features.stay_new') }}
            </v-btn>
            <v-btn 
                color="error" 
                variant="elevated" 
                class="px-8 text-none font-weight-bold ml-2" 
                elevation="2"
                rounded="lg"
                @click="handleLogout"
            >
                {{ $t('features.leave_new') }}
            </v-btn>
        </template>
    </ModalBase>

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-layout>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter,useRoute } from 'vue-router'
import { ref, computed, watch } from 'vue'
import { useDisplay } from 'vuetify'
import ModalBase from '../components/Modals/modalBase.vue'
import LanguageSelector from './Language/LanguageSelector.vue'
import Coinselector from './Currency/Coinselector.vue'

const authStore = useAuthStore()
const uiAuthStore = useUiStore()
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

const isAuthPage = computed(() => {
  return route.name === 'Login' || route.name === 'Register'
})


import { onMounted } from 'vue'
import { useUiStore } from '../stores/ui'
onMounted(async () => {
    if (authStore.isAuthenticated) {
        await authStore.fetchUser()
    }
})

const handleLogout = () => {
    confirmLogout.value = false
    authStore.logout()
    router.push({ name: 'Home' })
}

const getInitials = (name) => {
    if (!name) return ''
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
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
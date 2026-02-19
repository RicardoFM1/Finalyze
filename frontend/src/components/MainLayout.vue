<template>
  <v-layout>
    <template v-if="!uiAuthStore.loading">
      <v-app-bar color="primary" elevation="2">
         <v-app-bar-nav-icon
      v-if="
        (authStore.isAuthenticated &&
        !isAuthPage) && route.meta.hideNavBar !== true
      "
      @click="toggleDrawer"
      class="mr-2"
      variant="text"
    />

      <v-toolbar-title class="app-title pa-0">
  <div class="brand-wrapper" @click="$router.push({ name: 'Home' })">
    <img :src="logotipo" alt="Logo" class="logo" />
    <span class="brand-name">Finalyze</span>
  </div>
</v-toolbar-title>




      <v-spacer />

        <template v-if="isAuthPage">
          <v-btn
            prepend-icon="mdi-home"
            variant="text"
            color="white"
            class="text-none font-weight-bold"
            :to="{ name: 'Home' }"
          >
            {{ $t('login.btn_back_home') }}
          </v-btn>
        </template>
        <template v-else>
          <Coinselector />
        </template>

        <v-btn
          icon
          variant="text"
          color="white"
          class="ml-2"
          @click="uiAuthStore.toggleTheme"
        >
          <v-icon :icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'"></v-icon>
        </v-btn>

        <template v-if="!authStore.isAuthenticated">
            <v-btn :to="{ name: 'Plans' }" variant="text" color="white" class="mx-1 text-none font-weight-medium d-none d-sm-inline-flex">
              {{ $t('landing.btn_plans') }}
            </v-btn>
            <v-btn
              v-if="route.name === 'Register'"
              :to="{ name: 'Login' }"
              variant="elevated"
              color="white"
              class="ml-2 mr-4 font-weight-bold text-primary text-none"
            >
              {{ $t('landing.btn_login') }}
            </v-btn>
            <v-btn
              v-else
              :to="{ name: 'Register' }"
              variant="elevated"
              color="white"
              class="ml-2 mr-4 font-weight-bold text-primary text-none"
            >
              {{ $t('landing.btn_create_account') }}
            </v-btn>
        </template>
      </v-app-bar>


      <v-navigation-drawer
      v-if="
        (authStore.isAuthenticated &&
        !isAuthPage) && route.meta.hideNavBar !== true
      "
      v-model="drawer"
      :rail="isDesktop && rail"
      :permanent="isDesktop"
      :temporary="!isDesktop"
      class="animated-drawer "
      elevation="6"
    >

            <v-list>
            <v-list-item v-if="authStore.user" :title="authStore.user.nome" :subtitle="authStore.user.email">
                <template v-slot:prepend>
                    <v-avatar color="primary-lighten-4" size="40">
                        <v-img
                            v-if="authStore.user.avatar_url"
                            :src="authStore.user.avatar_url"
                            cover
                        ></v-img>
                        <v-img
                            v-else-if="authStore.user.avatar"
                            :src="authStore.getStorageUrl(authStore.user.avatar)"
                            cover
                        ></v-img>
                        <span v-else class="text-caption font-weight-bold text-primary">
                            {{ getInitials(authStore.user.nome) }}
                        </span>
                    </v-avatar>
                </template>
            </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list density="compact" nav>
           <v-list-item prepend-icon="mdi-home" :title="$t('sidebar.home')" :to="{ name: 'Home' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Painel Financeiro')" prepend-icon="mdi-view-dashboard" :title="$t('sidebar.dashboard')" :to="{ name: 'Dashboard' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Lançamentos')" prepend-icon="mdi-bank-transfer" :title="$t('sidebar.transactions')" :to="{ name: 'Lancamentos' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Metas')" prepend-icon="mdi-flag-checkered" :title="$t('sidebar.goals')" :to="{ name: 'Metas' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Relatórios Gráficos')" prepend-icon="mdi-chart-bar" :title="$t('sidebar.reports')" :to="{ name: 'Reports' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-account" :title="$t('sidebar.profile')" :to="{ name: 'Profile' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.admin" prepend-icon="mdi-shield-crown" :title="$t('sidebar.admin')" :to="{ name: 'Admin' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-tag-text-outline" :title="$t('sidebar.plans')" :to="{ name: 'Plans' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" :title="$t('sidebar.logout')"></v-list-item>
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
    <FinnChat v-if="authStore.isAuthenticated" />
    <v-main>
      <router-view />
    </v-main>
    </template>
  </v-layout>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useDisplay } from 'vuetify'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import logotipo from '../assets/logotipo.png'

import ModalBase from '../components/Modals/modalBase.vue'
import FinnChat from './IA/FinnChat.vue'

import Coinselector from './Currency/Coinselector.vue'

const authStore = useAuthStore()
const uiAuthStore = useUiStore()
const route = useRoute()
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


// getStorageUrl removed as it is now in authStore

onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser();
  }
  if (isDesktop.value && authStore.isAuthenticated) {
    drawer.value = true;
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
  display: flex !important;
  align-items: center;
}

.brand-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.logo {
  height: 64px; 
  width: auto;
  display: block;
}

.brand-name {
  font-weight: 800;
  font-size: 1.6rem; 
  letter-spacing: 0.5px;
  color: white;
}



@media (max-width: 600px) {
  .app-title {
    font-size: 1rem;
  }
}


</style>

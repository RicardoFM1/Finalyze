<template>
  <v-layout>
    <template v-if="!uiAuthStore.loading">
      <v-app-bar color="primary" elevation="2">
        <!-- LAYOUT MOBILE/TABLET: Centralizado e Compacto -->
        <div v-if="!lgAndUp" class="d-flex align-center justify-center w-100 px-2 h-100">
            <!-- Botão do Menu (Absoluto à Esquerda) -->
            <v-app-bar-nav-icon
                v-if="authStore.isAuthenticated && route.meta.hideNavBar !== true"
                @click="toggleDrawer"
                class="position-absolute left-0 ml-1"
                variant="text"
                size="small"
            />

            <!-- Grupo Central de Ações -->
            <div class="d-flex align-center gap-2">
                <!-- Se Logado: Logo + Nome Pequeno (Home já está no menu lateral) -->
                <div v-if="authStore.isAuthenticated" class="brand-wrapper-mobile mx-1" @click="$router.push({ name: 'Home' })">
                    <img :src="logotipo" alt="Logo" class="logo-mini" />
                    <span class="brand-name-mini">Finalyze</span>
                </div>
                
                <!-- Se Não Logado: Botão Home -->
                <v-btn v-else icon="mdi-home-outline" variant="text" color="white" :to="{ name: 'Home' }" size="small"></v-btn>

                <!-- Workspace Switcher Mobile -->
                <v-menu v-if="authStore.isAuthenticated && authStore.sharedAccounts.length > 1">
                    <template v-slot:activator="{ props }">
                        <v-btn
                            v-bind="props"
                            icon="mdi-office-building"
                            variant="text"
                            color="white"
                            size="small"
                        ></v-btn>
                    </template>
                    <v-list class="rounded-xl mt-2 overflow-hidden" elevation="4">
                        <v-list-item 
                            v-for="acc in authStore.sharedAccounts" 
                            :key="acc.id"
                            :active="authStore.workspaceId == acc.id"
                            @click="authStore.setWorkspace(acc.id)"
                        >
                            <template v-slot:prepend>
                                <v-avatar size="32" class="mr-2" color="primary">
                                    <span class="text-caption">{{ getInitials(acc.owner?.nome || 'User') }}</span>
                                </v-avatar>
                            </template>
                            <v-list-item-title class="font-weight-bold">
                                {{ acc.is_owner ? 'Minha Conta' : acc.owner?.nome }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>

                <v-btn v-if="authStore.hasFeature('lembretes')" icon variant="text" color="white" size="small" @click="$router.push({ name: 'Lembretes' })">
                    <v-icon icon="mdi-bell-ring-outline"></v-icon>
                </v-btn>
                
                <v-btn v-if="authStore.isAuthenticated" icon variant="text" color="white" size="small" @click="shareDialog = true">
                    <v-icon icon="mdi-account-group"></v-icon>
                </v-btn>

                <template v-if="authStore.isAuthenticated">
                    <Coinselector class="ml-1" />
                </template>

                <v-btn icon variant="text" color="white" @click="uiAuthStore.toggleTheme" size="small">
                    <v-icon :icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'"></v-icon>
                </v-btn>

                <template v-if="!authStore.isAuthenticated">
                    <v-btn
                        v-if="route.name === 'Register'"
                        :to="{ name: 'Login' }"
                        variant="elevated"
                        color="white"
                        class="ml-2 font-weight-bold text-primary text-none px-3"
                        size="small"
                        rounded="lg"
                    >
                        {{ $t('landing.btn_login') }}
                    </v-btn>
                    <v-btn
                        v-else
                        :to="{ name: 'Register' }"
                        variant="elevated"
                        color="white"
                        class="ml-2 font-weight-bold text-primary text-none px-3"
                        size="small"
                        rounded="lg"
                    >
                        {{ $t('landing.btn_create_account') }}
                    </v-btn>
                </template>
            </div>
        </div>

        <!-- LAYOUT DESKTOP -->
        <template v-else>
          <v-app-bar-nav-icon
            v-if="authStore.isAuthenticated && route.meta.hideNavBar !== true"
            @click="toggleDrawer"
            class="mr-2"
            variant="text"
          />

          <v-toolbar-title class="app-title pa-0" v-if="authStore.isAuthenticated || route.name !== 'Home'">
            <div class="brand-wrapper" @click="$router.push({ name: 'Home' })">
              <img :src="logotipo" alt="Logo" class="logo" />
              <span class="brand-name">Finalyze</span>
            </div>
          </v-toolbar-title>

          <v-spacer />

          <template v-if="isAuthPage">
            <v-btn
              prepend-icon="mdi-tag-multiple-outline"
              variant="text"
              color="white"
              class="text-none font-weight-bold"
              :to="{ name: 'Plans' }"
            >
              <span class="d-none d-sm-inline">{{ $t('landing.btn_plans') }}</span>
            </v-btn>
            
            <v-btn
              prepend-icon="mdi-home-outline"
              variant="text"
              color="white"
              class="text-none font-weight-bold ml-2"
              :to="{ name: 'Home' }"
            >
              <span>Home</span>
            </v-btn>
          </template>

          <template v-else>
            <!-- Workspace Switcher -->
            <v-menu v-if="authStore.sharedAccounts.length > 1">
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="tonal"
                        color="white"
                        prepend-icon="mdi-office-building"
                        class="text-none ml-2 rounded-xl"
                    >
                        {{ activeWorkspaceName }}
                    </v-btn>
                </template>
                <v-list class="rounded-xl mt-2 overflow-hidden" elevation="4">
                    <v-list-item 
                        v-for="acc in authStore.sharedAccounts" 
                        :key="acc.id"
                        :active="authStore.workspaceId == acc.id"
                        @click="authStore.setWorkspace(acc.id)"
                    >
                        <template v-slot:prepend>
                            <v-avatar size="32" class="mr-2" color="primary">
                                <span class="text-caption">{{ getInitials(acc.owner?.nome || 'User') }}</span>
                            </v-avatar>
                        </template>
                        <v-list-item-title class="font-weight-bold">
                            {{ acc.is_owner ? 'Minha Conta' : acc.owner?.nome }}
                        </v-list-item-title>
                        <v-list-item-subtitle v-if="!acc.is_owner">{{ acc.owner?.email }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
            </v-menu>

            <Coinselector />
            <v-btn v-if="authStore.hasFeature('lembretes')" icon variant="text" color="white" class="ml-2" @click="$router.push({ name: 'Lembretes' })">
                <v-icon icon="mdi-bell-ring-outline"></v-icon>
                <v-tooltip activator="parent" location="bottom">{{ $t('sidebar.reminders') }}</v-tooltip>
            </v-btn>
            <v-btn icon variant="text" color="white" class="ml-2" @click="shareDialog = true">
                <v-icon icon="mdi-account-group"></v-icon>
                <v-tooltip activator="parent" location="bottom">{{ $t('footer.colaboradores') || 'Colaboradores' }}</v-tooltip>
            </v-btn>
          </template>

          <v-btn icon variant="text" color="white" class="ml-4" @click="uiAuthStore.toggleTheme">
            <v-icon :icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'"></v-icon>
          </v-btn>

          <template v-if="!authStore.isAuthenticated">
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
            <v-list-item v-if="authStore.hasFeature('lembretes')" prepend-icon="mdi-calendar-clock" :title="$t('sidebar.reminders')" :to="{ name: 'Lembretes' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Relatórios Gráficos')" prepend-icon="mdi-chart-bar" :title="$t('sidebar.reports')" :to="{ name: 'Reports' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-account" :title="$t('sidebar.profile')" :to="{ name: 'Profile' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.user?.admin" prepend-icon="mdi-shield-crown" :title="$t('sidebar.admin')" :to="{ name: 'Admin' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-tag-text-outline" :title="$t('sidebar.plans')" :to="{ name: 'Plans' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-cog" :title="$t('sidebar.settings') || 'Configurações'" :to="{ name: 'Settings' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" :title="$t('sidebar.logout')"></v-list-item>
        </v-list>

        
    </v-navigation-drawer>

      <v-main>
        <router-view></router-view>
      </v-main>
    </template>
    <ModalBase v-model="confirmLogout" :title="$t('landing.features.DS')" maxWidth="400px" persistent>
        <div class="text-center mb-4">
            <v-avatar color="error-lighten-4" size="70" class="mb-2">
                <v-icon icon="mdi-logout-variant" color="error" size="40"></v-icon>
            </v-avatar>
            <p class="text-body-1 text-medium-emphasis">{{ $t('landing.features.VDS') }}</p>
        </div>
        
        <template #actions>
            <v-btn 
                variant="text" 
                class="px-6 text-none font-weight-bold" 
                rounded="lg"
                @click="confirmLogout = false"
            >
                {{ $t('landing.features.stay_new') }}
            </v-btn>
            <v-btn 
                color="error" 
                variant="elevated" 
                class="px-8 text-none font-weight-bold ml-2" 
                elevation="2"
                rounded="lg"
                @click="handleLogout"
            >
                {{ $t('landing.features.leave_new') }}
            </v-btn>
        </template>
    </ModalBase>
    <FinnChat v-if="authStore.isAuthenticated" />
    <CompartilharModal v-model="shareDialog" />
  </v-layout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useDisplay } from 'vuetify'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { toast } from 'vue3-toastify'
import logotipo from '../assets/logotipo.png'

import ModalBase from '../components/Modals/modalBase.vue'
import CompartilharModal from '../components/Modals/CompartilharModal.vue'
import FinnChat from './IA/FinnChat.vue'

import Coinselector from './Currency/Coinselector.vue'

const authStore = useAuthStore()
const uiAuthStore = useUiStore()
const router = useRouter()
const route = useRoute()

const confirmLogout = ref(false)
const shareDialog = ref(false)
const drawer = ref(false)
const rail = ref(false)

const { mdAndUp, lgAndUp } = useDisplay()
const isDesktop = computed(() => lgAndUp.value)

const isAuthPage = computed(() =>
  ['Login', 'Register'].includes(route.name)
)

const activeWorkspaceName = computed(() => {
    const active = authStore.sharedAccounts.find(a => a.id == authStore.workspaceId)
    if (active?.is_owner) return 'Área Pessoal'
    return active?.owner?.nome || 'Workspace'
})

const toggleDrawer = () => {
  if (uiAuthStore.loading) return;
  if (isDesktop.value) {
    rail.value = !rail.value;
    drawer.value = true;
  } else {
    rail.value = false;
    drawer.value = !drawer.value;
  }
}

const handleLogout = () => {
  confirmLogout.value = false
  authStore.logout()
  router.push({ name: 'Home' })
}


onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser();
  }
  if (authStore.isAuthenticated) {
    checkReminders();
    // Check every minute for precision
    setInterval(checkReminders, 60 * 1000);
  }
  if (isDesktop.value && authStore.isAuthenticated) {
    drawer.value = true;
  }
})

const notifiedSession = new Set()

const checkReminders = async () => {
    try {
        const response = await authStore.apiFetch('/lembretes')
        if (response.ok) {
            const list = await response.json()
            
            const now = new Date()
            const today = now.getFullYear() + '-' + 
                          String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(now.getDate()).padStart(2, '0')
            
            const currentTime = String(now.getHours()).padStart(2, '0') + ':' + 
                                String(now.getMinutes()).padStart(2, '0')
            
            const pending = list.filter(n => {
                const itemDate = n.prazo ? n.prazo.split('T')[0] : ''
                const isToday = itemDate === today
                const isAfterTime = !n.hora || currentTime >= n.hora
                
                return n.status === 'andamento' && 
                       isToday && 
                       isAfterTime &&
                       n.notificacao_site &&
                       !notifiedSession.has(n.id)
            })
            
            if (pending.length > 0) {
                pending.forEach(n => notifiedSession.add(n.id))
                toast.info(`⏰ Você tem ${pending.length} compromisso(s) na agenda para agora!`, {
                    autoClose: 10000,
                    theme: uiAuthStore.theme,
                    onClick: () => router.push({ name: 'Lembretes' })
                })
            }
        }
    } catch (e) {
        console.error('Erro ao verificar agenda:', e)
    }
}



const getInitials = (name) => {
    if (!name) return 'U'
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

watch(isDesktop, (desktop) => {
  drawer.value = desktop && authStore.isAuthenticated
  rail.value = false
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
  background-color: rgb(var(--v-theme-surface));
}
.app-title {
  display: flex !important;
  align-items: center;
  max-width: 250px; /* Limita a largura do título */
}

.brand-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  height: 48px; /* Altura fixa para alinhar */
  overflow: hidden;
}

.logo {
  height: 40px !important; /* Reduzido de 48px para ter margem */
  width: auto !important;
  max-width: 120px;
  object-fit: contain;
  display: block;
  flex-shrink: 0;
}

.brand-name {
  font-weight: 800;
  font-size: 1.4rem !important; /* Ligeiramente menor para caber melhor */
  letter-spacing: 0.5px;
  color: white;
  flex-shrink: 0;
  white-space: nowrap;
}

@media (max-width: 960px) {
  .logo {
    height: 32px !important;
  }
  .brand-name {
    font-size: 1.2rem !important;
  }
  .app-title {
    max-width: 180px;
  }
}

@media (max-width: 600px) {
  .app-title {
    max-width: 140px;
  }
  .brand-name {
    display: none; /* Esconde nome no mobile para sobrar espaço pros ícones */
  }
  .logo {
    height: 28px !important;
  }
}

  .brand-wrapper-mobile {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
  }

  .logo-mini {
    height: 24px;
    width: auto;
    flex-shrink: 0;
  }

  .brand-name-mini {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: white;
    white-space: nowrap;
  }


</style>
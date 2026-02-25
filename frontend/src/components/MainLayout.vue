<template>
  <v-layout>
    <v-app-bar color="primary" elevation="2">
        <!-- LAYOUT MOBILE/TABLET: Espaçado e Profissional -->
        <template v-if="!lgAndUp">
          <div class="d-flex align-center justify-space-between w-100 px-2 h-100">
            <!-- Left: Menu + Logo -->
            <div class="d-flex align-center">
                <v-app-bar-nav-icon
                    v-if="authStore.isAuthenticated && route.meta.hideNavBar !== true"
                    @click="toggleDrawer"
                    variant="text"
                    size="default"
                />
                <div class="brand-wrapper-mobile ml-1" @click="$router.push({ name: 'Home' })">
                    <img :src="logotipo" alt="Logo" class="logo-mini" />
                    <span class="brand-name-mini ml-1">Finalyze</span>
                </div>

            </div>

            <!-- Right: Action Icons -->
            <div class="d-flex align-center gap-2">

                <!-- AUTHENTICATED: bell + overflow menu -->
                <template v-if="authStore.isAuthenticated">
                    <v-btn v-if="authStore.hasFeature('lembretes')" icon variant="text" color="white" size="small" @click="$router.push({ name: 'Lembretes' })">
                        <v-icon icon="mdi-bell-ring-outline"></v-icon>
                    </v-btn>

                    <!-- Overflow "more" menu -->
                    <v-menu location="bottom end" class="rounded-xl">
                        <template v-slot:activator="{ props }">
                            <v-btn variant="text" color="white" v-bind="props" size="small">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                        <v-list class="rounded-lg" density="compact" min-width="200">
                            <!-- Currency -->
                            <v-list-subheader>{{ $t('landing.select_currency') }}</v-list-subheader>
                            <v-list-item
                                v-for="coin in mobileCoins"
                                :key="coin.value"
                                :active="mobileCurrency === coin.value"
                                color="primary"
                                @click="mobileCurrency = coin.value"
                            >
                                <template v-slot:prepend>
                                    <img :src="coin.flag" style="width:18px;height:18px;object-fit:cover;border-radius:3px" class="mr-2" />
                                </template>
                                <v-list-item-title>{{ coin.value }}</v-list-item-title>
                                <v-list-item-subtitle>{{ coin.name }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-divider class="my-2"></v-divider>

                <template v-if="authStore.sharedAccounts.length > 1 && !uiAuthStore.loading">
                    <v-list-subheader>Workspace</v-list-subheader>
                    <v-list-item
                        v-for="acc in authStore.sharedAccounts"
                        :key="acc.id"
                        :active="authStore.workspaceId == acc.id"
                        color="primary"
                        @click="authStore.setWorkspace(acc.id)"
                        prepend-icon="mdi-office-building"
                    >
                        <v-list-item-title class="font-weight-bold">{{ acc.is_owner ? ($t('common.my_account') || 'Minha Conta') : acc.owner?.nome }}</v-list-item-title>
                    </v-list-item>
                    <v-divider class="my-2"></v-divider>
                </template>

                <v-list-item prepend-icon="mdi-account-group" @click="shareDialog = true">
                    <v-list-item-title>{{ $t('footer.colaboradores') || 'Colaboradores' }}</v-list-item-title>
                </v-list-item>

                            
                       

                            
                            <v-list-item :prepend-icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'" @click="uiAuthStore.toggleTheme">
                                <v-list-item-title>{{ uiAuthStore.theme === 'light' ? $t('settings.dark_mode_active') : $t('settings.light_mode_active') }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </template>

              
                <template v-else>
                    <v-menu location="bottom end" class="rounded-xl">
                      <template v-slot:activator="{ props }">
                         <v-btn variant="text" color="white" v-bind="props" size="small" class="ml-1">
                             <v-icon>mdi-dots-vertical</v-icon>
                         </v-btn>
                      </template>
                      <v-list density="compact" class="rounded-lg" min-width="200">
                        <!-- Language -->
                        <v-list-subheader>Idioma / Language</v-list-subheader>
                        <v-list-item @click="authStore.setLanguage('pt')" :active="uiAuthStore.locale === 'pt'">
                          <template v-slot:prepend>🇧🇷</template>
                          <v-list-item-title class="ml-2">Português</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="authStore.setLanguage('en')" :active="uiAuthStore.locale === 'en'">
                          <template v-slot:prepend>🇺🇸</template>
                          <v-list-item-title class="ml-2">English</v-list-item-title>
                        </v-list-item>

                        <v-divider class="my-2"></v-divider>

                        <!-- Currency -->
                        <v-list-subheader>{{ $t('landing.select_currency') }}</v-list-subheader>
                        <v-list-item
                            v-for="coin in mobileCoins"
                            :key="coin.value"
                            :active="mobileCurrency === coin.value"
                            color="primary"
                            @click="mobileCurrency = coin.value"
                        >
                            <template v-slot:prepend>
                                <img :src="coin.flag" style="width:18px;height:18px;object-fit:cover;border-radius:3px" class="mr-2" />
                            </template>
                            <v-list-item-title>{{ coin.value }}</v-list-item-title>
                            <v-list-item-subtitle>{{ coin.name }}</v-list-item-subtitle>
                        </v-list-item>

                        <v-divider class="my-2"></v-divider>

                        <!-- Theme -->
                        <v-list-item :prepend-icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'" @click="uiAuthStore.toggleTheme">
                            <v-list-item-title>{{ uiAuthStore.theme === 'light' ? $t('settings.dark_mode_active') : $t('settings.light_mode_active') }}</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>

                    <v-btn
                        size="small"
                        v-bind="route.name === 'Register' ? { to: { name: 'Login' } } : { to: { name: 'Register' } }"
                        variant="elevated"
                        color="white"
                        class="ml-1 font-weight-bold text-primary text-none px-2"
                        rounded="lg"
                    >
                        {{ route.name === 'Register' ? $t('landing.btn_login') : $t('landing.btn_create_account') }}
                    </v-btn>
                </template>
            </div>
          </div>
        </template>

        <!-- LAYOUT DESKTOP -->
        <template v-else>
          <v-app-bar-nav-icon
            v-if="authStore.isAuthenticated && route.meta.hideNavBar !== true"
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

          <div class="d-flex align-center gap-3">
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
                v-if="route.name !== 'Home'"
                prepend-icon="mdi-home-outline"
                variant="text"
                color="white"
                class="text-none font-weight-bold"
                :to="{ name: 'Home' }"
              >
                <span>Home</span>
              </v-btn>
            </template>

            <template v-else>
              <!-- Workspace Switcher -->
              <v-menu v-if="authStore.isAuthenticated && authStore.sharedAccounts.length > 1">
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

              <Coinselector class="ml-2" />
              <v-btn v-if="authStore.hasFeature('lembretes') && route.name !== 'Lembretes' && route.name !== 'Checkout'" icon variant="text" color="white" class="ml-2" @click="$router.push({ name: 'Lembretes' })">
                  <v-icon icon="mdi-bell-ring-outline"></v-icon>
                  <v-tooltip activator="parent" location="bottom">{{ $t('sidebar.reminders') }}</v-tooltip>
              </v-btn>
              <v-btn v-if="authStore.isAuthenticated && route.name !== 'Checkout'" icon variant="text" color="white" class="ml-2" @click="shareDialog = true">
                  <v-icon icon="mdi-account-group"></v-icon>
                  <v-tooltip activator="parent" location="bottom">{{ $t('footer.colaboradores') || 'Colaboradores' }}</v-tooltip>
              </v-btn>
            </template>

            

            <v-btn icon variant="text" color="white" class="ml-2" @click="uiAuthStore.toggleTheme">
              <v-icon :icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'"></v-icon>
            </v-btn>

            <template v-if="!authStore.isAuthenticated || route.name === 'Checkout'">
              <v-menu location="bottom end" class="rounded-xl">
                <template v-slot:activator="{ props }">
                    <v-btn prepend-icon="mdi-translate" variant="text" color="white" v-bind="props" class="text-none">
                      {{ uiAuthStore.locale === 'pt' ? 'Português' : 'English' }}
                    </v-btn>
                </template>
                <v-list class="rounded-lg">
                  <v-list-item @click="authStore.setLanguage('pt')" :active="uiAuthStore.locale === 'pt'">
                    <template v-slot:prepend>🇧🇷</template>
                    <v-list-item-title class="ml-2">Português</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="authStore.setLanguage('en')" :active="uiAuthStore.locale === 'en'">
                    <template v-slot:prepend>🇺🇸</template>
                    <v-list-item-title class="ml-2">English</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>

              <v-btn
                v-if="route.name === 'Register' && route.name !== 'Checkout'"
                :to="{ name: 'Login' }"
                variant="elevated"
                color="white"
                class="font-weight-bold text-primary text-none ml-2"
              >
                {{ $t('landing.btn_login') }}
              </v-btn>
              <v-btn
                v-else-if="route.name !== 'Checkout'"
                :to="{ name: 'Register' }"
                variant="elevated"
                color="white"
                class="font-weight-bold text-primary text-none ml-2"
              >
                {{ $t('landing.btn_create_account') }}
              </v-btn>
            </template>
          </div>
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
      class="animated-drawer"
      elevation="6"
    >

            <v-list>
            <v-list-item v-if="authStore.user" class="px-2" :class="{ 'justify-center': rail && isDesktop }">
                <div class="d-flex align-center cursor-pointer hover-bg py-2 rounded-xl" :class="{ 'px-2': !rail || !isDesktop, 'justify-center w-100': rail && isDesktop }">
                    <v-avatar size="36" class="elevation-2 border-white" :class="{ 'mr-3': !rail || !isDesktop }">
                        <v-img v-if="authStore.user?.avatar_url" :src="authStore.user.avatar_url" cover></v-img>
                        <v-img v-else-if="authStore.user?.avatar" :src="authStore.getStorageUrl(authStore.user.avatar)" cover></v-img>
                        <div v-else class="fill-height d-flex align-center justify-center bg-primary-lighten-4 text-primary text-caption font-weight-bold">
                            {{ getInitials(authStore.user?.nome) }}
                        </div>
                    </v-avatar>
                    <div v-if="!rail || !isDesktop" class="flex-column align-start d-flex">
                        <span class="text-body-2 font-weight-bold text-truncate" style="max-width: 140px;">
                            {{ authStore.user?.nome }}
                        </span>
                        <span class="text-caption opacity-60 mt-n1 text-truncate" style="max-width: 140px; font-size: 0.65rem !important;">
                            {{ authStore.user?.email }}
                        </span>
                        
                    </div>
                </div>
            </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list density="compact" nav>
            <!-- Workspace Switcher in Sidebar -->
            <v-list-item 
                v-if="authStore.isAuthenticated"
                prepend-icon="mdi-office-building" 
                :title="activeWorkspaceName"
                :subtitle="t('common.active_workspace') || 'Área de Trabalho Ativa'"
                color="secondary"
                :class="{ 'mb-2 bg-secondary-lighten-5 rounded-lg border-dashed': authStore.sharedAccounts.length > 1, 'mb-2 opacity-80': authStore.sharedAccounts.length <= 1 }"
            >
                <v-menu v-if="authStore.sharedAccounts.length > 1" activator="parent" class="rounded-xl mt-2 overflow-hidden" elevation="4">
                    <v-list>
                        <v-list-item 
                            v-for="acc in authStore.sharedAccounts" 
                            :key="acc.id"
                            :active="authStore.workspaceId == acc.id"
                            @click="authStore.setWorkspace(acc.id)"
                        >
                            <v-list-item-title class="font-weight-bold">
                                {{ acc.is_owner ? $t('common.my_account') || 'Minha Conta' : acc.owner?.nome }}
                            </v-list-item-title>
                            <v-list-item-subtitle>{{ acc.owner?.email }}</v-list-item-subtitle>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-list-item>

           <v-list-item prepend-icon="mdi-home" :title="$t('sidebar.home')" :to="{ name: 'Home' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Painel Financeiro')" prepend-icon="mdi-view-dashboard" :title="$t('sidebar.dashboard')" :to="{ name: 'Dashboard' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Lançamentos')" prepend-icon="mdi-bank-transfer" :title="$t('sidebar.transactions')" :to="{ name: 'Lancamentos' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Metas')" prepend-icon="mdi-flag-checkered" :title="$t('sidebar.goals')" :to="{ name: 'Metas' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('lembretes')" prepend-icon="mdi-calendar-clock" :title="$t('sidebar.reminders')" :to="{ name: 'Lembretes' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Relatórios Gráficos')" prepend-icon="mdi-chart-bar" :title="$t('sidebar.reports')" :to="{ name: 'Reports' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-account" :title="$t('sidebar.profile')" :to="{ name: 'Profile' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item v-if="authStore.hasFeature('Admin')" prepend-icon="mdi-shield-crown" :title="$t('sidebar.admin')" :to="{ name: 'Admin' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-tag-text-outline" :title="$t('sidebar.plans')" :to="{ name: 'Plans' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item prepend-icon="mdi-cog" :title="$t('sidebar.settings') || 'Configurações'" :to="{ name: 'Settings' }" @click="!isDesktop && (drawer = false)"></v-list-item>
            <v-list-item color="error" class="text-error" variant="text" @click="confirmLogout = true" prepend-icon="mdi-logout" :title="$t('sidebar.logout')"></v-list-item>
        </v-list>

        
    </v-navigation-drawer>

      <v-main>
        <router-view></router-view>
      </v-main>
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
    <FinnChat v-if="authStore.isAuthenticated && authStore.hasFeature('Finn AI') && route.name !== 'Checkout'" />
    <CompartilharModal v-model="shareDialog" />
  </v-layout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useDisplay } from 'vuetify'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue3-toastify'
import logotipo from '../assets/logotipo.png'

import ModalBase from '../components/Modals/modalBase.vue'
import CompartilharModal from '../components/Modals/CompartilharModal.vue'
import FinnChat from './IA/FinnChat.vue'

import Coinselector from './Currency/Coinselector.vue'
import { useCurrencyStore } from '../stores/currency'
import { AVAILABLE_CURRENCIES } from '../composables/useMoney'

const authStore = useAuthStore()
const uiAuthStore = useUiStore()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const confirmLogout = ref(false)
const shareDialog = ref(false)
const drawer = ref(false)
const rail = ref(false)
const remindersIntervalId = ref(null)

const { mdAndUp, lgAndUp, smAndDown } = useDisplay()
const isDesktop = computed(() => lgAndUp.value)

// Mobile currency selector
const currencyStore = useCurrencyStore()
const mobileCurrency = ref(currencyStore.currency)
const mobileCoins = computed(() => 
  Object.values(AVAILABLE_CURRENCIES).map(c => ({
    value: c.code,
    name: t(`common.currencies.${c.code?.toLowerCase()}`),
    flag: c.code === 'BRL' ? 'https://flagcdn.com/w40/br.png' : 
          c.code === 'USD' ? 'https://flagcdn.com/w40/us.png' :
          c.code === 'EUR' ? 'https://flagcdn.com/w40/eu.png' :
          'https://flagcdn.com/w40/gb.png'
  }))
)
watch(mobileCurrency, (v) => currencyStore.setCurrency(v))
watch(() => currencyStore.currency, (v) => { mobileCurrency.value = v })

const isAuthPage = computed(() =>
  ['Login', 'Register'].includes(route.name)
)

const { locale } = useI18n()

watch(() => uiAuthStore.locale, (newLocale) => {
  locale.value = newLocale
}, { immediate: true })

const activeWorkspaceName = computed(() => {
    const active = authStore.sharedAccounts.find(a => a.id == authStore.workspaceId)
    if (!active && uiAuthStore.loading) return '...'
    if (!active) return authStore.user?.nome || 'Workspace'
    if (active?.is_owner) return t('common.my_account') || 'Minha Conta'
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
    authStore.fetchSharedAccounts();
    // Wait for initial load to finish before checking reminders to avoid multiple toasts
    setTimeout(() => {
        if (!authStore.hasFeature('lembretes')) return;
        checkReminders();
        remindersIntervalId.value = setInterval(checkReminders, 60 * 1000);
    }, 2000);
  }
  if (isDesktop.value && authStore.isAuthenticated) {
    drawer.value = true;
  }
})

onUnmounted(() => {
    if (remindersIntervalId.value) {
        clearInterval(remindersIntervalId.value);
        remindersIntervalId.value = null;
    }
})

const notifiedSession = new Set()

const checkReminders = async () => {
    if (!authStore.hasFeature('lembretes')) return;
    try {
        const response = await authStore.apiFetch('/lembretes', {
            suppress403Redirect: true,
            suppress403Toast: true
        })
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
                toast.info(t('metas.notifications.reminder_alert', { count: pending.length }), {
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
  height: 48px !important;
  width: auto !important;
  max-width: 160px;
  object-fit: contain;
  display: block;
  flex-shrink: 0;
  /* Always white - sits on primary-colored header */
  filter: brightness(10);
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
    .v-main {
        padding-left: 0 !important;
        padding-right: 0 !important;
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
    height: 36px;
    width: auto;
    flex-shrink: 0;
    /* Always white - sits on primary-colored header */
    filter: brightness(10);
  }

  .brand-name-mini {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: white;
    white-space: nowrap;
  }


</style>

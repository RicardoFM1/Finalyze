<template>
  <v-layout>
    <v-app-bar v-if="!authStore.mustVerifyEmail" color="primary" elevation="2">
        
        <template v-if="!lgAndUp">
          <div class="d-flex align-center justify-space-between w-100 px-2 h-100">
            
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

            <div class="d-flex align-center gap-2">

                <template v-if="authStore.isAuthenticated">
                    <v-btn v-if="authStore.hasFeature('lembretes')" icon variant="text" color="white" size="small" @click="$router.push({ name: 'Lembretes' })">
                        <v-icon icon="mdi-bell-ring-outline"></v-icon>
                    </v-btn>

                    <v-menu location="bottom end" class="rounded-xl">
                        <template v-slot:activator="{ props }">
                            <v-btn variant="text" color="white" v-bind="props" size="small">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                        <v-list class="rounded-lg" density="compact" min-width="200">
                            
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

                <template v-if="authStore.loadingSharedAccounts">
                    <v-list-subheader>Workspace</v-list-subheader>
                    <v-list-item v-for="i in 2" :key="i">
                        <v-skeleton-loader type="list-item-avatar"></v-skeleton-loader>
                    </v-list-item>
                </template>
                <template v-else-if="authStore.sharedAccounts.length > 1">
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
                    <div v-if="authStore.loadingSharedAccounts || uiAuthStore.loading" class="d-flex align-center">
                        <v-skeleton-loader type="actions" class="bg-transparent" width="120"></v-skeleton-loader>
                    </div>
                    <v-menu v-else location="bottom end" class="rounded-xl">
                      <template v-slot:activator="{ props }">
                         <v-btn variant="text" color="white" v-bind="props" size="small" class="ml-1">
                             <v-icon>mdi-dots-vertical</v-icon>
                         </v-btn>
                      </template>
                      <v-list density="compact" class="rounded-lg" min-width="200">
                        
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

                        <v-list-item :prepend-icon="uiAuthStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'" @click="uiAuthStore.toggleTheme">
                            <v-list-item-title>{{ uiAuthStore.theme === 'light' ? $t('settings.dark_mode_active') : $t('settings.light_mode_active') }}</v-list-item-title>
                        </v-list-item>

                        <v-divider class="my-2"></v-divider>

                        <v-list-item prepend-icon="mdi-tag-multiple-outline" :to="{ name: 'Plans' }">
                            <v-list-item-title>{{ $t('landing.btn_plans') }}</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>

                    <v-btn
                        v-if="!authStore.loadingSharedAccounts && !uiAuthStore.loading"
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
            <template v-if="isAuthPage || !authStore.isAuthenticated">
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
              <div v-if="authStore.loadingSharedAccounts || uiAuthStore.loading" class="d-flex align-center gap-2">
                <v-skeleton-loader type="button" class="bg-primary-lighten-2 ml-2 rounded-xl" width="130" height="36" color="primary"></v-skeleton-loader>
                <div class="d-flex gap-1">
                    <v-skeleton-loader type="avatar" class="bg-primary-lighten-2" size="32" color="primary"></v-skeleton-loader>
                    <v-skeleton-loader type="avatar" class="bg-primary-lighten-2" size="32" color="primary"></v-skeleton-loader>
                </div>
              </div>
              <template v-else>
                
                <v-menu v-if="authStore.isAuthenticated && authStore.sharedAccounts.length > 1">
                    <template v-slot:activator="{ props }">
                         <v-btn
                            v-bind="props"
                            variant="tonal"
                            color="white"
                            prepend-icon="mdi-office-building"
                             class="text-none ml-2 rounded-xl workspace-btn"
                             :loading="authStore.loadingSharedAccounts"
                             max-width="160"
                        >
                             <span class="text-truncate" style="max-width: 100px; display: inline-block;">{{ activeWorkspaceName }}</span>
                         </v-btn>
                    </template>
                     <v-list class="rounded-xl mt-2 overflow-hidden" elevation="4" min-width="220">
                        <template v-if="authStore.loadingSharedAccounts">
                            <v-list-item v-for="i in 2" :key="i">
                                <v-skeleton-loader type="list-item-avatar-two-line"></v-skeleton-loader>
                            </v-list-item>
                        </template>
                        <template v-else>
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
                        </template>
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
        !isAuthPage && !authStore.mustVerifyEmail) && route.meta.hideNavBar !== true
      "
      v-model="drawer"
      :rail="isDesktop && rail"
      :permanent="isDesktop"
      :temporary="!isDesktop"
      class="animated-drawer"
      elevation="6"
    >

        <v-list v-if="authStore.loadingSharedAccounts || uiAuthStore.loading">
            <v-list-item v-for="i in 3" :key="i">
                <v-skeleton-loader type="list-item-avatar-two-line" class="bg-transparent"></v-skeleton-loader>
            </v-list-item>
        </v-list>
        <template v-else>
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
                
                <v-list-item 
                    v-if="authStore.isAuthenticated"
                    prepend-icon="mdi-office-building" 
                    :title="activeWorkspaceName"
                    :subtitle="t('common.active_workspace') || 'Área de Trabalho Ativa'"
                    color="secondary"
                    :disabled="authStore.loadingSharedAccounts"
                    :class="{ 'mb-2 bg-secondary-lighten-5 rounded-lg border-dashed': authStore.sharedAccounts.length > 1, 'mb-2 opacity-80': authStore.sharedAccounts.length <= 1 }"
                >
                    <template v-slot:append>
                        <v-progress-circular v-if="authStore.loadingSharedAccounts" indeterminate size="16" width="2" color="primary"></v-progress-circular>
                        <v-icon v-else-if="authStore.sharedAccounts.length > 1" icon="mdi-chevron-down" size="16"></v-icon>
                    </template>
                    <v-menu v-if="authStore.sharedAccounts.length > 1" activator="parent" class="rounded-xl mt-2 overflow-hidden" elevation="4">
                        <v-list>
                            <v-list-item 
                                v-for="acc in authStore.sharedAccounts" 
                                :key="acc.id"
                                :active="authStore.workspaceId == acc.id"
                                @click="authStore.setWorkspace(acc.id)"
                            >
                                <template v-slot:prepend>
                                    <v-avatar size="32" class="mr-3" color="primary">
                                        <span class="text-caption">{{ getInitials(acc.owner?.nome || 'User') }}</span>
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="font-weight-bold">
                                    {{ acc.is_owner ? $t('common.my_account') || 'Minha Conta' : acc.owner?.nome }}
                                </v-list-item-title>
                                <v-list-item-subtitle>{{ acc.owner?.email }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-list-item>
            </v-list>
        </template>

        <v-list density="compact" nav>
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

    <ModalCompleteSocialRegistration 
        v-if="authStore.mustCompleteRegistration"
        :modelValue="true"
        :persistent="false"
        @update:modelValue="v => { if (!v) authStore.mustCompleteRegistration = null }"
        :usuario-id="authStore.mustCompleteRegistration.usuario_id"
        :email="authStore.mustCompleteRegistration.email"
        :onboarding-token="authStore.mustCompleteRegistration.onboarding_token"
        @complete="handleGlobalOnboardingComplete"
    />

    <ModalBase 
        v-if="authStore.mustVerifyEmail" 
        :modelValue="true" 
        persistent 
        hide-close
        maxWidth="450px" 
        :title="$t('auth.verify_email_title') || 'Verifique seu e-mail'"
    >
        <div class="pa-6">
            <EmailVerification 
                :email="authStore.mustVerifyEmail"
                :loading="globalVerifyLoading"
                :error="globalVerifyError"
                @verify="handleGlobalVerify"
                @resend="handleGlobalResend"
                @back="authStore.clearAuthModals(); $router.push({ name: 'Login' })"
            />
        </div>
    </ModalBase>
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

import ModalCompleteSocialRegistration from './Auth/ModalCompleteSocialRegistration.vue';
import EmailVerification from './Auth/EmailVerification.vue';

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

const globalVerifyLoading = ref(false)
const globalVerifyError = ref('')

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
    
    if (authStore.loadingSharedAccounts || uiAuthStore.loading) return '...'
    
    const active = authStore.sharedAccounts.find(a => a.id == authStore.workspaceId)
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
    
    setTimeout(() => {
        if (!authStore.hasFeature('lembretes')) return;
        checkReminders();
        remindersIntervalId.value = setInterval(checkReminders, 60 * 1000);
    }, 2000);
  }
  if (isDesktop.value && authStore.isAuthenticated) {
    drawer.value = true;
    rail.value = false;
  }
})

watch(() => route.name, (newName) => {
    if (newName === 'Home' && authStore.isAuthenticated && isDesktop.value) {
        drawer.value = true;
        rail.value = false;
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
                
                const itemDate = n.prazo ? n.prazo.substring(0, 10) : ''
                const isToday = itemDate === today
                const isAfterTime = !n.hora || currentTime >= n.hora

                const notifySite = !!n.notificacao_site

                return n.status === 'andamento' && 
                       isToday && 
                       isAfterTime &&
                       notifySite &&
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

const handleGlobalOnboardingComplete = (result) => {
    if (result.requer_verificacao) {
        authStore.mustCompleteRegistration = null
        authStore.mustVerifyEmail = result.email
    } else {
        authStore.clearAuthModals()
    }
}

const handleGlobalVerify = async (code) => {
    globalVerifyLoading.value = true
    globalVerifyError.value = ''
    try {
        await authStore.verifyCode(authStore.mustVerifyEmail, code)
        authStore.clearAuthModals()
        await authStore.fetchUser(true)
        await authStore.fetchSharedAccounts()
    } catch (e) {
        globalVerifyError.value = e.message
    } finally {
        globalVerifyLoading.value = false
    }
}

const handleGlobalResend = async () => {
    try {
        await authStore.resendCode(authStore.mustVerifyEmail)
        toast.success(t('checkout.toasts.resend_success') || 'Código reenviado!')
    } catch (e) {
        toast.error(e.message)
    }
}
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
  max-width: 250px; 
}

.brand-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  height: 48px; 
  overflow: hidden;
}

.logo {
  height: 48px !important;
  width: auto !important;
  max-width: 160px;
  object-fit: contain;
  display: block;
  flex-shrink: 0;
  
  filter: brightness(10);
}

.brand-name {
  font-weight: 800;
  font-size: 1.4rem !important; 
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
    display: none; 
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

<template>
  <div class="home-wrapper">
  
    <section class="hero-section d-flex align-center justify-center">
      <div class="hero-bg-overlay"></div>
      <v-container>
        <v-row align="center" justify="center">
          <v-col cols="12" md="7" class="text-center text-md-left hero-content">
            <h1 class="text-h3 text-md-h2 font-weight-black mb-6 animate-fade-up">
               {{ $t('landing.hero_title_alt') }}<span class="gradient-text ml-4" > {{$t('landing.destiny') }}</span> 
            </h1>
            <p class="text-body-1 text-md-h6 text-medium-emphasis mb-8 animate-fade-up-delay">
              {{ $t('landing.hero_subtitle_alt') }}
            </p>
            <div class="d-flex flex-wrap justify-center justify-md-start animate-fade-up-delay-2 gap-btns">
              <v-btn color="primary" size="x-large" class="rounded-xl px-8 hero-btn mb-3 mb-md-0 mr-md-4" :to="{ name: 'Plans' }" elevation="12">
                {{ $t('landing.btn_start') }}
              </v-btn>
              <v-btn v-if="!authStore.isAuthenticated" variant="tonal" size="x-large" class="rounded-xl px-8 glass-btn mb-3 mb-md-0 mr-md-4" :to="{ name: 'Login' }">
                {{ $t('landing.btn_login') }}
              </v-btn>
              <v-btn v-else variant="tonal" size="x-large" class="rounded-xl px-8 glass-btn mb-3 mb-md-0" :to="{ name: 'Dashboard' }">
                {{ $t('landing.btn_my_dashboard') }}
              </v-btn>
            </div>
          </v-col>
          <v-col cols="12" md="5" class="d-none d-md-flex justify-center position-relative">
            <div class="hero-visual animate-float">
              <v-icon icon="mdi-chart-areaspline" color="primary" size="300" class="opacity-20"></v-icon>
              <v-icon icon="mdi-shield-check" color="success" size="120" class="absolute-icon bottom-left"></v-icon>
              <v-icon icon="mdi-rocket-launch" color="warning" size="80" class="absolute-icon top-right"></v-icon>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

   
    <v-container class="py-16">
      <v-row class="mb-12 text-center">
        <v-col cols="12">
          <h2 class="text-h5 text-md-h4 font-weight-bold mb-2">{{ $t('landing.power_title') }}</h2>
          <p class="text-body-1 text-medium-emphasis">{{ $t('landing.power_subtitle') }}</p>
        </v-col>
      </v-row>
      <v-row>
        <v-col v-for="(feature, i) in features" :key="i" cols="12" md="4">
          <v-card class="feature-card glass-card h-100 pa-6 rounded-xl border-card" elevation="0">
            <div class="icon-circle mb-6" :class="feature.colorClass">
              <v-icon :icon="feature.icon" size="32" color="white"></v-icon>
            </div>
            <h3 class="text-h6 font-weight-bold mb-3">{{ feature.title }}</h3>
            <p class="text-body-2 text-medium-emphasis">{{ feature.desc }}</p>
            <div class="card-glow" :style="{ backgroundColor: feature.glowColor }"></div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const { t } = useI18n()

const features = computed(() => [
  {
    title: t('landing.features.analysis_title'),
    desc: t('landing.features.analysis_text'),
    icon: 'mdi-auto-fix',
    colorClass: 'receita-gradient',
    glowColor: 'rgba(56, 239, 125, 0.1)'
  },
  {
    title: t('landing.features.security_title'),
    desc: t('landing.features.security_text'),
    icon: 'mdi-security',
    colorClass: 'despesa-gradient',
    glowColor: 'rgba(255, 75, 43, 0.1)'
  },
  {
    title: t('landing.features.goals_title'),
    desc: t('landing.features.goals_text'),
    icon: 'mdi-bullseye-arrow',
    colorClass: 'saldo-gradient',
    glowColor: 'rgba(0, 131, 176, 0.1)'
  }
])
</script>

<style scoped>
.gap-btns > .v-btn {
  margin-bottom: 16px;
  margin-right: 0;
}
@media (min-width: 768px) {
  .gap-btns > .v-btn {
    margin-bottom: 0;
    margin-right: 24px;
  }
  .gap-btns > .v-btn:last-child {
    margin-right: 0;
  }
}

.hero-section {
  min-height: 90vh;
  position: relative;
  overflow: hidden;
  background: radial-gradient(circle at 10% 20%, rgba(24, 103, 192, 0.05) 0%, transparent 40%),
              radial-gradient(circle at 90% 80%, rgba(17, 153, 142, 0.05) 0%, transparent 40%);
}

.hero-content {
  z-index: 2;
}

.gradient-text {
  background: linear-gradient(135deg, #1867C0 0%, #11998E 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.1) !important;
  transition: all 0.4s ease;
}

.feature-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 30px 60px rgba(0,0,0,0.08) !important;
}

.feature-card:hover .card-glow {
  opacity: 1;
}

.card-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.4s ease;
  z-index: -1;
  filter: blur(40px);
}

.icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 24px rgba(0,0,0,0.1);
}

.receita-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.despesa-gradient { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
.saldo-gradient { background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%); }

.glass-btn {
  background: rgba(24, 103, 192, 0.05);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(24, 103, 192, 0.1);
}

.hero-visual {
  position: relative;
}

.absolute-icon {
  position: absolute;
  filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
}

.bottom-left { bottom: 20px; left: -20px; }
.top-right { top: 20px; right: 10px; }

.cta-card {
  background: linear-gradient(135deg, #1867C0 0%, #1A237E 100%) !important;
  position: relative;
}

.cta-bg-pattern {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.1) 1px, transparent 0);
  background-size: 30px 30px;
}


@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-up { animation: fadeInUp 0.8s ease-out forwards; }
.animate-fade-up-delay { animation: fadeInUp 0.8s 0.2s ease-out forwards; opacity: 0; }
.animate-fade-up-delay-2 { animation: fadeInUp 0.8s 0.4s ease-out forwards; opacity: 0; }

@keyframes float {
  0% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(2deg); }
  100% { transform: translateY(0px) rotate(0deg); }
}

.animate-float { animation: float 6s ease-in-out infinite; }
</style>

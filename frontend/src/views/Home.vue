<template>
  <div class="home-wrapper">
  
    <section class="hero-section d-flex align-center justify-center">
      <div class="hero-bg-overlay"></div>
      <v-container>
        <v-row align="center" justify="center">
          <v-col cols="12" md="7" class="text-center text-md-left hero-content">
            <template v-if="loading">
              <v-skeleton-loader type="heading" width="80%" class="mb-6 bg-transparent"></v-skeleton-loader>
              <v-skeleton-loader type="paragraph" width="90%" class="mb-8 bg-transparent"></v-skeleton-loader>
              <div class="d-flex gap-4">
                <v-skeleton-loader type="button" width="150" class="bg-transparent"></v-skeleton-loader>
                <v-skeleton-loader type="button" width="150" class="bg-transparent"></v-skeleton-loader>
              </div>
            </template>
            <template v-else>
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
                <v-btn v-if="!authStore.isAuthenticated" variant="tonal" size="x-large" class="rounded-xl px-8 glass-btn mb-3 mb-md-0 mr-md-4" :to="{ name: 'Register' }">
                  {{ $t('landing.btn_start') }}
                </v-btn>
                <v-btn v-else variant="tonal" size="x-large" class="rounded-xl px-8 glass-btn mb-3 mb-md-0" :to="{ name: 'Dashboard' }">
                  {{ $t('landing.btn_my_dashboard') }}
                </v-btn>
              </div>
            </template>
          </v-col>
          <v-col cols="12" md="5" class="d-none d-md-flex justify-center position-relative">
            <template v-if="loading">
              <v-skeleton-loader type="image" height="300" width="300" class="rounded-circle bg-transparent"></v-skeleton-loader>
            </template>
            <div v-else class="hero-visual animate-float">
              <v-icon icon="mdi-chart-areaspline" color="primary" size="300" class="opacity-20"></v-icon>
              <v-icon icon="mdi-shield-check" color="success" size="120" class="absolute-icon bottom-left"></v-icon>
              <v-icon icon="mdi-rocket-launch" color="warning" size="80" class="absolute-icon top-right"></v-icon>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

   
    <v-container class="py-16">
      <v-row class="mt-12 pt-4">
        <v-col cols="12" class="text-center mb-12">
          <h2 class="text-h3 font-weight-bold gradient-text">{{ $t('home.features.title') }}</h2>
          <p class="text-h6 text-medium-emphasis mt-4">{{ $t('home.features.subtitle') }}</p>
        </v-col>
      </v-row>

      <v-row justify="center" class="mt-8">
        <v-col cols="12" lg="10">
          <template v-if="loading">
            <v-skeleton-loader type="image" height="500" class="rounded-xl bg-transparent"></v-skeleton-loader>
          </template>
          <v-card v-else class="rounded-xl overflow-hidden elevation-12 glass-carousel" border>
            <v-carousel
              cycle
              height="500"
              hide-delimiter-background
              show-arrows="hover"
              interval="5000"
            >
              <v-carousel-item
                v-for="(slide, i) in slides"
                :key="i"
                :src="slide.image"
                contain
                class="carousel-slide-item"
              >
                <div class="carousel-overlay d-flex align-end pa-8">
                  <div class="glass-caption rounded-lg pa-4">
                    <h3 class="text-h4 font-weight-bold mb-2">{{ slide.title }}</h3>
                    <p class="text-subtitle-1 opacity-90">{{ slide.description }}</p>
                  </div>
                </div>
              </v-carousel-item>
            </v-carousel>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mt-16">
        <v-col v-for="(feature, i) in features" :key="i" cols="12" md="4">
          <v-card class="feature-card glass-card h-100 pa-6 rounded-xl border-card" elevation="0">
            <template v-if="loading">
              <v-skeleton-loader type="avatar" class="mb-4 bg-transparent"></v-skeleton-loader>
              <v-skeleton-loader type="heading" class="mb-2 bg-transparent"></v-skeleton-loader>
              <v-skeleton-loader type="text" class="bg-transparent"></v-skeleton-loader>
            </template>
            <template v-else>
              <div class="icon-circle mb-6" :class="feature.colorClass">
                <v-icon :icon="feature.icon" size="32" color="white"></v-icon>
              </div>
              <h3 class="text-h6 font-weight-bold mb-3">{{ feature.title }}</h3>
              <p class="text-body-2 text-medium-emphasis">{{ feature.desc }}</p>
              <div class="card-glow" :style="{ backgroundColor: feature.glowColor }"></div>
            </template>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'
import slide1 from '@/assets/slide1.png'
import slide2 from '@/assets/slide2.png'
import slide3 from '@/assets/slide3.png'
import slide4 from '@/assets/slide4.png'

const authStore = useAuthStore()
const { t } = useI18n()
const loading = ref(true)

onMounted(() => {
  // Simulate initial content loading for smoother experience
  setTimeout(() => {
    loading.value = false
  }, 1200)
})

const slides = computed(() => [
  {
    image: slide1,
    title: t('landing.carousel.dashboard_title') || 'Dashboard Inteligente',
    description: t('landing.carousel.dashboard_desc') || t('landing.power_subtitle')
  },
  {
    image: slide2,
    title: t('landing.carousel.reports_title') || 'Relatórios Detalhados',
    description: t('landing.carousel.reports_desc') || t('landing.features.analysis_text')
  },
  
  {
    image: slide3,
    title: t('landing.carousel.goals_title') || 'Metas e Objetivos',
    description: t('landing.carousel.goals_desc') || t('landing.features.goals_text')
  },
  {
    image: slide4,
    title: t('landing.carousel.transactions_title') || 'Lançamentos financeiros',
    description: t('landing.carousel.transactions_text') || t('landing.features.transactions_text')
  }
])

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

.glass-carousel {
  background: rgba(var(--v-theme-surface), 0.8) !important;
  border: 1px solid rgba(var(--v-border-color), 0.1) !important;
}

.carousel-slide-item {
  background: #121212;
}

.carousel-overlay {
  height: 100%;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
}

.glass-caption {
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  max-width: 600px;
  color: white;
}

.v-carousel :deep(.v-window__controls) {
  padding: 0 20px;
}

.v-carousel :deep(.v-btn--icon.v-btn--density-default) {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(4px);
}

.home-wrapper {
  background: radial-gradient(circle at top right, rgba(var(--v-theme-primary), 0.08), transparent 40%),
              radial-gradient(circle at bottom left, rgba(17, 153, 142, 0.08), transparent 40%);
  min-height: 100vh;
}

.hero-section {
  padding-top: 100px;
  padding-bottom: 60px;
  position: relative;
}

.gradient-text {
  background: linear-gradient(90deg, rgb(var(--v-theme-primary)), #11998E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-content {
  z-index: 2;
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.1) !important;
  transition: all 0.4s ease;
}

.feature-card {
  transition: all 0.3s ease;
  border: 1px solid rgba(var(--v-border-color), 0.05);
  background: rgba(var(--v-theme-surface), 0.7) !important;
  backdrop-filter: blur(10px);
}

.feature-card:hover {
  transform: translateY(-10px);
  border-color: rgba(var(--v-theme-primary), 0.3);
  box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
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

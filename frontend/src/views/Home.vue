<template>
  <div class="home-wrapper">
  
    <section class="hero-section d-flex align-center">
      <div class="hero-bg-overlay"></div>
      <v-container fluid class="px-md-16">
        <v-row align="center" no-gutters justify="center">
          <v-col cols="12" md="10" lg="6" class="text-center text-lg-left hero-content pr-lg-12 mb-10 mb-lg-0">
            <template v-if="loading">
              <v-skeleton-loader type="heading" width="80%" class="mb-6 bg-transparent mx-auto mx-lg-0"></v-skeleton-loader>
              <v-skeleton-loader type="paragraph" width="90%" class="mb-8 bg-transparent mx-auto mx-lg-0"></v-skeleton-loader>
              <div class="d-flex gap-4 justify-center justify-lg-start">
                <v-skeleton-loader type="button" width="150" class="bg-transparent"></v-skeleton-loader>
                <v-skeleton-loader type="button" width="150" class="bg-transparent"></v-skeleton-loader>
              </div>
            </template>
            <template v-else>
              <h1 class="text-h4 text-sm-h3 text-md-h2 font-weight-black mb-6 animate-fade-up" style="line-height: 1.1;">            
                <span class="mr-3">{{ $t('landing.hero_title_alt') }}</span>
                <span class="gradient-text">{{ $t('landing.destiny') }}</span>
              </h1>
              <p class="text-caption text-sm-body-1 text-md-h6 text-medium-emphasis mb-6 animate-fade-up-delay max-w-500 mx-auto mx-lg-0">
                {{ $t('landing.hero_subtitle_alt') }}
              </p>
              <div class="d-flex flex-wrap justify-center justify-lg-start animate-fade-up-delay-2 gap-btns">
                <v-btn color="primary" size="x-large" class="rounded-xl px-10 hero-btn font-weight-bold" :to="{ name: 'Plans' }" elevation="20">
                  {{ $t('landing.btn_start') }}
                </v-btn>
                <v-btn v-if="authStore.isAuthenticated && authStore.hasFeature('Painel Financeiro')" variant="tonal" size="x-large" class="rounded-xl px-10 glass-btn ml-md-6 mt-4 mt-sm-0" :to="{ name: 'Dashboard' }">
                  {{ $t('landing.btn_my_dashboard') }}
                </v-btn>
              </div>
            </template>
          </v-col>

          <v-col cols="12" lg="6" class="mt-12 mt-lg-0 position-relative carousel-col overflow-visible">
             <div class="modern-carousel-wrapper animate-fade-in">
                <!-- Navigation Buttons (Desktop) -->
                <div class="carousel-nav-container">
                   <v-btn 
                    icon="mdi-arrow-left" 
                    variant="elevated" 
                    color="surface" 
                    class="carousel-nav-btn prev" 
                    elevation="8"
                    @click="prevSlide"
                  ></v-btn>
                   <v-btn 
                    icon="mdi-arrow-right" 
                    variant="elevated" 
                    color="surface" 
                    class="carousel-nav-btn next" 
                    elevation="8"
                    @click="nextSlide"
                  ></v-btn>
                </div>
                
                <div class="carousel-track" :style="{ left: '50%', transform: `translateX(calc(-${slideWidth/2}px - ${currentSlide * slideWidth}px))` }">
                    <div 
                       v-for="(slide, i) in slides"
                      :key="i"
                      class="loose-slide-v2"
                      :class="{ 'active': currentSlide === i }"
                      @click="currentSlide = i"
                    >
                       <v-card class="slide-card rounded-xl overflow-hidden shadow-2xl" elevation="0">
                        <v-img :src="slide.image" class="slide-img">
                          <template v-slot:placeholder>
                            <v-skeleton-loader type="image"></v-skeleton-loader>
                          </template>
                        </v-img>
                        <div class="slide-info-overlay-v2" v-if="currentSlide === i">
                           <div class="d-flex align-center justify-space-between w-100">
                             <div>
                               <p class="text-overline mb-0 op-70">{{ $t('landing.carousel.premium_resource') }}</p>
                               <h4 class="text-h6 font-weight-black">{{ slide.title }}</h4>
                               <p class="text-caption mt-1 mb-0 op-80 d-none d-sm-block">{{ slide.description }}</p>
                             </div>
                             <v-icon icon="mdi-star" color="warning"></v-icon>
                           </div>
                        </div>
                      </v-card>
                    </div>
                </div>

                <!-- Navigation Indicators -->
                <div class="d-flex justify-center mt-8">
                  <div class="d-flex align-center px-4">
                     <div v-for="(_, i) in slides" :key="i" class="dot mx-1" :class="{ 'active': currentSlide === i }" @click="currentSlide = i"></div>
                  </div>
                </div>
             </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <v-container class="py-16">
      <v-row class="mt-8">
        <v-col cols="12" class="text-center mb-12">
          <h2 class="text-h3 font-weight-bold gradient-text">{{ $t('home.features.title') }}</h2>
          <p class="text-h6 text-medium-emphasis mt-4">{{ $t('home.features.subtitle') }}</p>
        </v-col>
      </v-row>

      <v-row class="mt-4">
        <v-col v-for="(feature, i) in features" :key="i" cols="12" md="4">
          <v-card class="feature-card glass-card h-100 pa-8 rounded-xl border-card" elevation="0">
            <template v-if="loading">
              <v-skeleton-loader type="avatar" class="mb-4 bg-transparent"></v-skeleton-loader>
              <v-skeleton-loader type="heading" class="mb-2 bg-transparent"></v-skeleton-loader>
              <v-skeleton-loader type="text" class="bg-transparent"></v-skeleton-loader>
            </template>
            <template v-else>
              <div class="icon-circle mb-8" :class="feature.colorClass">
                <v-icon :icon="feature.icon" size="36" color="white"></v-icon>
              </div>
              <h3 class="text-h5 font-weight-bold mb-4">{{ feature.title }}</h3>
              <p class="text-body-1 text-medium-emphasis line-height-relaxed">{{ feature.desc }}</p>
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
import { useUiStore } from '../stores/ui'
import { useI18n } from 'vue-i18n'
import { useDisplay } from 'vuetify'
import slidesBR1 from '@/assets/Painel-PT-BR.png'
import slidesBR2 from '@/assets/Lancamentos-PT-BR.png'
import slidesBR3 from '@/assets/Metas-PT-BR.png'
import slidesBR4 from '@/assets/Relatorios-PT-BR.png'
import slidesBR5 from '@/assets/Agenda-PT-BR.png'

import slidesEN1 from '@/assets/Painel-EN-US.png'
import slidesEN2 from '@/assets/Lancamentos-EN-US.png'
import slidesEN3 from '@/assets/Goals-EN-US.png'
import slidesEN4 from '@/assets/Reports-EN-US.png'
import slidesEN5 from '@/assets/Schedule-EN-US.png'
import { useLocale } from 'vuetify/lib/composables/locale.mjs'


const authStore = useAuthStore()
const uiAuthStore = useUiStore()
const { t } = useI18n()
const display = useDisplay()
const loading = ref(true)
const currentSlide = ref(0)

const isMobile = computed(() => display.smAndDown.value)
const slideWidth = computed(() => {
  if (display.smAndDown.value) return 280
  if (display.md.value) return 400
  return 500
})

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 600)
})

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
}

const slides = computed(() => {
    const isEn = uiAuthStore.locale === 'en'
    return [
      {
        image: isEn ? slidesEN1 : slidesBR1,
        title: t('landing.carousel.dashboard_title') || 'Dashboard Inteligente',
        description: t('landing.carousel.dashboard_desc') || t('landing.power_subtitle')
      },
      {
        image: isEn ? slidesEN2 : slidesBR2,
        title: t('landing.carousel.transactions_title') || 'Lançamentos financeiros',
        description: t('landing.carousel.transactions_text') || t('landing.features.transactions_text')
      },
      {
        image: isEn ? slidesEN3 : slidesBR3,
        title: t('landing.carousel.goals_title') || 'Metas e Objetivos',
        description: t('landing.carousel.goals_desc') || t('landing.features.goals_text')
      },
      {
        image: isEn ? slidesEN4 : slidesBR4,
        title: t('landing.carousel.reports_title') || 'Relatórios Detalhados',
        description: t('landing.carousel.reports_desc') || t('landing.features.analysis_text')
      },
      {
        image: isEn ? slidesEN5 : slidesBR5,
        title: t('landing.carousel.schedule_title') || 'Agenda de Lançamentos',
        description: t('landing.carousel.schedule_desc') || t('landing.features.schedule_desc')
      }
    ]
})

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
.home-wrapper {
  background: radial-gradient(circle at top right, rgba(var(--v-theme-primary), 0.12), transparent 40%),
              radial-gradient(circle at bottom left, rgba(17, 153, 142, 0.12), transparent 40%);
  min-height: 100vh;
  overflow-x: hidden;
}

.hero-section {
  padding-top: 140px;
  padding-bottom: 100px;
  position: relative;
  min-height: 80vh;
}

.gradient-text {
  background: linear-gradient(90deg, rgb(var(--v-theme-primary)), #11998E);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
}

.hero-content {
  z-index: 2;
}

.max-w-500 {
  max-width: 500px;
}

.modern-carousel-wrapper {
  position: relative;
  height: 520px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  overflow: visible;
  margin-top: -20px;
}

.carousel-nav-container {
  position: absolute;
  top: 50%;
  left: 10px;
  right: 10px;
  transform: translateY(-50%);
  display: flex;
  justify-content: space-between;
  pointer-events: none;
  z-index: 100;
}

.carousel-nav-btn {
  pointer-events: auto;
  background: rgba(var(--v-theme-surface), 0.9) !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.1) !important;
}
.carousel-track {
  display: flex;
  transition: transform 0.8s cubic-bezier(0.2, 0, 0, 1);
  padding: 20px 0;
  width: max-content;
  position: absolute;
  top: 50%;
  margin-top: -150px; /* Center vertically if needed, or adjust based on slide height */
}

.loose-slide-v2 {
  flex: 0 0 500px;
  padding: 0 10px;
  transition: all 0.7s cubic-bezier(0.2, 1, 0.2, 1);
  opacity: 0.5;
  transform: scale(0.85);
  cursor: pointer;
}

.loose-slide-v2.active {
  opacity: 1;
  transform: scale(1.1);
  z-index: 20;
}

.slide-card {
  width: 100%;
  aspect-ratio: 16/10;
  background: rgba(var(--v-theme-surface), 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 20px 50px rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 10px;
}

.slide-img {
  width: 100%;
  height: 100%;
  object-fit: contain !important;
  transition: transform 0.6s ease;
  filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
}

.loose-slide-v2.active .slide-img {
  transform: scale(1.02);
}

.slide-info-overlay-v2 {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
  padding: 30px 24px 20px;
  color: white;
  animation: slideInUp 0.6s cubic-bezier(0.2, 0, 0, 1) forwards;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.2);
  transition: all 0.3s ease;
}

.dot.active {
  width: 24px;
  border-radius: 4px;
  background: rgb(var(--v-theme-primary));
}

@keyframes slideInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1264px) {
  .loose-slide-v2 {
    flex: 0 0 400px;
  }
}

@media (max-width: 960px) {
  .hero-section {
    padding-top: 60px;
    padding-bottom: 30px;
  }
  .modern-carousel-wrapper {
    height: 380px;
    padding-left: 0 !important;
  }
  .carousel-track {
    margin-top: -100px;
  }
  .loose-slide-v2 {
    flex: 0 0 280px;
  }
  .carousel-nav-container {
    bottom: -15px;
    top: auto;
    left: 50%;
    right: auto;
    transform: translateX(-50%);
    width: 140px;
    justify-content: space-between;
  }
  .carousel-nav-btn {
    width: 44px !important;
    height: 44px !important;
  }
}

.glass-card {
  background: rgba(var(--v-theme-surface), 0.9) !important;
  border: 1px solid rgba(var(--v-border-color), 0.1) !important;
}

.feature-card {
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  border: 1px solid rgba(var(--v-border-color), 0.05);
  background: rgba(var(--v-theme-surface), 0.7) !important;
  backdrop-filter: blur(20px);
}

.feature-card:hover {
  transform: translateY(-15px);
  border-color: rgba(var(--v-theme-primary), 0.4);
  background: rgba(var(--v-theme-surface), 0.9) !important;
}

.feature-card:hover .icon-circle {
  transform: scale(1.1) rotate(5deg);
}

.icon-circle {
  width: 72px;
  height: 72px;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 24px rgba(0,0,0,0.1);
  transition: all 0.4s ease;
}

.receita-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.despesa-gradient { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
.saldo-gradient { background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%); }

.glass-btn {
  background: rgba(var(--v-theme-primary), 0.05);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(var(--v-theme-primary), 0.2);
  color: rgb(var(--v-theme-primary));
  display: flex;
  margin-left: 8px;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-up { animation: fadeInUp 0.8s ease-out forwards; }
.animate-fade-up-delay { animation: fadeInUp 0.8s 0.2s ease-out forwards; opacity: 0; }
.animate-fade-up-delay-2 { animation: fadeInUp 0.8s 0.4s ease-out forwards; opacity: 0; }

.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.brand-title {
  font-family: 'Outfit', sans-serif !important;
  font-weight: 950;
  letter-spacing: -1px;
}

.line-height-relaxed {
  line-height: 1.7;
}

</style>

<template>
  <v-container class="mt-12 not-found-container">
    <div class="d-flex align-center justify-center gap-4" style="flex-wrap: wrap;">
      <h1 class="text-h1 font-weight-bold not-found-title">{{ $t('NotFound.title') }}</h1>
      
      <video
        ref="bixo"
        width="500"
        autoplay
        muted
        playsinline
        class="rounded-xl video-bixo"
        style="max-width: 100%; height: auto;"
        @ended="handleEnded"
      >
        <source src="@/assets/Bixo.webm" type="video/webm">
        Seu navegador não suporta o elemento de vídeo.
      </video>
      
      <h1 class="text-h1 font-weight-bold not-found-title">{{ $t('NotFound.title') }}</h1>
    </div>
    
    <div class="text-center mt-12">
      <v-btn 
        size="large" 
        color="primary" 
        @click="goHome"
        rounded="xl"
        class="px-8 py-3 font-weight-bold"
        elevation="4"
      >
        <v-icon icon="mdi-home" class="mr-2"></v-icon>
        {{ $t('NotFound.btn_home') }}
      </v-btn>
    </div>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'

const bixo = ref(null)
let replayTimer = null

const handleEnded = () => {
 
  replayTimer = setTimeout(() => {
    if (bixo.value) {
      try {
        bixo.value.currentTime = 0
        bixo.value.play()
      } catch (e) {
        
      }
    }
  }, 3000)
}

onMounted(() => {
  if (bixo.value) {
  
    try { bixo.value.play() } catch (e) {}
  }
})

onBeforeUnmount(() => {
  if (replayTimer) clearTimeout(replayTimer)
})

const router = useRouter()
const goHome = () => router.push('/')
</script>

<style scoped>
.not-found-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.not-found-title {
  color: rgb(var(--v-theme-primary)) !important;
  text-shadow: 0 4px 8px rgba(var(--v-theme-primary), 0.15);
  letter-spacing: 2px;
  animation: fadeInScale 0.8s ease-out;
  font-size: 7rem !important;
  min-width: 180px;
  line-height: 1 !important;
padding-left: 32px;
}

.video-bixo {
  animation: fadeInScale 0.8s ease-out 0.2s backwards;
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@media (max-width: 1024px) {
  .not-found-title {
    text-align: center;
  }
}
</style>
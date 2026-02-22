<template>
  <div class="email-verification">
    <div class="text-center mb-10">
      <v-icon size="64" color="primary" class="mb-4">mdi-email-check-outline</v-icon>
      <h2 class="text-h4 font-weight-bold mb-2">{{ $t('auth.verify_title') }}</h2>
      <p class="text-body-1 text-medium-emphasis">
        {{ $t('auth.verify_subtitle') }} <br>
        <strong class="text-primary">{{ email }}</strong>
      </p>
    </div>

    <v-form @submit.prevent="handleVerify">
      <div class="otp-container mb-8 d-flex justify-center">
        <v-otp-input
          v-model="code"
          length="6"
          type="number"
          variant="outlined"
          :disabled="loading"
          @finish="handleVerify"
          height="80"
          class="justify-center"
        ></v-otp-input>
      </div>

      <v-alert v-if="error" type="error" variant="tonal" class="mb-6 rounded-lg" density="compact" closable>
        {{ error }}
      </v-alert>

      <v-btn
        color="primary"
        block
        size="x-large"
        class="rounded-xl font-weight-bold py-4 text-none elevation-8"
        type="submit"
        :loading="loading"
        :disabled="code.length < 6"
        append-icon="mdi-check-decagram"
      >
        {{ $t('auth.btn_verify') }}
      </v-btn>

      <div class="text-center mt-8">
        <p class="text-body-2 text-medium-emphasis mb-2">{{ $t('auth.no_email') }}</p>
        <v-btn
          variant="text"
          color="primary"
          class="text-none font-weight-bold"
          :disabled="resending || timer > 0"
          @click="handleResend"
        >
          {{ timer > 0 ? $t('auth.resend_timer', { seconds: timer }) : $t('auth.btn_resend') }}
        </v-btn>
      </div>

      <v-btn
        variant="text"
        block
        class="mt-4 text-none text-medium-emphasis"
        @click="$emit('back')"
      >
        {{ $t('auth.btn_back_login') }}
      </v-btn>
    </v-form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  email: {
    type: String,
    required: true
  },
  loading: Boolean,
  error: String
})

const emit = defineEmits(['verify', 'resend', 'back'])

const code = ref('')
const resending = ref(false)
const timer = ref(60)
let intervalId = null

const startTimer = () => {
  timer.value = 60
  intervalId = setInterval(() => {
    if (timer.value > 0) {
      timer.value--
    } else {
      clearInterval(intervalId)
    }
  }, 1000)
}

onMounted(() => {
  startTimer()
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

const handleVerify = () => {
  if (code.value.length === 6) {
    emit('verify', code.value)
  }
}

const handleResend = async () => {
  resending.value = true
  try {
    emit('resend')
    startTimer()
  } finally {
    resending.value = false
  }
}
</script>

<style scoped>
.otp-container :deep(.v-otp-input__content) {
  gap: 8px;
  padding: 0;
}

.otp-container :deep(.v-field) {
  border-radius: 12px;
  background-color: rgba(var(--v-theme-surface), 0.5);
}

@media (max-width: 600px) {
  .otp-container :deep(.v-otp-input__content) {
    gap: 4px;
  }
  .otp-container :deep(.v-field) {
    --v-otp-input-width: 40px;
  }
}
</style>

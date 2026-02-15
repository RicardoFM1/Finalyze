<template>
  <div class="finn-chat-container">
    <!-- Chat Button -->
    <v-btn
      v-if="!isOpen"
      color="primary"
      size="x-large"
      icon="mdi-robot"
      class="finn-fab elevation-8"
      @click="toggleChat"
    >
      <v-avatar size="45">
        <v-img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png"></v-img>
      </v-avatar>
    </v-btn>

    <!-- Chat Window -->
    <v-card
      v-if="isOpen"
      class="finn-window rounded-xl elevation-12 overflow-hidden"
      width="350"
      max-height="500"
    >
      <!-- Header -->
      <v-toolbar color="primary" density="comfortable">
        <v-avatar size="32" class="ml-4 mr-2">
            <v-img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png"></v-img>
        </v-avatar>
        <v-toolbar-title class="text-subtitle-1 font-weight-bold">Finn - Assistente IA</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" size="small" @click="isOpen = false"></v-btn>
      </v-toolbar>

      <!-- Messages Area -->
      <v-card-text ref="chatBox" class="chat-messages pa-4 bg-grey-lighten-4">
        <div v-for="(msg, i) in messages" :key="i" :class="['message-wrapper', msg.role]">
          <v-card
            :color="msg.role === 'user' ? 'primary' : 'white'"
            :class="['pa-3 mb-2 rounded-lg elevation-1', msg.role === 'user' ? 'text-white' : 'text-grey-darken-3']"
            max-width="85%"
          >
            <div class="text-body-2 white-space-pre">{{ msg.text }}</div>
          </v-card>
        </div>
        <div v-if="loading" class="typing-indicator mb-2">
          <v-progress-circular indeterminate size="20" width="2" color="primary"></v-progress-circular>
          <span class="text-caption ml-2 text-medium-emphasis">Finn está pensando...</span>
        </div>
      </v-card-text>

      <!-- Input Area -->
      <v-divider></v-divider>
      <v-card-actions class="pa-3 bg-white">
        <v-text-field
          v-model="input"
          placeholder="Pergunte sobre seus gastos..."
          variant="solo-filled"
          density="comfortable"
          hide-details
          rounded="pill"
          append-inner-icon="mdi-send"
          @click:append-inner="sendMessage"
          @keyup.enter="sendMessage"
          :disabled="loading"
        ></v-text-field>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const isOpen = ref(false)
const input = ref('')
const loading = ref(false)
const chatBox = ref(null)

const messages = ref([
  { role: 'bot', text: 'Olá! Eu sou o Finn. Como posso ajudar com suas finanças hoje?' }
])

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    scrollToBottom()
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (chatBox.value?.$el) {
    chatBox.value.$el.scrollTop = chatBox.value.$el.scrollHeight
  } else if (chatBox.value) {
    chatBox.value.scrollTop = chatBox.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!input.value.trim() || loading.value) return

  const userMsg = input.value
  messages.value.push({ role: 'user', text: userMsg })
  input.value = ''
  loading.value = true
  scrollToBottom()

  try {
    const response = await authStore.apiFetch('/chat/pergunta', {
      method: 'POST',
      body: JSON.stringify({ mensagem: userMsg })
    })
    
    const data = await response.json()
    
    if (data.resposta) {
      messages.value.push({ role: 'bot', text: data.resposta })
    } else {
      messages.value.push({ role: 'bot', text: data.error || 'Tive um probleminha. Pode repetir?' })
    }
  } catch (e) {
    messages.value.push({ role: 'bot', text: 'Desculpe, perdi a conexão com meu cérebro IA. tente em instantes!' })
  } finally {
    loading.value = false
    scrollToBottom()
  }
}
</script>

<style scoped>
.finn-chat-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 1000;
}

.finn-fab {
  border: 4px solid white !important;
}

.finn-window {
  position: absolute;
  bottom: 80px;
  right: 0;
  display: flex;
  flex-direction: column;
  box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important;
}

.chat-messages {
  height: 350px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.message-wrapper {
  display: flex;
  width: 100%;
}

.message-wrapper.user {
  justify-content: flex-end;
}

.message-wrapper.bot {
  justify-content: flex-start;
}

.white-space-pre {
  white-space: pre-wrap;
}

.typing-indicator {
  display: flex;
  align-items: center;
  opacity: 0.7;
}

/* Custom scrollbar */
.chat-messages::-webkit-scrollbar {
  width: 4px;
}
.chat-messages::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}
</style>

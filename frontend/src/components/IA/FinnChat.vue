<template>
  <div v-if="authStore.hasFeature('finn-ai') && authStore.isAuthenticated" class="finn-chat-wrapper">
    <!-- Chat Button (FAB) -->
    <v-btn
      v-if="!isOpen"
      color="primary"
      size="x-large"
      icon
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
      :width="isMobile ? '100%' : '380'"
      :style="isMobile ? 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; height: 100vh; max-height: 100vh; border-radius: 0 !important;' : ''"
      max-height="650"
    >
    <!-- Header -->
      <v-card-title class="d-flex align-center py-2 px-4">
        <v-avatar size="32" class="mr-3">
          <v-img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Finn"></v-img>
        </v-avatar>
        <span class="text-subtitle-1 font-weight-bold">Finn</span>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" variant="text" size="small" @click="isOpen = false"></v-btn>
      </v-card-title>

    <!-- Messages Area -->
    <v-card-text ref="chatBox" class="chat-messages pa-4 bg-grey-lighten-4 flex-grow-1">
      <div v-for="(msg, i) in messages" :key="i" :class="['message-wrapper', msg.role]">
        <v-card
          :color="msg.role === 'user' ? 'primary' : 'white'"
          :class="['pa-3 mb-2 rounded-lg elevation-1 message-card', msg.role === 'user' ? 'text-white' : 'text-grey-darken-3']"
          max-width="85%"
        >
          <div v-if="editingId === msg.id" class="edit-area">
              <v-textarea
                  v-model="editText"
                  rows="2"
                  density="compact"
                  hide-details
                  variant="plain"
                  auto-grow
                  class="mb-2 text-body-2"
              ></v-textarea>
              <div class="d-flex justify-end">
                  <v-btn size="x-small" variant="text" color="white" @click="editingId = null">Cancelar</v-btn>
                  <v-btn size="x-small" color="secondary" class="ml-1" @click="saveEdit">Salvar</v-btn>
              </div>
          </div>
          <div v-else class="text-body-2 white-space-pre">{{ msg.text }}</div>
          
          <!-- Actions for user messages -->
          <div v-if="msg.role === 'user' && editingId !== msg.id" class="message-actions">
              <v-btn icon="mdi-pencil" size="x-small" variant="text" density="comfortable" color="white" @click="startEdit(msg)"></v-btn>
              <v-btn icon="mdi-delete" size="x-small" variant="text" density="comfortable" color="white" @click="deleteMessage(msg.id)"></v-btn>
          </div>
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
import { ref, nextTick, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useDisplay } from 'vuetify'

const authStore = useAuthStore()
const { mobile: isMobile } = useDisplay()

const isOpen = ref(false)
const input = ref('')
const loading = ref(false)
const chatBox = ref(null)

const messages = ref([])

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await fetchHistory()
  }
})

const fetchHistory = async () => {
  try {
    const response = await authStore.apiFetch('/chat')
    if (response.ok) {
      const data = await response.json()
      messages.value = data.map(m => ({
        id: m.id,
        role: m.role,
        text: m.texto
      }))
      if (messages.value.length === 0) {
        messages.value.push({ role: 'bot', text: 'Olá! Eu sou o Finn. Como posso ajudar com suas finanças hoje?' })
      }
      scrollToBottom()
    }
  } catch (e) {
    console.error('Erro ao buscar histórico:', e)
  }
}

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
      await fetchHistory() // Refresh to get IDs
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

const deleteMessage = async (id) => {
  if (!id) return
  if (!confirm('Deseja remover esta mensagem?')) return

  const originalMessages = [...messages.value]
  messages.value = messages.value.filter(m => m.id !== id)

  try {
    const response = await authStore.apiFetch(`/chat/${id}`, {
      method: 'DELETE'
    })
    if (!response.ok) {
      messages.value = originalMessages
      const data = await response.json()
      console.error('Erro ao deletar:', data.error || response.statusText)
    }
  } catch (e) {
    messages.value = originalMessages
    console.error('Erro ao deletar:', e)
  }
}

const editingId = ref(null)
const editText = ref('')

const startEdit = (msg) => {
  editingId.value = msg.id
  editText.value = msg.text
}

const saveEdit = async () => {
  if (!editText.value.trim()) return
  
  try {
    const response = await authStore.apiFetch(`/chat/${editingId.value}`, {
      method: 'PATCH',
      body: JSON.stringify({ texto: editText.value })
    })
    if (response.ok) {
      const msg = messages.value.find(m => m.id === editingId.value)
      if (msg) msg.text = editText.value
      editingId.value = null
    }
  } catch (e) {
    console.error('Erro ao editar:', e)
  }
}
</script>
<style>
.finn-chat-wrapper {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  align-items: center;
  transition: right 0.3s ease;
}

.finn-chat-wrapper.is-hidden {
  right: -8px;
}

.finn-toggle-btn {
  transition: all 0.3s ease;
  z-index: 10000;
  margin-right: -4px;
}

.draggable-header {
  cursor: grab;
}

.draggable-header:active {
  cursor: grabbing;
}

.finn-chat-container {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.finn-chat-container.is-hidden {
  opacity: 0;
  transform: translateX(100px);
  pointer-events: none;
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
  z-index: 10001;
}

.chat-messages {
  flex-grow: 1;
  height: 400px;
  max-height: 100%;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

@media (max-width: 600px) {
  .finn-chat-wrapper {
    bottom: 0;
    right: 0;
  }
  
  /* Ensure chat container is always visible on mobile */
  .finn-chat-container {
    opacity: 1 !important;
    transform: none !important;
    pointer-events: all !important;
  }
  
  .finn-window {
    width: 100vw !important;
    height: 100vh !important;
    bottom: 0 !important;
  }
  .chat-messages {
    height: calc(100vh - 140px) !important;
  }
}

@media (min-width: 601px) and (max-width: 960px) {
  .finn-window {
    width: 400px !important;
    right: 0;
  }
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

.message-card {
  position: relative;
}

.message-actions {
  position: absolute;
  top: -10px;
  right: 0;
  display: none;
  background: rgba(var(--v-theme-primary), 0.9);
  border-radius: 20px;
  padding: 2px;
}

.message-card:hover .message-actions {
  display: flex;
}

.edit-area {
    min-width: 200px;
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

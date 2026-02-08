<template>
  <ModalBase :title="form.id ? (form.tipo === 'financeira' ? 'Editar Meta' : 'Editar Anotação') : (form.tipo === 'financeira' ? 'Nova Meta' : 'Nova Anotação')" v-model="internalValue" maxWidth="550px">
    <v-form ref="metaForm" @submit.prevent="saveMeta">

      <v-text-field
        v-model="form.titulo"
        label="Título"
        variant="outlined"
        placeholder="Ex: Reserva de Emergência"
        class="mb-2"
        rounded="lg"
        required
      ></v-text-field>

      <v-textarea
        v-model="form.descricao"
        :label="form.tipo === 'financeira' ? 'Descrição / Subtítulo' : 'Conteúdo da Anotação'"
        variant="outlined"
        :placeholder="form.tipo === 'financeira' ? 'Pequeno detalhe ou frase de impacto' : 'Escreva aqui tudo o que precisar...'"
        class="mb-2"
        rounded="lg"
        :rows="form.tipo === 'pessoal' ? 6 : 2"
        auto-grow
      ></v-textarea>

      <template v-if="form.tipo === 'financeira'">
        <v-row dense>
          <v-col cols="6">
            <v-text-field
              v-model="form.valor_atual"
              label="Valor Atual"
              prefix="R$"
              type="number"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.valor_objetivo"
              label="Objetivo"
              prefix="R$"
              type="number"
              variant="outlined"
              rounded="lg"
              required
            ></v-text-field>
          </v-col>
        </v-row>
      </template>

      <template v-else>
        <v-row dense>
          <v-col cols="6">
            <v-menu v-model="emojiMenu" :close-on-content-click="false" location="bottom">
              <template v-slot:activator="{ props }">
                <v-text-field
                  v-model="form.icone"
                  label="Ícone/Emoji"
                  placeholder="Selecione um emoji"
                  variant="outlined"
                  rounded="lg"
                  prepend-inner-icon="mdi-emoticon-outline"
                  v-bind="props"
                  readonly
                  class="cursor-pointer"
                  :rules="[v => !v || isEmoji(v) || 'Apenas um emoji é permitido']"
                ></v-text-field>
              </template>
              <v-card class="pa-0 rounded-xl emoji-picker-card" width="320" height="400" elevation="12">
                <v-card-title class="pa-4 pb-2">
                  <v-text-field
                    v-model="emojiSearch"
                    placeholder="Pesquisar emoji..."
                    variant="solo-filled"
                    density="compact"
                    hide-details
                    prepend-inner-icon="mdi-magnify"
                    flat
                    class="rounded-lg"
                  ></v-text-field>
                </v-card-title>
                
                <v-tabs v-model="activeCategory" density="compact" color="primary" grow>
                  <v-tab v-for="cat in emojiCategories" :key="cat.name" :value="cat.name">
                    <v-icon :icon="cat.icon" size="18"></v-icon>
                  </v-tab>
                </v-tabs>

                <v-window v-model="activeCategory" class="emoji-scroll-area">
                  <v-window-item v-for="cat in emojiCategories" :key="cat.name" :value="cat.name" class="pa-4">
                    <div class="d-flex flex-wrap gap-2">
                      <v-btn
                        v-for="emoji in filterEmojis(cat.emojis)"
                        :key="emoji"
                        icon
                        variant="text"
                        size="small"
                        class="text-h6"
                        @click="selectEmoji(emoji)"
                      >
                        {{ emoji }}
                      </v-btn>
                    </div>
                  </v-window-item>
                </v-window>
              </v-card>
            </v-menu>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.cor"
              label="Cor do Cabeçalho"
              type="color"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
        </v-row>
      </template>

      <v-row dense>
        <v-col :cols="form.tipo === 'financeira' ? 6 : 12">
          <v-text-field
            v-model="form.prazo"
            :label="form.tipo === 'financeira' ? 'Prazo' : 'Data Limite (Opcional)'"
            type="date"
            variant="outlined"
            rounded="lg"
          ></v-text-field>
        </v-col>
        <v-col v-if="form.tipo === 'financeira'" cols="6">
          <v-text-field
            v-model="form.cor"
            label="Cor (Hex)"
            type="color"
            variant="outlined"
            rounded="lg"
          ></v-text-field>
        </v-col>
      </v-row>

      <v-btn 
        type="submit" 
        color="primary" 
        block 
        size="large" 
        rounded="lg" 
        class="mt-4" 
        variant="flat"
        :loading="loading" 
        elevation="2"
      >
        Salvar Meta
      </v-btn>
    </v-form>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'

const props = defineProps({
  modelValue: Boolean,
  meta: Object,
  initialTipo: {
    type: String,
    default: 'financeira'
  }
})

const emit = defineEmits(['update:modelValue', 'saved'])

const authStore = useAuthStore()
const loading = ref(false)
const emojiMenu = ref(false)
const emojiSearch = ref('')
const activeCategory = ref('Smileys')

const emojiCategories = [
  { name: 'Smileys', icon: 'mdi-emoticon-happy-outline', emojis: ['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳'] },
  { name: 'Gestures', icon: 'mdi-hand-okay', emojis: ['👍', '👎', '👊', '✊', '🤛', '🤜', '🤞', '✌️', '🤟', '🤘', '👌', '👈', '👉', '👆', '👇', '✋', '🤚', '🖐️', '🖖', '👋', '🤙', '💪', '🙏'] },
  { name: 'Activity', icon: 'mdi-bike', emojis: ['🎯', '📚', '🏃', '💰', '🏋️', '💻', '🎸', '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🏉', '🎱', '🏓', '🏸', '🏒', '🏑', '🏏'] },
  { name: 'Travel', icon: 'mdi-airplane', emojis: ['🏠', '🚗', '✈️', '🚀', '🛸', '🚁', '🛶', '⛵', '🛥️', '🛳️', '⛴️', '🚢', '🚠', '🚟', '🚃', '🚋', '🚞', '🚝', '🚄', '🚅'] },
  { name: 'Objects', icon: 'mdi-lightbulb-outline', emojis: ['💡', '📅', '📝', '🛒', '🎁', '🎈', '🎏', '🎀', '🎊', '🎉', '🎎', '🏮', '🎐', '🧧', '✉️', '📩', '📨', '📧', '💌', '📥'] }
]

const filterEmojis = (emojis) => {
  if (!emojiSearch.value) return emojis
  return emojis.filter(e => e.includes(emojiSearch.value)) // Basic filter, would be better with names but emojis are just chars
}

const selectEmoji = (emoji) => {
  form.value.icone = emoji
  emojiMenu.value = false
}

const isEmoji = (str) => {
  const emojiRegex = /^(\u00a9|\u00ae|[\u2000-\u3300]|\ud83c[\ud000-\udfff]|\ud83d[\ud000-\udfff]|\ud83e[\ud000-\udfff])$/
  return emojiRegex.test(str)
}

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const form = ref({
  id: null,
  tipo: 'financeira',
  titulo: '',
  descricao: '',
  valor_objetivo: null,
  valor_atual: 0,
  meta_quantidade: null,
  atual_quantidade: 0,
  unidade: '',
  prazo: null,
  cor: '#1867C0',
  icone: ''
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    if (props.meta) {
      form.value = { 
        ...props.meta,
        prazo: props.meta.prazo ? new Date(props.meta.prazo).toISOString().substr(0, 10) : null
      }
    } else {
      form.value = {
        id: null,
        tipo: props.initialTipo,
        titulo: '',
        descricao: '',
        valor_objetivo: null,
        valor_atual: 0,
        meta_quantidade: null,
        atual_quantidade: 0,
        unidade: props.initialTipo === 'financeira' ? 'BRL' : '',
        prazo: null,
        cor: props.initialTipo === 'financeira' ? '#4CAF50' : '#FFF9BF',
        icone: props.initialTipo === 'pessoal' ? '🎯' : ''
      }
    }
  }
}, { immediate: true })

const saveMeta = async () => {
  loading.value = true
  try {
    const isEdit = !!form.value.id
    const isAnotacao = form.value.tipo === 'pessoal' || (!form.value.tipo && props.initialTipo === 'pessoal')
    const endpointBase = isAnotacao ? '/anotacoes' : '/metas'
    const endpoint = isEdit ? `${endpointBase}/${form.value.id}` : endpointBase
    
    const response = await authStore.apiFetch(endpoint, {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(form.value)
    })

    if (response.ok) {
      toast.success(isEdit ? (isAnotacao ? 'Anotação atualizada!' : 'Meta atualizada!') : (isAnotacao ? 'Anotação salva!' : 'Meta criada com sucesso!'))
      internalValue.value = false
      emit('saved')
    } else {
      const data = await response.json()
      toast.error(data.message || 'Erro ao salvar')
    }
  } catch (e) {
    toast.error('Erro de conexão')
  } finally {
    loading.value = false
  }
}
</script>

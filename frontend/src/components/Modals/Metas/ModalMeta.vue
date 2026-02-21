<template>
  <ModalBase :title="form.id ? (form.tipo === 'financeira' ? $t('modals.titles.edit_goal') : 'Editar Compromisso') : (form.tipo === 'financeira' ? $t('modals.titles.new_goal') : 'Novo Lembrete')" v-model="internalValue" maxWidth="550px">
    <v-form ref="metaForm" @submit.prevent="saveMeta">

      <v-text-field
        v-model="form.titulo"
        :label="$t('modals.labels.title')"
        variant="outlined"
        :placeholder="$t('modals.placeholders.goal_example')"
        class="mb-2"
        rounded="lg"
        required
        :disabled="loading"
      ></v-text-field>

      <v-textarea
        v-model="form.descricao"
        :label="form.tipo === 'financeira' ? $t('modals.labels.description') + ' / Subtítulo' : $t('modals.placeholders.desc_note')"
        variant="outlined"
        :placeholder="form.tipo === 'financeira' ? $t('modals.placeholders.desc_financial') : $t('modals.placeholders.desc_note')"
        class="mb-2"
        rounded="lg"
        :rows="form.tipo === 'pessoal' ? 6 : 2"
        auto-grow
        :disabled="loading"
      ></v-textarea>

      <template v-if="form.tipo === 'financeira'">
        <v-row dense>
          <v-col cols="12">
            <CurrencyInput
              v-model="form.valor_objetivo"
              :label="$t('modals.labels.goal_value')"
              :prefix="$t('common.currency')"
              variant="outlined"
              rounded="lg"
              required
              :disabled="loading"
            />
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
                  :label="$t('modals.labels.icon')"
                  :placeholder="$t('modals.placeholders.emoji')"
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
                    :placeholder="$t('modals.placeholders.search_emoji')"
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
              :label="$t('modals.labels.color') + ' do Cabeçalho'"
              type="color"
              variant="outlined"
              rounded="lg"
            ></v-text-field>
          </v-col>
        </v-row>
      </template>

      <v-row dense>
        <v-col :cols="form.tipo === 'publico' ? 12 : 6">
          <DateInput
            v-model="form.prazo"
            :label="form.tipo === 'financeira' ? $t('modals.labels.deadline') : $t('modals.labels.date_agenda')"
            clearable
          />
        </v-col>
        <v-col v-if="form.tipo === 'pessoal' || form.tipo === 'agenda'" cols="6">
          <v-text-field
            v-model="form.hora"
            :label="$t('modals.labels.time')"
            type="time"
            variant="outlined"
            rounded="lg"
            prepend-inner-icon="mdi-clock-outline"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col v-if="form.tipo === 'financeira'" cols="6">
          <v-text-field
            v-model="form.cor"
            :label="$t('modals.labels.color') + ' (Hex)'"
            type="color"
            variant="outlined"
            rounded="lg"
            :disabled="loading"
          ></v-text-field>
        </v-col>
      </v-row>

      <template v-if="form.tipo === 'pessoal' || form.tipo === 'agenda'">
        <v-divider class="my-4"></v-divider>
        <p class="text-caption font-weight-bold mb-2 text-medium-emphasis">NOTIFICAÇÕES</p>
        <v-row dense>
            <v-col cols="6">
                <v-switch
                    v-model="form.notificacao_site"
                    label="No Site"
                    color="primary"
                    density="compact"
                    hide-details
                ></v-switch>
            </v-col>
            <v-col cols="6">
                <v-switch
                    v-model="form.notificacao_email"
                    label="Por E-mail"
                    color="primary"
                    density="compact"
                    hide-details
                ></v-switch>
            </v-col>
        </v-row>
      </template>

      <v-btn 
        type="submit" 
        color="primary" 
        block 
        size="large" 
        rounded="lg" 
        class="mt-4" 
        variant="flat"
        :disabled="loading || uiStore.loading"
        elevation="2"
      >
        {{ $t('common.save') }}
      </v-btn>
    </v-form>
  </ModalBase>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { useUiStore } from '../../../stores/ui'
import { toast } from 'vue3-toastify'
import ModalBase from '../modalBase.vue'
import DateInput from '../../Common/DateInput.vue'
import CurrencyInput from '../../Common/CurrencyInput.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
const uiStore = useUiStore()
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
  return emojis.filter(e => e.includes(emojiSearch.value))
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
  meta_quantidade: null,
  atual_quantidade: 0,
  unidade: '',
  prazo: null,
  hora: null,
  notificacao_site: false,
  notificacao_email: true,
  cor: '#1867C0',
  icone: ''
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    if (props.meta) {
      // Fix date offset by ensuring we don't treat it as UTC
      let formattedPrazo = null
      if (props.meta.prazo) {
          const d = new Date(props.meta.prazo)
          // Se vier do banco como YYYY-MM-DD, o new Date() pode puxar UTC
          // Vamos garantir que pegamos exatamente o que está escrito
          formattedPrazo = typeof props.meta.prazo === 'string' ? props.meta.prazo.split('T')[0] : props.meta.prazo
      }

      form.value = { 
        ...props.meta,
        tipo: props.meta.tipo || props.initialTipo,
        prazo: formattedPrazo
      }
    } else {
      form.value = {
        id: null,
        tipo: props.initialTipo,
        titulo: '',
        descricao: '',
        valor_objetivo: null,
        meta_quantidade: null,
        atual_quantidade: 0,
        unidade: props.initialTipo === 'financeira' ? 'BRL' : '',
        prazo: null,
        hora: null,
        notificacao_site: false,
        notificacao_email: true,
        cor: props.initialTipo === 'financeira' ? '#4CAF50' : '#FFF9BF',
        icone: props.initialTipo === 'pessoal' ? '🎯' : ''
      }
    }
  }
}, { immediate: true })

const saveMeta = async () => {
  const isEdit = !!form.value.id
  const currentTipo = form.value.tipo || props.meta?.tipo || props.initialTipo
  const isAnotacao = currentTipo === 'pessoal' || currentTipo === 'agenda'
  
  // Clean empty strings to null for backend
  const cleanData = { ...form.value }
  Object.keys(cleanData).forEach(key => {
    if (cleanData[key] === '') cleanData[key] = null
  })

  // Perceived speed: Close and tell parent to refresh
  const optimisticItem = {
    ...cleanData,
    id: isEdit ? cleanData.id : 'opt-' + Date.now(),
    status: cleanData.status || 'andamento'
  }
  
  internalValue.value = false
  emit('saved', optimisticItem, isAnotacao) 

  try {
    const endpointBase = isAnotacao ? '/anotacoes' : '/metas'
    const endpoint = isEdit ? `${endpointBase}/${cleanData.id}` : endpointBase
    
    loading.value = true
    const response = await authStore.apiFetch(endpoint, {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(cleanData)
    })

    if (!response.ok) {
        throw new Error('Erro ao salvar')
    }
    
    toast.success(isEdit ? t('toasts.success_update') : t('toasts.success_add'))
  } catch (e) {
    console.error(e)
    toast.error(t('toasts.error_generic'))
  } finally {
      loading.value = false
  }
}
</script>

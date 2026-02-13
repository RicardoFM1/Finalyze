<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card style="max-height: 90vh; display: flex; flex-direction: column;">
      <v-card-title>
        {{ isEdit ? 'Editar aviso' : `Novo aviso - ${formattedDate}` }}
      </v-card-title>

      <v-card-text style="overflow-y: auto;">
        <v-text-field
          v-model="form.title"
          label="Titulo"
        >
          <template #append-inner>
            <EmojiPicker @select="emoji => form.title += emoji" />
          </template>
        </v-text-field>

        <v-textarea
          v-model="form.description"
          label="Descricao"
        >
          <template #append-inner>
            <EmojiPicker @select="emoji => form.description += emoji" />
          </template>
        </v-textarea>

        <v-text-field
          v-model="form.color"
          label="Cor (Hex)"
          type="color"
          variant="underlined"
          rounded="lg"
        />

        <v-select
          v-model="form.category"
          label="Categorias"
          :items="categories"
          item-title="label"
          item-value="value"
        />

        <v-time-picker
          v-model="form.time"
          format="24hr"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">Cancelar</v-btn>
        <v-btn color="primary" :loading="loading" :disabled="!form.title" @click="save">
          Salvar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { useAuthStore } from '../../../stores/auth'
import EmojiPicker from '../../EmojiPicker/EmojiPicker.vue'

const props = defineProps({
  modelValue: Boolean,
  date: Date,
  reminder: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'saved'])
const authStore = useAuthStore()
const loading = ref(false)

const dialog = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.reminder?.id)

const form = reactive({
  title: '',
  description: '',
  time: '09:00',
  color: '#1867c0',
  category: null
})

const categories = [
  { value: 'internacional', label: 'Internacional' },
  { value: 'investimento', label: 'Investimento' },
  { value: 'contabilidade', label: 'Contabilidade' },
  { value: 'lucro', label: 'Lucro' },
  { value: 'despesa', label: 'Despesa' }
]

const formattedDate = computed(() => {
  if (!props.date) return ''
  return props.date.toLocaleDateString('pt-BR')
})

watch(
  () => [props.modelValue, props.reminder],
  () => {
    if (!props.modelValue) return

    if (props.reminder) {
      const avisoDate = new Date(props.reminder.data_aviso || props.reminder.date)
      form.title = props.reminder.titulo || props.reminder.title || ''
      form.description = props.reminder.descricao || props.reminder.description || ''
      form.category = props.reminder.categoria || props.reminder.category || null
      form.color = props.reminder.cor || props.reminder.color || '#1867c0'
      form.time = `${String(avisoDate.getHours()).padStart(2, '0')}:${String(avisoDate.getMinutes()).padStart(2, '0')}`
      return
    }

    form.title = ''
    form.description = ''
    form.time = '09:00'
    form.color = '#1867c0'
    form.category = null
  },
  { immediate: true }
)

function toApiDatetime() {
  const selectedDate = isEdit.value && props.reminder?.data_aviso
    ? new Date(props.reminder.data_aviso)
    : props.date

  if (!selectedDate) return null

  const [hours, minutes] = form.time.split(':')
  const finalDate = new Date(selectedDate)
  finalDate.setHours(Number(hours), Number(minutes), 0, 0)
  return finalDate.toISOString()
}

async function save() {
  const dataAviso = toApiDatetime()
  if (!dataAviso) {
    toast.warning('Selecione uma data valida para o aviso.')
    return
  }

  loading.value = true

  try {
    const payload = {
      titulo: form.title,
      descricao: form.description || null,
      categoria: form.category,
      cor: form.color,
      data_aviso: dataAviso,
      status: props.reminder?.status || 'pendente'
    }

    const endpoint = isEdit.value ? `/avisos/${props.reminder.id}` : '/avisos'
    const method = isEdit.value ? 'PUT' : 'POST'

    const response = await authStore.apiFetch(endpoint, {
      method,
      body: JSON.stringify(payload)
    })

    const result = await response.json().catch(() => ({}))
    if (!response.ok) {
      throw new Error(result.message || 'Falha ao salvar aviso.')
    }

    emit('saved', result)
    close()
  } catch (err) {
    toast.error(err.message || 'Erro ao salvar aviso.')
  } finally {
    loading.value = false
  }
}

function close() {
  emit('update:modelValue', false)
}
</script>

<template>
  <v-dialog v-model="internalValue" max-width="480px" persistent>
    <v-card class="rounded-xl pa-2" elevation="8">
      <v-card-title class="d-flex align-center gap-3 pt-4 pb-2 px-5">
        <v-avatar color="primary" size="38" class="mr-2">
          <v-icon icon="mdi-bell-ring-outline" color="white" size="20"></v-icon>
        </v-avatar>
        <div>
          <div class="text-subtitle-1 font-weight-bold">
            {{ isEdit ? $t('metas.reminder.edit_title') : $t('metas.reminder.title') }}
          </div>
          <div v-if="meta?.titulo" class="text-caption text-medium-emphasis text-truncate" style="max-width: 280px;">
            {{ meta.titulo }}
          </div>
        </div>
      </v-card-title>

      <v-divider opacity="0.1" class="mx-4"></v-divider>

      <v-card-text class="px-5 pt-4 pb-2">
        
        <v-text-field
          v-model="form.titulo"
          :label="$t('metas.reminder.name_label')"
          variant="outlined"
          rounded="lg"
          density="comfortable"
          class="mb-3"
          hide-details="auto"
          :disabled="loading"
          :placeholder="$t('metas.reminder.name_placeholder')"
        ></v-text-field>

        <v-textarea
          v-model="form.descricao"
          :label="$t('metas.reminder.message_label')"
          :placeholder="$t('metas.reminder.message_placeholder')"
          variant="outlined"
          rounded="lg"
          density="comfortable"
          rows="2"
          auto-grow
          class="mb-3"
          hide-details
          :disabled="loading"
        ></v-textarea>

        <v-row dense class="mb-1">
          <v-col cols="7">
            <div class="text-caption text-medium-emphasis font-weight-medium mb-1">{{ $t('metas.reminder.date_label') }}</div>
            <v-text-field
              v-model="form.data"
              type="date"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              :min="todayDate"
              hide-details="auto"
              :disabled="loading"
            ></v-text-field>
          </v-col>
          <v-col cols="5">
            <div class="text-caption text-medium-emphasis font-weight-medium mb-1">{{ $t('metas.reminder.time_label') }}</div>
            <v-text-field
              v-model="form.hora"
              type="time"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details="auto"
              :disabled="loading"
            ></v-text-field>
          </v-col>
        </v-row>

        <v-select
          v-model="form.categoria"
          :label="$t('metas.reminder.category_label')"
          :items="categories"
          item-title="label"
          item-value="value"
          variant="outlined"
          rounded="lg"
          density="comfortable"
          hide-details
          :disabled="loading"
          clearable
          class="mb-1"
        ></v-select>
      </v-card-text>

      <v-card-actions class="px-5 pb-4 gap-2">
        <v-btn
          v-if="isEdit"
          variant="tonal"
          color="error"
          rounded="lg"
          prepend-icon="mdi-delete-outline"
          @click="deleteReminder"
          :disabled="loading"
        >
          {{ $t('common.delete') }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn variant="text" rounded="lg" @click="close" :disabled="loading">
          {{ $t('common.cancel') }}
        </v-btn>
        <v-btn
          color="primary"
          variant="flat"
          rounded="lg"
          prepend-icon="mdi-bell-plus-outline"
          @click="save"
          :loading="loading"
          :disabled="!form.titulo || !form.data || !form.hora"
        >
          {{ $t('metas.reminder.save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { useAuthStore } from '../../../stores/auth'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const authStore = useAuthStore()

const props = defineProps({
  modelValue: Boolean,
  meta: Object,
  reminder: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'saved', 'deleted'])

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const loading = ref(false)
const isEdit = computed(() => !!props.reminder?.id)

const todayDate = computed(() => new Date().toISOString().split('T')[0])

const form = ref({
  titulo:    '',
  descricao: '',
  data:      '',
  hora:      '09:00',
  categoria: null,
  cor:       '#1867c0',
})

const categories = [
  { value: 'internacional',  label: '🏧 Internacional' },
  { value: 'investimento',   label: '💲 Investimento' },
  { value: 'contabilidade',  label: '🏦 Contabilidade' },
  { value: 'lucro',          label: '📈 Lucro' },
  { value: 'despesa',        label: '📉 Despesa' },
]

watch(() => props.modelValue, (newVal) => {
  if (!newVal) return

  if (props.reminder?.id) {
    
    const d = new Date(props.reminder.data_aviso)
    form.value.titulo    = props.reminder.titulo    || ''
    form.value.descricao = props.reminder.descricao || ''
    form.value.categoria = props.reminder.categoria || null
    form.value.cor       = props.reminder.cor       || '#1867c0'
    form.value.data      = d.toISOString().split('T')[0]
    form.value.hora      = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`
  } else {
    
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    form.value.titulo    = props.meta?.titulo ? `${t('metas.reminder.default_name')}: ${props.meta.titulo}` : ''
    form.value.descricao = ''
    form.value.data      = tomorrow.toISOString().split('T')[0]
    form.value.hora      = '09:00'
    form.value.categoria = null
    form.value.cor       = '#1867c0'
  }
}, { immediate: true })

const buildDataAviso = () => {
  if (!form.value.data || !form.value.hora) return null
  return new Date(`${form.value.data}T${form.value.hora}:00`).toISOString()
}

const save = async () => {
  const dataAviso = buildDataAviso()
  if (!dataAviso) return
  if (new Date(dataAviso) <= new Date()) {
    toast.error(t('metas.reminder.past_error'))
    return
  }

  loading.value = true
  try {
    const payload = {
      titulo:        form.value.titulo,
      descricao:     form.value.descricao || null,
      categoria:     form.value.categoria,
      cor:           form.value.cor,
      data_aviso:    dataAviso,
      status:        props.reminder?.status || 'pendente',
      meta_id:       props.meta?.id || null,
    }

    const endpoint = isEdit.value ? `/lembretes/${props.reminder.id}` : '/lembretes'
    const method   = isEdit.value ? 'PUT' : 'POST'

    const response = await authStore.apiFetch(endpoint, {
      method,
      body: JSON.stringify(payload)
    })

    const result = await response.json().catch(() => ({}))
    if (!response.ok) throw new Error(result.message || t('toasts.error_generic'))

    toast.success(isEdit.value ? t('toasts.success_update') : t('metas.reminder.saved_success'))
    emit('saved', result)
    close()
  } catch (err) {
    toast.error(err.message || t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}

const deleteReminder = async () => {
  if (!props.reminder?.id) return
  loading.value = true
  try {
    const response = await authStore.apiFetch(`/lembretes/${props.reminder.id}`, { method: 'DELETE' })
    if (!response.ok && response.status !== 204) throw new Error()
    toast.success(t('metas.reminder.removed_success'))
    emit('deleted', props.reminder.id)
    close()
  } catch {
    toast.error(t('toasts.error_generic'))
  } finally {
    loading.value = false
  }
}

const close = () => {
  emit('update:modelValue', false)
}
</script>

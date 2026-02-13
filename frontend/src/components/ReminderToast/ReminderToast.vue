<template>
  <div class="reminder-toast">
    <div class="header">
      <span class="date">
        {{ formattedDate }}
      </span>

      <span class="days">
        {{ daysLeft }} dia{{ daysLeft !== 1 ? 's' : '' }}
      </span>
    </div>

    <div class="title">
      {{ reminder.titulo || reminder.title }}
    </div>

    <div class="actions">
      <v-btn
        size="x-small"
        variant="tonal"
        color="success"
        :loading="updating"
        @click="toggleStatus"
      >
        {{ currentStatus === 'concluido' ? 'Reabrir' : 'Concluir' }}
      </v-btn>

      <v-btn
        size="x-small"
        variant="tonal"
        color="error"
        :loading="deleting"
        @click="removeAviso"
      >
        Excluir
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { differenceInDays, startOfDay } from 'date-fns'
import { useAuthStore } from '../../stores/auth'
import { toast } from 'vue3-toastify'

const props = defineProps({
  reminder: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated', 'deleted'])
const authStore = useAuthStore()
const updating = ref(false)
const deleting = ref(false)

const avisoDate = computed(() => props.reminder.data_aviso || props.reminder.date)
const currentStatus = computed(() => props.reminder.status || 'pendente')

const formattedDate = computed(() => {
  if (!avisoDate.value) return ''
  return new Date(avisoDate.value).toLocaleDateString('pt-BR')
})

const daysLeft = computed(() => {
  if (!avisoDate.value) return 0

  const today = startOfDay(new Date())
  const date = startOfDay(new Date(avisoDate.value))
  return differenceInDays(date, today)
})

async function toggleStatus() {
  updating.value = true
  const newStatus = currentStatus.value === 'concluido' ? 'pendente' : 'concluido'

  try {
    const response = await authStore.apiFetch(`/avisos/${props.reminder.id}/status`, {
      method: 'PATCH',
      body: JSON.stringify({ status: newStatus })
    })

    const data = await response.json().catch(() => ({}))
    if (!response.ok) {
      throw new Error(data.message || 'Nao foi possivel atualizar o aviso.')
    }

    emit('updated', data)
  } catch (err) {
    toast.error(err.message || 'Erro ao atualizar aviso.')
  } finally {
    updating.value = false
  }
}

async function removeAviso() {
  deleting.value = true

  try {
    const response = await authStore.apiFetch(`/avisos/${props.reminder.id}`, {
      method: 'DELETE'
    })

    if (!response.ok) {
      const data = await response.json().catch(() => ({}))
      throw new Error(data.message || 'Nao foi possivel excluir o aviso.')
    }

    emit('deleted', props.reminder.id)
  } catch (err) {
    toast.error(err.message || 'Erro ao excluir aviso.')
  } finally {
    deleting.value = false
  }
}
</script>

<style scoped>
.reminder-toast {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.header {
  display: flex;
  justify-content: space-between;
  font-size: 0.85rem;
  opacity: 0.8;
}

.title {
  font-weight: 600;
  font-size: 0.95rem;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>

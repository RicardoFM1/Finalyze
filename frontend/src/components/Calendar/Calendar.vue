<template>
  <VDatePicker
    :attributes="calendarAttributes"
    @update:modelValue="onSelect"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { startOfDay, isAfter } from 'date-fns'
import { useAuthStore } from '../../stores/auth'

const emit = defineEmits(['select', 'loaded'])
const authStore = useAuthStore()
const lembretes = ref([])

const calendarAttributes = computed(() =>
  lembretes.value.map(lembrete => ({
    key: `lembrete-${lembrete.id}`,
    dates: new Date(lembrete.data_aviso || lembrete.data_lembrete),
    dot: {
      color: lembrete.cor || '#1867c0'
    },
    popover: {
      label: lembrete.titulo
    }
  }))
)

onMounted(() => {
  loadAvisos()
})

async function loadAvisos() {
  try {
    const response = await authStore.apiFetch('/lembretes')
    if (!response.ok) return
    lembretes.value = await response.json()
    emit('loaded', lembretes.value)
  } catch (error) {
    console.error(error)
  }
}

function onSelect(value) {
  if (!value) return

  const rawDate = Array.isArray(value) ? value[0] : value
  const clickedDate = startOfDay(new Date(rawDate))
  const today = startOfDay(new Date())

  emit('select', { date: clickedDate, isFuture: isAfter(clickedDate, today) })
}
</script>

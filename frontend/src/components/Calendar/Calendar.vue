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
const avisos = ref([])

const calendarAttributes = computed(() =>
  avisos.value.map(aviso => ({
    key: `aviso-${aviso.id}`,
    dates: new Date(aviso.data_aviso),
    dot: {
      color: aviso.cor || '#1867c0'
    },
    popover: {
      label: aviso.titulo
    }
  }))
)

onMounted(() => {
  loadAvisos()
})

async function loadAvisos() {
  try {
    const response = await authStore.apiFetch('/avisos')
    if (!response.ok) return
    avisos.value = await response.json()
    emit('loaded', avisos.value)
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

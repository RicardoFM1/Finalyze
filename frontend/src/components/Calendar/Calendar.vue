
<template>
  <VDatePicker
    v-model="selectedDate"
    @day-click="handleDayClick"
  />

  <ReminderModal
    v-model="showModal"
    :date="selectedDate"
    @saved="onSaved"
  />
</template>
<script setup>
import { ref } from 'vue'
import { isAfter, startOfDay } from 'date-fns'
import { toast } from 'vue3-toastify'

const selectedDate = ref(null)
const showModal = ref(false)

function handleDayClick(day) {
  const clickedDate = startOfDay(day.date)
  const today = startOfDay(new Date())

  if (isAfter(clickedDate, today)) {
    selectedDate.value = clickedDate
    showModal.value = true
  } else {
    toast.warning('Só é possível criar lembretes para datas futuras')
  }
}

function onSaved(data) {
  console.log('Lembrete salvo:', data)
}
</script>

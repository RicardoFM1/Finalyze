<template>
  <VDatePicker
    @update:modelValue="onSelect"
  />
</template>

<script setup>
import { startOfDay, isAfter } from 'date-fns'

const emit = defineEmits(['select'])

function onSelect(value) {
  if (!value) return

  const rawDate = Array.isArray(value) ? value[0] : value
  const clickedDate = startOfDay(new Date(rawDate))
  const today = startOfDay(new Date())

  emit('select', { date: clickedDate, isFuture: isAfter(clickedDate, today) })
}
</script>
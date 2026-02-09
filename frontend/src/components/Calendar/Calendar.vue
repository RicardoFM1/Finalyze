<template>
  <VDatePicker
    @update:model-value="handleDateChange"
  />
</template>

<script setup>
const emit = defineEmits(['select-date'])

function handleDateChange(date) {
  if (!date) return
  emit('select-date', date)
}
</script>

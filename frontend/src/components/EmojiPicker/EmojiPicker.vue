<template>
  <v-menu
    v-model="open"
    :close-on-content-click="false"
    location="top"
  >
    <template #activator="{ props }">
      <v-btn
        v-bind="props"
        icon
        variant="text"
        size="small"
      >
        <v-icon>mdi-emoticon-outline</v-icon>
      </v-btn>
    </template>

    <EmojiPicker 
      :native="true" 
      theme="light"
      @select="onSelect" 
    />
  </v-menu>
</template>

<script setup>
import { ref } from 'vue'

import EmojiPicker from 'vue3-emoji-picker'
import 'vue3-emoji-picker/css'

const emit = defineEmits(['select'])
const open = ref(false)

function onSelect(emoji) {
  emit('select', emoji.i)
  open.value = false
}
</script>

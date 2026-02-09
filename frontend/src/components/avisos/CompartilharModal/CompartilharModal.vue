<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card style="max-height: 90vh; display: flex; flex-direction: column;">

      <v-card-title>
        Compartilhar
      </v-card-title>

      <v-card-text style="overflow-y: auto;">
        <v-text-field
          label="Email"
          v-model="form.email"
          type="email"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">Cancelar</v-btn>
        <v-btn color="primary" @click="save">Enviar convite</v-btn>
      </v-card-actions>

    </v-card>
  </v-dialog>
</template>

<script setup>
import { reactive, computed } from 'vue'

/* props */
const props = defineProps({
  modelValue: Boolean
})

/* emits */
const emit = defineEmits(['update:modelValue', 'invite'])

/* v-model do dialog */
const dialog = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v)
})

/* form */
const form = reactive({
  email: ''
})

function close() {
  emit('update:modelValue', false)
}

function save() {
  emit('invite', {
    email: form.email
  })

  form.email = ''
  close()
}
</script>

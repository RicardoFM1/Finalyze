<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="emit('update:modelValue', $event)"
    max-width="500"
  >
    <v-card>
      <v-card-title>
        Novo lembrete — {{ formattedDate }}
      </v-card-title>

      <v-card-text>
        <v-text-field
          label="Título"
          v-model="form.title"
        />

        <v-textarea
          label="Descrição"
          v-model="form.description"
        />

        <v-time-picker
          v-model="form.time"
          format="24hr"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">Cancelar</v-btn>
        <v-btn color="primary" @click="save">
          Salvar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script setup>
import { computed, reactive, watch } from 'vue'
import { format } from 'date-fns'



const props = defineProps({
  modelValue: Boolean,
  date: Date
})

const emit = defineEmits(['update:modelValue', 'saved'])

const form = reactive({
  title: '',
  description: '',
  time: '09:00'
})

watch(() => props.modelValue, (open) => {
  if (open) {
    form.title = ''
    form.description = ''
    form.time = '09:00'
  }
})

const formattedDate = computed(() =>
  format(props.date, 'dd/MM/yyyy')
)

function close() {
  emit('update:modelValue', false)
}

function save() {
  emit('saved', {
    date: props.date,
    ...form
  })
  close()
}
</script>
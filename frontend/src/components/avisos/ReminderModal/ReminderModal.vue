<template>
<v-dialog v-model="dialog" max-width="500">
  <v-card style="max-height: 90vh; display: flex; flex-direction: column;">
    
    <v-card-title>
      Novo lembrete — {{ formattedDate }}
    </v-card-title>

    <v-card-text style="overflow-y: auto;">
      
      <v-text-field
        label="Título"
        v-model="form.title"
      >
        <template #append-inner>
          <EmojiPicker @select="e => form.title += e" />
        </template>
      </v-text-field>

      <v-textarea
        label="Descrição"
        v-model="form.description"
      >
        <template #append-inner>
          <EmojiPicker @select="e => form.description += e" />
        </template>
      </v-textarea>

      <v-text-field
      label="cor (Hex)"
      type="color"
      variant="underlined"
      rounded="lg"
      />
      <v-select
        label="Categorias"
        v-model="category"
        :items="categories"
        item-title="label"
        item-value="value"
      />

      <v-time-picker
        v-model="form.time"
        format="24hr"
      />

    </v-card-text>

    <v-card-actions>
      <v-spacer />
      <v-btn text @click="close">Cancelar</v-btn>
      <v-btn color="primary" @click="save">Salvar</v-btn>
    </v-card-actions>

  </v-card>
</v-dialog>
</template>
<script setup>
import { ref, computed, reactive } from 'vue'
import EmojiPicker from '../../EmojiPicker/EmojiPicker.vue'

const category = ref(null)

const props = defineProps({
  modelValue: Boolean,
  date: Date
})

const emit = defineEmits(['update:modelValue', 'saved'])

const dialog = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v)
})

const form = reactive({
  title: '',
  description: '',
  time: '09:00'
})

const formattedDate = computed(() => {
  if (!props.date) return ''
  return props.date.toLocaleDateString('pt-BR')
})

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

const categories = [
  { value: 1, label: '🏧 Internacional' },
  { value: 2, label: '💲 Investimento' },
  { value: 3, label: '🏦 Contabilidade' },
  { value: 4, label: '📈 Lucro' },
  { value: 5, label: '📉 Despesa' },
]

</script>
>
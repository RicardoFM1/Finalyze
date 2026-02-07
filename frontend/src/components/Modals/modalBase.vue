<template>
    <transition name="fade">
        <v-dialog v-model="modelValue" max-width="500px">
        <v-card class="rounded-xl pa-4">
            <div class="d-flex align-center justify-space-between mb-4">
              <v-card-title class="pa-0 font-weight-bold">{{ title }}</v-card-title>
              <v-btn icon="mdi-close" variant="text" size="small" @click="modelValue = false"></v-btn>
            </div>
              <v-card-text class="pa-0">
                <v-form @submit.prevent="submitForm">
                    <v-row dense>
                        <v-col cols="12">
                            <v-btn-toggle v-model="form.tipo" mandatory color="primary" class="w-100 mb-4 rounded-lg" border>
                                <v-btn value="receita" class="flex-grow-1" prepend-icon="mdi-cash-plus">Receita</v-btn>
                                <v-btn value="despesa" class="flex-grow-1" prepend-icon="mdi-cash-minus">Despesa</v-btn>
                            </v-btn-toggle>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="form.valor" label="Valor" prefix="R$" type="number" step="0.01" variant="outlined" rounded="lg" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="form.data" label="Data" type="date" variant="outlined" rounded="lg" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="form.categoria" label="Categoria" variant="outlined" rounded="lg" required placeholder="Ex: Alimentação, Salário"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="form.descricao" label="Descrição" variant="outlined" rounded="lg" rows="2" placeholder="Opcional"></v-textarea>
                        </v-col>
                    </v-row>
                    <v-btn type="submit" color="primary" block size="large" rounded="lg" class="mt-4" :loading="saving" elevation="3">Salvar Lançamento</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
</transition>

</template>

<script>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
    required: true
  },
  title: {
    type: String,
    default: 'Título do Modal',
    required: true
  }
})

const emit = defineEmits(['update:modelValue', 'submit'])

const submitForm = () => {
  emit('submit', form)
  
}

</script>

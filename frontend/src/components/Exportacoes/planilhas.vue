<template>

<v-btn @click="exportarExcel" class="mt-4" color="success" variant="outlined" :disabled="loading">
    <v-icon left>mdi-file-excel</v-icon>
    Exportar para Excel
</v-btn>

</template>


<script setup>


const props = defineProps({
    dados: Array,
    loading: Boolean
})
const emit = defineEmits(['exportar'])

const exportarExcel = () => {
    emit("exportar")
}
</script>
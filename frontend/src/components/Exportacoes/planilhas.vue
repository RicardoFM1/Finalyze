<template>

<v-btn @click="exportarExcel" color="success" variant="outlined">
    <v-icon left>mdi-file-excel</v-icon>
    Exportar para Excel
</v-btn>

</template>


<script setup>
import * as XLSX from "xlsx"

const props = defineProps({
    dados: Array
})
const emit = defineEmits(['exportar'])

const exportarExcel = () => {
const worksheet = XLSX.utils.json_to_sheet([{ nome: "Ricardo", valor: 1500 },
    { nome: "João", valor: 2300 },])
const workbook = XLSX.utils.book_new()

XLSX.utils.book_append_sheet(workbook, worksheet, 'Dados')

XLSX.writeFile(workbook, 'tabela.xlsx')
}
emit("exportar", exportarExcel)
</script>
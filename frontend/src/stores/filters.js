import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useFilterStore = defineStore('filters', () => {
    const filters = ref({
        data: '',
        descricao: '',
        categoria: '',
        tipo: 'todos',
        valor: ''
    })

    const setFilters = (newFilters) => {
        filters.value = { ...filters.value, ...newFilters }
    }

    const clearFilters = () => {
        filters.value = {
            data: '',
            descricao: '',
            categoria: '',
            tipo: 'todos',
            valor: ''
        }
    }

    return { filters, setFilters, clearFilters }
})

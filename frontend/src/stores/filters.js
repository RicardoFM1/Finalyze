import { defineStore } from 'pinia'
import { ref, watch, computed } from 'vue'

export const useFilterStore = defineStore('filters', () => {
    const filters = ref({
        data: '',
        descricao: '',
        categoria: '',
        tipo: 'todos',
        valor: ''
    })

    const queryString = computed(() => {
        const params = new URLSearchParams()
        if (filters.value.data) {
            if (filters.value.data.includes(' to ')) {
                const [inicio, fim] = filters.value.data.split(' to ')
                params.append('data_inicio', inicio)
                params.append('data_fim', fim)
            } else {
                params.append('data', filters.value.data)
            }
        }
        if (filters.value.descricao) params.append('descricao', filters.value.descricao)
        if (filters.value.categoria) params.append('categoria', filters.value.categoria)
        if (filters.value.tipo && filters.value.tipo !== 'todos') params.append('tipo', filters.value.tipo)
        if (filters.value.valor) params.append('valor', filters.value.valor)
        return params.toString()
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

    return { filters, setFilters, clearFilters, queryString }
})


import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const loading = ref(false)
  const theme = ref(localStorage.getItem('theme') || 'light')

  const setLoading = (value) => {
    loading.value = value
  }

  const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
    localStorage.setItem('theme', theme.value)
  }

  return { loading, setLoading, theme, toggleTheme }
})

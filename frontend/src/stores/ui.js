
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const loading = ref(false)
  const theme = ref(localStorage.getItem('theme') || 'light')
  const locale = ref(localStorage.getItem('locale') || 'pt')

  const setLoading = (value) => {
    loading.value = value
  }

  const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
    localStorage.setItem('theme', theme.value)
  }

  const setLocale = (value) => {
    locale.value = value
    localStorage.setItem('locale', value)
  }

  return { loading, setLoading, theme, toggleTheme, locale, setLocale }
})

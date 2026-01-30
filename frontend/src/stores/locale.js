import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLocaleStore = defineStore('locale', () => {
  const locale = ref('pt')

  function setLocale(lang) {
    locale.value = lang
  }

  return { locale, setLocale }
})

import { createI18n } from 'vue-i18n'
import pt from './pt.json'
import en from './en.json'
import { ca } from 'vuetify/locale'
import { computed } from 'vue'

export const i18n = createI18n({
  legacy: false,
  locale: 'pt',
  fallbackLocale: 'en',
  messages: { pt, en }
})

const currencyprefix = computed(() => {
  switch (locale.value) {
    case 'pt':
      return 'R$'
    case 'en':
      return '$'
      default:
      return 'R$'
  }
})
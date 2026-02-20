import { createI18n } from 'vue-i18n'
import pt from '../components/Language/pt.json'
import en from '../components/Language/en.json'

export const i18n = createI18n({
  legacy: false,
  locale: 'pt',
  fallbackLocale: 'en',
  messages: { pt, en }
})

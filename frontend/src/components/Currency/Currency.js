import { computed } from 'vue'
import { useLocaleStore } from '@/stores/locale'

export function formatCurrency(value) {
  const localeStore = useLocaleStore()

  const currencyMap = {
    pt: { locale: 'pt-BR', currency: 'BRL' },
    en: { locale: 'en-US', currency: 'USD' }
  }

  const config = currencyMap[localeStore.locale] || currencyMap.pt

  return new Intl.NumberFormat(config.locale, {
    style: 'currency',
    currency: config.currency
  }).format(value)
}

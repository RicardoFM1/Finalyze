import { computed } from 'vue'
import { useCurrencyStore } from '../stores/currency'

export function useCurrency() {
  const currencyStore = useCurrencyStore()

  const currency = computed(() => currencyStore.currency)
  const locale = computed(() => currencyStore.locale)

  const formatCurrency = (value, sourceCurrency = 'BRL') => {
    const converted = currencyStore.convert(value, sourceCurrency, currency.value)
    return new Intl.NumberFormat(locale.value, {
      style: 'currency',
      currency: currency.value
    }).format(converted)
  }

  const formatNumber = (value, sourceCurrency = 'BRL') => {
    const converted = currencyStore.convert(value, sourceCurrency, currency.value)
    return new Intl.NumberFormat(locale.value, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(converted)
  }

  const currencyPrefix = computed(() => {
    const parts = new Intl.NumberFormat(locale.value, {
      style: 'currency',
      currency: currency.value
    }).formatToParts(1)
    return parts.find((part) => part.type === 'currency')?.value || currency.value
  })

  return {
    currency,
    locale,
    currencyPrefix,
    convert: (value, fromCurrency = 'BRL', toCurrency = currency.value) =>
      currencyStore.convert(value, fromCurrency, toCurrency),
    convertFromBRL: (value, targetCurrency = currency.value) =>
      currencyStore.convertFromBRL(value, targetCurrency),
    formatCurrency,
    formatNumber
  }
}

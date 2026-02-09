import { ref } from 'vue'

const currency = ref('BRL') // moeda padrão

export function useCurrency() {
  const setCurrency = (newCurrency) => {
    currency.value = newCurrency
  }

  return {
    currency,
    setCurrency
  }
}

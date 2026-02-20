import { defineStore } from 'pinia'

const SUPPORTED_CURRENCIES = ['BRL', 'USD', 'EUR', 'GBP']
const DEFAULT_RATES = {
  BRL: 1,
  USD: 0.2,
  EUR: 0.18,
  GBP: 0.15
}

const LOCALE_BY_CURRENCY = {
  BRL: 'pt-BR',
  USD: 'en-US',
  EUR: 'de-DE',
  GBP: 'en-GB'
}

export const useCurrencyStore = defineStore('currency', {
  state: () => ({
    currency: 'BRL',
    ratesFromBRL: { ...DEFAULT_RATES }
  }),

  getters: {
    locale(state) {
      return LOCALE_BY_CURRENCY[state.currency] || 'pt-BR'
    }
  },

  actions: {
    setCurrency(newCurrency) {
      if (!SUPPORTED_CURRENCIES.includes(newCurrency)) return
      this.currency = newCurrency
      localStorage.setItem('currency', newCurrency)
    },

    setRatesFromBRL(rates = {}) {
      const next = { ...this.ratesFromBRL }
      for (const [currency, rate] of Object.entries(rates)) {
        if (!SUPPORTED_CURRENCIES.includes(currency)) continue
        const numericRate = Number(rate)
        if (!Number.isFinite(numericRate) || numericRate <= 0) continue
        next[currency] = numericRate
      }
      this.ratesFromBRL = next
      localStorage.setItem('currencyRatesFromBRL', JSON.stringify(next))
    },

    convertFromBRL(value, targetCurrency = this.currency) {
      const amount = Number(value)
      if (!Number.isFinite(amount)) return 0
      const rate = this.ratesFromBRL[targetCurrency]
      if (!rate) return amount
      return amount * rate
    },

    convert(value, fromCurrency = 'BRL', toCurrency = this.currency) {
      const amount = Number(value)
      if (!Number.isFinite(amount)) return 0
      if (fromCurrency === toCurrency) return amount
      if (fromCurrency === 'BRL') return this.convertFromBRL(amount, toCurrency)

      const fromRate = this.ratesFromBRL[fromCurrency]
      if (!fromRate) return amount
      const inBRL = amount / fromRate
      return this.convertFromBRL(inBRL, toCurrency)
    },

    loadCurrency() {
      const savedCurrency = localStorage.getItem('currency')
      if (savedCurrency && SUPPORTED_CURRENCIES.includes(savedCurrency)) {
        this.currency = savedCurrency
      }

      const savedRates = localStorage.getItem('currencyRatesFromBRL')
      if (!savedRates) return

      try {
        const parsed = JSON.parse(savedRates)
        this.setRatesFromBRL(parsed)
      } catch (error) {
        console.error('Failed to load saved currency rates:', error)
      }
    }
  }
})

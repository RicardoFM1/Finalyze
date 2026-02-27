

import { ref, computed } from 'vue'
import { useCurrencyStore } from '../stores/currency'

const FALLBACK_RATES = {
    BRL: 1,
    USD: 0.18,
    EUR: 0.17,
    GBP: 0.14,
}

const rates = ref({ ...FALLBACK_RATES })
const ratesFetched = ref(false)

const AVAILABLE_CURRENCIES = {
    BRL: { symbol: 'R$', locale: 'pt-BR', code: 'BRL', icon: 'mdi-currency-brl', flag: '🇧🇷' },
    USD: { symbol: '$', locale: 'en-US', code: 'USD', icon: 'mdi-currency-usd', flag: '🇺🇸' },
    EUR: { symbol: '€', locale: 'de-DE', code: 'EUR', icon: 'mdi-currency-eur', flag: '🇪🇺' },
    GBP: { symbol: '£', locale: 'en-GB', code: 'GBP', icon: 'mdi-currency-gbp', flag: '🇬🇧' },
}

const CURRENCY_META = AVAILABLE_CURRENCIES

async function fetchRates() {
    if (ratesFetched.value) return
    try {
        const res = await fetch('https://open.er-api.com/v6/latest/BRL')
        if (!res.ok) throw new Error('rate fetch failed')
        const data = await res.json()
        if (data.rates) {
            rates.value = {
                BRL: 1,
                USD: data.rates.USD ?? FALLBACK_RATES.USD,
                EUR: data.rates.EUR ?? FALLBACK_RATES.EUR,
                GBP: data.rates.GBP ?? FALLBACK_RATES.GBP,
            }
            ratesFetched.value = true
        }
    } catch {
        
    }
}

export function useMoney() {
    const currencyStore = useCurrencyStore()
    const currentCurrency = computed(() => currencyStore.currency || 'BRL')
    const meta = computed(() => CURRENCY_META[currentCurrency.value] ?? CURRENCY_META.BRL)

    fetchRates()

    const fromBRL = (brlValue) => {
        const rate = rates.value[currentCurrency.value] ?? 1
        return Number(brlValue) * rate
    }

    const toBRL = (value) => {
        const rate = rates.value[currentCurrency.value] ?? 1
        if (rate === 0) return Number(value)
        return Number(value) / rate
    }

    const formatMoney = (brlValue, { withSymbol = false } = {}) => {
        const converted = fromBRL(brlValue)
        const formatted = converted.toLocaleString(meta.value.locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
        return withSymbol ? `${meta.value.symbol} ${formatted}` : formatted
    }

    const formatNumber = (value) => {
        return Number(value).toLocaleString(meta.value.locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
    }

    return {
        currentCurrency,
        currencySymbol: computed(() => meta.value.symbol),
        currencyLabel: computed(() => currentCurrency.value),
        fromBRL,
        toBRL,
        formatMoney,
        formatPrice: formatMoney,
        formatNumber,
        meta,
        allCurrencies: CURRENCY_META,
        rates,
        currencyLocale: computed(() => meta.value.locale),
    }
}

export { AVAILABLE_CURRENCIES }

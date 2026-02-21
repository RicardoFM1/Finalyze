/**
 * useMoney.js
 * Central composable for currency formatting and BRL conversion.
 *
 * Strategy:
 *  - The DATABASE always stores values in BRL.
 *  - When DISPLAYING, we convert from BRL → selected currency using static rates.
 *  - When SAVING (new transaction), user types in selected currency → we convert to BRL → send to backend.
 *  - Rates are refreshed once per session from open.er-api.com (free, no key needed).
 *    If the fetch fails, hard-coded fallback rates are used.
 */

import { ref, computed } from 'vue'
import { useCurrencyStore } from '../stores/currency'

// ── static fallback rates relative to BRL ───────────────────────────────────
const FALLBACK_RATES = {
    BRL: 1,
    USD: 0.18,   // ~R$ 5.55 per USD
    EUR: 0.17,   // ~R$ 5.95 per EUR
    GBP: 0.14,   // ~R$ 7.20 per GBP
}

// singleton: shared across all instances
const rates = ref({ ...FALLBACK_RATES })
const ratesFetched = ref(false)

const CURRENCY_META = {
    BRL: { symbol: 'R$', locale: 'pt-BR', code: 'BRL', icon: 'mdi-currency-brl', flag: '🇧🇷' },
    USD: { symbol: '$', locale: 'en-US', code: 'USD', icon: 'mdi-currency-usd', flag: '🇺🇸' },
    EUR: { symbol: '€', locale: 'de-DE', code: 'EUR', icon: 'mdi-currency-eur', flag: '🇪🇺' },
    GBP: { symbol: '£', locale: 'en-GB', code: 'GBP', icon: 'mdi-currency-gbp', flag: '🇬🇧' },
}

async function fetchRates() {
    if (ratesFetched.value) return
    try {
        // Free API – no key required, returns base in BRL
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
        // silently keep fallback rates
    }
}

export function useMoney() {
    const currencyStore = useCurrencyStore()

    const currentCurrency = computed(() => currencyStore.currency || 'BRL')
    const meta = computed(() => CURRENCY_META[currentCurrency.value] ?? CURRENCY_META.BRL)

    // Initialize rates fetch once
    fetchRates()

    /**
     * Convert a BRL value to the selected display currency.
     * @param {number} brlValue - value as stored in the database (BRL)
     * @returns {number}
     */
    const fromBRL = (brlValue) => {
        const rate = rates.value[currentCurrency.value] ?? 1
        return Number(brlValue) * rate
    }

    /**
     * Convert a value from the selected currency to BRL (for saving to DB).
     * @param {number} value - typed by user in selected currency
     * @returns {number}
     */
    const toBRL = (value) => {
        const rate = rates.value[currentCurrency.value] ?? 1
        if (rate === 0) return Number(value)
        return Number(value) / rate
    }

    /**
     * Format a BRL database value for display in the current currency.
     * @param {number} brlValue
     * @param {object} [opts]
     * @param {boolean} [opts.withSymbol=true]
     * @returns {string}
     */
    const formatMoney = (brlValue, { withSymbol = false } = {}) => {
        const converted = fromBRL(brlValue)
        const formatted = converted.toLocaleString(meta.value.locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
        return withSymbol ? `${meta.value.symbol} ${formatted}` : formatted
    }

    /**
     * Format a number that is ALREADY in the display currency (no conversion).
     */
    const formatNumber = (value) => {
        return Number(value).toLocaleString(meta.value.locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
    }

    const currencySymbol = computed(() => meta.value.symbol)
    const currencyLabel = computed(() => currentCurrency.value)

    return {
        currentCurrency,
        currencySymbol,
        currencyLabel,
        fromBRL,
        toBRL,
        formatMoney,
        formatNumber,
        meta,
        allCurrencies: CURRENCY_META,
        rates,
    }
}

import { computed } from 'vue'
import { useCurrencyStore } from '@/stores/currency'

/**
 * Currency Options Map
 * Maps currency codes to their Intl locale and symbol.
 */
const CURRENCY_MAP = {
    BRL: { locale: 'pt-BR', symbol: 'R$' },
    USD: { locale: 'en-US', symbol: '$' },
    EUR: { locale: 'de-DE', symbol: '€' },
    GBP: { locale: 'en-GB', symbol: '£' },
}

export const AVAILABLE_CURRENCIES = Object.entries(CURRENCY_MAP).map(([code, info]) => ({
    code,
    label: `${info.symbol} — ${code}`,
    symbol: info.symbol,
}))

export function useMoney() {
    const currencyStore = useCurrencyStore()

    const currentCurrency = computed(() => currencyStore.currency || 'BRL')

    const currencyInfo = computed(() => CURRENCY_MAP[currentCurrency.value] ?? CURRENCY_MAP.BRL)

    /** Returns the currency symbol (e.g. "R$", "$") */
    const currencySymbol = computed(() => currencyInfo.value.symbol)

    /** Returns the BCP 47 locale string (e.g. "pt-BR", "en-US") */
    const currencyLocale = computed(() => currencyInfo.value.locale)

    /**
     * Formats a numeric value as a full currency string.
     * e.g. formatPrice(1234.5) → "R$ 1.234,50"  (BRL)
     *                           → "$1,234.50"     (USD)
     */
    function formatPrice(value) {
        const num = Number(value)
        if (isNaN(num)) return currencySymbol.value + ' 0,00'
        return new Intl.NumberFormat(currencyLocale.value, {
            style: 'currency',
            currency: currentCurrency.value,
        }).format(num)
    }

    /**
     * Formats a number WITHOUT the currency symbol — just the numeric part.
     * e.g. formatNumber(1234567.89) → "1.234.567,89"  (BRL)
     *                                → "1,234,567.89"  (USD)
     */
    function formatNumber(value) {
        const num = Number(value)
        if (isNaN(num)) return '0,00'
        return new Intl.NumberFormat(currencyLocale.value, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(num)
    }

    /**
     * Compact / abbreviated currency string meant for tight spaces.
     * Uses "K" for thousands and "M" for millions.
     * e.g. formatShortPrice(1234) → "R$ 1,2K"
     */
    function formatShortPrice(value) {
        const num = Number(value)
        if (isNaN(num)) return currencySymbol.value + ' 0'
        return new Intl.NumberFormat(currencyLocale.value, {
            style: 'currency',
            currency: currentCurrency.value,
            notation: 'compact',
            maximumFractionDigits: 1,
        }).format(num)
    }

    /**
     * Returns the decimal separator character for the current locale.
     * e.g. ',' for BRL/pt-BR, '.' for USD/en-US
     */
    const decimalSeparator = computed(() => {
        const formatted = new Intl.NumberFormat(currencyLocale.value).format(1.1)
        return formatted[1]
    })

    return {
        currentCurrency,
        currencySymbol,
        currencyLocale,
        decimalSeparator,
        AVAILABLE_CURRENCIES,
        formatPrice,
        formatNumber,
        formatShortPrice,
    }
}

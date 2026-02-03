import { defineStore } from "pinia";

export const useCurrencyStore = defineStore("currency", {
    state: () => ({
        currency: 'BRL',
    }),

    actions: {
        setCurrency(newCurrency) {
            this.currency = newCurrency;
            localStorage.setItem('currency', newCurrency);
        },

        loadCurrency() {
            const saved =  localStorage.getItem('currency');
            if (saved) {
                this.currency = saved;
            }
        },
    },
})
import { defineStore } from 'pinia';

export const useAppStore = defineStore('app', {
  state: () => ({
    locale: 'pt',
    corrency: 'BRL',
  }),
  actions: {
    setLocale(locale) {
      this.locale = locale;
    
    if (locale === 'pt') {
        this.corrency = 'BRL';
      } else if (locale === 'en') {
        this.corrency = 'USD';
      }
    }
  }
})
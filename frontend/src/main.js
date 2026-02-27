import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import LogRocket from 'logrocket'

import vuetify from './plugins/vuetify'
import router from './router'

import { createPinia } from 'pinia'

import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import { createI18n } from 'vue-i18n'
import pt from './components/Language/pt.json'
import en from './components/Language/en.json'

import { VueDatePicker } from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const pinia = createPinia()

const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'pt',
  fallbackLocale: 'pt',
  messages: {
    pt,
    en,
  },
})

const app = createApp(App)

// LogRocket.init('x6etoi/finalyze')

app.use(pinia)
app.use(router)
app.use(vuetify)
app.use(i18n)
app.use(Vue3Toastify, { autoClose: 3000 })
app.component('VueDatePicker', VueDatePicker)

app.mount('#app')

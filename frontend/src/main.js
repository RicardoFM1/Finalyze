import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import { createPinia } from 'pinia'
import Vue3Toastify, { toast } from 'vue3-toastify';

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)
app.use(Vue3Toastify, {
    autoClose: 3000,
});

app.mount('#app')

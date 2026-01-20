// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import 'vue3-toastify/dist/index.css'

// Composables
import { createVuetify } from 'vuetify'

// https://vuetifyjs.com/en/introduction/why-vuetify/#feature-guides
export default createVuetify({
  theme: {
    defaultTheme: 'light',
    themes: {
        light: {
            colors: {
                primary: '#1867C0', // Customize as needed
                secondary: '#5CBBF6',
            },
        },
    },
  },
})

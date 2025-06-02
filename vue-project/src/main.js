import { createApp } from 'vue'
import App from './App.vue'

// Vuetify setup
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

// Router setup
import router from './router' // Make sure you created src/router.js

// Create Vuetify instance
const vuetify = createVuetify({
  components,
  directives,
  // optional: theme, icons, etc.
})

const app = createApp(App)
app.use(vuetify)
app.use(router)
app.mount('#app')

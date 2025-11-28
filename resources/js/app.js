import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import InertiaBaseRouterPlugin from './plugins/inertia-router-base.js'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    
    if (!page) {
      console.error(`Página no encontrada: ${name}`)
      console.log('Páginas disponibles:', Object.keys(pages))
      throw new Error(`Página no encontrada: ${name}`)
    }
    
    return page
  },
  title: title => title ? `${title} - Ping CRM` : 'Ping CRM',
    setup({ el, App, props, plugin }) {

    createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(InertiaBaseRouterPlugin)
      .mount(el)
  },
})

import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import es from 'vuetify/es5/locale/es'

Vue.use(Vuetify)

const opts = {
    lang: {
        locales: {es},
        current: 'es',
    },

    icons: {
        iconfont: 'mdi',  // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
    },
    theme: {
        dark: false,
        themes: {
            light: {
                primary: '#66bfbf',
                secondary: '#5cbbf6',
                accent: '#8c9eff',
                info: '#3c7ee3',
                success: '#4F8A10',
                warning: '#fb8c00',
                error: '#fa4659',
                tooltip: '#3c7ee3'
            },
        },
    },
}

export default new Vuetify(opts)

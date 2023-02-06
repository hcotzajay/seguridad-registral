require('./bootstrap');

window.Vue = require('vue');

import '@mdi/font/css/materialdesignicons.min.css';

import router from "./router";
import vuetify from "./plugins/Vuetify";
import Msj from "./plugins/Msj";
import VueProgressBar from "vue-progressbar";
import Helper from "./plugins/Helper";
import Moment from './plugins/Moment';
import store from './components/store'

Vue.use(Helper);
Vue.use(Moment);
Vue.use(Msj);
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red'
});

Vue.component('login', require('./components/login.vue').default);
Vue.component('workArea', require('./components/work-area.vue').default);

Vue.component('AlignActions', require('./components/base/Align-actions.vue').default);
Vue.component('CancelBtn', require('./components/base/Btns/Cancel-Btn.vue').default);
Vue.component('ActionBtn', require('./components/base/Btns/Action-Btn.vue').default);
Vue.component('EditBtn', require('./components/base/Btns/Edit-Btn.vue').default);
Vue.component('DeleteBtn', require('./components/base/Btns/Delete-Btn.vue').default);

const app = new Vue({
    el: '#app',
    router,
    vuetify,
    store,
});

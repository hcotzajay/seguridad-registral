import Vue from 'vue';
import VueRouter from 'vue-router';
import view_404 from "../components/errors/view_404";
import renap from "../components/consulta/renap";

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        component: renap
    },
    // {
    //     path: '/',
    //     component: {
    //         template: `<div>Área del DASHBOARD<i class="mdi mdi-view-dashboard mdi-24px" style="color:#009688;"></i></div>`
    //     }
    // },
    {
        path: '*',
        component: view_404
    }
];

let router = new VueRouter({
    mode: 'history',
    //base: process.env.BASE_URL,
    routes
})

router.beforeEach((to, from, next) => {
    /*if (to.path != from.path)*/
    next()
})

export default router;

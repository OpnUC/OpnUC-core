//import Pace from 'pace'

import Vue from 'vue'
import VueRouter from 'vue-router'

import AppView from './components/App.vue'

Vue.use(VueRouter);

import routes from './routes'

var router = new VueRouter({
    routes: routes,
    mode: 'history',
    scrollBehavior: function (to, from, savedPosition) {
        return savedPosition || { x: 0, y: 0 }
    }
})

const app = new Vue({
    router: router,
    el: '#root',
    render: h => h(AppView)
});

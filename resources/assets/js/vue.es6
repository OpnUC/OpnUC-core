//import Pace from 'pace'

import Vue from 'vue'
import VueRouter from 'vue-router'
import Auth from './auth'

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

// router.beforeEach((to, from, next) => {
//     if (to.matched.some(record => record.meta.requiresAuth) && !Auth.user.authenticated) {
//         next({ path: '/login', query: { redirect: to.fullPath }});
//     } else {
//         next();
//     }
// });

export {router};

const app = new Vue({
    router: router,
    el: '#root',
    data:{
        sidebar: null
    },
    watch:{
        // サイドバーの有無をチェック
        sidebar: function(flag) {
            console.log('change sidebar' + flag);

            if(flag){
                $('body').addClass('sidebar-mini');
                $('body').removeClass('sidebar-collapse');
                $('.sidebar-toggle').show();
            }else{
                $('body').removeClass('sidebar-mini');
                $('body').addClass('sidebar-collapse');
                $('.sidebar-toggle').hide();
            }
        }
    },
    render: function(h){
        return h(AppView);
    }
});

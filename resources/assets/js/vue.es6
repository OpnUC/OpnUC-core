require('es6-promise').polyfill();

import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/ja'
import 'element-ui/lib/theme-default/index.css'
import AppView from './components/App.vue'

import axios from 'axios'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'
import VueEvents from 'vue-events'

Vue.use(VueEvents)
Vue.use(VueAxios, axios)
Vue.use(VueRouter);
Vue.use(ElementUI, {locale})

Vue.axios.defaults.baseURL = '/api/v1';

import routes from './routes'

Vue.router = new VueRouter({
    routes: routes,
    mode: 'history',
    linkActiveClass: 'active',
    scrollBehavior: function (to, from, savedPosition) {
        return savedPosition || {x: 0, y: 0}
    }
})

var myBearer = {
    request: function (req, token) {
        this.options.http._setHeaders.call(this, req, {Authorization: 'Bearer ' + token});
    },

    response: function (res) {
        var headers = this.options.http._getHeaders.call(this, res),
            token = headers.Authorization || headers.authorization;

        if (token) {
            token = token.split(/Bearer\:?\s?/i);

            // for LaravelEcho
            if (window.echo) {
                window.echo.options.auth.headers.Authorization = 'Bearer ' + token
            }

            return token[token.length > 1 ? 1 : 0].trim();
        }
    }
}

Vue.use(VueAuth, {
    // auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    auth: myBearer,
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    rolesVar: 'permissions',
    tokenExpired: function (item) {
        return true;
    }
})

//export {router};

Vue.component('tel-contact', require('./components/common_TelContact.vue'))

const app = new Vue({
    router: Vue.router,
    el: '#root',
    data: {
        sidebar: null
    },
    watch: {
        // サイドバーの有無をチェック
        sidebar: function (flag) {
            if (flag) {
                $('body').addClass('sidebar-mini');
                $('body').removeClass('sidebar-collapse');
                $('.sidebar-toggle').show();
            } else {
                $('body').removeClass('sidebar-mini');
                $('body').addClass('sidebar-collapse');
                $('.sidebar-toggle').hide();
            }
        }
    },
    events: {
        'LaravelEcho:init': function () {
            var _this = this
            if (!window.echo) {
                // ToDo
                _this.$message({
                    type: 'error',
                    message: 'WebSocketサーバに接続出来ませんでした。'
                });
                return;
            }

            // 初回は接続しているものとする
            this.$events.$emit('LaravelEcho:connect')

            // 切断時
            window.echo.connector.socket.on('disconnect', function () {
                _this.$events.$emit('LaravelEcho:disconnect')
            });

            // 接続時
            window.echo.connector.socket.on('connect', function () {
                _this.$events.$emit('LaravelEcho:connect')
            });

            // 再接続時
            window.echo.connector.socket.on('reconnect', function () {
                _this.$events.$emit('LaravelEcho:reconnect')
            });

            // Broadcast Channel
            window.echo.channel('BroadcastChannel')
                .listen('MessageCreateBroadcastEvent', (e) => {
                    this.$events.$emit('LaravelEcho:Broadcast', e)
                })
                .listen('PresenceUpdated', (e) => {
                    this.$events.$emit('LaravelEcho:PresenceUpdated', e)
                });

            // 認証に通っている場合はPrivateChannel
            if (this.$auth.check()) {
                window.echo.private('PrivateChannel.' + this.$auth.user().id)
                    .listen('MessageCreatePrivateEvent', (e) => {
                        this.$events.$emit('LaravelEcho:Private', e)
                    });
            }
        },
        // Click to Callの発信処理
        'Click2Call': function(number){
            var _this = this

            console.log(number)

            this.$confirm(number + 'に発信します。よろしいですか？', '確認', {
                confirmButtonText: '発信',
                cancelButtonText: 'キャンセル',
            }).then(() => {
                axios.post('/click2call/originate',
                    {
                        number: number
                    })
                    .then(function (response) {
                        _this.$message({
                            type: 'info',
                            message: '発信中です。しばらくお待ちください。'
                        });
                    })
                    .catch(function (error) {
                        _this.$message({
                            type: 'error',
                            message: '発信に失敗しました。'
                        });
                    });
            });
        },
    },
    render: function (h) {
        return h(AppView);
    }
});

// 30min interval JWT Token Refresh
var timer = setInterval(function () {
    app.$auth.refresh()
}, 30 * 60 * 1000);

// window.app = app

// window.axios.interceptors.response.use((response) => {
//     return response
// }, function (error) {
//     var originalRequest = error.config
//     console.log(originalRequest)
//     if (error.response.status === 401 && !originalRequest._retry) {
//         originalRequest._retry = true
//     }
//     // Do something with response error
//     return Promise.reject(error)
// })

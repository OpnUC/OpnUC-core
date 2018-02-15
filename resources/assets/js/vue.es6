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
            token = token[token.length > 1 ? 1 : 0].trim()

            // for LaravelEcho
            if (window.echo) {
                window.echo.options.auth.headers.Authorization = 'Bearer ' + token
            }

            return token
        }
    }
}

Vue.use(VueAuth, {
    auth: myBearer,
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    rolesVar: 'permissions',
    tokenExpired: function (item) {
        return true;
    }
})

Vue.component('tel-contact', require('./components/common_TelContact.vue'))

// VueのグローバルMixin
Vue.mixin({
    data() {
        return {
            // 1ページの件数
            perPageList: [10, 30, 50, 100],
            // Vuetable-2
            vueTableCss: {
                tableClass: 'table table-striped table-bordered',
                loadingClass: 'loading',
                ascendingIcon: 'glyphicon glyphicon-chevron-up',
                descendingIcon: 'glyphicon glyphicon-chevron-down',
                handleIcon: 'glyphicon glyphicon-menu-hamburger',
            },
            vueTableCssPagination: {
                wrapperClass: 'pagination pull-right',
                activeClass: 'btn-primary',
                disabledClass: 'disabled',
                pageClass: 'btn btn-border',
                linkClass: 'btn btn-border',
                icons: {
                    first: '',
                    prev: '',
                    next: '',
                    last: '',
                },
            },
            vueTableIcons: {
                first: '',
                prev: '',
                next: '',
                last: '',
            },
        }
    },
    methods: {
        /**
         * Vuetable-2のページネーションデータ
         * @param paginationData
         */
        onVuetablePaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
            this.$refs.paginationInfo.setPaginationData(paginationData)
        },
        /**
         * Vuetable-2でページが変更された時
         * @param page
         */
        onVuetableChangePage(page) {
            this.$refs.vuetable.changePage(page)

            // スクロールの速度(ms)
            var speed = 400
            var target = $('#tableScrollTop')

            // スクロール先が見つからない場合は、処理しない
            if (target && target.length === 0)
                return false

            var position = target.offset().top
            $('body,html').animate({scrollTop: position}, speed, 'swing')

            return false
        },
    }
})

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

            // Broadcast ChannelにJoinする
            window.echo.channel('BroadcastChannel')
                .listen('MessageCreateBroadcastEvent', function (e) {
                    _this.$events.$emit('LaravelEcho:Broadcast', e)
                })
                .listen('PresenceUpdated', function (e) {
                    _this.$events.$emit('LaravelEcho:PresenceUpdated', e)
                });

            // 認証に通っている場合はPrivateChannelにもJoinする
            if (this.$auth.check()) {
                window.echo.private('PrivateChannel.' + this.$auth.user().id)
                    .listen('MessageCreatePrivateEvent', function (e) {
                        _this.$events.$emit('LaravelEcho:Private', e)
                    })
                    .listen('IncomingCallEvent', function (e) {
                        _this.$events.$emit('LaravelEcho:IncomingCall', e)
                    });
            }
        },
        // Click to Callの発信処理
        'Click2Call': function (number) {
            var _this = this

            // Click 2 Callが有効で無い場合は処理しない
            if (!window.opnucConfig.enable_c2c || !this.$auth.user().address_book.tel1) {
                return;
            }

            // OpnUC上で処理する場合、ブラウザ側のイベント処理を無効にする
            event.preventDefault();

            this.$confirm(this.$auth.user().address_book.tel1 + ' から ' + number + ' に発信します。よろしいですか？', '確認', {
                confirmButtonText: '発信',
                cancelButtonText: 'キャンセル',
            }).then(function () {
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
                        var message = '発信に失敗しました。'

                        if (error.response.status === 422 || error.response.status === 403) {
                            message = error.response.data.message
                        }

                        _this.$message({
                            type: 'error',
                            message: message,
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

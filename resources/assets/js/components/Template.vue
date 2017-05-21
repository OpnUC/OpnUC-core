<template>
    <div class="wrapper">
        <header class="main-header">
            <router-link to="/" class="logo">
                <span class="logo-mini"><b>U</b>C</span>
                <span class="logo-lg"><b>Opn</b>UC</span>
            </router-link>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <ul class="nav navbar-nav">
                    <router-link tag="li" to="/cdr" v-if="$auth.check('cdr-user')"><a>発着信履歴</a></router-link>
                    <router-link tag="li" to="/AddressBook" v-if="$auth.check('addressbook-user')"><a>Web電話帳</a>
                    </router-link>
                    <router-link tag="li" to="/Messenger" v-if="$auth.check('messenger-user')"><a>Messenger</a>
                    </router-link>
                </ul>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <template v-if="$auth.check()">
                            <li class="notifications-menu">
                                <a>
                                    <i v-if="isConnectLaravelEcho" class="fa fa-bell-o"></i>
                                    <i v-else class="fa fa-bell-slash-o"></i>
                                </a>
                            </li>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">
                                    <img v-bind:src="$auth.user().avatar_path"
                                         class="user-image" alt="Avatar">
                                    {{ $auth.user().display_name }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img v-bind:src="$auth.user().avatar_path"
                                             class="img-circle" alt="Avatar">
                                        <p>
                                            {{ $auth.user().display_name }}
                                        </p>
                                    </li>
                                    <li class="user-body" v-if="$auth.check('system-admin')">
                                        <div class="col-xs-12 text-center">
                                            <router-link to="/Admin">
                                                <i class="fa fa-cog"></i>
                                                システム管理
                                            </router-link>
                                        </div>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <router-link to="/User" class="btn btn-default btn-flat">
                                                ユーザ情報
                                            </router-link>
                                        </div>
                                        <div class="pull-right">
                                            <button type="submit" class="btn btn-default btn-flat" v-on:click="signout">
                                                ログアウト
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-else>
                            <li>
                                <router-link to="/login">ログイン</router-link>
                            </li>
                        </template>

                    </ul>
                </div>
            </nav>
        </header>

        <router-view name="sidebar">
        </router-view>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    {{ $route.meta.title }}
                    <small>{{ $route.meta.description }}</small>
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <router-link to="/"><i class="fa fa-home"></i>Home</router-link>
                    </li>
                    <li class="active">{{ $route.meta.title }}</li>
                </ol>
            </section>

            <router-view></router-view>
        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; {{year}} OpnUC Developer Team.</strong> All rights reserved.
        </footer>
    </div>
</template>

<script>

    import pushjs from 'push.js'

    export default {
        data() {
            return {
                isConnectLaravelEcho: false,
            }
        },
        computed: {
            year: function () {
                var y = new Date()
                return y.getFullYear()
            },
        },
        events: {
            'LaravelEcho:PresenceUpdated': function (e) {
                var ext = e.ext
                var status = e.status

                // プレゼンスの更新
                $('i.fa.fa-circle.extStatus.ext' + ext)
                    .removeClass(function (index, className) {
                        return (className.match(/\btext-\S+/g) || []).join(' ');
                    })
                    .addClass(window.extStatus[status]['statusClass'])
                    .attr('title', window.extStatus[status]['statusText']);
            },
            'LaravelEcho:connect': function (e) {
                this.isConnectLaravelEcho = true
            },
            'LaravelEcho:disconnect': function (e) {
                this.isConnectLaravelEcho = false
            },
            'LaravelEcho:reconnect': function (e) {
                this.isConnectLaravelEcho = true
            },
            'LaravelEcho:IncomingCall': function (e) {
                if (e.state) {
                    // start
                    pushjs.create('着信中...',
                        {
                            body: e.callerid_name + ' <' + e.callerid_num + '> から着信中です。',
                            requireInteraction: true,
                            icon: {
                                x32: window.appUrl + 'images/iconmonstr-phone-7-32.png',
                            },
                            tag: 'incomingcall',
                        }
                    )
                } else {
                    // end
                    pushjs.clear('incomingcall')
                }
            },
        },
        mounted(){
            // デスクトップ通知の許可をリクエスト
            pushjs.Permission.request();

            this.$events.$emit('LaravelEcho:init')
        },
        methods: {
            changeloading () {
                //this.store.commit('TOGGLE_SEARCHING')
            },
            toggleMenu(event) {
                // remove active from li
                window.$('li.pageLink').removeClass('active')
                // Add it to the item that was clicked
                event.toElement.parentElement.className = 'pageLink active'
            },
            signout() {
                this.$auth.logout({
                    makeRequest: true,
                    error() {
                        console.log('error');
                    }
                });
            }
        }
    }
</script>

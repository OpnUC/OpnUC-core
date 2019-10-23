<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div class="overlay" v-if="isLoading">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="box-header">ログイン</div>
                <div class="box-body">
                    <div v-if="error" class="alert alert-danger">
                        {{error.message}}
                    </div>
                    <div class="box-body text-center" v-if="$route.query.mode === 'restore'">
                        リダイレクト中・・・
                    </div>
                    <form class="form-signin" autocomplete="off" v-on:submit.prevent="signin" v-else>
                        <div class="form-group">
                            <label for="username" class="sr-only">ユーザ名</label>
                            <input type="text" id="username" name="username" class="form-control" v-model="username"
                                   placeholder="ユーザ名" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="password" class="sr-only">パスワード</label>
                            <input type="password" id="password" name="password" class="form-control"
                                   v-model="password"
                                   placeholder="パスワード" required>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="remember" v-model="remember"> ログインを維持する
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <button class="btn btn-primary btn-block" type="submit">
                                    <i class="fa fa-sign-in"></i>
                                    ログイン
                                </button>
                            </div>
                        </div>

                        <div class="text-center" v-if="enable_saml2_auth">
                            <p></p>
                            <a href="/extensions/saml2/kcprd/login" class="btn btn-block btn-social btn-google">
                                <i class="fa fa-sign-in"></i>
                                SAML2でログイン
                            </a>
                        </div>

                        <p></p>

                        <div class="text-center">
                            <router-link to="/PasswordResetEmail">パスワードをお忘れですか？</router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    export default {
        computed: {
            enable_saml2_auth(){
                return window.opnucConfig.enable_saml2_auth;
            },
        },
        created() {
            this.$root.sidebar = false;
        },
        data() {
            return {
                isLoading: false,
                username: null,
                password: null,
                remember: false,
                error: null
            }
        },
        mounted(){
            // mode が restore の場合は、渡されたTokenで認証を試みる
            if (this.$route.query.mode === 'restore' && this.$route.query.token) {
                this.isLoading = true

                var redirect = this.$auth.redirect();

                this.$auth.login({
                    params: {
                        'mode': 'restore',
                        'token': this.$route.query.token,
                    },
                    rememberMe: false,
                    redirect: redirect ? redirect.from.fullPath : '/',
                    error(res) {
                        _this.isLoading = false
                        _this.error = res.response.data;
                    }
                });
            }
        },
        methods: {
            signin() {
                var _this = this

                this.isLoading = true

                var redirect = this.$auth.redirect();

                this.$auth.login({
                    params: {
                        'username': this.username,
                        'password': this.password,
                    },
                    rememberMe: this.remember,
                    redirect: redirect ? redirect.from.fullPath : '/',
                    error(res) {
                        _this.isLoading = false
                        _this.error = res.response.data;
                    }
                });
            }
        }
    }
</script>
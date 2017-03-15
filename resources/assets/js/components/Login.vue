<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div id="resultLoading" style="visibility: hidden;" class="overlay">
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
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="remember" v-model="remember"> ログインを維持する
                            </label>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
                        <a href="" class="btn btn-primary btn-block">SAML2でログイン</a>
                        <br/>
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
        created() {
            this.$root.sidebar = false;
        },
        data() {
            return {
                username: null,
                password: null,
                remember: false,
                error: null
            }
        },
        mounted(){
            // mode が restore の場合は、Laravelのログイン情報を元にTokenの取得を試みる
            if(this.$route.query.mode === 'restore'){

                $('#resultLoading').css('visibility', 'visible');

                var redirect = this.$auth.redirect();

                this.$auth.login({
                    params:{
                        'mode': 'restore',
                    },
                    rememberMe: false,
                    redirect: redirect ? redirect.from.fullPath : '/',
                    error(res) {
                        $('#resultLoading').css('visibility', 'hidden');
                        this.error = res.response.data;
                    }
                });
            }
        },
        methods: {
            signin() {
                $('#resultLoading').css('visibility', 'visible');

                var redirect = this.$auth.redirect();

                this.$auth.login({
                    params:{
                        'username': this.username,
                        'password': this.password,
                    },
                    rememberMe: this.remember,
                    redirect: redirect ? redirect.from.fullPath : '/',
                    error(res) {
                        $('#resultLoading').css('visibility', 'hidden');
                        this.error = res.response.data;
                    }
                });
            }
        }
    }
</script>
<style>
</style>
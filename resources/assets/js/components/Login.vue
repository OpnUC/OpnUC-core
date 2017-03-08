<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div class="box-header">ログイン</div>
                <div class="box-body">
                    <div v-if="error" class="alert alert-danger">
                        {{error.message}}
                    </div>
                    <form class="form-signin" autocomplete="off" v-on:submit.prevent="signin">
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
                            <a href="">パスワードをお忘れですか？</a>
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
        methods: {
            signin() {
                var redirect = this.$auth.redirect();

                this.$auth.login({
                    params:{
                        'username': this.username,
                        'password': this.password,
                    },
                    rememberMe: this.remember,
                    redirect: redirect ? redirect.from.fullPath : '/',
                    error(res) {
                        this.error = res.response.data;
                    }
                });
            }
        }
    }
</script>
<style>
</style>
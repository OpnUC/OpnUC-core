<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div class="box-header">ログイン</div>
                <div class="box-body">
                    <form class="form-signin" autocomplete="off" v-on:submit="signin">
                        <div class="form-group">
                            <label for="username" class="sr-only">ユーザ名</label>
                            <input type="text" id="username" name="username" class="form-control"  v-model="username"
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
                                <input type="checkbox" value="remember"> ログインを維持する
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
    import auth from '../auth'

    export default {
        created: function () {
            this.$root.sidebar = false;
        },
        data() {
            return {
                username: null,
                password: null,
                error: false
            }
        },
        methods: {
            signin(event) {
                event.preventDefault()
                auth.signin(this, this.username, this.password)
            }
        }
    }
</script>
<style>
</style>
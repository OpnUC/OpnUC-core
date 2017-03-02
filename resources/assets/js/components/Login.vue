<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div class="box-header">ログイン</div>
                <div class="box-body">
                    <form class="form-signin" method="post" action="">
                        <div class="form-group">
                            <label for="username" class="sr-only">ユーザ名</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="ユーザ名"
                                   required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="password" class="sr-only">パスワード</label>
                            <input type="password" id="password" name="password" class="form-control"
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
    module.exports = {
        name: 'TOP',
        mounted: function () {
            // Page is ready. Let's load our functions!
            $('body').removeClass('sidebar-mini');
            $('body').addClass('sidebar-collapse');

            $('.sidebar-toggle').hide();

            console.log(this.$route.meta.sidebar);
            console.log("Login Loaded");
        }
    }
</script>

<style>
</style>
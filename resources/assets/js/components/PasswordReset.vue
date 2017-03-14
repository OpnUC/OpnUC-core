<template>
    <section class="content">
        <div class="col-md-4 col-md-offset-4">
            <div class="box box-solid box-info">
                <div id="resultLoading" style="visibility: hidden;" class="overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="box-header">パスワードリセット</div>
                <div class="box-body">
                    <div v-if="status == 'success'" class="alert alert-success">
                        {{message}}
                    </div>
                    <div v-else-if="status == 'error'" class="alert alert-error">
                        {{message}}
                    </div>
                    <form v-on:submit.prevent="reset">
                        <div class="form-group" :class="errors.email ? 'has-error' : ''">
                            <label for="email" class="sr-only">メールアドレス</label>
                            <input type="email" class="form-control" id="email" v-model="email" placeholder="メールアドレス" required autofocus>
                            <span class="help-block" v-if="errors.email">
                                <ul>
                                    <li v-for="item in errors.email">
                                        {{ item }}
                                    </li>
                                </ul>
                            </span>
                        </div>

                        <div class="form-group" :class="errors.password ? 'has-error' : ''">
                            <label for="password" class="sr-only">パスワード</label>
                            <input type="password" class="form-control" id="password" v-model="password" placeholder="パスワード" required>
                            <span class="help-block" v-if="errors.email">
                                <ul>
                                    <li v-for="item in errors.password">
                                        {{ item }}
                                    </li>
                                </ul>
                            </span>
                        </div>

                        <div class="form-group" :class="errors.password_confirm ? 'has-error' : ''">
                            <label for="password_confirm" class="sr-only">パスワード(確認)</label>
                            <input type="password" class="form-control" id="password_confirm" v-model="password_confirm" placeholder="パスワード(確認)" required>
                            <span class="help-block" v-if="errors.email">
                                <ul>
                                    <li v-for="item in errors.password_confirm">
                                        {{ item }}
                                    </li>
                                </ul>
                            </span>
                        </div>

                        <button class="btn btn-primary btn-block" type="submit">パスワードをリセットする</button>
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
                email: null,
                password: null,
                password_confirm: null,
                status: null,
                message: null,
                errors: [],
            }
        },
        methods: {
            reset() {
                var _this = this

                _this.status = null
                _this.message = null
                _this.errors = []

                $('#resultLoading').css('visibility', 'visible');

                axios.post('/auth/reset',
                    {
                        email: this.email,
                        password: this.password,
                        password_confirm: this.password_confirm,
                        token: this.$route.params.token
                    })
                    .then(function (response) {
                        if(response.status === 200){
                            console.log("redirect")
                        }
                    })
                    .catch(function (error) {
                        $('#resultLoading').css('visibility', 'hidden');

                        _this.status = 'error'

                        if(error.response.status === 422){
                            // 422 - Validation Error
                            _this.message = '入力に問題があります。'

                            _this.errors = error.response.data
                        }else{
                            _this.message = 'エラーが発生しました。'
                        }

                    });
            }
        }
    }
</script>
<style>
</style>
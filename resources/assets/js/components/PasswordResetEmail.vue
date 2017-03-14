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
                    <form v-on:submit.prevent="resetEmail">
                        <div class="form-group" :class="errors.email ? 'has-error' : ''">
                            <label for="email" class="sr-only">メールアドレス</label>
                            <input type="email" class="form-control" v-model="email" id="email" placeholder="メールアドレス"
                                   required
                                   autofocus>
                            <span class="help-block" v-if="errors.email">
                                <ul>
                                    <li v-for="item in errors.email">
                                        {{ item }}
                                    </li>
                                </ul>
                            </span>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">パスワードリセットを行う</button>
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
                status: null,
                message: null,
                errors: [],
            }
        },
        methods: {
            resetEmail() {
                var _this = this

                _this.status = null
                _this.message = null
                _this.errors = []

                $('#resultLoading').css('visibility', 'visible');

                axios.post('/auth/resetEmail',
                    {
                        email: this.email,
                    })
                    .then(function (response) {
                        $('#resultLoading').css('visibility', 'hidden');

                        if(response.status === 200){
                            _this.status = response.data.status;
                            _this.message = response.data.message;

                            if(response.data.status === 'success'){
                                _this.email = ''
                            }
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
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
                        <div class="form-group">
                            <label for="email" class="sr-only">メールアドレス</label>
                            <input type="email" class="form-control" v-model="email" name="email" placeholder="メールアドレス"
                                   required
                                   autofocus>
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
            }
        },
        methods: {
            resetEmail() {
                var _this = this

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
                        }
                    })
                    .catch(function (error) {
                        $('#resultLoading').css('visibility', 'hidden');
                        // 422 - Validation Error
                        _this.status = 'error'
                        console.log(error);
                    });
            }
        }
    }
</script>
<style>
</style>
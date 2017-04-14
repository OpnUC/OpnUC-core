<template>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <form class="form-horizontal" v-on:submit.prevent="onSave">
                    <div class="box box-primary">
                        <div class="overlay" v-if="isLoading">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                パスワードの変更
                            </h3>
                        </div>
                        <div class="box-body">
                            <div v-if="status == 'success'" class="alert alert-success">
                                {{message}}
                            </div>
                            <div v-else-if="status == 'error'" class="alert alert-error">
                                {{message}}
                            </div>

                            <div class="form-group" :class="errors.password ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputPassword">現在のパスワード</label>
                                <div class="col-xs-7">
                                    <input type="password" class="form-control input-sm" id="inputPassword"
                                           placeholder="現在のパスワード" v-model="password">
                                    <span class="help-block" v-if="errors.password">
                                        <ul>
                                            <li v-for="item in errors.password">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.newPassword ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputNewPassword">新しいパスワード</label>
                                <div class="col-xs-7">
                                    <input type="password" class="form-control input-sm" id="inputNewPassword"
                                           placeholder="新しいパスワード" v-model="newPassword">
                                    <span class="help-block" v-if="errors.newPassword">
                                        <ul>
                                            <li v-for="item in errors.newPassword">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.newPassword_confirmation ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputNewPasswordConfirm">新しいパスワード(確認)</label>
                                <div class="col-xs-7">
                                    <input type="password" class="form-control input-sm" id="inputNewPasswordConfirm"
                                           placeholder="新しいパスワード(確認)" v-model="newPassword_confirmation">
                                    <span class="help-block" v-if="errors.newPassword_confirmation">
                                        <ul>
                                            <li v-for="item in errors.newPassword_confirmation">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary pull-right">保存</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<script>
    import Vue from 'vue'

    export default {
        data() {
            return {
                password: null,
                newPassword: null,
                newPassword_confirmation: null,
                // Validation
                status: null,
                message: null,
                errors: [],
                // 読み込み中かどうか
                isLoading: false,
            }
        },
        methods: {
            onSave(){
                var _this = this

                _this.isLoading = true

                // 初期化
                _this.status = null
                _this.message = null
                _this.errors = []

                // 編集処理
                axios.post('/user/passwordChange', {
                    password: _this.password,
                    newPassword: _this.newPassword,
                    newPassword_confirmation: _this.newPassword_confirmation,
                })
                    .then(function (response) {
                        _this.isLoading = false

                        _this.$message({
                            type: response.data.status,
                            message: response.data.message,
                        });
                    })
                    .catch(function (error) {
                        _this.isLoading = false
                        _this.status = 'error'

                        if (error.response.status === 422) {
                            // 422 - Validation Error
                            _this.message = '入力に問題があります。'

                            _this.errors = error.response.data
                        } else {
                            _this.message = 'エラーが発生しました。'
                        }
                    });
            },
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>

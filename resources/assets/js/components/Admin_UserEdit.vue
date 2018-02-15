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
                                ユーザ 追加・編集
                            </h3>
                        </div>
                        <div class="box-body" v-if="selectItem">
                            <div v-if="status == 'success'" class="alert alert-success">
                                {{message}}
                            </div>
                            <div v-else-if="status == 'error'" class="alert alert-error">
                                {{message}}
                            </div>

                            <div class="form-group" :class="errors.id ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputId">ユーザID</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputId"
                                           placeholder="ユーザID" readonly="readonly" v-model="selectItem.id">
                                    <span class="help-block" v-if="errors.id">
                                        <ul>
                                            <li v-for="item in errors.id" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.username ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputUsername">ユーザ名</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputUsername"
                                           placeholder="ユーザ名" v-model="selectItem.username">
                                    <span class="help-block" v-if="errors.username">
                                        <ul>
                                            <li v-for="item in errors.username" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.display_name ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputDisplayName">表示名</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputDisplayName"
                                           placeholder="表示名" v-model="selectItem.display_name">
                                    <span class="help-block" v-if="errors.display_name">
                                        <ul>
                                            <li v-for="item in errors.display_name" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.email ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputEmail">メールアドレス</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputEmail"
                                           placeholder="メールアドレス" v-model="selectItem.email">
                                    <span class="help-block" v-if="errors.email">
                                        <ul>
                                            <li v-for="item in errors.email" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.roles ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputRoles">所属ロール</label>
                                <div class="col-xs-7">
                                    <el-select id="inputRoles" v-model="selectItem.roles" multiple placeholder="所属ロール">
                                        <el-option
                                                v-for="role in roles"
                                                :key="role.id"
                                                :label="role.display_name"
                                                :value="role.id">
                                        </el-option>
                                    </el-select>
                                    <span class="help-block" v-if="errors.roles">
                                        <ul>
                                            <li v-for="item in errors.roles" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.password ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputPassword">パスワード</label>
                                <div class="col-xs-7">
                                    <input type="password" class="form-control input-sm" id="inputPassword"
                                           placeholder="パスワード" v-model="selectItem.password">
                                    <span class="help-block" v-if="selectItem.id">
                                        ※ パスワードを変更しない場合は、空欄としてください。
                                    </span>
                                    <span class="help-block" v-if="errors.password">
                                        <ul>
                                            <li v-for="item in errors.password">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.password_confirmation ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputPasswordConfirm">パスワード(確認)</label>
                                <div class="col-xs-7">
                                    <input type="password" class="form-control input-sm" id="inputPasswordConfirm"
                                           placeholder="パスワード(確認)" v-model="selectItem.password_confirmation">
                                    <span class="help-block" v-if="selectItem.id">
                                        ※ パスワードを変更しない場合は、空欄としてください。
                                    </span>
                                    <span class="help-block" v-if="errors.password_confirmation">
                                        <ul>
                                            <li v-for="item in errors.password_confirmation">
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
                selectItem: null,
                // Validation
                status: null,
                message: null,
                errors: [],
                // 読み込み中かどうか
                isLoading: true,
                roles: [],
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
                axios.post('/admin/userEdit', _this.selectItem)
                    .then(function (response) {
                        _this.isLoading = false

                        // 一覧ページに戻す
                        _this.$router.push('/admin/users')

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

                            _this.errors = error.response.data.errors
                        } else {
                            _this.message = 'エラーが発生しました。'
                        }
                    });
            },
        },
        mounted() {
            var _this = this

            _this.isLoading = true

            if (this.$route.params.id) {
                // Read
                axios.get('/admin/user', {
                    params: {
                        id: this.$route.params.id
                    }
                })
                    .then(function (response) {
                        _this.selectItem = response.data

                        _this.isLoading = false
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            } else {
                _this.selectItem = {
                    // selectは配列として初期化する必要あり
                    roles: [],
                }

                _this.isLoading = false
            }
        },
        created() {
            var _this = this
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)

            axios.get('/admin/roles')
                .then(function (response) {
                    _this.roles = response.data
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
</script>

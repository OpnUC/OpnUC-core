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
                                ロール 追加・編集
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
                                <label class="control-label col-xs-3" for="inputId">ロールID</label>
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

                            <div class="form-group" :class="errors.name ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputName">ロール名</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputName"
                                           placeholder="ロール名" v-model="selectItem.name">
                                    <span class="help-block" v-if="errors.name">
                                        <ul>
                                            <li v-for="item in errors.name" :key="item">
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

                            <div class="form-group" :class="errors.description ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputDescription">説明</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputDescription"
                                           placeholder="説明" v-model="selectItem.description">
                                    <span class="help-block" v-if="errors.description">
                                        <ul>
                                            <li v-for="item in errors.description" :key="item">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.perms ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputPerms">付与パーミッション</label>
                                <div class="col-xs-7">
                                    <el-select id="inputPerms" v-model="selectItem.perms" multiple placeholder="付与パーミッション">
                                        <el-option
                                                v-for="perm in perms"
                                                :key="perm.id"
                                                :label="perm.display_name"
                                                :value="perm.id">
                                        </el-option>
                                    </el-select>
                                    <span class="help-block" v-if="errors.perms">
                                        <ul>
                                            <li v-for="item in errors.perms" :key="item">
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
                perms: [],
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
                axios.post('/admin/roleEdit', _this.selectItem)
                    .then(function (response) {
                        _this.isLoading = false

                        // 一覧ページに戻す
                        _this.$router.push('/admin/roles')

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
                axios.get('/admin/role', {
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
                    prems: [],
                }

                _this.isLoading = false
            }
        },
        created() {
            var _this = this
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)

            axios.get('/admin/permissions', {
                params: {
                    per_page: 65535
                }
            })
                .then(function (response) {
                    _this.perms = response.data.data
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
</script>

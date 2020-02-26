<template>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="overlay" v-if="isLoading">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            アバター
                        </h3>
                    </div>
                    <div class="box-body box-profile" v-if="selectItem">
                        <img class="profile-user-img img-responsive img-circle"
                             v-bind:src="selectItem.avatar_path" alt="Avatar">

                        <h3 class="profile-username text-center">
                            {{ selectItem.display_name }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <form class="form-horizontal" v-on:submit.prevent="onSave">
                    <div class="box box-primary">
                        <div class="overlay" v-if="isLoading">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                基本情報
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
                                <label class="control-label col-xs-3" for="inputId">ログインID</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputId"
                                           placeholder="ログインID" readonly="readonly" v-model="selectItem.id">
                                    <span class="help-block" v-if="errors.id">
                                        <ul>
                                            <li v-for="item in errors.id">
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
                                           placeholder="ユーザ名" readonly="readonly" v-model="selectItem.username">
                                    <span class="help-block" v-if="errors.username">
                                        <ul>
                                            <li v-for="item in errors.username">
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
                                            <li v-for="item in errors.display_name">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.email ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputEmail">メールアドレス</label>
                                <div class="col-xs-7">
                                    <input type="email" class="form-control input-sm" id="inputEmail"
                                           placeholder="メールアドレス" v-model="selectItem.email">
                                    <span class="help-block" v-if="errors.email">
                                        <ul>
                                            <li v-for="item in errors.email">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.avatar_type ? 'has-error' : ''">
                                <label class="control-label col-xs-3">アバタータイプ</label>
                                <div class="col-xs-7">
                                    <input type="radio" name="avatar_type" id="inputAvatarType1" value="1"
                                           v-model="selectItem.avatar_type"/>
                                    <label for="inputAvatarType1">標準(アップロードした画像が優先されます)</label>
                                    <br/>
                                    <input type="radio" name="avatar_type" id="inputAvatarType2" value="2"
                                           v-model="selectItem.avatar_type"/>
                                    <label for="inputAvatarType2">Gravatarを利用</label>
                                    <span class="help-block" v-if="errors.avatar_type">
                                        <ul>
                                            <li v-for="item in errors.avatar_type">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.avatar_file ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputAvatarFile">アバター画像</label>
                                <div class="col-xs-7">
                                    <input type="file" id="inputAvatarFile" name="inputAvatarFile"
                                           v-on:change.prevent="onAvatarUpload"/>
                                    <span class="help-block">
                                        ※ 現在の画像から変更する場合のみ、選択してください。<br/>
                                        ※ 横 640ピクセル 縦 480ピクセル以下の<br/>
                                        jpeg/gif/pngファイルのみがアップロード出来ます。
                                    </span>
                                    <span class="help-block" v-if="errors.avatar_file">
                                        <ul>
                                            <li v-for="item in errors.avatar_file">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                    <button v-if="selectItem.avatar_filename" class="btn btn-default" v-on:click.prevent="onAvatarDelete">
                                        <i class="fa fa-times-circle"></i>
                                        アバター画像を削除する
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i>
                                保存
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>
<script>
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
            }
        },
        methods: {
            onAvatarUpload: function (e) {
                // アバター画像のアップロード

                if (e.target.files.length === 0) {
                    // ファイルが選択されていない場合は処理しない
                    return;
                }

                var _this = this

                _this.isLoading = true

                // 初期化
                _this.status = null
                _this.message = null
                _this.errors = []

                var formData = new FormData();
                formData.append('avatar_file', e.target.files[0]);

                axios.post('/user/uploadAvatar',
                    formData, {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    })
                    .then(function (response) {
                        _this.isLoading = false

                        _this.selectItem.avatar_path = response.data.path

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
                            _this.$message({
                                type: error.response.data.status,
                                message: error.response.data.message,
                            });
                        }
                    })
            },
            onAvatarDelete() {
                // アバター画像の削除
                var _this = this

                this.$confirm('アバター画像を削除してもよろしいですか？', '確認', {
                    confirmButtonText: '削除',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    _this.isLoading = true

                    axios.post('/user/deleteAvatar')
                        .then(function (response) {
                            _this.isLoading = false

                            _this.selectItem.avatar_path = response.data.path
                            // 削除ボタンを非表示にするため、nullを代入
                            _this.selectItem.avatar_filename = null

                            _this.$message({
                                type: response.data.status,
                                message: response.data.message,
                            });
                        })
                        .catch(function (error) {
                            _this.isLoading = false
                            _this.status = 'error'

                            _this.$message({
                                type: error.response.data.status,
                                message: error.response.data.message,
                            });
                        })
                }).catch(function (error) {
                    console.log(error.message);
                });
            },
            onSave(){
                var _this = this

                _this.isLoading = true

                // 初期化
                _this.status = null
                _this.message = null
                _this.errors = []

                // 編集処理
                axios.post('/user/edit', _this.selectItem)
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

            axios.get('/auth/user')
                .then(function (response) {
                    _this.selectItem = response.data.data

                    _this.isLoading = false
                })
                .catch(function (error) {
                    console.log(error);

                    _this.isLoading = false
                });
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
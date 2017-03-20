<template>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <form class="form-horizontal" v-if="selectItem" v-on:submit.prevent="onSave">
                    <div class="box box-primary">
                        <div id="resultLoading" style="visibility: hidden;" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                連絡先 追加・編集
                            </h3>
                        </div>
                        <div class="box-body">
                            <div v-if="status == 'success'" class="alert alert-success">
                                {{message}}
                            </div>
                            <div v-else-if="status == 'error'" class="alert alert-error">
                                {{message}}
                            </div>

                            <div class="form-group" :class="errors.id ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputId">アドレス帳ID</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputId"
                                           placeholder="アドレス帳ID" readonly="readonly" v-model="selectItem.id">
                                    <span class="help-block" v-if="errors.id">
                                        <ul>
                                            <li v-for="item in errors.id">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.position ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputPosition">役職</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputPosition"
                                           placeholder="役職" v-model="selectItem.position">
                                    <span class="help-block" v-if="errors.position">
                                        <ul>
                                            <li v-for="item in errors.position">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.name_kana ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputNameKana">名前(カナ)</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputNameKana"
                                           placeholder="名前(カナ)" v-model="selectItem.name_kana">
                                    <span class="help-block" v-if="errors.name_kana">
                                        <ul>
                                            <li v-for="item in errors.name_kana">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.name ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputName">名前</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputName"
                                           placeholder="名前" v-model="selectItem.name">
                                    <span class="help-block" v-if="errors.name">
                                        <ul>
                                            <li v-for="item in errors.name">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.type ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputType">電話帳種別</label>
                                <div class="col-xs-7">
                                    <select class="form-control input-sm" id="inputType" v-model="selectItem.type">
                                        <option v-for="(value, key) in addressBookType" v-bind:value="key">
                                            {{ value }}
                                        </option>
                                    </select>
                                    <span class="help-block" v-if="errors.type">
                                        <ul>
                                            <li v-for="item in errors.type">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.groupid ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputGroup">所属グループ</label>
                                <div class="col-xs-7">
                                    <select class="form-control input-sm" id="inputGroup"
                                            v-model="selectItem.groupid">
                                        <option v-for="item in addressBookGroup[selectItem.type]"
                                                v-bind:value="item.id">
                                            {{ item.full_group_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" v-if="errors.groupid">
                                        <ul>
                                            <li v-for="item in errors.groupid">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.tel1 ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputTel1">電話番号1</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputTel1"
                                           placeholder="電話番号1" v-model="selectItem.tel1">
                                    <span class="help-block" v-if="errors.tel1">
                                        <ul>
                                            <li v-for="item in errors.tel1">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.tel2 ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputTel2">電話番号2</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputTel2"
                                           placeholder="電話番号2" v-model="selectItem.tel2">
                                    <span class="help-block" v-if="errors.tel2">
                                        <ul>
                                            <li v-for="item in errors.tel2">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.tel3 ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputTel3">電話番号3</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputTel3"
                                           placeholder="電話番号3" v-model="selectItem.tel3">
                                    <span class="help-block" v-if="errors.tel3">
                                        <ul>
                                            <li v-for="item in errors.tel3">
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

                            <div class="form-group" :class="errors.comment ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputComment">備考</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputComment"
                                           placeholder="備考" v-model="selectItem.comment">
                                    <span class="help-block" v-if="errors.comment">
                                        <ul>
                                            <li v-for="item in errors.comment">
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
                status: null,
                message: null,
                errors: [],
                selectItem: null,
                addressBookType: {
                    1: '内線電話帳',
                    2: '共通電話帳',
                    //9 : '個人電話帳',
                },
                addressBookGroup: {},
            }
        },
        methods: {
            onSave(){
                var _this = this

                // 編集処理
                axios.post('/addressbook/edit', _this.selectItem)
                    .then(function (response) {
                        _this.$message({
                            type: response.data.status,
                            message: response.data.message,
                        });
                    })
                    .catch(function (error) {
                        _this.status = 'error'

                        if (error.response.status === 422) {
                            // 422 - Validation Error
                            _this.message = '入力に問題があります。'

                            _this.errors = error.response.data
                        } else {
                            _this.message = 'エラーが発生しました。'
                        }
                    });
            }
        },
        mounted() {
            $('#resultLoading').css('visibility', 'visible');

            var _this = this

            if (this.$route.params.id) {
                // Read
                axios.get('/addressbook/detail', {
                    params: {
                        id: this.$route.params.id
                    }
                })
                    .then(function (response) {
                        _this.selectItem = response.data

                        $('#resultLoading').css('visibility', 'hidden');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            } else {
                _this.selectItem = {
                    type: 1
                }
                $('#resultLoading').css('visibility', 'hidden');
            }

            axios.get('/addressbook/groups')
                .then(function (response) {
                    _this.addressBookGroup = response.data
                    console.log(response)
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
<style>
</style>
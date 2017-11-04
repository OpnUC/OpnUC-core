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
                                グループ 追加・編集
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
                                <label class="control-label col-xs-3" for="inputId">グループID</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputId"
                                           placeholder="グループID" readonly="readonly" v-model="selectItem.id">
                                    <span class="help-block" v-if="errors.id">
                                        <ul>
                                            <li v-for="item in errors.id">
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

                            <div class="form-group" :class="errors.parent_groupid ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputParentGroup">親グループ</label>
                                <div class="col-xs-7">
                                    <select class="form-control input-sm" id="inputParentGroup"
                                            v-model="selectItem.parent_groupid">
                                        <option value="0">
                                            親グループ
                                        </option>
                                        <option v-for="item in addressBookGroup[selectItem.type]"
                                                :disabled="selectItem.id === item.id"
                                                v-bind:value="item.id" >
                                            {{ item.full_group_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" v-if="errors.parent_groupid">
                                        <ul>
                                            <li v-for="item in errors.parent_groupid">
                                                {{ item }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="errors.group_name ? 'has-error' : ''">
                                <label class="control-label col-xs-3" for="inputGroupName">グループ名</label>
                                <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" id="inputGroupName"
                                           placeholder="グループ名" v-model="selectItem.group_name">
                                    <span class="help-block" v-if="errors.group_name">
                                        <ul>
                                            <li v-for="item in errors.group_name">
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
                // ページ上のデータ
                addressBookType: [],
                addressBookGroup: [],
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
                axios.post('/addressbook/groupEdit', _this.selectItem)
                    .then(function (response) {
                        _this.onLoadGroup()

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
            onLoadGroup(){
                var _this = this

                // すべてのグループ情報が欲しいため、自前で取得する
                axios.get('/addressbook/groupList',{
                    params: {
                        isAll: true
                    }
                })
                    .then(function (response) {
                        _this.addressBookGroup = response.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        mounted() {
            var _this = this

            _this.isLoading = true

            if (this.$route.params.id) {
                // Read
                axios.get('/addressbook/group', {
                    params: {
                        groupId: this.$route.params.id
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
                    type: 1
                }

                _this.isLoading = false
            }
        },
        created() {
            var _this = this

            this.addressBookType = this.$parent.$data.addressBookType
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)

            this.onLoadGroup()
        },
    }
</script>

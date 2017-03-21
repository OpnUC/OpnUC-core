<template>
    <section class="content">
        <div class="box box-primary">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    グループ管理
                </h3>
            </div>
            <div class="box-body">
                <div v-if="status == 'success'" class="alert alert-success">
                    {{message}}
                </div>
                <div v-else-if="status == 'error'" class="alert alert-error">
                    {{message}}
                </div>

                <a href="" class="btn btn-default">
                    <i class="fa fa-plus"></i>
                    グループの追加
                </a>
                <p/>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li :class="{ active : typeId == 1 }" v-for="(typeName, typeId) in addressBookType">
                            <a :href="'#tab_' + typeId" data-toggle="tab" aria-expanded="true">{{ typeName }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" :class="{ active : typeId == 1 }" :id="'tab_' + typeId"
                             v-for="(typeName, typeId) in addressBookType">
                            <table class="table table-condensed table-striped tree">
                                <thead>
                                <tr>
                                    <th>グループ名</th>
                                    <th width="200">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr :class="'treegrid-' + groupIndex" v-for="(groupItem, groupIndex) in addressBookGroups[typeId]">
                                        <td>{{groupItem.Name}}</td>
                                        <td>
                                            <a href="" class="btn btn-default btn-xs">
                                                <i class="fa fa-edit"></i>
                                                編集
                                            </a>
                                            <button type="button" class="btn btn-default btn-xs" >
                                                <i class="fa fa-times"></i>
                                                削除
                                            </button>
                                        </td>
                                    </tr>
                                    <group-list :groupItem="groupItem" :typeId="typeId"></group-list>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    import Vue from 'vue'

    import AddressBookGroup_GroupList from './AddressBookGroup_GroupList.vue'

    Vue.component('group-list',
        AddressBookGroup_GroupList
    );

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
                addressBookGroups: [],
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
                axios.post('/addressbook/edit', _this.selectItem)
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
            }
        },
        mounted() {
            var _this = this

            _this.isLoading = false
        },
        created() {
            this.addressBookType = this.$parent.$data.addressBookType
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)

            var _this = this

            $.each(this.addressBookType, function (typeId, val) {
                axios.get('/addressbook/groups', {
                    params: {
                        typeId: typeId
                    }
                })
                    .then(function (response) {
                        Vue.set(_this.addressBookGroups, typeId, response.data)
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        },
    }
</script>
<style>
</style>
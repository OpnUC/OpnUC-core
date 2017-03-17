<template>
    <section class="content">
        <div class="box box-primary">
            <div id="resultLoading" style="visibility: hidden;" class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    電話帳一覧
                    <span style="padding-left: 10px; color:gray; font-size:75%">
                        {{ addressBookType[moreParams.typeId] }} > {{ groupName }}
                        <span v-if="isSearch">
                            > 検索結果
                        </span>
                    </span>
                </h3>
            </div>
            <div class="box-body">
                <vuetable class="table table-striped"
                          ref="vuetable"
                          api-url="/addressbook/search"
                          :css="css"
                          :fields="fields"
                          :sort-order="sortOrder"
                          :append-params="moreParams"
                          detail-row-id="id"
                          @vuetable:pagination-data="onPaginationData"
                          pagination-path=""></vuetable>
                <div class="vuetable-pagination ui basic segment grid">
                    <vuetable-pagination-info ref="paginationInfo"
                                              info-class="pull-left">
                    </vuetable-pagination-info>
                    <vuetable-pagination ref="pagination"
                                         :css="cssPagination"
                                         :icons="icons"
                                         @vuetable-pagination:change-page="onChangePage">
                    </vuetable-pagination>
                </div>
            </div>
        </div>
        <el-dialog title="詳細" v-model="detailDialog.visible">
            <table class="table table-bordered table-striped" v-if="detailDialog.selectItem != null">
                <tbody>
                <tr>
                    <th width="150">
                        アドレス帳ID
                    </th>
                    <td>
                        {{ detailDialog.selectItem.id }}
                    </td>
                </tr>
                <tr>
                    <th>
                        役職
                    </th>
                    <td>
                        {{ detailDialog.selectItem.position }}
                    </td>
                </tr>
                <tr>
                    <th>
                        名前
                    </th>
                    <td>
                        <small>({{ detailDialog.selectItem.name_kana }})</small>
                        </br>
                        {{ detailDialog.selectItem.name }}
                    </td>
                </tr>
                <tr>
                    <th>
                        電話帳種別
                    </th>
                    <td>
                        {{ addressBookType[detailDialog.selectItem.type] }}
                    </td>
                </tr>
                <tr>
                    <th>
                        所属グループ
                    </th>
                    <td>
                        {{ detailDialog.selectItem.group_name }}
                    </td>
                </tr>
                <tr>
                    <th>
                        電話番号1
                    </th>
                    <td>
                        <a :href="`tel:${detailDialog.selectItem.tel1}`" v-if="detailDialog.selectItem.tel1">{{
                            detailDialog.selectItem.tel1 }}</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        電話番号2
                    </th>
                    <td>
                        <a :href="`tel:${detailDialog.selectItem.tel2}`" v-if="detailDialog.selectItem.tel2">{{
                            detailDialog.selectItem.tel2 }}</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        電話番号3
                    </th>
                    <td>
                        <a :href="`tel:${detailDialog.selectItem.tel3}`" v-if="detailDialog.selectItem.tel3">{{
                            detailDialog.selectItem.tel3 }}</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        メールアドレス
                    </th>
                    <td>
                        <a :href="`mailto:${detailDialog.selectItem.email}`" v-if="detailDialog.selectItem.email">{{
                            detailDialog.selectItem.email }}</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        備考
                    </th>
                    <td>
                        {{ detailDialog.selectItem.comment }}
                    </td>
                </tr>
                </tbody>
            </table>
            <span slot="footer" class="dialog-footer">
                <button class="btn btn-default" v-on:click="detailDialog.visible = false">閉じる</button>
             </span>
        </el-dialog>

        <el-dialog title="追加・編集" v-model="editDialog.visible" v-if="editDialog.selectItem != null">
            <div v-if="status == 'success'" class="alert alert-success">
                {{message}}
            </div>
            <div v-else-if="status == 'error'" class="alert alert-error">
                {{message}}
            </div>
            <form class="form-horizontal">
                <div class="form-group" :class="errors.id ? 'has-error' : ''">
                    <label class="control-label col-xs-3" for="inputId">アドレス帳ID</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control input-sm" id="inputId"
                               placeholder="アドレス帳ID" readonly="readonly" v-model="editDialog.selectItem.id">
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
                               placeholder="役職" v-model="editDialog.selectItem.position">
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
                               placeholder="名前(カナ)" v-model="editDialog.selectItem.name_kana">
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
                               placeholder="名前" v-model="editDialog.selectItem.name">
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
                        <select class="form-control input-sm" id="inputType" v-model="editDialog.selectItem.type">
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
                                v-model="editDialog.selectItem.groupid">
                            <option v-for="item in addressBookGroup[editDialog.selectItem.type]"
                                    v-bind:value="item.key">
                                {{ item.value }}
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
                               placeholder="電話番号1" v-model="editDialog.selectItem.tel1">
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
                               placeholder="電話番号2" v-model="editDialog.selectItem.tel2">
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
                               placeholder="電話番号3" v-model="editDialog.selectItem.tel3">
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
                               placeholder="メールアドレス" v-model="editDialog.selectItem.email">
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
                               placeholder="備考" v-model="editDialog.selectItem.comment">
                        <span class="help-block" v-if="errors.comment">
                            <ul>
                                <li v-for="item in errors.comment">
                                    {{ item }}
                                </li>
                            </ul>
                        </span>
                    </div>
                </div>
            </form>

            <span slot="footer" class="dialog-footer">
                <button class="btn btn-default" v-on:click="editDialog.visible = false">キャンセル</button>
                <button class="btn btn-primary" v-on:click="onEditDialogCallback">保存</button>
            </span>
        </el-dialog>
    </section>
</template>
<script>
    import Vue from 'vue'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import columnAvatar from './AddressBook_ColumnAvatar.vue'
    import columnName from './AddressBook_ColumnName.vue'
    import columnContact from './AddressBook_ColumnContact.vue'
    import columnAction from './AddressBook_ColumnAction.vue'

    Vue.component('columnAvatar', columnAvatar)
    Vue.component('columnName', columnName)
    Vue.component('columnContact', columnContact)
    Vue.component('columnAction', columnAction)

    export default {
        data() {
            return {
                status: null,
                message: null,
                errors: [],
                detailDialog: {
                    visible: false,
                    selectItem: null,
                },
                editDialog: {
                    visible: false,
                    selectItem: null,
                    callback: null,
                },
                addressBookType: {
                    1: '内線電話帳',
                    2: '共通電話帳',
                    //9 : '個人電話帳',
                },
                addressBookGroup: {},
                sortOrder: [
                    {
                        field: '__component:columnName',
                        sortField: 'name_kana',
                        direction: 'asc'
                    }
                ],
                css: {
                    tableClass: 'table table-striped table-bordered',
                    loadingClass: 'loading',
                    ascendingIcon: 'glyphicon glyphicon-chevron-up',
                    descendingIcon: 'glyphicon glyphicon-chevron-down',
                    sortHandleIcon: 'glyphicon glyphicon-menu-hamburger',
                },
                cssPagination: {
                    wrapperClass: 'pagination pull-right',
                    activeClass: 'btn-primary',
                    disabledClass: 'disabled',
                    pageClass: 'btn btn-border',
                    linkClass: 'btn btn-border',
                },
                icons: {
                    first: '',
                    prev: '',
                    next: '',
                    last: '',
                },
                fields: [
                    {
                        name: '__component:columnAvatar',
                        title: '',
                        titleClass: 'columnAvatar',
                        dataClass: 'text-center',
                    },
                    {
                        name: '__component:columnName',
                        title: '役職/名前',
                        sortField: 'name_kana',
                        titleClass: 'columnName',
                    },
                    {
                        name: '__component:columnContact',
                        title: '連絡先',
                        sortField: 'tel1',
                        titleClass: 'columnContact',
                    },
                    {
                        name: 'comment',
                        title: '備考',
                    },
                    {
                        name: '__component:columnAction',
                        title: '操作',
                        titleClass: 'columnAction',
                    },
                ],
                moreParams: {
                    typeId: 1,
                    groupId: 0,
                    keyword: '',
                },
                isSearch: false,
                groupName: 'すべてを表示',
            }
        },
        components: {
            Vuetable,
            VuetablePagination,
            VuetablePaginationInfo
        },
        methods: {
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page)
            },
            onSearch(){
                this.isSearch = this.moreParams.keyword ? true : false;

                this.$refs.vuetable.refresh()
            },
            onEditDialogCallback(){
                // Callbackを実行
                if (this.editDialog.callback) {
                    this.editDialog.callback();
                }
            },
            regEvent(){
                this.$refs.vuetable.$on('vuetable:loading', () => {
                    $('#resultLoading').css('visibility', 'visible');
                })
                this.$refs.vuetable.$on('vuetable:loaded', () => {
                    $('#resultLoading').css('visibility', 'hidden');
                })
            },
        },
        mounted() {
            var _this = this
            this.regEvent();

            $.each(this.addressBookType, function (index, val) {
                axios.get('/addressbook/groups', {
                    params: {
                        typeId: index
                    }
                })
                    .then(function (response) {
                        var items = [];

                        // 再帰処理のため、匿名関数を作成
                        var buildGroupList = function (group, parentGroupName = null) {
                            $.each(group, function (index, val) {
                                if (val.Child) {
                                    // 子供がある場合は再帰処理
                                    buildGroupList(val.Child, val.Name)
                                } else {
                                    // 末端グループの場合は表示する
                                    if (parentGroupName) {
                                        items.push({
                                            key: val.Id,
                                            value: parentGroupName + ' > ' + val.Name
                                        })
                                    } else {
                                        items.push({
                                            key: val.Id,
                                            value: val.Name
                                        })
                                    }
                                }
                            })
                        }

                        // 1度目の処理
                        buildGroupList(response.data);

                        // グループ名でソート
                        items.sort(function (a, b) {
                            return (a.value < b.value) ? -1 : 1;
                        });

                        Vue.set(_this.addressBookGroup, index, items)
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
        events: {
            // 詳細の表示(ColumNameからのイベント)
            'AddressBook:showDetail': function (item) {
                this.detailDialog.visible = true
                this.detailDialog.selectItem = item
            },
            // 編集(ColumnActionからのイベント)
            'AddressBook:edit': function (item) {
                var _this = this

                _this.status = null
                _this.message = null
                _this.errors = []

                // itemがundefinedの場合は新規作成
                if (typeof item === "undefined") {
                    // typeを設定しないと、selectが動かないため、初期値設定
                    item = {
                        type : 1
                    }
                }

                _this.editDialog.selectItem = $.extend(true, {}, item);

                this.editDialog.callback = function () {
                    // 編集処理
                    axios.post('/addressbook/edit', _this.editDialog.selectItem)
                        .then(function (response) {
                            _this.$message({
                                type: response.data.status,
                                message: response.data.message,
                            });

                            _this.editDialog.visible = false

                            _this.$refs.vuetable.refresh()
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

                this.editDialog.visible = true
            },
            // 削除(ColumnActionからのイベント)
            'AddressBook:delete': function (item) {
                var _this = this

                this.$confirm('選択された連絡先を削除しても良いですか？', '確認', {
                    confirmButtonText: '削除',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    axios.post('/addressbook/delete',
                        {
                            id: item.id
                        })
                        .then(function (response) {
                            _this.$message({
                                type: 'success',
                                message: '削除が完了しました。'
                            });

                            _this.$refs.vuetable.refresh()
                        })
                        .catch(function (error) {
                            _this.$message({
                                type: 'error',
                                message: '削除に失敗しました。'
                            });
                        });
                }).catch(function (error) {
                    console.log(error.message);
                });
            },
            // 検索(Sidebarからのイベント)
            'AddressBook:search': function (keyword, typeId, groupId, groupName) {
                if (typeof typeId != "undefined") {
                    this.moreParams.typeId = typeId;
                }
                if (typeof groupId != "undefined") {
                    this.moreParams.groupId = groupId;
                }
                if (typeof groupName != "undefined") {
                    this.groupName = groupName
                }

                this.moreParams.keyword = keyword;

                this.onSearch();
            },
        }
    }
</script>
<style>
    .pagination {
        margin-top: 0;
    }

    .btn.btn-border {
        border: 1px solid;
        margin-right: 2px;
    }

    .vuetable-pagination-info {
        margin-top: 8px !important;
    }

    .vuetable th.columnAvatar {
        width: 100px;
    }

    .vuetable th.columnName {
        width: 250px;
    }

    .vuetable th.columnContact {
        width: 300px;
    }

    .vuetable th.columnAction {
        width: 150px;
    }

    /* 内線プレゼンス */
    i.extStatus::after {
        padding-left: 3px;
        font-size: 90%;
        color: #333;
        content: attr(title);
    }
</style>
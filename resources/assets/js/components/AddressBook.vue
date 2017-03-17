<template>
    <section class="content">
        <div class="box box-primary">
            <div id="resultLoading" style="visibility: visible;" class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    電話帳一覧
                    <span style="padding-left: 10px; color:gray; font-size:75%">
                        {{ typeName }} > {{ groupName }}
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
                          :append-params="searchParam"
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
                detailDialog: {
                    visible: false,
                    selectItem: null,
                },
                addressBookType: {
                    1: '内線電話帳',
                    2: '共通電話帳',
                    //9 : '個人電話帳',
                },
                addressBookGroups: null,
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
                isSearch: false,
                typeName: null,
                groupId: null,
                groupName: 'すべてを表示',
                searchParam: {
                    typeId: null,
                    groupId: null,
                    keyword: null,
                },
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
            regEvent(){
                this.$refs.vuetable.$on('vuetable:loading', () => {
                    $('#resultLoading').css('visibility', 'visible');
                })
                this.$refs.vuetable.$on('vuetable:loaded', () => {
                    $('#resultLoading').css('visibility', 'hidden');
                })
            },
        },
        watch: {
            '$route' (to, from) {
                console.log(to)
                if(to.params.groupId){
                    this.groupId = to.params.groupId
                }
            },
            groupId: function () {
                // グループIDが変更された場合
                var _this = this

                var setGroupName = function(){
                    if(_this.addressBookGroups[1][_this.groupId]){
                        _this.searchParam.groupId = _this.groupId
                        _this.searchParam.typeId = 1

                        _this.groupName = _this.addressBookGroups[_this.searchParam.typeId][_this.searchParam.groupId].full_group_name
                        _this.typeName = _this.addressBookType[_this.searchParam.typeId]
                        _this.$refs.vuetable.refresh()
                    }
                }

                // 初回はサーバから取得する
                if(!this.addressBookGroups){
                    axios.get('/addressbook/groups')
                        .then(function (response) {
                            _this.addressBookGroups = response.data

                            setGroupName()
                        })
                        .catch(function (error) {
                            console.log(error)
                        });
                }else{
                    setGroupName()
                }
            }
        },
        mounted() {
            this.regEvent();
            this.groupId = this.$route.params.groupId
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
            'AddressBook:search': function (keyword) {
                this.searchParam.keyword = keyword;

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
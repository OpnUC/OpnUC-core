<template>
    <section class="content">
        <div class="box box-primary">
            <div class="overlay" v-if="isLoading">
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
                // Vuetableのパラメタ
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
                searchParam: {
                    typeId: null,
                    groupId: null,
                    keyword: null,
                },
                // ここまで：Vuetableのパラメタ
                detailDialog: {
                    visible: false,
                    selectItem: null,
                },
                // ページデータ
                addressBookType: [],
                addressBookGroup: [],
                // 検索中かどうか
                isSearch: false,
                // 読み込み中かどうか
                isLoading: true,
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
                this.isSearch = this.searchParam.keyword ? true : false;
                this.$refs.vuetable.refresh()
            },
            regEvent(){
                var _this = this
                this.$refs.vuetable.$on('vuetable:loading', () => {
                    _this.isLoading = true
                })
                this.$refs.vuetable.$on('vuetable:loaded', () => {
                    _this.isLoading = false
                })
            },
            updateSearchParam(){
                // パラメタ判断
                if (this.$route.query.groupId) {
                    // グループIDが設定されているとき
                    this.searchParam.typeId = this.$route.query.typeId
                    this.searchParam.groupId = this.$route.query.groupId
                } else if (this.$route.query.typeId) {
                    this.searchParam.typeId = this.$route.query.typeId
                    this.searchParam.groupId = 0
                } else {
                    this.searchParam.typeId = 1
                    this.searchParam.groupId = 0
                }

                this.searchParam.keyword = this.$route.query.keyword

                this.onSearch()
            },
        },
        watch: {
            '$route' (to, from) {
                this.updateSearchParam()
            },
        },
        mounted() {
            this.regEvent()

            this.updateSearchParam()
        },
        created() {
            this.addressBookType = this.$parent.$data.addressBookType
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
        computed: {
            // 種別名
            typeName: function () {
                return this.addressBookType[this.searchParam.typeId]
            },
            // グループ名
            groupName: function () {
                if (this.searchParam.groupId === 0) {
                    return 'すべてを表示'
                } else if (_.get(this.addressBookGroup, this.searchParam.typeId + '.' + this.searchParam.groupId)) {
                    return this.addressBookGroup[this.searchParam.typeId][this.searchParam.groupId].full_group_name
                }
            },
        },
        events: {
             // グループ情報が更新された場合
            'AddressBook:updateGroup': function (group) {
                this.addressBookGroup = group
            },
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
</style>
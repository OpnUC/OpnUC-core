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
                <div class="form-inline pull-right">
                    <label>
                        1ページの件数：
                        <select class="form-control" v-model="perPage">
                            <option v-for="n in [10,30,50,100]" :value="n">
                                {{ n }}
                            </option>
                        </select>
                    </label>
                </div>
                <vuetable class="table table-striped"
                          ref="vuetable"
                          api-url="/addressbook/search"
                          :css="css"
                          :fields="fields"
                          :sort-order="sortOrder"
                          :append-params="searchParam"
                          detail-row-id="id"
                          :per-page="perPage"
                          @vuetable:pagination-data="onPaginationData"
                          pagination-path="">
                    <template slot="avatar" scope="props">
                        <div class="image">
                            <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&amp;s=60"
                                 class="img-circle"
                                 alt="User Image">
                        </div>
                    </template>
                    <template slot="name" scope="props">
                        <div>
                            <div v-if="props.rowData.position">
                                <small>{{ props.rowData.position }}</small>
                            </div>
                            <a href="" v-on:click.prevent="showDetail(props.rowData)" :title="props.rowData.name_kana">{{
                                props.rowData.name }}</a>
                        </div>
                    </template>
                    <template slot="contact" scope="props">
                        <div>
                            <tel-contact :number="props.rowData.tel1" :status="props.rowData.tel1_status">
                            </tel-contact>
                            <tel-contact :number="props.rowData.tel2" :status="props.rowData.tel2_status">
                            </tel-contact>
                            <tel-contact :number="props.rowData.tel3" :status="props.rowData.tel3_status">
                            </tel-contact>
                            <div v-if="props.rowData.email">
                                <i class="fa fa-envelope"></i> <a :href="`mailto:${props.rowData.email}`">{{
                                props.rowData.email }}</a>
                            </div>
                        </div>
                    </template>
                    <template slot="actions" scope="props">
                        <div>
                            <router-link v-if="$auth.check('addressbook-admin')"
                                         :to="{ name: 'AddressBookEdit', params: { id: props.rowData.id }}"
                                         class="btn btn-default btn-xs">
                                <i class="fa fa-edit"></i> 編集
                            </router-link>
                            <button v-if="$auth.check('addressbook-admin')" type="button" class="btn btn-default btn-xs"
                                    v-on:click.prevent="onDelete(props.rowData)">
                                <i class="fa fa-times"></i> 削除
                            </button>
                        </div>
                    </template>
                </vuetable>
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
                        <tel-contact :number="detailDialog.selectItem.tel1"
                                     :status="detailDialog.selectItem.tel1_status">
                        </tel-contact>
                    </td>
                </tr>
                <tr>
                    <th>
                        電話番号2
                    </th>
                    <td>
                        <tel-contact :number="detailDialog.selectItem.tel2"
                                     :status="detailDialog.selectItem.tel2_status">
                        </tel-contact>
                    </td>
                </tr>
                <tr>
                    <th>
                        電話番号3
                    </th>
                    <td>
                        <tel-contact :number="detailDialog.selectItem.tel3"
                                     :status="detailDialog.selectItem.tel3_status">
                        </tel-contact>
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

    export default {
        data() {
            return {
                perPage: 10,
                // Vuetableのパラメタ
                sortOrder: [
                    {
                        field: '__slot:name',
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
                        name: '__slot:avatar',
                        title: '',
                        titleClass: 'columnAvatar',
                        dataClass: 'text-center',
                    },
                    {
                        name: '__slot:name',
                        title: '役職/名前',
                        sortField: 'name_kana',
                        titleClass: 'columnName',
                    },
                    {
                        name: '__slot:contact',
                        title: '連絡先',
                        sortField: 'tel1',
                        titleClass: 'columnContact',
                    },
                    {
                        name: 'comment',
                        title: '備考',
                    },
                    {
                        name: '__slot:actions',
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
            // 詳細の表示
            showDetail(item) {
                this.detailDialog.visible = true
                this.detailDialog.selectItem = item
            },
            // 削除
            onDelete(item) {
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
            perPage: function () {
                this.$nextTick(function () {
                    this.$refs.vuetable.refresh()
                })
            }
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
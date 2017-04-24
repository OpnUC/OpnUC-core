<template>
    <section class="content">
        <div class="box box-primary">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    ユーザ管理
                </h3>
            </div>
            <div class="box-body">
                <div class="pull-left">
                    <router-link :to="{ name: 'AdminUserEdit'}" class="btn btn-default">
                        <i class="fa fa-plus"></i>
                        ユーザの追加
                    </router-link>
                </div>
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
                          api-url="/admin/users"
                          :css="css"
                          :fields="fields"
                          :sort-order="sortOrder"
                          detail-row-id="id"
                          :per-page="perPage"
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
    </section>
</template>

<script>

    import Vue from 'vue'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import columnAction from './Admin_Users_ColumnAction.vue'
    import columnRole from './Admin_Users_ColumnRole.vue'

    Vue.component('columnAction', columnAction)
    Vue.component('columnRole', columnRole)

    export default {
        data() {
            return {
                perPage: 10,
                // Vuetableのパラメタ
                sortOrder: [
                    {
                        field: 'id',
                        sortField: 'id',
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
                        name: 'id',
                        title: '#',
                        sortField: 'id',
                        titleClass: 'columnId',
                    },
                    {
                        name: 'username',
                        title: 'ユーザ名',
                        sortField: 'username',
                        titleClass: 'columnUsername',
                    },
                    {
                        name: 'display_name',
                        title: '表示名',
                        sortField: 'display_name',
                    },
                    {
                        name: '__component:columnRole',
                        title: 'ロール',
                        titleClass: 'columnRole',
                    },
                    {
                        name: '__component:columnAction',
                        title: '操作',
                        titleClass: 'columnAction',
                    },
                ],
                // ここまで：Vuetableのパラメタ
                detailDialog: {
                    visible: false,
                    selectItem: null,
                },
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
            regEvent(){
                var _this = this
                this.$refs.vuetable.$on('vuetable:loading', () => {
                    _this.isLoading = true
                })
                this.$refs.vuetable.$on('vuetable:loaded', () => {
                    _this.isLoading = false
                })
            },
        },
        watch: {
            perPage: function () {
                this.$nextTick(function () {
                    this.$refs.vuetable.refresh()
                })
            }
        },
        mounted() {
            this.regEvent()
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
        events: {
            // 詳細の表示(ColumNameからのイベント)
            'AdminUser:showDetail': function (item) {
                this.detailDialog.visible = true
                this.detailDialog.selectItem = item
            },
            // 削除(ColumnActionからのイベント)
            'AdminUser:delete': function (item) {
                var _this = this

                this.$confirm('選択されたユーザを削除しても良いですか？', '確認', {
                    confirmButtonText: '削除',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    axios.post('/admin/userDelete',
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

    .vuetable th.columnId {
        width: 50px;
    }

    .vuetable th.columnUsername {
        width: 150px;
    }

    .vuetable th.columnRole {
        width: 200px;
    }

    .vuetable th.columnAction {
        width: 150px;
    }
</style>
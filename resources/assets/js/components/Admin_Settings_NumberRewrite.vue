<template>
    <section class="content">
        <div class="box box-primary" id="tableScrollTop">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    番号変換パターン
                </h3>
            </div>
            <div class="box-body">
                <div class="pull-left">
                    <router-link :to="{ name: 'AdminSettings_NumberRewriteEdit'}" class="btn btn-default">
                        <i class="fa fa-plus"></i>
                        パターンの追加
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
                          api-url="/admin/settingNumberRewrites"
                          :http-fetch="onVuetableHttpFetch"
                          :css="css"
                          :fields="fields"
                          :sort-order="sortOrder"
                          :per-page="perPage"
                          @vuetable:pagination-data="onPaginationData"
                          no-data-template="データがありませんでした。"
                          pagination-path="">
                    <template slot="actions" slot-scope="props">
                        <div>
                            <router-link :to="{ name: 'AdminSettings_NumberRewriteEdit', params: { id: props.rowData.id }}"
                                         class="btn btn-default btn-xs">
                                <i class="fa fa-edit"></i> 編集
                            </router-link>
                            <button type="button" class="btn btn-default btn-xs"
                                    v-on:click.prevent="onDelete(props.rowData)">
                                <i class="fa fa-times"></i> 削除
                            </button>
                        </div>
                    </template>
                </vuetable>
                <div class="vuetable-pagination ui basic segment grid">
                    <vuetable-pagination-info ref="paginationInfo"
                                              no-data-template=""
                                              info-template="{from}件～{to}件目を表示({total}件中)"
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
                        name: 'pattern',
                        title: 'パターン(正規表現)',
                        sortField: 'pattern',
                    },
                    {
                        name: 'replacement',
                        title: '置換文字列',
                        sortField: 'replacement',
                    },
                    {
                        name: 'description',
                        title: '説明',
                        sortField: 'description',
                    },
                    {
                        name: '__slot:actions',
                        title: '操作',
                        titleClass: 'columnAction',
                    },
                ],
                // ここまで：Vuetableのパラメタ
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
            // 削除
            onDelete(item) {
                var _this = this

                this.$confirm('選択された変換パターンを削除しても良いですか？', '確認', {
                    confirmButtonText: '削除',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    axios.post('/admin/settingNumberRewriteDelete',
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
            onPaginationData(paginationData) {
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            },
            onChangePage(page) {
                this.$refs.vuetable.changePage(page)
            },
            regEvent() {
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
            var _this = this
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)
        },
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
</style>
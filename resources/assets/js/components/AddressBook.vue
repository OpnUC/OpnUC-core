<template>
    <section class="content">
        <div class="box box-primary">
            <div id="resultLoading" style="visibility: hidden;" class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    電話帳一覧
                    <span id="breadcrumb" style="padding-left: 10px; color:gray; font-size:75%">
                    内線電話帳 > すべてを表示
                </span>
                    <span id="breadcrumbKeyword" style="color:gray; font-size:75%; visibility: hidden;">
                    > 検索結果
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
        <el-dialog title="Tips" v-model="dialog.visible" v-on:open="onDialogOpen" size="tiny">
            <span>This is a message</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialog.visible = false">Cancel</el-button>
                <el-button type="primary" @click="dialog.visible = false">Confirm</el-button>
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
                dialog: {
                    visible: false,
                    selectId: null,
                },
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
                }
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
                event.preventDefault()

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
            onDialogOpen(){
                console.log('dialog open:' + this.dialog.selectId)
            }
        },
        mounted() {
            this.regEvent();
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
        events: {
            // 詳細の表示(ColumNameからのイベント)
            'AddressBook:showDetail': function (id) {
                this.dialog.visible = true
                this.dialog.selectId = id
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
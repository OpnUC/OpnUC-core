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
                <vuetable class="table table-condensed table-striped"
                          ref="vuetable"
                          api-url="/cdr/search"
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
    </section>
</template>
<script>
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'

    export default {
        data() {
            return {
                sortOrder: [
                    {
                        field: 'start_datetime',
                        sortField: 'start_datetime',
                        direction: 'desc'
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
                        name: 'start_datetime',
                        title: '通話日時',
                        sortField: 'start_datetime',
                        callback: 'formatDate|YYYY/MM/DD hh:mm:ss',
                    },
                    {
                        name: 'duration',
                        title: '通話時間',
                        sortField: 'duration',
                        callback: 'toHMS',
                    },
                    {
                        name: 'type',
                        title: '種別',
                        sortField: 'type',
                        callback: 'convertType',
                    },
                    {
                        name: 'sender',
                        title: '発信者',
                        sortField: 'sender',
                    },
                    {
                        name: 'destination',
                        title: '着信先',
                        sortField: 'destination',
                    },
                ],
                moreParams: {
                    sender: '',
                    destination: '',
                    type: 0,
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
        },
        mounted() {
            this.regEvent();
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
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
</style>
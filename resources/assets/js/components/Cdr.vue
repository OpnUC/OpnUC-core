<template>
    <section class="content">
        <div class="box-group" id="search">
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a role="button" data-toggle="collapse" data-parent="#search" href="#collapseOne"
                           aria-expanded="false" aria-controls="collapseOne">
                            <span class="glyphicon glyphicon-search"></span>
                            検索条件
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="search">
                    <form class="form-horizontal" id="searchForm" v-on:submit="onSearch">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="searchSender" class="col-sm-1 control-label">発信者：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="searchSender"
                                           v-model="moreParams.sender">
                                </div>
                                <label for="searchDestination" class="col-sm-1 control-label">着信先：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="searchDestination"
                                           v-model="moreParams.destination">
                                </div>
                                <label for="searchType" class="col-sm-1 control-label">種別：</label>
                                <div class="col-sm-2">
                                    <select class="form-control" id="searchType" v-model="moreParams.type"
                                            options="types">
                                        <option v-for="option in types" v-bind:value="option.key">
                                            {{ option.value }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="searchDateStart" class="col-sm-1 control-label">期間：</label>
                                <div class="col-sm-5">
                                    <el-date-picker
                                            v-model="moreParams.datetime" type="daterange"
                                            placeholder="日時を選択してください"
                                            format="yyyy/MM/dd"
                                            range-separator="～"
                                            :picker-options="dpOptions"></el-date-picker>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                                検索
                            </button>
                            <button class="btn btn-default" type="reset">
                                <span class="glyphicon glyphicon-remove"></span>
                                リセット
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box">
                <div id="resultLoading" style="visibility: visible;" class="overlay">
                    <i class="fa fa-refresh fa-spin"></i>
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
                              pagination-path=""
                    ></vuetable>
                    <div class="vuetable-pagination ui basic segment grid">
                        <vuetable-pagination-info ref="paginationInfo"
                                                  info-class="pull-left"
                        ></vuetable-pagination-info>
                        <vuetable-pagination ref="pagination"
                                             :css="cssPagination"
                                             :icons="icons"
                                             @vuetable-pagination:change-page="onChangePage"
                        ></vuetable-pagination>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    import auth from '../auth'
    import moment from 'moment'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    require('moment/min/locales');
    export default {
        data() {
            return {
                auth: auth,
                dpOptions: {
                    firstDayOfWeek: 1,
                    shortcuts: [
                        {
                            text: '今日',
                            onClick(picker) {
                                const start = new Date();
                                picker.$emit('pick', [start, start]);
                            }
                        },
                        {
                            text: '昨日',
                            onClick(picker) {
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 1);
                                picker.$emit('pick', [start, start]);
                            }
                        },
                        {
                            text: '過去7日間',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 6);
                                picker.$emit('pick', [start, end]);
                            }
                        },
                        {
                            text: '過去30日間',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 29);
                                picker.$emit('pick', [start, end]);
                            }
                        },
                        {
                            text: '今月',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 29);
                                picker.$emit('pick', [
                                    moment().startOf('month'),
                                    moment().endOf('month')
                                ]);
                            }
                        },{
                            text: '先月',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 29);
                                picker.$emit('pick', [
                                    moment().subtract(1, 'month').startOf('month'),
                                    moment().subtract(1, 'month').endOf('month')
                                ]);
                            }
                        }
                    ]
                },
                types: [
                    {
                        key: 0,
                        value: '全てを選択'
                    },
                    {
                        key: 10,
                        value: '内線通話'
                    },
                    {
                        key: 21,
                        value: '外線発信'
                    },
                    {
                        key: 22,
                        value: '外線応答'
                    },
                    {
                        key: 23,
                        value: '外線着信'
                    }
                ],
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
                    datetime: [
                        moment().startOf('month'),
                        moment().endOf('month')
                    ],
                }
            }
        },
        components: {
            Vuetable,
            VuetablePagination,
            VuetablePaginationInfo
        },
        methods: {
            convertType(value){
                var result = this.$data.types.filter(function (item) {
                    return item.key == value;
                });

                return result ? result[0].value : '';
            },
            toHMS(value){
                var hms = "";
                var h = value / 3600 | 0;
                var m = value % 3600 / 60 | 0;
                var s = value % 60;
                if (h != 0) {
                    hms = h + "時間" + m + "分" + s + "秒";
                } else if (m != 0) {
                    hms = m + "分" + s + "秒";
                } else {
                    hms = s + "秒";
                }
                return hms;
            },
            formatDate (value, fmt) {
                return (value == null)
                    ? ''
                    : moment(value, 'YYYY-MM-DD hh:mm:ss').format(fmt)
            },
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
        mounted: function () {
            moment.locale('ja')
            this.regEvent();
        },
        created: function () {
            this.$root.sidebar = false;
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
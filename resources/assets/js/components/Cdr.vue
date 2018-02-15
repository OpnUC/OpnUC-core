<template>
    <section class="content">
        <div class="box-group" id="search">
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a role="button" data-toggle="collapse" data-parent="#search" href="#collapseOne"
                           aria-expanded="false" aria-controls="collapseOne">
                            <span class="fa fa-search"></span>
                            検索条件
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="search">
                    <form class="form-horizontal" id="searchForm" v-on:submit.prevent="onSearch">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="searchSender" class="col-sm-1 control-label">発信者：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="searchSender"
                                           v-model="searchParams.sender">
                                </div>
                                <label for="searchDestination" class="col-sm-1 control-label">着信先：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="searchDestination"
                                           v-model="searchParams.destination">
                                </div>
                                <label for="searchDateStart" class="col-sm-1 control-label">期間：</label>
                                <div class="col-sm-5">
                                    <el-date-picker
                                            id="searchDateStart"
                                            v-model="searchParams.datetime" type="daterange"
                                            placeholder="日時を選択してください"
                                            format="yyyy/MM/dd"
                                            range-separator="～"
                                            :picker-options="dpOptions"></el-date-picker>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-search"></i>
                                検索
                            </button>
                            <button class="btn btn-default" type="reset">
                                <i class="fa fa-times"></i>
                                リセット
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box" id="tableScrollTop">
                <div style="visibility: visible;" class="overlay" v-if="isLoading">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="box-body">
                    <div class="pull-left">
                        <el-button v-on:click="onDownload" :loading="isDownloading">
                            <i class="fa fa-download"></i>
                            CSVでダウンロード
                        </el-button>
                    </div>
                    <div class="form-inline pull-right">
                        <label>
                            1ページの件数：
                            <select class="form-control" v-model="perPage">
                                <option v-for="n in perPageList" :value="n">
                                    {{ n }}
                                </option>
                            </select>
                        </label>
                    </div>
                    <vuetable class="table table-condensed table-striped"
                              ref="vuetable"
                              api-url="/cdr/search"
                              :http-fetch="onVuetableHttpFetch"
                              :css="vueTableCss"
                              :fields="fields"
                              :sort-order="sortOrder"
                              :append-params="searchParams"
                              detail-row-id="id"
                              :per-page="perPage"
                              @vuetable:pagination-data="onVuetablePaginationData"
                              no-data-template="データがありませんでした。"
                              pagination-path=""
                    >
                        <template slot="sender" slot-scope="props">
                            <span
                                :style="props.rowData.sender_name ? 'border-bottom: dashed 1px gray;' : ''"
                                :title="props.rowData.sender_name"
                        >{{ formatCdrPhoneNumber(props.rowData.sender) }}</span>
                        </template>
                        <template slot="destination" slot-scope="props">
                            <span
                                    :style="props.rowData.destination_name ? 'border-bottom: dashed 1px gray;' : ''"
                                    :title="props.rowData.destination_name"
                            >{{ formatCdrPhoneNumber(props.rowData.destination) }}</span>
                        </template>
                    </vuetable>
                    <div class="vuetable-pagination ui basic segment grid">
                        <vuetable-pagination-info ref="paginationInfo"
                                                  no-data-template=""
                                                  info-template="{from}件～{to}件目を表示({total}件中)"
                                                  info-class="pull-left"
                        ></vuetable-pagination-info>
                        <vuetable-pagination ref="pagination"
                                             :css="vueTableCssPagination"
                                             :icons="vueTableIcons"
                                             @vuetable-pagination:change-page="onVuetableChangePage"
                        ></vuetable-pagination>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    import moment from 'moment'
    import 'moment-duration-format'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import PhoneNumber from './common_PhoneNumber'

    require('moment/min/locales');

    export default {
        mixins: [PhoneNumber],
        data() {
            return {
                perPage: 50,
                isDownloading: false,
                isLoading: true,
                dpOptions: {
                    firstDayOfWeek: 1,
                    shortcuts: [
                        {
                            text: '今日',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().startOf('day'),
                                    moment().startOf('day')
                                ]);
                            }
                        },
                        {
                            text: '昨日',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().subtract(1, 'day').startOf('day'),
                                    moment().subtract(1, 'day').endOf('day')
                                ]);
                            }
                        },
                        {
                            text: '過去7日間',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().subtract(6, 'day').startOf('day'),
                                    moment().endOf('day')
                                ]);
                            }
                        },
                        {
                            text: '過去30日間',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().subtract(29, 'day').startOf('day'),
                                    moment().endOf('day')
                                ]);
                            }
                        },
                        {
                            text: '今月',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().startOf('month'),
                                    moment().endOf('month')
                                ]);
                            }
                        }, {
                            text: '先月',
                            onClick(picker) {
                                picker.$emit('pick', [
                                    moment().subtract(1, 'month').startOf('month'),
                                    moment().subtract(1, 'month').endOf('month')
                                ]);
                            }
                        }
                    ]
                },
                sortOrder: [
                    {
                        field: 'start_datetime',
                        sortField: 'start_datetime',
                        direction: 'desc'
                    }
                ],
                fields: [
                    {
                        name: 'start_datetime',
                        title: '通話日時',
                        sortField: 'start_datetime',
                        callback: 'formatDate|YYYY/MM/DD HH:mm:ss',
                    },
                    {
                        name: 'duration',
                        title: '通話時間',
                        sortField: 'duration',
                        callback: 'toHMS',
                    },
                    {
                        name: '__slot:sender',
                        title: '発信者',
                        sortField: 'sender',
                    },
                    {
                        name: '__slot:destination',
                        title: '着信先',
                        sortField: 'destination',
                    },
                ],
                searchParams: {
                    sender: '',
                    destination: '',
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
        watch: {
            perPage: function () {
                // 1ページに表示する件数が変更された場合は、Tableを更新する
                this.$nextTick(function () {
                    this.$refs.vuetable.refresh()
                })
            }
        },
        methods: {
            /**
             * CDR向けに電話番号をフォーマットする
             */
            formatCdrPhoneNumber(number) {
                    var text1 = number.replace(/^(\d+)(\(.+\))?$/g, '$1')
                    var text2 = number.replace(/^(\d+)(\(.+\))?$/g, '$2')

                    return this.formatPhoneNumber(text1) + text2;
            },
            /**
             * 秒数を 時間 分 秒にフォーマット
             * @param value
             * @returns {string}
             */
            toHMS(value) {
                return (value == null)
                    ? ''
                    : moment.duration(value, 'seconds').format('h時間mm分ss秒')
            },
            /**
             * 日付のフォーマット
             * @param value
             * @param fmt
             * @returns {string}
             */
            formatDate(value, fmt) {
                return (value == null)
                    ? ''
                    : moment(value).format(fmt)
            },
            /**
             * 検索
             */
            onSearch() {
                this.$refs.vuetable.refresh()
            },
            /**
             * ダウンロード
             */
            onDownload() {
                var self = this

                // ボタンを無効にする
                self.isDownloading = true

                self.$message({
                    type: 'info',
                    message: 'ダウンロードを開始しました。',
                });

                axios.get('/cdr/download', {
                        params: this.searchParams
                    }
                )
                    .then(function (response) {
                        var headers = response.headers;
                        var blob = new Blob([response.data], {type: headers['content-type']});
                        var link = document.createElement('a');
                        var contentDisposition = response.headers['content-disposition'] || '';
                        var filename = contentDisposition.split('filename=')[1];
                        filename = filename ? filename.replace(/"/g, "") : 'cdr.csv'

                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename
                        link.click();

                        self.isDownloading = false
                    })
                    .catch(function (error) {
                        console.log(error)

                        self.$message({
                            type: 'error',
                            message: 'ダウンロードに失敗しました。',
                        });

                        self.isDownloading = false
                    });
            },
        },
        mounted() {
            self = this

            moment.locale('ja')

            // Vuetableが読み込み中の場合は、Loadingを表示する
            this.$refs.vuetable.$on('vuetable:loading', () => {
                self.isLoading = true;
            })
            this.$refs.vuetable.$on('vuetable:loaded', () => {
                self.isLoading = false;
            })
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
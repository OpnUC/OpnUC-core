<template>
    <section class="content">
        <div class="box box-primary">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">チャンネル一覧</h3>
            </div>
            <div class="box-body">
                <div class="pull-left">
                    <button class="btn btn-default btn-sm" v-on:click.prevent="isShowDialog = true">
                        <i class="fa fa-plus"></i>
                        新しいチャンネル
                    </button>
                </div>

                <form class="form-horizontal" v-on:submit.prevent="onCreateChannel">
                    <el-dialog title="新しいチャンネルの作成" v-model="isShowDialog">
                        <div v-if="status == 'success'" class="alert alert-success">
                            {{message}}
                        </div>
                        <div v-else-if="status == 'error'" class="alert alert-error">
                            {{message}}
                        </div>

                        <div class="form-group" :class="errors.name ? 'has-error' : ''">
                            <label class="control-label col-xs-3" for="inputName">チャンネル名</label>
                            <div class="col-xs-7">
                                <input type="text" class="form-control input-sm" id="inputName"
                                       placeholder="チャンネル名" v-model="newChannel.name">
                                <span class="help-block" v-if="errors.name">
                                    <ul>
                                        <li v-for="item in errors.name">
                                            {{ item }}
                                        </li>
                                    </ul>
                                </span>
                            </div>
                        </div>

                        <div class="form-group" :class="errors.topic ? 'has-error' : ''">
                            <label class="control-label col-xs-3" for="inputTopic">トピック</label>
                            <div class="col-xs-7">
                                <input type="text" class="form-control input-sm" id="inputTopic"
                                       placeholder="トピック" v-model="newChannel.topic">
                                <span class="help-block" v-if="errors.topic">
                                    <ul>
                                        <li v-for="item in errors.topic">
                                            {{ item }}
                                        </li>
                                    </ul>
                                </span>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <button class="btn btn-default" v-on:click.prevent="isShowDialog = false">キャンセル</button>
                            <button class="btn btn-primary" type="submit"
                                    v-bind:disabled="isPosting">作成</button>
                        </span>
                    </el-dialog>
                </form>

                <vuetable class="table table-striped"
                          ref="vuetable"
                          api-url="/messenger/channels"
                          :css="css"
                          :fields="fields"
                          :sort-order="sortOrder"
                          detail-row-id="id"
                          :per-page="perPage"
                          @vuetable:pagination-data="onPaginationData"
                          pagination-path="">
                    <template slot="actions" scope="props">
                        <div>
                            <router-link :to="{ name: 'MessengerChannel', params: { id: props.rowData.id }}"
                                         class="btn btn-default btn-xs">
                                <i class="fa fa-sign-in"></i> 参加
                            </router-link>
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
    </section>
</template>
<script>
    import Vue from 'vue'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'

    // ToDo: チャンネル作成のダイアログでエンターを入力すると、ダイアログが閉じてしまう
    export default {
        components: {
            Vuetable,
            VuetablePagination,
            VuetablePaginationInfo
        },
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
                        name: 'name',
                        title: 'ルーム名',
                        sortField: 'name',
                        titleClass: 'columnUsername',
                    },
                    {
                        name: 'topic',
                        title: 'トピック',
                        sortField: 'topic',
                    },
                    {
                        name: 'member_count',
                        title: '参加者数',
                        sortField: 'member_count',
                    },
                    {
                        name: '__slot:actions',
                        title: '操作',
                        titleClass: 'columnAction',
                    },
                ],
                // ここまで：Vuetableのパラメタ
                channels: [],
                isLoading: true,
                isShowDialog: false,
                isPosting: false,
                // validation
                status: null,
                errors: [],
                message: null,
                newChannel: [],
            }
        },
        methods: {
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page)
            },
            onCreateChannel(){
                var self = this

                self.isPosting = true

                axios.post('/messenger/newChannel',
                    {
                        name: self.newChannel.name,
                        topic: self.newChannel.topic,
                    })
                    .then(function (response) {
                        self.isPosting = false
                        self.isShowDialog = false

                        self.$message({
                            message: 'チャンネルを作成しました。',
                        });

                        self.$refs.vuetable.refresh()
                    })
                    .catch(function (error) {
                        console.log(error)

                        self.isPosting = false
                        self.status = 'error'

                        if (error.response.status === 422) {
                            // 422 - Validation Error
                            self.message = '入力に問題があります。'

                            self.errors = error.response.data
                        } else {
                            self.message = 'エラーが発生しました。'
                        }
                    });
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
        mounted() {
            this.regEvent()
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
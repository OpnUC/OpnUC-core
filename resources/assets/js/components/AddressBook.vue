<template>
    <section class="content">
        <div class="box box-primary" id="tableScrollTop">
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
                <div class="pull-left">
                    <el-dropdown v-on:command="onImportExport">
                        <el-button>
                            <i class="fa fa-file-o"></i>
                            外部連携 <i class="el-icon-caret-bottom el-icon-right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="standard">エクスポート：標準形式</el-dropdown-item>
                            <el-dropdown-item command="hitachi-phs">エクスポート：PHS電話帳(日立)</el-dropdown-item>
                            <el-dropdown-item command="import" divided v-if="$auth.check('system-admin')">
                                <i class="fa fa-upload"></i>
                                インポート
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
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
                <vuetable class="table table-striped"
                          ref="vuetable"
                          api-url="/addressbook/search"
                          :http-fetch="onVuetableHttpFetch"
                          :css="vueTableCss"
                          :fields="fields"
                          :sort-order="sortOrder"
                          :append-params="searchParam"
                          :per-page="perPage"
                          @vuetable:pagination-data="onVuetablePaginationData"
                          no-data-template="データがありませんでした。"
                          pagination-path="">
                    <template slot="avatar" slot-scope="props">
                        <div class="image">
                            <img v-bind:src="props.rowData.avatar_path"
                                 width="60" height="60"
                                 class="img-circle"
                                 alt="Avatar">
                        </div>
                    </template>
                    <template slot="name" slot-scope="props">
                        <div>
                            <div v-if="props.rowData.position">
                                <small>{{ props.rowData.position }}</small>
                            </div>
                            <a href="" v-on:click.prevent="showDetail(props.rowData)" :title="props.rowData.name_kana">{{
                                props.rowData.name }}</a>
                        </div>
                    </template>
                    <template slot="contact" slot-scope="props">
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
                    <template slot="actions" slot-scope="props">
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
                                              no-data-template=""
                                              info-template="{from}件～{to}件目を表示({total}件中)"
                                              info-class="pull-left">
                    </vuetable-pagination-info>
                    <vuetable-pagination ref="pagination"
                                         :css="vueTableCssPagination"
                                         :icons="vueTableIcons"
                                         @vuetable-pagination:change-page="onVuetableChangePage">
                    </vuetable-pagination>
                </div>
            </div>
        </div>

        <el-dialog title="インポート" v-model="importDialog.visible" v-loading="importDialog.loading"
                   element-loading-text="Loading...">
            <input type="file" id="inputImportFile" name="inputImportFile"
                   v-on:change.prevent="onImport"/>

            <span slot="footer" class="dialog-footer">
                <button class="btn btn-default" v-on:click="importDialog.visible = false">閉じる</button>
             </span>
        </el-dialog>

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

        <el-dialog title="状態変更" v-model="extStateChangeDialog.visible">
            <div class="row">
                <div class="col-xs-7">
                    <form v-on:submit.prevent="onSetForward">
                        <div class="form-group">
                            <label for="tel1Forward">不在転送設定</label>
                            <div class="input-group input-group-sm">
                                <input type="text" v-model="tel1Forward" id="tel1Forward" class="form-control input-sm">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-flat btn-primary">設定</button>
                                </span>
                            </div>
                            <p class="help-block">解除は、空欄のまま設定をクリックしてください。</p>
                        </div>
                    </form>
                </div>
            </div>

            <span slot="footer" class="dialog-footer">
                    <button class="btn btn-default" v-on:click="extStateChangeDialog.visible = false">閉じる</button>
            </span>
        </el-dialog>
    </section>
</template>
<script>
    import Vue from 'vue'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import Encoding from 'encoding-japanese'

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
                fields: [
                    {
                        name: '__slot:avatar',
                        title: '',
                        titleClass: 'columnAvatar',
                        dataClass: 'text-center vcenter',
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
                    downloadType: null,
                },
                // ここまで：Vuetableのパラメタ
                extStateChangeDialog: {
                    visible: false,
                },
                tel1Forward: '',
                detailDialog: {
                    visible: false,
                    selectItem: null,
                },
                importDialog: {
                    visible: false,
                    loading: false,
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
            /**
             * 詳細の表示
             */
            showDetail: function (item) {
                this.detailDialog.visible = true
                this.detailDialog.selectItem = item
            },
            /**
             * 外部連携
             */
            onImportExport(type) {
                var self = this

                if (type === 'import') {
                    self.importDialog.visible = true
                    return
                }

                self.searchParam.downloadType = type

                //self.isDownloading = true

                self.$message({
                    type: 'info',
                    message: 'ダウンロードを開始しました。',
                });

                axios.get('/addressbook/download', {
                        params: self.searchParam
                    }
                )
                    .then(function (response) {
                        var str2array = function (str) {
                            var array = [], i, il = str.length;
                            for (i = 0; i < il; i++) array.push(str.charCodeAt(i));
                            return array;
                        };

                        var array = str2array(response.data);
                        var sjis_array = Encoding.convert(array, 'SJIS', 'UNICODE');
                        var uint8_array = new Uint8Array(sjis_array);

                        var headers = response.headers;
                        var blob = new Blob([uint8_array], {type: headers['content-type']});
                        var link = document.createElement('a');
                        var contentDisposition = response.headers['content-disposition'] || '';
                        var filename = contentDisposition.split('filename=')[1];
                        filename = filename ? filename.replace(/"/g, "") : 'addressbook.csv'

                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename
                        link.click();

                        //self.isDownloading = false
                    })
                    .catch(function (error) {
                        console.log(error)

                        self.$message({
                            type: 'error',
                            message: 'ダウンロードに失敗しました。' + error.response.data.message,
                        });

                        //self.isDownloading = false
                    });
            },
            /**
             * インポート
             * @param e
             */
            onImport: function (e) {
                var self = this

                if (e.target.files.length === 0) {
                    // ファイルが選択されていない場合は処理しない
                    return;
                }

                self.importDialog.loading = true

//                // 初期化
//                _this.status = null
//                _this.message = null
//                _this.errors = []

                var formData = new FormData();
                formData.append('import_file', e.target.files[0]);

                axios.post('/addressbook/import',
                    formData, {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    })
                    .then(function (response) {
                        self.importDialog.loading = false
                        self.importDialog.visible = false

                        self.$message({
                            type: response.data.status,
                            message: response.data.message,
                        });
                    })
                    .catch(function (error) {
                        self.importDialog.loading = false
                        self.importDialog.visible = false

                        if (error.response.status === 422) {
                            // 422 - Validation Error
//                            _this.message = '入力に問題があります。'
//                            _this.errors = error.response.data.errors
                        } else {
                            self.$message({
                                type: error.response.data.status,
                                message: error.response.data.message,
                            });
                        }
                    })
            },
            /**
             * 削除
             * @param item
             */
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
                                message: '削除に失敗しました。' + error.response.data.message
                            });
                        });
                }).catch(function (error) {
                    console.log(error.message);
                });
            },
            /**
             * 不在転送設定
             */
            onSetForward() {
                var _this = this

                axios.post('/pbxlinker/forward', {
                    ExtNumber: this.$auth.user().address_book ? this.$auth.user().address_book.tel1 : '',
                    Number: _this.tel1Forward,
                })
                    .then(function (response) {
                        _this.$message({
                            type: 'success',
                            message: '転送設定が完了しました。',
                        });
                    })
                    .catch(function (error) {
                        _this.isLoading = false

                        var message = ''

                        if (error.response.status === 422) {
                            // 422 - Validation Error
                            message = '入力に問題があります。' + error.response.data.message
                        } else {
                            message = 'エラーが発生しました。' + error.response.data.message
                        }

                        _this.$message({
                            type: 'error',
                            message: message,
                        });
                    });

                // 転送先を消す
                _this.tel1Forward = ''
            },
            onSearch() {
                this.isSearch = this.searchParam.keyword ? true : false;
                this.$refs.vuetable.refresh()
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
            updateSearchParam() {
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
            '$route'(to, from) {
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
            // アドレス帳 種別とグループの初期値
            this.addressBookType = this.$parent.$data.addressBookType
            this.addressBookGroup = this.$parent.$data.addressBookGroup

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
            'AddressBook:onExtStateVisible': function (flag) {
                this.extStateChangeDialog.visible = flag
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

    .vuetable td.vcenter {
        vertical-align: middle !important;
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
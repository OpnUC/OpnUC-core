<template>
    <section class="content">
        <div class="box box-primary direct-chat direct-chat-success">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">#{{channelName}}</h3>
                <small v-if="channelTopic">&lt;{{channelTopic}}&gt;</small>
                <div class="pull-right">
                    <button class="btn btn-xs btn-primary" v-on:click="isMemberListDialogVisible = true">
                        <i class="fa fa-users"></i>
                        参加者一覧
                        <span class="badge">{{members.length}}</span>
                    </button>
                    <button class="btn btn-xs btn-primary" v-on:click="onLeave()">
                        <i class="fa fa-sign-out"></i>
                        退室
                    </button>
                </div>
            </div>
            <div class="box-body chat" v-if="channelId">
                <div class="direct-chat-messages">
                    <div class="alert alert-info" role="alert" v-if="messages.length == 0">
                        まだ、このチャンネルにはメッセージがありません。
                    </div>

                    <div class="item" v-for="message in messages" v-else>
                        <img v-bind:src="message.avatar_url" alt="Avatar" class="online">

                        <p class="message">
                            <a href="#" class="name">
                                {{ message.display_name }}
                            </a>
                            <small class="text-muted pull-right">
                                <i class="fa fa-clock-o"></i> {{ message.updated_at | formatDatetime }}
                                <i class="fa fa-check-circle" title="配信済み"
                                   v-if="message.is_posted && message.from_user_id == $auth.user().id"></i>
                            </small>
                            {{ message.message }}
                        </p>
                        <div class="attachment" v-if="message.attach_file">
                            <h4>添付ファイル:</h4>
                            <p class="filename">
                            <ul>
                                <li v-for="(value, key) in message.attach_file">
                                    {{ value }}
                                </li>
                            </ul>
                            </p>
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary btn-sm btn-flat"
                                        v-on:click="onDownload(message.id)">開く
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <form v-on:submit.prevent="onPost">
                    <div class="input-group">
                        <input v-model="postMessage" v-on:keydown="onTyping" type="text" placeholder="メッセージ"
                               class="form-control" v-bind:readonly="isPosting">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-flat"
                                    v-on:click="isUploadDialogVisible = true"
                                    v-bind:disabled="isPosting">
                                <i class="fa fa-paperclip"></i>
                            </button>
                            <button type="submit" class="btn btn-danger btn-flat"
                                    v-bind:disabled="isPosting || !postMessage">
                                <i class="fa fa-paper-plane-o"></i>
                                送信
                            </button>
                        </span>
                    </div>
                </form>
                <span v-show="typingUsername" class="help-block" style="font-style: italic;">
                            {{ typingUsername }}が入力中...
                </span>
            </div>
        </div>
        <el-dialog title="アップロード" v-model="isUploadDialogVisible">
            <div class="text-center">
                <el-upload
                        class="upload-demo"
                        ref="upload"
                        drag
                        :headers="uploadHeader"
                        :data="uploadData"
                        :action="uploadPath"
                        :on-success="onUploadSuccess"
                        :on-error="onUploadError"
                        :file-list="uploadFileList"
                        :auto-upload="false"
                        mutiple>
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">ファイルをドラッグ＆ドロップ</div>
                    <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>
                </el-upload>
            </div>
            <span slot="footer" class="dialog-footer">
                <button class="btn btn-default" v-bind:disabled="isUploading"
                        v-on:click="isUploading=true; $refs.upload.submit()">アップロード</button>
                <button class="btn btn-primary" v-bind:disabled="isUploading"
                        v-on:click="isUploadDialogVisible = false">閉じる</button>
            </span>
        </el-dialog>
        <el-dialog
                title="参加者一覧"
                v-model="isMemberListDialogVisible"
                size="tiny">
            <ul class="users-list clearfix">
                <li v-for="member in members">
                    <img v-bind:src="member.avatar_path" class="user-image"/>
                    <span class="users-list-name">{{member.name}}</span>
                </li>
            </ul>
            <span slot="footer" class="dialog-footer">
                <button class="btn btn-primary" v-on:click="isMemberListDialogVisible = false">閉じる</button>
            </span>
        </el-dialog>
    </section>
</template>
<script>
    import moment from 'moment'

    export default {
        computed: {
            uploadPath: function () {
                return this.axios.defaults.baseURL + '/messenger/upload'
            },
            uploadHeader: function () {
                // JWT Auth
                return {
                    Authorization: 'Bearer ' + this.$auth.token(),
                }
            },
            uploadData: function () {
                return {
                    channelId: this.channelId,
                }
            },
        },
        data() {
            return {
                isLoading: true,
                isUploading: false,
                isMemberListDialogVisible: false,
                isUploadDialogVisible: false,

                uploadFileList: [],

                typingUsername: null,

                channelId: null,
                channelName: null,
                channelTopic: null,
                members: [],
                messages: [],

                isPosting: false,
                postMessage: null,
            }
        },
        events: {
            // WebScoketからメッセージを受信した場合
            'Messenger:RecieveMessage': function (channelId, message) {
                if (channelId != this.channelId) {
                    return;
                }

                this.messages.push(message.message)
            },
            'Messenger:UpdateMemberList': function (channelId, members) {
                if (channelId != this.channelId) {
                    return;
                }

                this.members = members.filter(function (item) {
                    return true
                })
            },
            'Messenger:JoiningUser': function (channelId, joiningUser) {
                if (channelId != this.channelId) {
                    return;
                }

                //this.onPushMessage(joiningUser.id, joiningUser.name, joiningUser.avatar_path, new Date(), 'チャンネルに参加しました。(システム)')
            },
            'Messenger:LeavingUser': function (channelId, leavingUser) {
                if (channelId != this.channelId) {
                    return;
                }

                //this.onPushMessage(leavingUser.id, leavingUser.name, leavingUser.avatar_path, new Date(), 'チャンネルから退室しました。(システム)')
            },
            'Messenger:RecieveTyping': function (channelId, username) {
                var self = this

                if (channelId != self.channelId) {
                    return;
                }

                self.typingUsername = username

                // remove is typing indicator after 0.9s
                setTimeout(function () {
                    self.typingUsername = null
                }, 900);
            },
        },
        methods: {
            onDownload(messageId){
                var self = this

                self.$message({
                    type: 'info',
                    message: 'ダウンロードを開始しました。',
                });

                axios.get('/messenger/download', {
                        // Binaryでダウンロードする場合はこれが必要
                        responseType: 'arraybuffer',
                        params: {
                            id: messageId
                        }
                    }
                )
                    .then(function (response) {
                        var headers = response.headers;
                        var blob = new Blob([response.data], {type: headers['content-type']});
                        var link = document.createElement('a');
                        var contentDisposition = response.headers['content-disposition'] || '';
                        var filename = contentDisposition.split('filename=')[1];
                        filename = filename ? filename.replace(/"/g, "") : 'download'

                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename
                        link.click();
                    })
                    .catch(function (error) {
                        var msg = ''

                        switch (error.response.status) {
                            case 404:
                                msg = 'メッセージが削除されているか、添付ファイルがすでに削除されています。'
                                break
                            case 400:
                                msg = 'メッセージに添付ファイルがありませんでした。'
                                break
                            default:
                                msg = 'エラーが発生しました。'
                                break
                        }

                        self.$message({
                            type: 'error',
                            message: msg,
                        });
                    });
            },
            onUploadError(err, file, fileList){
                this.isUploading = false

                this.$message({
                    type: 'error',
                    message: 'アップロードに失敗しました。',
                });
            },
            onUploadSuccess(response, file, fileList){
                this.isUploading = false
                this.$refs.upload.clearFiles()
                this.isUploadDialogVisible = false

                this.$message({
                    message: 'アップロードしました。',
                });
            },
            onTyping(){
                var self = this

                setTimeout(function () {
                    self.$events.$emit('Messenger:onTyping', self.channelId)
                }, 300);
            },
            onLeave(){
                this.$confirm('このチャンネルから退室します。よろしいですか？', '確認', {
                    confirmButtonText: '退室',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    this.$events.$emit('Messenger:leaveChannel', this.channelId)
                }).catch(function (error) {
                    console.log(error.message);
                });
            },
            /**
             * メッセージの投稿処理
             */
            onPost(){
                var self = this

                // メッセージが入力されていない場合は処理しない
                if (!self.postMessage) {
                    return
                }

                var objMessage = {
                    message: self.postMessage,
                    from_user_id: self.$auth.user().id,
                    display_name: self.$auth.user().display_name,
                    avatar_url: self.$auth.user().avatar_path,
                    updated_at: moment().toISOString(),
                    is_posted: false,
                }

                this.messages.push(objMessage)

                // APIに投げる
                axios.post('/messenger/message',
                    {
                        channelId: self.channelId,
                        message: self.postMessage,
                    })
                    .then(function (response) {
                        objMessage.is_posted = true
                    })
                    .catch(function (error) {
                        console.log(error)
                    });

                // メッセージをクリア
                self.postMessage = null
            },
            onInitView(){
                var self = this

                self.isLoading = true

                if(!window.echo){
                    self.$message({
                        type: 'error',
                        message: 'WebSocketが接続されていないため、参加出来ません。'
                    });
                    return;
                }

                axios.get('/messenger/channel', {
                    params: {
                        channelId: self.channelId
                    }
                })
                    .then(function (response) {
                        // チャンネルが変更されたので、いったんクリア
                        self.channelName = response.data.name
                        self.channelTopic = response.data.topic
                        self.members = []
                        self.messages = []

                        // サイドバー更新のため
                        self.$events.$emit('Messenger:joinChannel', self.channelId, self.channelName)

                        // APIで取得したメッセージを流す
                        _.forEachRight(response.data.recently_post, function (val, index, ar) {
                            self.messages.push(val)
                        });

                        self.isLoading = false
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            updateSearchParam(){
                if (this.$route.params.id) {
                    this.channelId = this.$route.params.id
                    this.onInitView()
                } else {
                    this.channelId = null
                    this.channelName = null
                    this.channelTopic = null
                    this.members = []
                    this.message = []

                    this.isLoading = true
                }
            },
        },
        watch: {
            '$route' (to, from) {
                this.updateSearchParam()
            },
            'messages' (to, from){
                // メッセージが更新された場合

                // 最後にスクロール
                var el = this.$el.getElementsByClassName('direct-chat-messages')[0]
                this.$nextTick(() => {
                    el.scrollTop = el.scrollHeight
                })
            },
        },
        mounted() {
            this.updateSearchParam()
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
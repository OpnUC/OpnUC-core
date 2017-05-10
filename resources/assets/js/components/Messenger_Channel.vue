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
                    <button class="btn btn-xs btn-primary" v-on:click="isDialogVisible = true">
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
            <div class="box-body" v-if="channelId">
                <div class="direct-chat-messages">
                    <div class="alert alert-info" role="alert" v-if="messages.length == 0">
                        まだ、このチャンネルにはメッセージがありません。
                    </div>
                    <div class="direct-chat-msg" v-bind:class="message.userId == $auth.user().id ? 'right' : ''"
                         v-for="message in messages" v-else>
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name"
                                  v-bind:class="message.userId == $auth.user().id ? 'pull-right' : 'pull-left'">{{ message.username }}</span>
                            <span class="direct-chat-timestamp"
                                  v-bind:class="message.userId == $auth.user().id ? 'pull-left' : 'pull-right'">
                                {{ message.datetime | formatDatetime }}
                                <i class="fa fa-check-circle" title="配信済み" v-if="message.isPosted"></i>
                            </span>
                        </div>
                        <img class="direct-chat-img" v-bind:src="message.avatarUrl"
                             alt="message user image">
                        <div class="direct-chat-text">
                            {{ message.message }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <form v-on:submit.prevent="onPost">
                    <div class="input-group">
                        <input v-model="postMessage" v-on:keydown="onTyping" type="text" placeholder="Type Message ..."
                               class="form-control" v-bind:readonly="isPosting">
                        <span class="input-group-btn">
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
        <el-dialog
                title="参加者一覧"
                v-model="isDialogVisible"
                size="tiny">
            <ul class="users-list clearfix">
                <li v-for="member in members">
                    <img v-bind:src="member.avatar_path" class="user-image"/>
                    <span class="users-list-name">{{member.name}}</span>
                </li>
            </ul>
            <span slot="footer" class="dialog-footer">
                <button class="btn btn-primary" v-on:click="isDialogVisible = false">閉じる</button>
            </span>
        </el-dialog>
    </section>
</template>
<script>
    export default {
        data() {
            return {
                isLoading: true,
                isDialogVisible: false,

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
            'Messenger:RecieveMessage': function (channelId, message) {
                if (channelId != this.channelId) {
                    return;
                }

                this.messages.push(message)

                // 最後にスクロール
                var el = this.$el.getElementsByClassName('direct-chat-messages')[0]
                this.$nextTick(() => {
                    el.scrollTop = el.scrollHeight
                })
            },
            'Messenger:PostedMessage': function (channelId, messageId) {
                if (channelId != this.channelId) {
                    return;
                }

                // チャンネルを返す
                var message =  this.messages.filter(function (item) {
                    return item.messageId == messageId
                })[0]

                message.isPosted = true
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
            onPost(){
                this.$events.$emit('Messenger:onPost', this.channelId, this.postMessage)

                this.postMessage = null
            },
            onInitView(){
                var self = this

                self.isLoading = true

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

                        self.$events.$emit('Messenger:joinChannel', self.channelId, self.channelName)

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
        },
        mounted() {
            this.updateSearchParam()
        },
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
<template>
    <div class="main-sidebar">
        <div class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img v-bind:src="$auth.user().avatar_path" class="img-circle"
                         alt="Avatar">
                </div>
                <div class="pull-left info">
                    <p>{{ $auth.user().display_name }}</p>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="treeview">
                    <router-link to="/Messenger">
                        <span>チャンネルリスト</span>
                    </router-link>
                </li>
                <li class="header">
                    参加チャンネル
                </li>
                <li class="treeview" v-for="channel in joinChannels">
                    <router-link :to="{ name: 'MessengerChannel', params: { id: channel.channelId }}">
                        <span>#{{channel.name}}</span>
                    </router-link>
                </li>

                <li class="header">
                    ダイレクトメッセージ
                </li>
                <li class="treeview">
                    <router-link to="/">
                        <span>Dummy</span>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                joinChannels: [],
            }
        },
        events: {
            'Messenger:onPost': function (channelId, message) {
                var self = this

                if (!message) {
                    return
                }

                var messageId = '' + _.now() + Math.floor(Math.random() * 65535)

                self.postMessage(
                    channelId,
                    message,
                    messageId, // MessageeID track
                )

                axios.post('/messenger/message',
                    {
                        channelId: channelId,
                        message: message,
                    })
                    .then(function (response) {
                        self.$events.$emit('Messenger:PostedMessage', channelId, messageId)
                    })
                    .catch(function (error) {
                        console.log(error)
                    });
            },
            'Messenger:onTyping': function (channelId) {
                // 入力中のイベントが発生した場合

                var channel = this.getChannel(channelId)

                // WebSocketで通知する
                channel.echo
                    .whisper('typing', {
                        channelId: channelId,
                        username: this.$auth.user().display_name
                    });
            },
            'Messenger:joinChannel': function (channelId, channelName) {
                // チャンネルに参加するイベントが発生した場合

                var self = this

                // Webアプリ側に参加通知
                // ToDo: WebSocketのセッションが切れると、退室となるため、保持不要？
                axios.post('/messenger/joinChannel', {
                    channelId: channelId
                })
                    .then(function (response) {
                        var channel = self.getChannel(channelId)

                        // LaravelEchoが初期化されていない場合のみ、初期化
                        // 2度 初期化すると、イベントが2回登録されてしまうため
                        if (channel.echo === null) {
                            channel.echo = window.echo
                            // チャンネルに参加
                                .join('MessengerChannel.' + channelId)
                                .here(function (members) {
                                    // Join時にメンバーリストを取得
                                    var channel = self.getChannel(channelId)
                                    channel.members = members

                                    self.$events.$emit('Messenger:UpdateMemberList', channelId, channel.members)
                                })
                                .joining(function (joiningMember) {
                                    var channel = self.getChannel(channelId)

                                    // メンバーリストに入室したメンバーを追加
                                    channel.members.push(joiningMember)

                                    self.$events.$emit('Messenger:UpdateMemberList', channelId, channel.members)
                                })
                                .leaving(function (leavingMember, members) {
                                    var channel = self.getChannel(channelId)

                                    // メンバーリストから退室したメンバーを削除
                                    channel.members = channel.members.filter(function (item) {
                                        return item.id != leavingMember.id
                                    })

                                    self.$events.$emit('Messenger:UpdateMemberList', channelId, channel.members)
                                })
                                .listenForWhisper('typing', (e) => {
                                    // 他のユーザが入力中の場合
                                    self.$events.$emit('Messenger:RecieveTyping', channelId, e.username)
                                })
                                .listen('MessengerNewMessage', (e) => {
                                    // メッセージを受信した場合
                                    self.postMessage(
                                        channelId,
                                        e.message,
                                        '', // MessageId
                                        e.ownerUserId,
                                        e.ownerUserName,
                                        e.ownerAvatarUrl,
                                        e.datetime,
                                    )
                                });
                        }

                        // Webアプリ側で参加済みかどうか
                        if (response.data.channels.attached.length === 1) {
                            // 参加済みで無い
                            var channel = self.getChannel(channelId)
                            channel.name = channelName

                            self.$message({
                                message: 'チャンネルに参加しました。'
                            });
                        } else {
                            var channel = self.getChannel(channelId)

                            // 保存していたメッセージを流す
                            channel.messages.forEach(function (val, index, ar) {
                                self.$events.$emit('Messenger:RecieveMessage', channelId, val)
                            });

                            self.$events.$emit('Messenger:UpdateMemberList', channelId, channel.members)
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            'Messenger:leaveChannel': function (channelId) {
                // チャンネルから退室するイベントが発生した場合
                var self = this

                // Webアプリ側に退室を通知
                axios.post('/messenger/leaveChannel', {
                    channelId: channelId
                })
                    .then(function (response) {
                        // WebSocket leave
                        window.echo.leave('MessengerChannel.' + channelId)

                        // チャンネル一覧から退室したチャンネルを削除
                        self.joinChannels = self.joinChannels.filter(function (channel) {
                            return channel.channelId != channelId
                        });

                        self.$message({
                            message: '退室しました。'
                        });

                        // チャンネル一覧へ移動する
                        self.$router.push('/Messenger')
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
        },
        methods: {
            /**
             * チャンネルを取得
             * @param channelId int チャンネルID
             * @returns {T}
             */
            getChannel(channelId){
                // 既にチャンネルに参加しているか確認
                var exist = this.joinChannels.some(function (item) {
                    return item.channelId == channelId
                })

                // 参加していない場合は、新しく作成
                if (!exist) {
                    this.joinChannels.push({
                        channelId: channelId,
                        channelName: null,
                        echo: null,
                        members: [],
                        messages: [],
                    })
                }

                // チャンネルを返す
                return this.joinChannels.filter(function (item) {
                    return item.channelId == channelId
                })[0]
            },
            /**
             * メッセージの流し込み
             * @param channelId
             * @param message
             * @param messageId
             * @param userId
             * @param username
             * @param avatarUrl
             * @param datetime
             */
            postMessage(channelId, message, messageId, userId = null, username = null, avatarUrl = null, datetime = null){
                var channel = this.getChannel(channelId)

                var objMessage = {
                    message: message,
                    messageId: messageId,
                    userId: userId === null ? this.$auth.user().id : userId,
                    username: username === null ? this.$auth.user().display_name : username,
                    avatarUrl: avatarUrl === null ? this.$auth.user().avatar_path : avatarUrl,
                    datetime: datetime === null ? new Date() : datetime,
                    isPosted: false,
                }

                // メッセージを保存
                channel.messages.push(objMessage)

                // 最大件数は100件
                while (channel.messages.length > 100) {
                    channel.messages.shift();
                }

                this.$events.$emit('Messenger:RecieveMessage', channelId, objMessage)
            },
        },
        mounted()
        {
            var self = this

            // Webアプリ側で参加済みのチャンネル一覧を取得する
            axios.get('/messenger/joinedChannels')
                .then(function (response) {
                    response.data.forEach(function (val, index, ar) {
                        var channel = self.getChannel(val.id)

                        channel.name = val.name
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        created()
        {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>
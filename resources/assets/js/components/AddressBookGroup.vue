<template>
    <section class="content">
        <div class="box box-primary">
            <div class="overlay" v-if="isLoading">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    グループ管理
                </h3>
            </div>
            <div class="box-body">
                <a href="" class="btn btn-default">
                    <i class="fa fa-plus"></i>
                    グループの追加
                </a>
                <p/>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li :class="{ active : typeId == 1 }" v-for="(typeName, typeId) in addressBookType">
                            <a :href="'#tab_' + typeId" data-toggle="tab" aria-expanded="true">{{ typeName }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" :class="{ active : typeId == 1 }" :id="'tab_' + typeId"
                             v-for="(typeName, typeId) in addressBookType">
                            <el-tree
                                    :props="treeProps"
                                    :data="addressBookGroups[typeId]"
                                    node-key="Id"
                                    default-expand-all
                                    :expand-on-click-node="false"
                                    :render-content="renderContent">
                            </el-tree>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    import Vue from 'vue'

    export default {
        data() {
            return {
                // 読み込み中かどうか
                isLoading: true,
                // ページ上のデータ
                addressBookType: [],
                addressBookGroups: [],
                // Treeのプロパティ
                treeProps: {
                    children: 'Child',
                    label: 'Name',
                },
            }
        },
        methods: {
            renderContent(h, {node, data, store}) {
                return this.$createElement('span', {}, [
                    this.$createElement('span', {}, [
                        this.$createElement('span', {}, [
                            data.Name
                        ])
                    ]),
                    this.$createElement('span', {style: 'float: right; margin-right: 20px'}, [
                        this.$createElement('button', {
                            class: 'btn btn-default btn-xs',
                            on: {
                                click: (function (data, fnc) {
                                    return function (event) {
                                        fnc(event, data);
                                    }
                                })(data, this.onEdit)
                            },
                        }, [
                            '編集'
                        ]),
                        !data.Child && !data.ItemCount ? this.$createElement('button', {
                                class: 'btn btn-default btn-xs',
                                on: {
                                    click: (function (data, fnc) {
                                        return function (event) {
                                            fnc(event, data);
                                        }
                                    })(data, this.onDelete)
                                },
                            }, [
                                '削除'
                            ]) : null
                    ])
                ])
            },
            // 削除
            onDelete(event, data){
                var _this = this

                // 削除確認
                this.$confirm('選択されたグループを削除しても良いですか？', '確認', {
                    confirmButtonText: '削除',
                    cancelButtonText: 'キャンセル',
                    type: 'warning'
                }).then(() => {
                    // Ajaxで削除
                    axios.post('/addressbook/groupDelete',
                        {
                            groupId: data.Id
                        })
                        .then(function (response) {
                            _this.$message({
                                type: response.data.type,
                                message: response.data.message
                            });

                            // 削除完了のため、更新
                            _this.onLoadGroup();
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
            // 編集
            onEdit(event, data){
                this.$router.push({ name: 'AddressBookGroupEdit', params: { id: data.Id }})
            },
            // グループ情報の読み込み
            onLoadGroup(){
                var _this = this

                this.isLoading = true

                // Ajaxで読み込み
                $.each(this.addressBookType, function (typeId, val) {
                    axios.get('/addressbook/groups', {
                        params: {
                            typeId: typeId
                        }
                    })
                        .then(function (response) {
                            Vue.set(_this.addressBookGroups, typeId, response.data)

                            _this.isLoading = false
                        })
                        .catch(function (error) {
                            console.log(error);

                            _this.isLoading = false
                        });
                });
            }
        },
        created() {
            this.addressBookType = this.$parent.$data.addressBookType
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar)

            // グループ情報の読み込み
            this.onLoadGroup()
        },
    }
</script>
<style>
</style>
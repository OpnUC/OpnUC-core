<template>
    <div class="main-sidebar">
        <div class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm" class="img-circle"
                         alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{ $auth.user().display_name }}</p>
                    <i class="myExtStatus extStatus ext text-gray extStatus ext" title="不明"></i>
                </div>
            </div>

            <form class="sidebar-form" id="AddressBookSearch" v-on:submit.prevent="onSearch">
                <div class="input-group">
                    <input type="text" name="keyword" v-model="keyword" class="form-control" placeholder="検索...">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>

            <ul class="sidebar-menu">
                <li class="header">電話帳</li>
                <!-- // 電話帳種別  -->
                <li class="treeview" v-for="(type, index) in types">
                    <a href="#">
                        <i class="fa fa-address-book"></i>
                        <span>{{ type }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="#" v-on:click="onSelect(index, 0, 'すべてを表示', null)">すべてを表示</a>
                        </li>
                        <!--//ここから切り出す-->
                        <li v-for="item in groups[index]">
                            <a href="#" v-on:click="onSelect(index, item.Id, item.Name, item.Child)">
                                {{ item.Name }}
                                <i class="fa fa-angle-left pull-right" v-if="item.Child"></i>
                            </a>
                            <group-list :item="item" :index="index" :keyword="keyword"></group-list>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul class="sidebar-menu">
                <li class="header">
                    <i class="fa fa-cog"></i> 管理
                </li>
                <li class="treeview">
                    <a href="#" v-on:click.prevent="onEdit">
                        <i class="fa fa-plus-square"></i>
                        <span>連絡先追加</span>
                    </a>
                </li>
                <li class="treeview">
                    <a>
                        <span>グループ管理</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import AddressBook_Sidebar_GroupList from './AddressBook_Sidebar_GroupList.vue'

    Vue.component('group-list',
        AddressBook_Sidebar_GroupList
    );

    export default {
        data(){
            return {
                keyword: '',
                types: null,
                groups: [],
            }
        },
        created() {
            var _this = this

            this.types = this.$route.matched[1].components.default.data().addressBookType

            $.each(this.types, function (index, val) {
                axios.get('/addressbook/groups', {
                    params: {
                        typeId: index
                    }
                })
                    .then(function (response) {
                        Vue.set(_this.groups, index, response.data)
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        },
        methods: {
            // 検索
            onSearch () {
                this.$events.$emit('AddressBook:search', this.keyword)
            },
            // グループの選択
            onSelect(typeId, groupId, groupName, flag){
                if (!flag) {
                    this.$events.$emit('AddressBook:search', this.keyword, typeId, groupId, groupName)
                }
            },
            // 追加
            onEdit() {
                this.$events.$emit('AddressBook:edit', this.rowData)
            },
        },
    }
</script>
<style>
</style>
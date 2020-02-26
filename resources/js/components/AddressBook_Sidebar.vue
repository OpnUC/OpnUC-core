<template>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img v-bind:src="$auth.user().avatar_path" class="img-circle"
                         alt="Avatar">
                </div>
                <div class="pull-left info">
                    <p>{{ $auth.user().display_name }}</p>
                    <div v-if="my_ext">
                        <i class="fa fa-phone"></i>
                        {{ my_ext }}

                        <a href="#" v-on:click.prevent="onExtStateVisible()">
                            <i v-if="enableTelPresence && isPhoneNumberExt(my_ext)"
                               class="extStatus" :class="`ext${my_ext} ${my_ext_status_class}`"
                               :title="my_ext_status"></i>
                        </a>
                    </div>
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
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">電話帳</li>
                <!-- // 電話帳種別  -->
                <li class="treeview" v-for="(typeName, typeId) in addressBookType">
                    <a href="#">
                        <i class="fa fa-address-book"></i>
                        <span>{{ typeName }}</span>
                        <span class="pull-right-container">
                           <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <router-link :to="{ name: 'AddressBook', query: { typeId: typeId }}">
                                すべてを表示
                            </router-link>
                        </li>
                        <!--//ここから切り出す-->
                        <li class="treeview" v-for="item in addressBookGroups[typeId]">
                            <a href="#" v-if="item.Child">
                                {{ item.Name }}
                                <i class="fa fa-angle-left pull-right" v-if="item.Child"></i>
                            </a>
                            <router-link v-else
                                         :to="{ name: 'AddressBook', query: { typeId: typeId, groupId: item.Id }}">
                                {{ item.Name }}
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right" v-if="item.Child"></i>
                                </span>
                            </router-link>
                            <group-list :item="item" :typeId="typeId"></group-list>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">
                    <i class="fa fa-cog"></i> 管理
                </li>
                <li class="treeview">
                    <router-link to="/AddressBook/Edit">
                        <i class="fa fa-plus-square"></i>
                        <span>連絡先追加</span>
                    </router-link>
                </li>
                <li class="treeview">
                    <router-link to="/AddressBook/Group">
                        <i class="fa fa-cog"></i>
                        <span>グループ管理</span>
                    </router-link>
                </li>
            </ul>
        </section>
    </aside>
</template>
<script>
    import Vue from 'vue'
    import AddressBook_Sidebar_GroupList from './AddressBook_Sidebar_GroupList.vue'
    import PhoneNumber from './common_PhoneNumber'

    Vue.component('group-list',
        AddressBook_Sidebar_GroupList
    );

    export default {
        mixins: [PhoneNumber],
        computed: {
            my_ext() {
                if (this.$auth.user().address_book) {
                    return this.$auth.user().address_book.tel1
                } else {
                    return ''
                }
            },
            my_ext_status() {
                if (this.$auth.user().address_book) {
                    return window.extStatus[this.$auth.user().address_book.tel1_status]['statusText']
                } else {
                    return ''
                }
            },
            my_ext_status_class() {
                if (this.$auth.user().address_book) {
                    return window.extStatus[this.$auth.user().address_book.tel1_status]['statusClass']
                } else {
                    return ''
                }
            },
            enableTelPresence() {
                return window.opnucConfig.enable_tel_presence;
            },
        },
        data() {
            return {
                keyword: '',
                addressBookType: null,
                addressBookGroups: [],
            }
        },
        created() {
            var _this = this

            // 種別はテンプレートからもらう
            this.addressBookType = this.$route.matched[1].components.default.data().addressBookType
            this.keyword = this.$route.query.keyword

            $.each(this.addressBookType, function (typeId, val) {
                axios.get('/addressbook/groups', {
                    params: {
                        typeId: typeId
                    }
                })
                    .then(function (response) {
                        Vue.set(_this.addressBookGroups, typeId, response.data)
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        },
        mounted() {
            // Activate Sidebar Tree
            $('ul.sidebar-menu').tree();
        },
        methods: {
            onExtStateVisible(){
                this.$events.$emit('AddressBook:onExtStateVisible', true)
            },
            // 検索
            onSearch() {
                this.$router.replace({
                    query: {
                        keyword: this.keyword,
                        typeId: this.$route.query.typeId,
                        groupId: this.$route.query.groupId,
                    }
                })
            },
        },
    }
</script>
<style>
</style>
<template>
    <div class="main-sidebar">
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li class="header">
                    ユーザ情報
                </li>
                <li class="treeview">
                    <router-link to="/User">
                        <i class="fa fa-user"></i>
                        <span>基本情報</span>
                    </router-link>
                </li>
                <li class="treeview" v-if="$auth.user().address_book">
                    <router-link to="/User/AddressBook">
                        <i class="fa fa-address-book"></i>
                        <span>内線電話帳情報</span>
                    </router-link>
                </li>
                <li class="treeview">
                    <router-link to="/User/PasswordChange">
                        <i class="fa fa-key"></i>
                        <span>パスワードの変更</span>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>
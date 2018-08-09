<template>
    <div class="main-sidebar">
        <div class="sidebar">
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">
                    システム管理
                </li>
                <li class="treeview">
                    <router-link to="/Admin/Users">
                        <i class="fa fa-user"></i>
                        <span>ユーザ管理</span>
                    </router-link>
                    <router-link to="/Admin/Roles">
                        <i class="fa fa-key"></i>
                        <span>ロール管理</span>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>
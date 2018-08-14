<template>
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">
                    システム管理
                </li>
                <li>
                    <router-link to="/Admin/Users">
                        <i class="fa fa-user"></i>
                        <span>ユーザ管理</span>
                    </router-link>
                </li>
                <li>
                    <router-link to="/Admin/Roles">
                        <i class="fa fa-key"></i>
                        <span>ロール管理</span>
                    </router-link>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-cog"></i>
                        <span>各種設定</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <router-link to="/Admin/Settings_NumberRewrite">
                                <span>番号変換パターン</span>
                            </router-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
    </aside>
</template>

<script>
    export default {
        mounted() {
            // Activate Sidebar Tree
            $('ul.sidebar-menu').tree();
        },
    }
</script>
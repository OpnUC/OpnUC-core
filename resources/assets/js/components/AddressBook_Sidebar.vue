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
                    <i class="myExtStatus extStatus ext" title="不明"></i>
                </div>
            </div>

            <form action="#" method="get" class="sidebar-form" id="AddressBookSearch">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="検索...">
                    <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
                </div>
            </form>

            <ul class="sidebar-menu" id="TypeList">
                <li class="header">電話帳</li>
            </ul>

            <ul class="sidebar-menu">
                <li class="header">
                    <i class="fa fa-cog"></i> 管理
                </li>
                <li class="treeview">
                    <a>
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
    export default {}
</script>
<style>
</style>
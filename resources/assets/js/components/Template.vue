<template>
    <div class="wrapper">
        <header class="main-header">
            <router-link to="/" class="logo">
                <span class="logo-mini"><b>U</b>C</span>
                <span class="logo-lg"><b>Opn</b>UC</span>
            </router-link>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <ul class="nav navbar-nav">
                    <li class="active">
                        <router-link to="/dummyurl">Item1</router-link>
                    </li>
                </ul>
            </nav>
        </header>

        <div class="main-sidebar">
            <div class="sidebar">
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    {{$route.name.toUpperCase() }}
                    <small>{{ $route.meta.description }}</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="javascript:;"><i class="fa fa-home"></i>Home</a></li>
                    <li class="active">{{$route.name.toUpperCase() }}</li>
                </ol>
            </section>

            <router-view></router-view>
        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; {{year}} OpnUC Developer Team.</strong> All rights reserved.
        </footer>
    </div>
</template>

<script>
    module.exports = {
        name: 'Dash',
        computed: {
            year: function () {
                var y = new Date()
                return y.getFullYear()
            }
        },
        methods: {
            changeloading: function () {
                //this.store.commit('TOGGLE_SEARCHING')
            },
            toggleMenu: function (event) {
                // remove active from li
                window.$('li.pageLink').removeClass('active')
                // Add it to the item that was clicked
                event.toElement.parentElement.className = 'pageLink active'
            }
        },
        mounted: function () {
            // Page is ready. Let's load our functions!
            console.log("Template Loaded");
        }
    }
</script>

<style>
</style>
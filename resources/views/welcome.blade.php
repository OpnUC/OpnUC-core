<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OpenUC</title>
    <link href="{{mix('/css/app.css')}}" rel="stylesheet" type="text/css">
    <link href="{{mix('/css/vendor.css')}}" rel="stylesheet" type="text/css">
</head>
<body class="hold-transition skin-blue-light sidebar-mini">

<div class="wrapper">
    <header class="main-header">
        <a href="/" class="logo">
            <span class="logo-mini"><b>U</b>C</span>
            <span class="logo-lg"><b>Opn</b>UC</span>
        </a>

        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="/">Item1</a>
                </li>
            </ul>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @if (Auth::guest())
                        <li><a href="">ログイン</a></li>
                    @else
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <img src="" class="user-image" alt="User Image">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <img src="" class="img-circle" alt="User Image">
                                    <p>
                                        UserName
                                    </p>
                                </li>
                                <li class="user-body">
                                    <div class="col-xs-12 text-center">
                                        Dummy
                                    </div>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-left">
                                    </div>
                                    <div class="pull-right">
                                    </div>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </header>

    <div class="main-sidebar">
        <div class="sidebar">
        </div>
    </div>

    <div class="content-wrapper">
    </div>
</div>
<script src="{{mix('/js/manifest.js')}}" type="text/javascript"></script>
<script src="{{mix('/js/vendor.js')}}" type="text/javascript"></script>
<script src="{{mix('/js/app.js')}}" type="text/javascript"></script>
</body>
</html>

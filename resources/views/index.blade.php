<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OpnUC</title>
    <link rel="shortcut icon" href="{{{ asset('images/favicon.ico') }}}">
    <link href="{{mix('/css/app.css')}}" rel="stylesheet" type="text/css">
    <link href="{{mix('/css/vendor.css')}}" rel="stylesheet" type="text/css">
</head>
<body class="hold-transition skin-blue-light">

<div id="root"></div>

<script>
    window.appUrl = '{{ asset('') }}';

    window.opnucConfig = <?php
    echo json_encode([
        'enable_saml2_auth' => \Config::get('saml2_settings.useSaml2Auth', false),
        'enable_c2c' => \Config::get('opnuc.enable_c2c'),
        'enable_tel_presence' => \Config::get('opnuc.enable_tel_presence'),
    ]);
    ?>

    window.Laravel = <?php
    echo json_encode([
        'csrfToken' => csrf_token(),
    ]);
    ?>
</script>
<script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
<script src="{{mix('/js/manifest.js')}}" type="text/javascript"></script>
<script src="{{mix('/js/vendor.js')}}" type="text/javascript"></script>
<script src="{{mix('/js/app.js')}}" type="text/javascript"></script>
</body>
</html>

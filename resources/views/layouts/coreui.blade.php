<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="SIGEMA V2">
    <meta name="author" content="dorado Labs">
    <title>{!! $title ?? 'Ingresar' !!} | {{ config('app.name', 'Laravel') }}</title>
    <meta name="theme-color" content="#ffffff">
    <!-- Main styles for this application-->
    <link href="coreui/css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="vendor/font-awesome/css/all.css">
    <link rel="stylesheet" type="text/css" href="vendor/font-awesome/css/v4-shims.css">
    @notifyCss
</head>

<body>

    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
        <div class="sidebar-brand d-none d-md-flex">
            <a href="{{ url('/') }}" class="navbar-brand"><i class="fab fa-app-store"></i>&nbsp;&nbsp;SIGEMA<b>&nbsp;&nbsp;G&Therefore;L&Therefore;B&Therefore;</b> </a>
        </div>
        {{-- <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button> --}}
        @include('layouts.menu')
    </div>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <header class="mb-1 header header-sticky">
            @include('layouts.panel_superior')
            {{-- @include('layouts.panel_nav') --}}
        </header>
        <div class="px-1 pb-2 body flex-grow-1">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    @notifyJs
</body>

</html>

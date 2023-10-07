<!DOCTYPE html>
<html>
    <style>
        html,body{
          height: 100%;
          overflow: hidden;
          margin: 0;
          padding: 0;
        }
        </style>
<head>
  <meta charset="UTF-8">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/font-awesome/css/all.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/font-awesome/css/v4-shims.css') }}">
  <!-- JQuery EasyUI -->
  <link href="{{ asset('jquery-easyui/themes/default/easyui.css" rel="stylesheet') }}" />
  <link href="{{ asset('jquery-easyui/themes/icon.css') }}" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="{{ asset(Auth::user()->plantilla) }}">
  {{-- <link href="{{ asset('jquery-easyui/themes/mobile.css') }}" rel="stylesheet" /> --}}
  <script src="{{ asset('jquery-easyui/jquery.min.js') }}"></script>
  <script src="{{ asset('jquery-easyui/jquery.easyui.min.js') }}"></script>
  <script src="{{ asset('jquery-easyui/jquery.sistema.js') }}"></script>
  <script src="{{ asset('jquery-easyui/locale/easyui-lang-es.js') }}"></script>
  <script src="{{ asset('jquery-easyui/ext/datagrid-export.js') }}"></script>
  <script src="{{ asset('jquery-easyui/ext/datagrid-filter.js') }}"></script>
  <script type="text/javascript" src="{{ asset('jquery-easyui/ext/datagrid-groupview.js') }}"></script>
  <script type="text/javascript" src="{{ asset('jquery-easyui/ext/datagrid-filter.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>

  {{-- <link rel="stylesheet" type="text/css" href="vendor/toastr/toastr.css"> --}}
  {{-- <script type="text/javascript" src="vendor/toastr/toastr.min.js"></script> --}}
</head>

<body>
  <script>
    var tokenModule = '{{ csrf_token() }}';
  </script>
  @yield('content')
  {{-- <div id="ventana"></div>
  <input id="url" value="" class="easyui-textbox" style="display: none;">
  <div class="loader"></div> --}}
</body>

</html>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="description" content="SIGEMA, SISTEMA DE GESTION MASONICO" />
  <meta name="author" content="Juan Carlos Dorado - doradojc@gmail.com" />
  <meta name="copyright" content="Juan Carlos Dorado - Carlos torrez B." />
  <meta name="robots" content="noindex" />
  <meta name="robots" content="nofollow" />
  <meta http-equiv="cache-control" content="no-cache" />
  <title>{{ $title }}</title>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/font-awesome/css/all.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/font-awesome/css/v4-shims.css') }}">
  <!-- JQuery EasyUI -->
  <link href="{{ asset('jquery-easyui/themes/default/easyui.css" rel="stylesheet') }}" />
  <link href="{{ asset('jquery-easyui/themes/icon.css') }}" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="{{ asset(Auth::user()->plantilla) }}">
  <script src="{{ asset('jquery-easyui/jquery.min.js') }}"></script>
  <script src="{{ asset('jquery-easyui/jquery.easyui.min.js') }}"></script>
  <script src="{{ asset('jquery-easyui/jquery.sistema.js') }}"></script>
  <script src="{{ asset('jquery-easyui/locale/easyui-lang-es.js') }}"></script>
  <script>
    function cambiaClave() {
      $('#dlg_clave').dialog('open').dialog('setTitle', 'Cambiar clave de ingreso');
      url = 'inicio/update_clave?_token={{ csrf_token() }}';
    }

    function enviando_cform() {
      $('#fm_clave').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (result.success === 'false') {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#dlg_clave').dialog('close'); // close the dialog
          }
        }
      });
    }

    function cambiaTemplate() {
      $('#dlg_template').dialog('open').dialog('setTitle', 'Cambiar apariencia del sistema');
      url = 'inicio/update_template?_token={{ csrf_token() }}';
    }

    function enviando_tform() {
      $('#fm_template').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            window.top.location.href = "{{ url('/') }}";
            $('#dlg_template').dialog('close'); // close the dialog
          }
        }
      });
    }
  </script>
</head>

<body class="easyui-layout">
  <div data-options="region:'north'" style="height:32px;" class="panel-title">
    <span style="cursor: pointer;float: left;padding: 2px; padding-left: 30px;font-size: 16px; position: absolute;
    text-transform: uppercase;padding-right: 77px;font-weight: 600;"><a href="" style="text-decoration: none;font-size: 16px; " class="panel-title"><i
          class="fab fa-app-store danger"></i> {{ $title }}</a> {{ Auth::user()->Rol }}</span>
    <div style="float: right; ">
      <a href="javascript:void(0)" id="tpage" class="easyui-menubutton" data-options="menu:'#mtpage',iconCls:'fa fa-user blue'" plain="false">Usuario: {{ Auth::user()->username }}</a>
      <div id="mtpage" style="width:210px;">
        <div data-options="iconCls:'fa fa-key orange'"><a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" onclick="cambiaClave();">Cambiar clave</a></div>
        <div data-options="iconCls:'fa fa-list-alt aqua'"><a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" onclick="cambiaTemplate();">Elegir Diseño</a></div>

        <div data-options="iconCls:'fa fa-file'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="#" target="_blank">Manual Secretarios Logia</a></div><div data-options="iconCls:'fa fa-weixin teal'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="https://wa.me/+numero" target="_blank">Soporte Técnico</a></div>
        {{-- <div data-options="iconCls:'fa fa-file'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="https://docs.google.com/document/d/1EkeP1AkD-A1JT9HupNWZunzkgDeGPVcDubsN4G0Ek8c/edit?usp=sharing" target="_blank">Manual Secretarios Logia</a></div> --}}
        {{-- <div data-options="iconCls:'fa fa-file'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="https://docs.google.com/document/d/1ILSgcGmQD2skn_9jqJdkwNSx_GoCUVu8MhJbWhMkLfQ/edit?usp=sharing" target="_blank">Manual Tesoreros Logia</a></div>
        <div data-options="iconCls:'fa fa-file'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="https://docs.google.com/document/d/1L9_kmTIhi8DHwu2kQvKiJ5P6U7G7IevEesYwhdSYHwo/edit?usp=sharing" target="_blank">Manual Secretarios GLD/GDR</a></div>
        <div data-options="iconCls:'fa fa-file'"><a class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;" href="https://docs.google.com/document/d/1znB24hxC2hZefqn3IekgzA21XsMqMLg_RkcrgVKhM6s/edit?usp=sharing" target="_blank">Manual Tesoreros GLD/GDR</a></div> --}}
        <div data-options="iconCls:'fa fa-plug red'"><a href="salir" class="easyui-linkbutton" data-options="width:'100%'" style="text-align: left;"> Salir del sistema</a></div>
      </div>
      {{-- <span style="padding: 8px 34px 6px 19px;border-left:1px solid #183c61;cursor: pointer;position: relative;float: left;"><i class="fa fa-user teal"></i> {{ Auth::user()->username }}</span> --}}
      {{-- <a style="padding: 7px 9px 3px 14px;font-size: 14px;float: right;text-decoration: none;" href="salir" class="panel-header"><i class="fa fa-sign-out red"></i> Salir</a> --}}
    </div>
  </div>
  <div data-options="region:'west',split:true, hideCollapsedContent:false" class="menu-user" title="&nbsp;MENU MODULOS" style="width:280px; height: 300px !important">
    <div class="easyui-accordion" style="height:100%; border: 0px;">
        {!! App\Models\Menu_items_model::getMenuUI(\Auth::user()->idRol,\Auth::user()->nivel) !!}
    </div>
  </div>
  <div data-options="region:'center'">
    <div id="tabs" class="easyui-tabs" data-options="fit:true,border:false">
      <div class="contenido" data-options="iconCls:'fa fa-university correcto'" title="Inicio" style="padding:10px;overflow:hidden;">
        <script type="text/javascript">
          $(function() {
            var dginicio = $('#dginicio').datagrid({
              url: 'inicio/get_datos?_token={{ csrf_token() }}',
              type: 'get',
              dataType: 'json',
              toolbar: '#toolbarinicio',
              pagination: false,
              fitColumns: true,
              rownumbers: true,
              singleSelect: true,
              nowrap: true,
              pageList: [20, 50, 100, 200],
              pageSize: '20',
              columns: [
                [{
                    field: 'tipo',
                    title: 'Tramite'
                  },
                  {
                    field: 'nivel',
                    title: 'Estado de tramite'
                  },
                  {
                    field: 'nLogia',
                    title: 'R:.L:.S:.'
                  },
                  {
                    field: 'numero',
                    title: 'Nro.'
                  },
                  {
                    field: 'NombreCompleto',
                    title: 'DATOS'
                  },
                  {
                    field: 'fechaModificacion',
                    title: 'Modificacion'
                  }
                ]
              ]
            });
          });
        </script>
        <table id="dginicio" class="easyui-datagrid" style="width:100%;height:100%;"></table>
        <div class="datagridtoolbar" id="toolbarinicio" style="display:inline-block">
          @if (Auth::user()->permisos == 1)
            <div style="float:left;padding: 10px 10px 5px 10px;"><b>ACTUALIZACIONES RECIENTES</b></div>
          @endif
        </div>

      </div>
    </div>
  </div>
  <!--dialogo template-->
  <div id="dlg_template" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#cambiar-buttons" data-options="iconCls:'icon-man',modal:true">
    <form id="fm_template" method="post" novalidate>
      <div style="margin-top:4px">
        <select id="template" name="template" class="easyui-combobox" style="width:100%" label="Tema:" labelWidth="100" labelPosition="left" panelHeight="auto">
          @foreach ($temas as $key => $ttt)
            @if ($key == Auth::user()->template)
              <option value="{{ $key }}" selected>{{ $ttt }}</option>
            @else
              <option value="{{ $key }}">{{ $ttt }}</option>
            @endif
          @endforeach
        </select>
      </div>
    </form>
    <div id="cambiar-buttons">
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="enviando_tform();" style="width:90px">Cambiar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_template').dialog('close');" style="width:90px">Cancelar</a>
    </div>
  </div>
  <!--dialogo clave-->
  <div id="dlg_clave" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#clave-buttons" data-options="iconCls:'icon-man',modal:true">
    <form id="fm_clave" method="post" novalidate>
      <div style="margin-top:4px"><input name="clave1" label="Clave anterior:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="clave2" label="Clave nueva" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="clave3" label="Clave nueva(repita):" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
    </form>
    <div id="clave-buttons">
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="enviando_cform();" style="width:90px">Cambiar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_clave').dialog('close');" style="width:90px">Cancelar</a>
    </div>
  </div>
</body>

</html>

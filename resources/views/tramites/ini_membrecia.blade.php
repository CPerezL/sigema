@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbar{{ $_mid }}',
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: true,
        nowrap: true,
        pageList: [20, 50, 100, 200],
        pageSize: '20',

        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'fechaCircular',
              title: 'F. Circular'
            },
            {
              field: 'fechaIniciacion',
              title: 'F. Iniciacion'
            },
            {
              field: 'numeroCertificado',
              title: 'Nº Cert.'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'Taller'
            },
            {
              field: 'numero',
              title: 'Nro'
            },
            {
              field: 'apPaterno',
              title: 'Ap. Paterno'
            },
            {
              field: 'apMaterno',
              title: 'Ap. Materno'
            },
            {
              field: 'nombres',
              title: 'Nombres'
            },
            {
              field: 'estadoPago',
              title: 'Estado Pago'
            },
            {
              field: 'estado',
              title: 'Estado Miembro'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block;">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-user correcto" onclick="copiar_datos();">Registrar Datos en el RUM</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-image activo" onclick="copiar_foto();">Copiar foto</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-users correcto" onclick="ini_listaDatos();">Buscar Miembro y asignar RUM</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar R:.L:.S:.</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchUser,prompt:'Buscar Apellido'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchUser();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function doSearchUser(value) {
      filterDatos(value, 'palabra');
    }

    function clearSearchUser() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filterrDatos('', 'palabra');
    }

    function filterDatos(value, campo) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgdatos{{ $_mid }}" class="easyui-dialog" style="width:480px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fdatos{{ $_mid }}" method="post" novalidate>
      <div style="margin-bottom:10px;margin-top:5px;margin-left:50px"><img id='foto' width="140"><label id="labelfoto" style="margin-left:10px"></label></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="nLogia" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="80"></div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveDatos_rum();" style="width:180px">Copiar Datos al RUM</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdatos{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <form id="fasignar" method="post"><input type="hidden" name="task" value="3" /></form>
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="toolbarobs{{ $_mid }}">
      @if (Auth::user()->permisos == 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square activo" onclick="asignarRUM();">Asignar RUM</a></div>
      @endif

    </div>
  </div>
  <script type="text/javascript">
    $(function() {
      var dgobs{{ $_mid }} = $('#dgobs{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_nombres?id=0',
        type: 'post',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbarobs{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: false,
        striped: true,
        autoRowHeight: true,
        pageList: [10],
        pageSize: '10',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'Paterno',
              title: 'Paterno'
            },
            {
              field: 'Materno',
              title: 'Materno'
            },
            {
              field: 'Nombres',
              title: 'Nombres'
            },
            {
              field: 'logia',
              title: 'R:.L:.S:.'
            }
          ]
        ]
      });
    });

    function copiar_datos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idMiembro > 0) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Ya tiene Registro creado'
          });
        } else {
          if (row.numeroCertificado > 0) {
            $('#fdatos{{ $_mid }}').form('clear');
            $('#fdatos{{ $_mid }}').form('load', row);
            if (row.foto) {
              $('#foto').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
              $('#foto').attr('src', $('#foto').attr('src')); //recraga imagen
            } else {
              $('#foto').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
              $('#foto').attr('src', $('#foto').attr('src'));
            }
            var nombre = row.nombres + ' ' + row.apPaterno + ' ' + row.apMaterno;
            $("#labelfoto").text(nombre);
            $('#dlgdatos{{ $_mid }}').dialog('open').dialog('setTitle', 'Registrar datos en RUM');
          } else {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>El tramite debe tener datos de certificado'
            });
          }
        }
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&task=1&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione ceremonia primero'
        });
      }
    }

    function copiar_foto() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idMiembro > 0) {
          $('#fdatos{{ $_mid }}').form('clear');
          $('#fdatos{{ $_mid }}').form('load', row);
          if (row.foto) {
            $('#foto').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
            $('#foto').attr('src', $('#foto').attr('src')); //recraga imagen
          } else {
            $('#foto').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
            $('#foto').attr('src', $('#foto').attr('src'));
          }
          var nombre = row.nombres + ' ' + row.apPaterno + ' ' + row.apMaterno;
          $("#labelfoto").text(nombre);
          $('#dlgdatos{{ $_mid }}').dialog('open').dialog('setTitle', 'Copiar foto');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Dbe asignar RUM primero</div>'
          });
        }
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&task=2&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione ceremonia primero'
        });
      }
    }

    function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_nombres?id=' + row.idTramite);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Lista de nombres');
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione Miembro</div>'
        });
      }
    }

    function asignarRUM() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      $('#fasignar').form('submit', {
        url: url,
        onSubmit: function(param) {
          param.idm = row.id;
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
            $('#dlgobs{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function saveDatos_rum() {
      $('#fdatos{{ $_mid }}').form('submit', {
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
            $('#fdatos{{ $_mid }}').form('clear');
            $('#dlgdatos{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>

@endsection

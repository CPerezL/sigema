@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    tramite = 0;
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
              field: 'idTramite',
              title: 'Tramite',
              hidden: true
            },
            {
              field: 'obstxt',
              title: 'Observaciones'
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
              field: 'documento',
              title: 'Documento'
            },
            {
              field: 'foto',
              title: 'Foto',
              hidden: 'false'
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
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="ini_listaDatos();">Gestionar observaciones</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar R:.L:.S:.</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }} </option>
            @endforeach
          </select>
        @else
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchData,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchData();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function obs_saveDatos() {
      $('#fmini0{{ $_mid }}').form('submit', {
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
            $('#fmini0{{ $_mid }}').form('clear');
            $('#dlgini0{{ $_mid }}').dialog('close'); // close the dialog
            $('#dgobs{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function delete_obs() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/delete_obs', {
              _token: tokenModule,
              id: row.idObservacion
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: '@lang('mess.ok')',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dgobs{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }

    function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_tramites?idt=' + row.idTramite);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Observaciones en tramite');
        url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite</div>'
        });
      }
    }

    function obs_close(value) {
      $('#dlgobs{{ $_mid }}').dialog('close')
      $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini0{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlgini0-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="idTramite" />
      <input type="hidden" name="idObservacion" />
      <div style="margin-top:5px"><input class="easyui-datebox" name="fechaRegistro" style="width:100%" data-options="label:'Fecha de Denuncia *:',required:true" labelWidth="170"></div>
      <div style="margin-top:5px">
        <select id="tipo" name="tipo" class="easyui-combobox" style="width:100%" label="Tipo de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          <option value="1" selected>Balota negra</option>
          <option value="2">Reporte de Miembro</option>
        </select>
      </div>
      <div style="margin-top:5px">
        <select id="estado" name="estado" class="easyui-combobox" style="width:100%" label="Estado de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          <option value="0" selected>Registrado</option>
          <option value="1">Descartado/Sin respaldo</option>
          <option value="2">Aprobado/Comprobado</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <textarea id="descripcion" name="descripcion" class="easyui-textbox" data-options="multiline:true,required:true" label="Observación:" labelPosition="top" style="width:100%;height:200px"></textarea>
      </div>
    </form>
  </div>
  <div id="dlgini0-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="obs_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:140px">Cancelar/Cerrar</a>
  </div>
  <script type="text/javascript">
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

    function doSearchData(value) {
      filterDatos(value, 'palabra');
    }

    function clearSearchData() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filterDatos('', 'palabra');
    }
    $(function() {
      var dgobs{{ $_mid }} = $('#dgobs{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_tramites?idt=' + tramite,
        type: 'get',
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
              field: 'fechaRegistro',
              title: 'Fecha'
            },
            {
              field: 'descripcion',
              title: 'Descripcion'
            },
            {
              field: 'tipotxt',
              title: 'Tipo'
            },
            {
              field: 'estadotxt',
              title: 'Estado'
            }
          ]
        ]
      });
    });

    function ini_observaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', row);
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Formulario de observación de tramite');
        url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function ini_observaEdita() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', row);
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Modificar observación de tramite');
        url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione observacion</div>'
        });
      }
    }
  </script>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true,closable:false">
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="toolbarobs{{ $_mid }}">
      @if (Auth::user()->permisos == 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square activo" onclick="ini_observaDatos();">Adicionar observacion</a></div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square alerta" onclick="ini_observaEdita();">Modificar observacion</a></div>
        @if (Auth::user()->nivel > 4)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="delete_obs();">Borrar observacion</a></div>
        @endif
      @endif
      <div style="float:right;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-remove danger" onclick="obs_close();">Cerrar</a></div>
    </div>
  </div>
@endsection

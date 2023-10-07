@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    function filtrarDatosExtras(value, campo) {
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

    function searchExtra(value) {
      filtrarDatosExtras(value, 'palabra');
    }

    function clearSearchExtra() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosExtras('', 'palabra');
    }
  </script>
  <script type="text/javascript">
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos?_token={{ csrf_token() }}',
        type: 'post',
        dataType: 'json',
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
              field: 'id',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'LogiaActual',
              title: 'Logia'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre completo'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo pago'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
      <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          <a href="#" class="easyui-linkbutton" onclick="verVisitas();"><i class="fa fa-eye activo"></i> Ver asistencias extras</a>
          @if (Auth::user()->permisos == 1)
            <a href="#" class="easyui-linkbutton" onclick="addVisitas();"><i class="fa fa-suitcase correcto"></i> Asistencia extra</a>
          @endif
        </div>
        <div class="datagrid-btn-separator"></div>
        <div style="float:left;">
          <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosExtras(rec,'taller');}">
            <option value="0">Seleccionar taller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        </div>
        <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
          <select id="gradom" name="gradom" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight: 'auto',editable:false,onChange: function(rec){filtrarDatosExtras(rec,'grado');}">
            <option value="0" selected="selected">Todos</option>
            <option value="1">Aprendiz</option>
            <option value="2">Compañero</option>
            <option value="3">Maestro</option>
            <option value="4">V:.M:. o Ex V:.M:.</option>
          </select>
        </div>
        <div style="float:left;"><input class="easyui-searchbox" style="width:180px" data-options="searcher:searchExtra,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchExtra();"></a>
        </div>
      </div>
    </div>
    <div data-options="region:'east',collapsed:false" style="width:400px;">
      <table id="dgvis{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url:'{{ $_controller }}/get_visitas?_token={{ csrf_token() }}',fitColumns:true,singleSelect:true,pagination:false,nowrap:false" toolbar="#toolbarobol{{ $_mid }}" rownumbers="true">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'fechaExtra'">F. Asistencia extra</th>
            <th data-options="field:'nombre'">Razon</th>
            <th data-options="field:'Taller'">Logia visitada</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbarobol{{ $_mid }}" style="border-bottom:1px solid #ddd;height:32px;padding:2px 5px;">
        @if (Auth::user()->permisos == 1)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="removeVisita();">Eliminar visita</a></div>
        @endif
      </div>
      <div id="dlg3{{ $_mid }}" class="easyui-dialog" style="width:420px;height:auto;padding:5px 5px" closed="true" buttons="#dlgvv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fm3{{ $_mid }}" method="post" novalidate>
          <div style="margin-bottom:5px">
            <b>Fecha de visita o asistencia extra: </b> <input id="fvisita" name="fvisita" type="text" class="easyui-datebox" required="required">
          </div>
          <div style="margin-top:4px">
            <select id="logiaVisita" name="logiaVisita" class="easyui-combobox" style="width:99%" data-options="panelHeight:300,valueField: 'id',textField: 'text',editable:true">
              <option value="0">Asistencia extra</option>
              @foreach ($alllogias as $key => $logg)
                <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:4px">
            <select id="motivo" name="motivo" class="easyui-combobox" style="width:99%" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false">
              @foreach ($motivos as $key => $lom)
                <option value="{{ $key }}">{{ $lom }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>
    <div id="dlgvv-buttons{{ $_mid }}">
      @if (Auth::user()->permisos == 1)
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveVisitas();" style="width:90px">Grabar</a>
      @endif
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
    </div>
    <script type="text/javascript">
      function removeVisita() {
        var row = $('#dgvis{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_visita', {
                _token: tokenModule,
                id: row.idExtra
              }, function(result) {
                if (!result.success) {
                  $.messager.show({ // show error message
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div>' + result.Msg
                  });
                } else {
                  $.messager.show({
                    title: 'Correcto',
                    msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                  });
                  $('#dgvis{{ $_mid }}').datagrid('reload'); // reload the user data
                }
              }, 'json');
            }
          });
        }
      }

      function addVisitas() {
        var value = $('#dg{{ $_mid }}').datagrid('getSelected');
        if (value) {
          $('#dlg3{{ $_mid }}').dialog('open').dialog('setTitle', 'Agregar visita o asistencia extra');
          url = '{{ $_controller }}/set_visita?idm=' + value.id + '&_token=' + tokenModule;
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
          });
        }
      }

      function saveVisitas(value) {
        $('#fm3{{ $_mid }}').form('submit', {
          url: url,
          onSubmit: function() {
            return $(this).form('validate');
          },
          success: function(result) {
            var result = eval('(' + result + ')');
            if (!result.success) {
              $.messager.show({
                title: 'Error',
                msg: '<div class="messager-icon messager-error"></div>' + result.Msg
              });
            } else {
              $.messager.show({
                title: 'Correcto',
                msg: '<div class="messager-icon messager-info"></div>' + result.Msg
              });
              $('#fm3{{ $_mid }}').form('clear');
              $('#dlg3{{ $_mid }}').dialog('close'); // close the dialog
              $('#dgvis{{ $_mid }}').datagrid('reload'); // reload the user data
            }
          }
        });
      }

      function verVisitas() {
        var value = $('#dg{{ $_mid }}').datagrid('getSelected');
        if (value) {
          $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
            valor: value.id,
            filtro: 'idm',
            _token: tokenModule
          }, function(result) {
            if (result.success) {
              $('#dgvis{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
          });
        }
      }
    </script>
  </div>
@endsection

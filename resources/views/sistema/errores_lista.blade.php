@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="
      url: '{{ $_controller }}/get_datos',
      queryParams:{
      _token: tokenModule
      },
      " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true"
    nowrap="false" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="username">Usuario Rep</th>
        <th field="valletxt">Valle</th>
        <th field="logiatxt">R:.L:.S:.</th>
        <th field="modulotxt">Modulo</th>
        <th field="estadotxt">Estado</th>
        <th field="descripcion" width="25%">Descripcion</th>
        <th field="respuesta" width="25%">Respuesta</th>
        <th field="fechaModificacion">Modificacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="width:160,panelHeight:500,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos_17(rec,'valle');}">
          <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos_17(rec,'valle');}">
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg blue2" onclick="newItem_138();">Nuevo reporte de error</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="editItem_138();">Editar reporte</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg red" onclick="destroyItem_138();" id="borrarbtn{{ $_mid }}">Borrar Reporte</a></div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearch_17,prompt:'Buscar dato'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearch_17();"></a>
    </div>
  </div>
  <script type="text/javascript">
    function filtrarDatos_17(value, campo) {
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

    function doSearch_17(value) {
      filtrarDatos_17(value, 'palabra');
    }

    function clearSearch_17() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatos_17('', 'palabra');
    }
  </script>
  <script type="text/javascript">
    var url;

    function newItem_138() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.newrepo')');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editItem_138() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado > 1) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>No se puede modificar porque ya fue solucionado o descartado</div>'
          });
        } else {
          $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.nuevo', ['job' => 'reporte de error'])');
          $('#fm{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datos?id=' + row.id + '&_token=' + tokenModule;
        }
      }
    }

    function saveItem_138() {
      $('#fm{{ $_mid }}').form('submit', {
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
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyItem_138() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado > 0) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>No se puede modificar porque ya fue solucionado o descartado</div>'
          });
        } else {
          $.messager.confirm('@lang('mess.question')', '@lang('mess.questdel')?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_datos', {
                _token: tokenModule,
                id: row.id
              }, function(result) {
                if (result.errorMsg) {
                  $.messager.show({ // show error message
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div>' + result.errorMsg
                  });
                } else {
                  $.messager.show({
                    title: 'Correcto',
                    msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                  });
                  $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
                }
              }, 'json');
            }
          });
        }
      }

    }
  </script>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <select id="modulo" class="easyui-combobox" name="modulo" style="width:98%" label="Modulo con error:" labelWidth="130" labelPosition="top" editable="true" required="required">
          @foreach ($modulos as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px"><input name="descripcion" id="descripcion" class="easyui-textbox" label="Descripcion de Error:" labelPosition="top" multiline="true" style="width:98%;height:140px" required="required"></div>
    </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveItem_138();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options=" url:'{{ $_controller }}/get_datos', queryParams:{ _token: tokenModule },
             " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idCargo" hidden="true">ID</th>
        <th field="orden">Orden</th>
        <th field="cargo">Cargo de Oficial</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Comision: </b>
      <select id="fvvalles" name="fvvalles" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos_19(rec,'comision');}" style="width:200px;">
        <option value="0">Seleccionar Tipo Valle</option>
        @foreach ($comisiones as $key => $ver)
          <option value="{{ $key }}">{{ $ver }}</option>
        @endforeach
      </select>
    </div>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg blue2" onclick="vvcarg_newItem();">Nuevo Cargo</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="vvcarg_editItem();">Editar Cargo</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg red" onclick="vvcarg_destroyItem();" id="borrarbtn{{ $_mid }}">Borrar Cargo</a>
  </div>
  <script type="text/javascript">
    var url;
    /*funcnode filtro de datos*/
    function filtrarDatos_19(value, campo) {
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

    function vvcarg_newItem() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Dato');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function vvcarg_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Datos');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?id=' + row.idCargo + '&_token=' + tokenModule;
      }
    }

    function vvcarg_saveItem() {
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

    function vvcarg_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idCargo
            }, function(result) {
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
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      }
    };
  </script>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:0px"><input name="cargo" id="cargo" required="required" label="Cargo Oficial:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:96%"></div>
      <div style="margin-top:4px"><input id="orden" name="orden" class="easyui-numberspinner" required="required" data-options="min:1,max:100,editable:false" label="Posicion/Orden:" labelPosition="left" labelWidth="150" style="width:96%;">
    </form>
  </div>
  <div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="vvcarg_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options=" url:'{{ $_controller }}/get_datos?_token={{ csrf_token() }}', queryParams:{ _token: tokenModule },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idComision" hidden="true">ID</th>
        <th field="orden">Orden</th>
        <th field="nombre">Cargo de Oficial</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg correcto" onclick="glbcom_newItem();">Nueva Comision</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg success" onclick="glbcom_editItem();">Editar Comisiones</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg danger" onclick="glbcom_destroyItem();" id="borrarbtn{{ $_mid }}">Borrar Comision</a>
  </div>
  <script type="text/javascript">
    var url;

    function glbcom_newItem() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Dato');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function glbcom_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Datos');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idComision;
      }
    }

    function glbcom_saveItem() {
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

    function glbcom_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idComision
            }, function(result) {
              if (result.errorMsg) {
                $.messager.show({
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
  </script>

  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:0px"><input name="nombre" id="nombre" required="required" label="Nombre de Cargo:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:96%"></div>
      <div style="margin-top:4px"><input id="orden" name="orden" required="required" label="Posicion/Orden:" labelPosition="left" labelWidth="150" style="width:96%;"></div>
    </form>
  </div>
  <div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="glbcom_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script>
    $('#orden').numberspinner({
      min: 1,
      max: 100,
      editable: false,
    });
  </script>
@endsection

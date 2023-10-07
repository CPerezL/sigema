@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="
       url: '{{ $_controller }}/get_datos',
       queryParams: {
        _token: tokenModule
      },
       rowStyler: function(index,row){
       if (row.activo == '1') {
       return {class:'activo'};
       }
       else{return {class:'inactivo'} }
       }
       "
    toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idDescuento">ID</th>
        <th field="comentario">Descripcion</th>
        <th field="desde">Mes inicio</th>
        <th field="hasta">Mes fin</th>
        <th field="numeroCuotas">N. Cuotas</th>
        <th field="activotxt">Estado</th>
        <th field="username">Usuario</th>
        <th field="fechaCreacion">Modificacion</th>
        <th field="fechaCreacion">Creacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newRegistromm();">Nuevo Descuento</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editRegistromm();">Editar Descuento</a>
  </div>
  <script type="text/javascript">
    var url;

    function newRegistromm() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Descuento');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editRegistromm() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Descuento');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?id=' + row.idDescuento + '&_token=' + tokenModule;
      }
    }

    function saveRegistromm() {
      $('#fm{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: result.Msg,
              icon: 'info'
            });
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({
              title: 'Error',
              msg: result.Msg
            });
          }
        }
      });
    }
  </script>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:440px;height:340px;padding:5px 5px" closed="true" buttons="#dlg-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input id="desdefrm" name="desdefrm" type="text" class="easyui-datebox" required="required" label="Mes inicial de descuento:" labelPosition="left" labelWidth="180" style="width:96%;">
      </div>
      <div style="margin-top:4px">
        <input id="hastafrm" name="hastafrm" type="text" class="easyui-datebox" required="required" label="Mes final de descuento:" labelPosition="left" labelWidth="180" style="width:96%;">
      </div>
      <div style="margin-top:4px">
        <input name="comentario" id="comentario" class="easyui-textbox" label="Descripcion:" labelPosition="top" multiline="true" style="width:96%;height:80px">
      </div>
      <div style="margin-top:4px">
        <select id="activo" class="easyui-combobox" name="activo" style="width:96%" label="Estado de descuento:" labelWidth="150" labelPosition="left" panelHeight="auto">
          <option value="0">No habilitado</option>
          <option value="1">Habilitado</option>
        </select>
      </div>
    </form>
  </div>
  <div id="dlg-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveRegistromm();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

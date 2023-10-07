@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options=" url:'{{ $_controller }}/get_datos', queryParams:{ _token: tokenModule },
   " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="false" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idconfig">ID</th>
        <th field="gestion">Gestion Actual</th>
        <th field="title" hidden="true">Titulo</th>
        <th field="fechaApertura">F. Inicio de trabajos</th>
        <th field="fechaCierre">F. Receso de trabajos</th>
        <th field="mesesDeuda">Meses para Suspencion</th>
        <th field="mesesIrregular">Meses para Irregular</th>
        <th field="diasCircular">Cant. dias de circularización</th>
        <th field="mesesAumento">Meses para Aumento</th>
        <th field="asisAumento">% para Aumento</th>
        <th field="mesesExaltacion">Meses para Exaltacion</th>
        <th field="asisExaltacion">% para Exaltacion</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg green" onclick="config_edit();">Editar Configuracion</a>
  </div>
  <script type="text/javascript">
    var url;
    var vista{{ $_mid }} = 0;

    function config_edit() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Configuracion');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule;
      }
    }

    function config_saveItem() {
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
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: '@lang('mess.ok')',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:0px">
        <input id="gestion" name="gestion" class="easyui-numberspinner" label="Gestion Actual:" labelPosition="left" labelWidth="150" style="width:100%;" data-options="min:2022,max:2030,editable:false">
      </div>
      <div style="margin-top:4px">
        <input class="easyui-datebox" style="width:100%" label="Fecha de apertura" labelPosition="left" labelWidth="150" name="fechaApertura" id="fechaApertura" required="required">
      </div>
      <div style="margin-top:4px">
        <input class="easyui-datebox" style="width:100%" label="Fecha de receso" labelPosition="left" labelWidth="150" name="fechaCierre" id="fechaCierre" required="required">
      </div>
      <div style="margin-top:0px">
        <input id="diasCircular" name="diasCircular" class="easyui-numberspinner" label="Dias de Circularización:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:120,editable:true">
      </div>
      <div style="margin-top:0px">
        <input id="mesesDeuda" name="mesesDeuda" class="easyui-numberspinner" label="N. Meses para suspencion:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:24,editable:false">
      </div>
      <div style="margin-top:0px">
        <input id="mesesDeuda" name="mesesIrregular" class="easyui-numberspinner" label="N. Meses para ser Irregular:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:48,editable:false">
      </div>
      <div style="margin-top:0px">
        <input id="asisAumento" name="asisAumento" class="easyui-numberspinner" label="Porcentaje para Aumento:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:100,editable:true">
      </div>
      <div style="margin-top:0px">
        <input id="mesesAumento" name="mesesAumento" class="easyui-numberspinner" label="Meses para Aumento:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:100,editable:true">
      </div>
      <div style="margin-top:0px">
        <input id="asisExaltacion" name="asisExaltacion" class="easyui-numberspinner" label="Porcentaje para Exaltacion:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:100,editable:true">
      </div>
      <div style="margin-top:0px">
        <input id="mesesExaltacion" name="mesesExaltacion" class="easyui-numberspinner" label="Meses para Exaltacion:" labelPosition="left" labelWidth="220" style="width:100%;" data-options="min:1,max:100,editable:true">
      </div>
    </form>
  </div>
  <div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="config_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

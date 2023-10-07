@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options=" url:'{{ $_controller }}/get_datos', queryParams:{ _token: tokenModule },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idGestion" hidden="true">ID</th>
        <th field="desde" hidden="true">dd</th>
        <th field="hasta" hidden="true">hh</th>
        <th field="descripcion">Descripcion</th>
        <th field="gestion">Gestion</th>
        <th field="fechaModificacion">fecha Modificacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Comision: </b>
      <select id="fglbcar2" name="fglbcar2" class="easyui-combobox" data-options="panelHeight:300,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarGlbGestiones(rec,'comision');}">
        <option value="0">Seleccionar comision</option>
        @foreach ($comisiones as $key => $ver)
          <option value="{{ $key }}">{{ $ver }}</option>
        @endforeach
      </select>
    </div>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg correcto" onclick="glbges_newItem();">Nueva Gestion</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg warning" onclick="glbges_editItem();">Editar Gestion</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg danger" onclick="glbges_destroyItem();" id="borrarbtn{{ $_mid }}">Borrar Gestion</a>
  </div>
  <script type="text/javascript">
    var url;

    function filtrarGlbGestiones(value, campo) {
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

    function glbges_newItem() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Datos');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function glbges_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Datos');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idGestion;
      }
    }

    function glbges_saveItem() {
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

    function glbges_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idGestion
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
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:0px"><input name="descripcion" id="descripcion" required="required" label="Descripcion de gestion:" labelPosition="top" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:4px"><input id="desde" name="desde" required="required" class="easyui-numberspinner" label="Año inicio:" labelPosition="left" labelWidth="120" data-options="min:1950,max:2030,editable:true" style="width:70%;"></div>
      <div style="margin-top:4px"><input id="hasta" name="hasta" required="required" class="easyui-numberspinner" label="Año final:" labelPosition="left" labelWidth="120" data-options="min:1950,max:2030,editable:true" style="width:70%;"></div>
    </form>
  </div>
  <div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="glbges_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="
       url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[200]" pageSize="200">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="tipo">Tipo</th>
        <th field="activotxt">Activo</th>
        <th field="nombre">Entidad</th>
        <th field="entidad">Nombre</th>
        <th field="codigo">Cod e BISA</th>
        <th field="cuenta">Cuenta</th>
        <th field="banco">Banco</th>
        <th field="usuario">Usuario</th>
        <th field="fechaModificacion">Fecha de Modificacion</th>
        <th field="fechaCreacion">Fecha de creacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">

    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg teal" onclick="entidad_newItem(1);">Nuevo Valle</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg teal" onclick="entidad_newItem(2);">Nuevo Taller</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg orange" onclick="entidad_editItem();">Editar Dato</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg danger" onclick="entidad_destroyItem();" id="borrarbtn{{ $_mid }}">Borrar Dato</a></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
  </div>
  <script type="text/javascript">
    var url;
    var vista{{ $_mid }} = 0;

    function entidad_newItem(tipo) {
      if (tipo == '1') {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Valle');
        $('#fm{{ $_mid }}').form('clear');
        $('#valle').removeAttr('disabled')
        url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
      } else {
        $('#dlgtt{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Taller');
        $('#fmtt{{ $_mid }}').form('clear');
        $('#numero').combobox({
          disabled: false
        });
        url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
      }
    }

    function entidad_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.tipo == '4') {

          $('#dlgtt{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar logia');
          $('#fmtt{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datos?_token=' + tokenModule;
        } else if (row.tipo == '3') {

          $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Valle');
          $('#fm{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datos?_token=' + tokenModule;
        } else {
          $('#dlggg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Entidad');
          $('#fmgg{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datos?_token=' + tokenModule;
        }
      }
    }

    function entidad_saveItem() {
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

    function entidad_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idEntidad > 2) {
          $.messager.confirm('@lang('mess.question')', '@lang('mess.questdel')', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_datos', {
                _token: tokenModule,
                id: row.idEntidad
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
        } else {
          $.messager.alert('Error', 'Este dato no es borrable');
        }
      }
    }

    function entidad_saveItemtt() {
      $('#fmtt{{ $_mid }}').form('submit', {
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
            $('#fmtt{{ $_mid }}').form('clear');
            $('#dlgtt{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function entidad_saveItemgg() {
      $('#fmgg{{ $_mid }}').form('submit', {
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
            $('#fmgg{{ $_mid }}').form('clear');
            $('#dlggg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--valles-->
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="tipo" value="" />
      <input type="hidden" name="idEntidad" value="" />
      <div style="margin-top:4px">
        <select id="valle" class="easyui-combobox" name="valle" style="width:95%" label="Valle:" labelWidth="130" labelPosition="left" editable="false" data-options="disabled:false">
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        </select>
        <div style="margin-top:0px"><input name="cuenta" id="cuenta" label="Nro Cuenta:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="banco" id="banco" label="Nombre Banco:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="codigo" id="codigo" label="Cod e BISA:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
    </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="entidad_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <!--Talleres -->
  <div id="dlgtt{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttonstt{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmtt{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="tipo" value="" />
      <input type="hidden" name="idEntidad" value="" />
      <input type="hidden" name="llave" value="" />
      <div style="margin-top:4px">
        <select id="numero" class="easyui-combobox" name="numero" style="width:95%" label="Taller:" labelWidth="130" labelPosition="left" editable="false" disabled>
          @foreach ($talleres as $key => $tall)
            <option value="{{ $key }}">{{ $tall }}</option>
          @endforeach
        </select>
        <div style="margin-top:0px"><input name="cuenta" id="cuenta" label="Nro Cuenta:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="banco" id="banco" label="Nombre Banco:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="codigo" id="codigo" label="Cod e BISA:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <select id="activo" class="easyui-combobox" name="activo" style="width:95%" label="Activo:" labelWidth="130" labelPosition="left" editable="false" panelHeight="auto">
        <option value="0">Desactivado</option>
        <option value="1">Activo</option>
      </select>
      </div>
    </form>
  </div>
  <div id="dlgl-buttonstt{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="entidad_saveItemtt();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgtt{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>

  <!--glb comap -->
  <div id="dlggg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttonsgg{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmgg{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="tipo" value="" />
      <input type="hidden" name="idEntidad" value="" />
      <div style="margin-top:4px">
        <div style="margin-top:0px"><input name="entidad" id="entidad" label="Entidad:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" disabled="true"></div>
        <div style="margin-top:0px"><input name="cuenta" id="cuenta" label="Nro Cuenta:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="banco" id="banco" label="Nombre Banco:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
        <div style="margin-top:0px"><input name="codigo" id="codigo" label="Cod e BISA:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
    </form>
  </div>
  <div id="dlgl-buttonsgg{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="entidad_saveItemgg();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlggg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

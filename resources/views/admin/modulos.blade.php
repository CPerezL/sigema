@extends('layouts.easyuitab')
@section('content')
  <table id="dguu{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="
       url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       rowStyler: function (index, row) {
        if (row.estado == '2') {
        return {class:'activo'};
        } else if (row.estado == '1') {
        return {class:'inactivo'};
        } else {
        return {class:'alerta'};
        }
        }
       "
    toolbar="#toolbaruu{{ $_mid }}" pagination="true" rownumbers="true" nowrap="false" fitColumns="true" singleSelect="true" pageList="[200,250,300]" pageSize="200">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" sortable="true">ID</th>
        <th field="name" sortable="true">Modulo</th>
        <th field="link" sortable="true">Enlace</th>
        <th field="niveltxt">Nivel de uso</th>
        <th field="version">Version</th>
        <th field="estadotxt">Estado</th>
        <th field="comentarios">Comentarios</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbaruu{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="level" name="level" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMod(rec,'level');}">
        <option value="0" selected="selected">Todos los Niveles</option>
        @foreach ($levels as $key => $rrr)
          <option value="{{ $key }}">{{ $rrr }}</option>
        @endforeach
      </select>
    </div>
    <div style="float:left;">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newModulo();">Nuevo</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editModulo();">Editar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyModulo();">Borrar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-filter" onclick="editRoles();">Roles asignados</a>
      <a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dguu{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a>
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:120px" data-options="searcher:doSearchMod,prompt:'Buscar modulo'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchMod();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    /*funcnode filtro de datos*/
    function filtrarDatosMod(value, campo) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          $('#dguu{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function doSearchMod(value) {
      filtrarDatosMod(value, 'palabra');
    }

    function clearSearchMod() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filtrarDatosMod('', 'palabra');
    }

    function newModulo() {
      $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.nuevo",["job"=>"Modulo"])');
      $('#fmuu{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editModulo() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Modulo');
        $('#fmuu{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }

    function saveModulo() {
      $('#fmuu{{ $_mid }}').form('submit', {
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
            $('#fmuu{{ $_mid }}').form('clear');
            $('#dlguu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyModulo() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', '@lang("mess.questdel")', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.id
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang("mess.alert")',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }
  </script>
  <div id="dlguu{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:5px 5px" closed="true" buttons="#dlguu-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmuu{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <div style="margin-top:4px">
          <input name="name" id="name" label="Nombre Modulo:" labelPosition="left" labelWidth="110" class="easyui-textbox" style="width:100%" required="true">
        </div>
        <div style="margin-top:4px">
          <input name="link" id="link" label="Enlace:" labelPosition="left" labelWidth="100" class="easyui-textbox" style="width:100%" required="true">
        </div>
        <div style="margin-top:4px">
          <select id="level" class="easyui-combobox" name="level" style="width:100%" label="Nivel de usuario:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            @foreach ($levels as $key => $levs)
              <option value="{{ $key }}">{{ $levs }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-top:4px">
          <select id="estado" class="easyui-combobox" name="estado" style="width:100%" label="Estado:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            <option value="0">No revisado</option>
            <option value="1">Para revisar</option>
            <option value="2">Revisado</option>
          </select>
        </div>
        <div style="margin-top:4px">
          <input id="version" name="version" class="easyui-numberspinner" style="width:100%" required="required" label="Version:" labelWidth="130" labelPosition="left" data-options="min:0,max:100,editable:false">
        </div>
        <div style="margin-top:4px">
          <input name="comentarios" id="comentarios" class="easyui-textbox" label="Comentarios:" labelPosition="top" multiline="true" value="" style="width:100%;height:80px">
        </div>
    </form>
  </div>

  <div id="dlguu-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveModulo();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlguu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>

  <div id="dlgmods{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:0px" closed="true" buttons="#dlgmods-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmmods{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="roles">
      <div class="easyui-datalist" id="modlist" style="width:100%;height:250px"
        data-options="
            url: '',
            method: 'get',
            lines:true,
            valueField: 'id',
            textField: 'name',
            checkbox: true,
            selectOnCheck: false,
            checkOnSelect:true,
            onBeforeSelect: function(){return false;},
            ">
      </div>
    </form>
  </div>
  <div id="dlgmods-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveRoles();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgmods{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function editRoles() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        url = '{{ $_controller }}/get_roles?id=' + row.id + '&_token={{ csrf_token() }}';
        $('#modlist').datalist('load', url);
        $('#dlgmods{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.editar",["job"=>"Roles de Modulo"])');
        url = '{{ $_controller }}/update_roles?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }

    function saveRoles() {
      var ids = [];
      $("form input:checkbox").each(function() {
        if (this.checked === true) {
          ids.push(this.value);
        }
      });
      if (ids.length > 0) {
        lista = ids.join(',');
        $('input[name=roles]').val(lista);
        $('#fmmods{{ $_mid }}').form('submit', {
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
              //$('#fmmods{{ $_mid }}').form('clear');
              $('#dlgmods{{ $_mid }}').dialog('close'); // close the dialog
              $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
            }
          }
        });
      } else {
        $.messager.show({
          title: 'Correcto',
          msg: '<div class="messager-icon messager-info"></div>@lang("mess.alertdata")'
        });
      }
    }
  </script>
@endsection

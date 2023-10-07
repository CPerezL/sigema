@extends('layouts.easyuitab')
@section('content')
  <table id="dguu{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="
       url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       }
       " toolbar="#toolbaruu{{ $_mid }}" pagination="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="name">Rol</th>
        <th field="guard_name">Guard</th>
        <th field="niveltxt">Nivel de Rol</th>
        <th field="nmodulos">Modulos</th>
        <th field="updated_at">Modificacion</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbaruu{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="level" name="level" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosRol(rec,'level');}">
        <option value="0" selected="selected">Todos los Niveles</option>
        @foreach ($levels as $key => $rrr)
          <option value="{{ $key }}">{{ $rrr }}</option>
        @endforeach
      </select>
    </div>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newRol();">Nuevo Rol</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editRol();">Editar Rol</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyRol();">Borrar Rol</a>
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchRol,prompt:'Buscar rol'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchRol();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;

    function filtrarDatosRol(value, campo) {
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

    function doSearchRol(value) {
      filtrarDatosRol(value, 'palabra');
    }

    function clearSearchRol() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filtrarDatosRol('', 'palabra');
    }

    function newRol() {
      $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Rol');
      $('#fmuu{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editRol() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Rol');
        $('#fmuu{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-alert"></div><div>Seleccione rol primero</div>'
        });
      }
    }

    function saveRol() {
      roltxt = $('#idRol option:selected').text();
      $('input[name=rol]').val(roltxt);
      $('#fmuu{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
                title: '@lang("mess.error")',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
                title: '@lang("mess.ok")',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fmuu{{ $_mid }}').form('clear');
            $('#dlguu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyRol() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang("mess.alertquest")', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.id
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: '@lang("mess.error")',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: '@lang("mess.ok")',
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
          msg: '<div class="messager-icon messager-alert"></div><div>@lang("mess.alertdata")</div>'
        });
      }
    }
  </script>
  <div id="dlguu{{ $_mid }}" class="easyui-dialog" style="width:460px;height:auto;padding:5px 5px" closed="true" buttons="#dlguu-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmuu{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input type="hidden" name="rol">
      </div>
      <div style="margin-top:4px">
        <div style="margin-top:4px">
          <input name="name" id="name" label="Nombre del Rol:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:98%" required="true">
        </div>
        <div style="margin-top:4px">
          <select id="level" class="easyui-combobox" name="level" style="width:98%" label="Nivel de usuario:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            @foreach ($levels as $key => $levs)
              <option value="{{ $key }}">{{ $levs }}</option>
            @endforeach
          </select>
        </div>
    </form>
  </div>

  <div id="dlguu-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveRol();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlguu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

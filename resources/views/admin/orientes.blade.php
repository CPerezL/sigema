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
        <th field="orientetxt">Oriente</th>
        <th field="gradoMinimotxt">Grado minimo</th>
        <th field="gradoMaximotxt">Grado maximo</th>
        <th field="pais">Pais</th>
        <th field="fechaModificacion">Modificacion</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbaruu{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="oriente" name="oriente" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosOri(rec,'padre');}">
        <option value="0" selected="selected">Todos los Orientes</option>
        @foreach ($oris as $key => $rrr)
          <option value="{{ $key }}">{{ $rrr }}</option>
        @endforeach
      </select>
    </div>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newOriente();">Nuevo Oriente</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editOriente();">Editar Oriente</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyOriente();">Borrar Oriente</a>
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchOri,prompt:'Buscar rol'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchOri();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    /*funcnode filtro de datos*/
    function filtrarDatosOri(value, campo) {
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

    function doSearchOri(value) {
      filtrarDatosOri(value, 'palabra');
    }

    function clearSearchOri() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filtrarDatosOri('', 'palabra');
    }

    function newOriente() {
      $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.nuevo",["job"=>"Oriente"])');
      $('#fmori{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editOriente() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.editar",["job"=>"Oriente"])');
        $('#fmori{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idOriente;
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveOriente() {
      $('#fmori{{ $_mid }}').form('submit', {
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
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fmori{{ $_mid }}').form('clear');
            $('#dlguu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyOriente() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
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
                  title: '@lang('mess.ok')',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <div id="dlguu{{ $_mid }}" class="easyui-dialog" style="width:460px;height:auto;padding:5px 5px" closed="true" buttons="#dlguu-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmori{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input type="hidden" name="rol">
      </div>
      <div style="margin-top:4px">
        <div style="margin-top:4px">
          <input name="oriente" id="oriente" label="Nombre de Oriente:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="true">
        </div>
        <div style="margin-top:4px">
          <input name="pais" id="pais" label="Pais:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="true">
        </div>
        <div style="margin-top:4px">
          <select id="gradoMinimo" class="easyui-combobox" name="gradoMinimo" style="width:100%" label="Grado minimo:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            <option value="1">Aprendiz</option>
            <option value="2">Compañero</option>
            <option value="3">Maestro</option>
            <option value="4">Ex V.M./Past Master</option>
          </select>
        </div>
        <div style="margin-top:4px">
          <select id="gradoMaximo" class="easyui-combobox" name="gradoMaximo" style="width:100%" label="Grado maximo:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            <option value="1">Aprendiz</option>
            <option value="2">Compañero</option>
            <option value="3">Maestro</option>
            <option value="4">Ex V.M./Past Master</option>
          </select>
        </div>
        <div style="margin-top:4px">
          <select id="idOrientePadre" class="easyui-combobox" name="idOrientePadre" style="width:100%" label="Oriente Padre:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
            <option value="0">Oriente principal</option>
            @foreach ($oris as $key => $levs)
              <option value="{{ $key }}">{{ $levs }}</option>
            @endforeach
          </select>
        </div>
    </form>
  </div>
  <div id="dlguu-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveOriente();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlguu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

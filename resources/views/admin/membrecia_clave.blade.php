@extends('layouts.easyuitab')
@section('content')
  <script>
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbar{{ $_mid }}',
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: true,
        nowrap: true,
        pageList: [50, 100, 200],
        pageSize: '50',
        pagination: false,
        groupFormatter: function(value, rows) {
          return value + ' - ' + rows.length + ' registros';
        },
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'valletxt',
              title: 'Valle de uso'
            },
            {
              field: 'info',
              title: 'Tipo Clave'
            },
            {
              field: 'clave',
              title: 'Clave'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"> <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newClave();">Nueva clave</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editClave();">Editar Clave</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyClave();">Borrar Clave</a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function newClave() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.nuevo', ['job' => 'Clave'])');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editClave() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.editar', ['job' => 'clave'])');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idClave;
      } else {
        $.messager.show({
          title: 'No seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveClave() {
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
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyClave() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang('mess.question')', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idClave
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
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
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
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:300px;height:auto;" closed="true" buttons="#buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate><input type="hidden" name="orden" value="" />
      <div style="margin-top:4px">
        <select id="valle" class="easyui-combobox" name="valle" style="width:95%" label="Valle Clave:" labelWidth="130" labelPosition="left" required="required" editable="false">
          <option value="0">General(Todos)</option>
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="tipo" class="easyui-combobox" name="tipo" style="width:95%" label="Tipo Clave:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="1">Palabra de pase</option>
          <option value="2">Clave General</option>
        </select>
      </div>
      <div class="easyui-panel" title="Palabra Clave" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="clave" id="clave" label="Clave:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <input type="hidden" name="idClave">
    </form>
  </div>
  <div id="buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveClave();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

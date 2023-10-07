@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
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
        view: groupview,
        groupField: 'valle',
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
              field: 'orden',
              title: 'Orden'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'miembro',
              title: 'Tipo Tramite'
            },
            {
              field: 'concepto',
              title: 'Concepto'
            },
            {
              field: 'montoglb',
              title: 'Monto GLB'
            },
            {
              field: 'montogdr',
              title: 'Monto GDR'
            },
            {
              field: 'montocomap',
              title: 'Monto COMAP'
            },
            {
              field: 'montoTotal',
              title: 'Total'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"> <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newMontoTra();">Nuevo Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editMontoTra();">Editar Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyMontoTra();">Borrar Monto</a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function newMontoTra() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Monto de tramite');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editMontoTra() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar usuario');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveMontoTra() {
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

    function destroyMontoTra() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idValle,
              ord: row.orden,
              miembro: row.miembro
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
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
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;" closed="true" buttons="#buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate><input type="hidden" name="orden" value="" />
      <div style="margin-top:4px">
        <select id="idValle" class="easyui-combobox" name="idValle" style="width:95%" label="Valle Tramite:" labelWidth="130" labelPosition="left" required="required" editable="false">
          <option value="0">General(Todos)</option>
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="miembro" class="easyui-combobox" name="miembro" style="width:95%" label="Tipo de tramite:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          @foreach ($tipos as $key => $dta)
            <option value="{{ $key }}_{{ $dta }}">{{ $dta }}</option>
          @endforeach
        </select>
      </div>
      <div class="easyui-panel" title="Cuota GLB" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="montoglb" id="montoglb" label="Monto GLB:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <div class="easyui-panel" title="Cuota GDR" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="montogdr" id="montogdr" label="Monto GDR:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <div class="easyui-panel" title="Cuota COMAP" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="montocomap" id="montocomap" label="Monto COMAP:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
    </form>
  </div>
  <div id="buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMontoTra();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

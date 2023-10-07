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
        pagination: false,
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
              title: 'Tipo Miembro'
            },
            {
              field: 'concepto',
              title: 'Concepto'
            },
            {
              field: 'montoglb',
              title: 'Monto GLSP',
              hidden: true
            },
            {
              field: 'montocomap',
              title: 'Monto SALUD',
              hidden: true
            },
            {
              field: 'montoglbtxt',
              title: 'Monto GLSP'
            },
            {
              field: 'montocomaptxt',
              title: 'Monto SALUD'
            },
            {
              field: 'fechaIniciotxt',
              title: 'F. Inicio'
            },
            {
              field: 'fechaFintxt',
              title: 'F. Fin'
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
      <div style="float:left;"> <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newMontoOb();">Nuevo Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editMontoOb();">Editar Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyMontoOb();">Borrar Monto</a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function newMontoOb() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo usuario');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editMontoOb() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar usuario');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: '@lang("mess.alert")',
          msg: '<div class="messager-icon messager-warning"></div>@lang("mess.alertdata")'
        });
      }
    }

    function saveMontoOb() {
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

    function destroyMontoOb() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang("mess.question")', '@lang("mess.questdel")', function(r) {
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
          title: 'No seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;" closed="true" buttons="#buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate><input type="hidden" name="orden" value="" />
      <div style="margin-top:4px">
        <select id="idValle" class="easyui-combobox" name="idValle" style="width:95%" label="Valle Cuota:" labelWidth="130" labelPosition="left" required="required" editable="false">
          <option value="0">General(Todos)</option>
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="miembro" class="easyui-combobox" name="miembro" style="width:95%" label="Tipo miembro:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          @foreach ($tipos as $key => $dta)
            <option value="{{ $dta }}">{{ $dta }}</option>
          @endforeach
        </select>
      </div>
      <div class="easyui-panel" title="Cuota GLSP" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="montoglb" id="montoglb" label="Monto GLSP:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <div class="easyui-panel" title="Cuota Fondo de Salud" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="montocomap" id="montoglb" label="Fondo Salud:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <div class="easyui-panel" title="Fechas de uso de monto programado" style="width:100%;padding:5px;">
        <div style="margin-top:0px">
          <input name="fechaInicio" id="fechaInicio" type="text" class="easyui-datebox" required="required" label="Mes inicio de uso" labelPosition="left" labelWidth="150" style="width:95%">
        </div>
        <div style="margin-top:0px">
          <input name="fechaFin" id="fechaFin" type="text" class="easyui-datebox" required="required" label="Mes Final de uso:" labelPosition="left" labelWidth="150" style="width:95%">
        </div>
      </div>
    </form>
  </div>
  <div id="buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMontoOb();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

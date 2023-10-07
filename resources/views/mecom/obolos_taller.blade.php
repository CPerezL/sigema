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
        fitColumns: true,
        rownumbers: false,
        singleSelect: true,
        pageList: [50, 100, 200],
        pageSize: '20',
        view: groupview,
        groupField: 'ordenar',
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
              field: 'idMonto',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'orden',
              title: 'Orden',
              hidden: 'true'
            },
            {
              field: 'tipotxt',
              title: 'Tipo de pago'
            },
            {
              field: 'concepto',
              title: 'Concepto'
            },
            {
              field: 'miembro',
              title: 'Tipo Miembro'
            },
            {
              field: 'monto',
              title: 'Monto Taller',
              hidden: 'true'
            },
            {
              field: 'montotxt',
              title: 'Monto Taller'
            },
            {
              field: 'fechaModifica',
              title: 'F. Modificacion'
            },
            {
              field: 'fechaIniciotxt',
              title: 'F. Inicio',
              hidden: 'true'
            },
            {
              field: 'fechaFintxt',
              title: 'F. Fin',
              hidden: 'true'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"> <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newMontoTall();">Nuevo Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editMontoTall();">Editar Monto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyMontoTall();">Borrar Monto</a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function newMontoTall() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.nuevo",["job"=>"tipo de pago"])');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editMontoTall() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.editar",["job"=>"tipo de pago"])');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idMonto;
      } else {
        $.messager.show({
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }

    function saveMontoTall() {
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

    function destroyMontoTall() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang("mess.question")', '@lang("mess.questdel")', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idMonto
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
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang("mess.alertdata")'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;" closed="true" buttons="#buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate><input type="hidden" name="orden" value="" />
      <div style="margin-top:4px">
        <select id="idTaller" class="easyui-combobox" name="idTaller" style="width:95%" label="Taller Monto:" labelWidth="130" labelPosition="left" required="required" editable="false">
          @foreach ($logias as $key => $tall)
            <option value="{{ $key }}">{{ $tall }}</option>
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

      <div class="easyui-panel" title="Concepto del pago" style="width:100%;padding:5px;">
        <div style="margin-top:4px">
          <select id="tipo" class="easyui-combobox" name="tipo" style="width:95%" label="Tipo pago:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
            @foreach ($tpagos as $key => $pta)
              <option value="{{ $key }}" selected>{{ $pta }}</option>
            @endforeach
          </select>
        </div>
        {{-- <div style="margin-top:4px"><input name="concepto" id="concepto" label="Concepto si es extra:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div> --}}
      </div>
      <div class="easyui-panel" title="Monto a cobrar" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="monto" label="Monto (Gs.):" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
    </form>
  </div>
  <div id="buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMontoTall();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

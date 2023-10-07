@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
queryParams: {
 _token: tokenModule
},rowStyler: function(index,row){if (row.activo == '1') {return 'color:#76d7c4 ;';}else{return 'color: #f5b7b1;'; }}"
    toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" nowrap="false">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idMonto" hidden="true">ID</th>
        <th field="tipotxt">Tipo</th>
        <th field="cobro" style="width: 20%">Cobro</th>
        <th field="descripcion" style="width: 20%">Descripcion</th>
        <th field="valletxt">Valle</th>
        <th field="logiatxt">Logia</th>
        <th field="monto">Monto</th>
        <th field="cantidadtxt"># Pagos</th>
        <th field="numerotxt">Cupos</th>
        <th field="transac">C. pagados</th>
        <th field="fechaInicio">Mes inicio</th>
        <th field="fechaFin">Mes fin</th>
        <th field="fechaModificacion">Fecha</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newPagoGLB_136();">Nuevo Pago GLB</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newPagoGDR_136();">Nuevo Pago GLD/GDR</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newPagoLog_136();">Nuevo Pago Logia</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editPago_136();">Editar Pago Extra</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="delPago_136();">Eliminar Pago Extra</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" onclick="ver_reporte_136();">Ver reporte de pagos</a>
  </div>
  <script type="text/javascript">
    var url;

    function newPagoGLB_136() {
      $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Pago Extra - GLB');
      $('#fm_glb{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?tipo=1&_token=' + tokenModule;
    }

    function newPagoGDR_136() {
      $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Pago Extra - GLD/GDR');
      $('#fm_glb{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?tipo=2&_token=' + tokenModule;
    }

    function newPagoLog_136() {
      $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Pago Extra - Logias');
      $('#fm_glb{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?tipo=3&_token=' + tokenModule;
    }

    function editPago_136() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.entidad == '1') {
          $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Pago para GLB');
          $('#fm_glb{{ $_mid }}').form('load', row);
        } else if (row.entidad == '2') {
          $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Pago para GDL/GDR');
          $('#fm_glb{{ $_mid }}').form('load', row);
        } else if (row.entidad == '3') {
          $('#dlg_glb{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Pago para Logias');
          $('#fm_glb{{ $_mid }}').form('load', row);
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Seleccione Pago para ver'
          });
        }
        url = '{{ $_controller }}/update_datos?id=' + row.idMonto + '&_token=' + tokenModule;;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function delPago_136() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este Dato?', function(r) {
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
          title: 'No seleccionado',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveRegistromm() {
      $('#fm_glb{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: result.Msg,
              icon: 'info'
            });
            $('#fm_glb{{ $_mid }}').form('clear');
            $('#dlg_glb{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({
              title: 'Error',
              msg: result.Msg
            });
          }
        }
      });
    }
  </script>
  {{-- dialogo glb --}}
  <div id="dlg_glb{{ $_mid }}" class="easyui-dialog" style="width:440px;height:520px;padding:5px 5px" closed="true" buttons="#dlg-buttons_glb{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm_glb{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:2px"><input name="cobro" id="cobro" class="easyui-textbox" label="Titulo de Cobro:" labelPosition="top" style="width:96%"></div>
      <div style="margin-top:4px"><input name="descripcion" id="descripcion" class="easyui-textbox" label="Descripcion:" labelPosition="top" multiline="true" style="width:96%;height:80px"></div>
      <div style="margin-top:4px"><input id="monto" name="monto" class="easyui-numberspinner" required="required" data-options="min:10,max:2000,editable:true" label="Monto para cobrar en Bs.:" labelWidth="180" labelPosition="left" style="width:98%;"></div>
      <div style="margin-top:4px"><input id="numeroPagos" name="numeroPagos" class="easyui-numberspinner" required="required" data-options="min:1,max:12,editable:false" label="Cantidad de pagos(monto*cantidad=total):" labelWidth="280" labelPosition="left" style="width:96%;"></div>
      <div style="margin-top:4px"><input id="numeroPersonas" name="numeroPersonas" class="easyui-numberspinner" required="required" data-options="min:0,max:1000,editable:true" label="Cantidad de Cupos(N. de puestos maximo):" labelWidth="280" labelPosition="left" style="width:96%;"></div>
      <div style="margin-top:4px"><input id="fechaInicio" name="fechaInicio" type="text" class="easyui-datebox" required="required" label="Fecha inicial de pago:" labelPosition="left" labelWidth="180" style="width:96%;"></div>
      <div style="margin-top:4px"><input id="fechaFin" name="fechaFin" type="text" class="easyui-datebox" required="required" label="Fecha final de pago:" labelPosition="left" labelWidth="180" style="width:96%;"></div>
      <div style="margin-top:4px"><input id="vallecb" name="valle" style="width:95%"></div>
      <div style="margin-top:4px"><input id="logiacb" name="taller" style="width:95%"></div>
      <div style="margin-top:4px">
        <select id="activo" class="easyui-combobox" name="activo" style="width:96%" label="Estado de pago:" labelWidth="150" labelPosition="left" panelHeight="auto">
          <option value="0">No habilitado</option>
          <option value="1">Habilitado</option>
        </select>
      </div>
    </form>
  </div>
  <div id="dlg-buttons_glb{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveRegistromm();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_glb{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    var prueba = 0;
    $('#vallecb').combobox({
      url: '{{ $_controller }}/get_valles?_token=' + tokenModule,
      panelHeight: '350',
      required: true,
      valueField: 'idValle',
      textField: 'valle',
      method: 'get',
      label: 'Valle:',
      labelWidth: '130',
      labelPosition: 'left',
      onSelect: function(rec) {
        fillComboLogia(rec.idValle);
      },
    });

    function fillComboLogia(valleid) {
      $('#logiacb').combobox('reload', '{{ $_controller }}/get_logias?_token=' + tokenModule + '&valleid=' + valleid);
    }
    $('#logiacb').combobox({
      url: '',
      panelHeight: '350',
      valueField: 'nlogia',
      textField: 'logian',
      method: 'get',
      label: 'Taller:',
      labelWidth: '130',
      labelPosition: 'left'
    });

    function ver_reporte_136() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgrep_136').datagrid('load', '{{ $_controller }}/get_reporte?idm=' + row.idMonto);
        $('#wrep_136').window('open');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    $(function() {
      var dgrep_136 = $('#dgrep_136').datagrid({
      url: '{{ $_controller }}/get_reporte?idm=0',
      type: 'get',
      dataType: 'json',
      queryParams: {
        _token: tokenModule
      },
      pagination: false,
      fitColumns: false,
      rownumbers: true,
      singleSelect: true,
      remoteFilter: false,
      nowrap: true,
      autoRowHeight: true,
      pageList: [20],
      pageSize: '20',
      columns: [
        [{
            field: 'valle',
            title: 'Valle'
          },
          {
            field: 'logiatxt',
            title: 'R:.L:.S:.'
          },
          {
            field: 'GradoActual',
            title: 'Grado'
          },
          {
            field: 'miembro',
            title: 'Aportante'
          },
          {
            field: 'monto',
            title: 'Aporte'
          },
          {
            field: 'fechaPago',
            title: 'Fecha de Pago'
          }
        ]
      ]
    });
});
    function file_reporte_136() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgrep_136').datagrid('toExcel', 'aportes.xls')
      }
    }
  </script>
  <div id="wrep_136" class="easyui-window" title="Reporte de pagos" data-options="modal:true,closed:true,iconCls:'icon-save'" style="width:800px;height:500px;padding:0px;">
    <table id="dgrep_136" style="width:100%;height:100%" toolbar="#tbrep_136"></table>
  </div>
  <div class="datagrid-toolbar" id="tbrep_136">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" onclick="file_reporte_136();">Exporta a excel</a>
  </div>
@endsection

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
        pageList: [20, 50, 100, 200],
        pageSize: '20',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID'
            },
            {
              field: 'circular',
              title: 'Circular'
            },
            {
              field: 'fecha',
              title: 'Fecha de emision'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'numero',
              title: 'Cantidad'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="generarCircular();">Imprimir/Ver circular</a></div>
      <div style="float:left;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'valle');}">
            <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'valle');}">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <!--   formulario de modificacion de datos de tramite  -->
  <script type="text/javascript">
    function filterDatos(value, campo) {
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

    function generarCircular() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      $.messager.progress({
        title: 'Por favor espere',
        msg: 'Creando circular'
      });
      $.post('{{ $_controller }}/ver_circular?_token={{ csrf_token() }}', {
        idc: row.id
      }, function(result) {
        if (result.success) {
          var fileName = result.filename;
          //console.log(fileName);
          $('#dialogcir').empty().html('<embed width="1180" height="600" src="{{ $_folder }}' + fileName + '"></embed>');
          $.messager.progress('close');
          $('#dialogcir').dialog('open').dialog('setTitle', 'Nuevo tramite');
        } else {
          $.messager.progress('close');
          $.messager.show({ // show error message
            title: 'Error',
            msg: result.Msg
          });
        }
      }, 'json');
    }
  </script>
  <div id="dialogcir" class="easyui-dialog" closed="true" style="width:1200px;height:auto;top:30px" closed="true" buttons="#dlgcir-buttons" data-options="iconCls:'icon-edit',modal:true">
  </div>
  <div id="dlgcir-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dialogcir').dialog('close');" style="width:90px">Cerrar</a>
  </div>
@endsection

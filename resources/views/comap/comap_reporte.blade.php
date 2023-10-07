@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
   queryParams:{
   _token: tokenModule
   }
   " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="100" enableFilter="true">
    <thead>
      <tr>
        <th field="valle">Valle</th>
        <th field="taller">N. Logia</th>
        <th field="regaa">Reg AA</th>
        <th field="regcc">Reg CC</th>
        <th field="regmm">Reg MM</th>
        <th field="regvh">Reg VH</th>
        <th field="regulares">Total Reg.</th>
        <th field="honorarios">Honorarios</th>
        <th field="advitam">Ad vitam</th>
        <th field="admeritum">Ad-Meritum</th>
        <th field="fallecidos">fallecidos</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false">
          <option value="0">Mostrar logia de todos los valles &nbsp;&nbsp;&nbsp;</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false">
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>

    <div style="float:left;"><b>&nbsp;&nbsp;Obolos:</b>
      <select id="resul{{ $_mid }}" name="resul{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false">
        <option value="0" selected="selected">Pagos al dia</option>
        <option value="1">Por entrar en mora casi 6 meses</option>
        <option value="2">En mora</option>
        <option value="3">Todos</option>
        <option value="4">Pagos al dia x Logia</option>
        <option value="5">Todos x Logia</option>
      </select>
    </div>

    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-file blue2" onclick="r_listacom{{ $_mid }}();">Ver reporte</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg danger" onclick="toPdfeleg();">Exportar a PDF</a></div>
  </div>
  <!-- Funciones javascript -->
  <!--filtros de datos -->
  <script type="text/javascript">
    function r_listacom{{ $_mid }}() {
      var valle = $('#valle{{ $_mid }}').combobox('getValue');
      var resul = $('#resul{{ $_mid }}').combobox('getValue');
      if (valle > 0) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valle,
          resul: resul,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        alert('@lang('mess.alertvalle')');
      }
    }
  </script>

@endsection

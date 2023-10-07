@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="100">
    <thead>
      <tr>
        <th field="numero" sortable="false">#</th>
        <th field="taller" sortable="false">RLS</th>
        <th field="Miembro" sortable="false">Miembro</th>
        <th field="GradoActual" sortable="false">Grado</th>
        <th field="FechaIniciacion">F. Iniciacion</th>
        <th field="FechaAumentoSalario">F. Aumento</th>
        <th field="FechaExaltacion">F. Exaltacion</th>
        <th field="nombre" sortable="false">Nombre completo</th>
        <th field="nTenidas">T. Tenidas de G:.</th>
        <th field="ordinaria" sortable="false">Asis. Tenidas</th>
        <th field="asisextra" sortable="false">Asis. Extra</th>
        <th field="porcentaje">%</th>
        <th field="extratemplo" sortable="false">Extra Temp.</th>
        <th field="extratemploGDR" sortable="false" hidden="true">Extra Temp. GLSP</th>
        <th field="ettPorcentaje" sortable="false">% Extra Temp.</th>
        <th field="ultimoPago" sortable="false">Ult. pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>Gestion: </b><input id="gestionreas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year }},editable:false" value="{{ $year }}"></div>
    <div style="float:left;"><b>&nbsp;Logia:</b>
      @if (count($logias) > 1)
        <select id="logia{{ $_mid }}" name="logia{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'500'">
          <option value="0" selected="selected"> SELECCIONE LOGIA </option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="logia{{ $_mid }}" name="logia{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'auto'">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }} </option>
          @endforeach
        </select>
      @endif
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><b>&nbsp;Grado: </b>
      <select id="gradom" name="gradom" class="easyui-combobox" panelHeight="170">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="r_habilitados{{ $_mid }}();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print fa-lg purple" onclick="gen_reporte();" id="darasis">Imprimir PDF</a></div>
    <div class="datagrid-btn-separator"></div>
  </div>
  <!-- Funciones javascript actuales -->
  <script type="text/javascript">
    function gen_reporte() {
      var logia = $('#logia{{ $_mid }}').combobox('getValue');
      if (logia > 0) {
        var gradom = $('#gradom').combobox('getValue');
        var gesreas = $('#gestionreas').val();
        $.post('{{ $_controller }}/set_datos', {
          _token: tokenModule,
          taller: logia,
          gestion: gesreas,
          grado: gradom
        }, function(result) {
          if (result.success) {
            window.open("{{ $_controller }}/gen_reporte");
          }
        }, 'json');
      } else {
        alert('Seleccione datos para el reporte');
      }
    }

    function r_habilitados{{ $_mid }}() {
      var logia = $('#logia{{ $_mid }}').combobox('getValue');
      var gradom = $('#gradom').combobox('getValue');
      var gesreas = $('#gestionreas').val();
      if (logia > 0) {
        $.post('{{ $_controller }}/set_datos', {
          _token: tokenModule,
          taller: logia,
          gestion: gesreas,
          grado: gradom
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        alert('Seleccione un logia para el reporte');
      }
    }
  </script>
@endsection

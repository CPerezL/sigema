@extends('layouts.easyuitab')
@section('content')

  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"
    data-options="url: '{{ $_controller }}/get_datos',
  queryParams:{
  _token: tokenModule
  },
  rowStyler: function (index, row) {
  if (row.Estado == '1') {
  return {class:'iactivo'};
  } else {
  return {class:'inactivo'};
  }
  }
  " toolbar="#toolbar{{ $_mid }}" pagination="true"
    fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:false"></th>
        <th field="valle" sortable="false">Valle</th>
        <th field="nlogia" sortable="false">R:.L:.S:. Actual</th>
        <th field="GradoActual" sortable="false">Grado</th>
        <th field="NombreCompleto" sortable="false">Nombre completo</th>
        <th field="Miembro" sortable="falsetrue">Miembro</th>
        <th field="FechaIniciacion" sortable="false">F. Iniciacion</th>
        <th field="FechaAumentoSalario" sortable="false">F. Aumento S.</th>
        <th field="FechaExaltacion" sortable="false">F Exaltacion</th>
        <th field="ultimoPago" sortable="false">Ultimo Pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">&nbsp;&nbsp;
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMieVal(rec,'taller');}">
          <option value="0" selected="selected">Todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
          <option value="1000">Sin Taller Asignado</option>
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMieVal(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
      <select id="gradom" name="gradom" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMieVal(rec,'grado');}">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Estado: </b>
      <select id="estado" name="estado" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMieVal(rec,'estado');}">
        <option value="0">Inactivo</option>
        <option value="1">Activo</option>
        <option value="2">Fallecido</option>
        <option value="3">Ninguno</option>
        <option value="4" selected="selected">Todos</option>
      </select>
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchMieVal,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchMieVal();"></a>
    </div>
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    /*funcnode filtro de datos*/
    function filtrarDatosMieVal(value, campo) {
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

    function doSearchMieVal(value) {
      filtrarDatosMieVal(value, 'palabra');
    }

    function clearSearchMieVal() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosMieVal('', 'palabra');
    }
  </script>
@endsection

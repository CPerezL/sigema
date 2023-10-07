@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       rowStyler: function (index, row) {
       if (row.upago == '1') {
       return {class:'activo'};
       } else if (row.upago == '0') {
       return {class:'inactivo'};
       } else {
       return {class:'alerta'};
       }
       }
       "
    toolbar="#toolbar{{ $_mid }}" fit="true" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,50,100,200]" pageSize="20" enableFilter="false">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" hidden="true">ID</th>
        <th field="valle">Valle</th>
        <th field="nlogia">R:.L:.S:. Actual</th>
        <th field="LogiaAfiliada">Afiliado A</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Miembro">Miembro</th>
        <th field="estadotxt">Estado</th>
        <th field="Ingreso">Ingreso</th>
        <th field="FechaIniciacion">F. Iniciacion</th>
        <th field="FechaAumentoSalario">F. Aumento S.</th>
        <th field="FechaExaltacion">F Exaltacion</th>
        <th field="ultimoPago">Ultimo Pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-file blue2" onclick="win_verKardex();">Ver Kardex</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="win_printKardex();">Imprimir Kardex</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosKardex(rec,'taller');}">
          <option value="0" selected="selected">Todas las Logias</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
          <option value="1000">Sin Logia Asignada</option>
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosKardex(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
      <select id="gradom" name="gradom" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosKardex(rec,'grado');}">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Estado: </b>
      <select id="estado" name="estado" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosKardex(rec,'estado');}">
        @foreach ($estados as $keye => $esta)
          <option value="{{ $keye }}">{{ $esta }}</option>
        @endforeach
        <option value="4" selected="selected">Todos</option>
      </select>
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchKardex,prompt:'Buscar miembro'" id="searchbox{{ $_mid }}" value="">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchKardex();"></a>
    </div>
  </div>

  <!--filtros de datos -->
  <script type="text/javascript">
    function filtrarDatosKardex(value, campo) {
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

    function doSearchKardex(value) {
      filtrarDatosKardex(value, 'palabra');
    }

    function clearSearchKardex() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosKardex('', 'palabra');
    }
  </script>
  <div id="win" class="easyui-window" title="Kardex" style="width:960px;height:680px" closed="true" data-options="iconCls:'icon-save',modal:true"></div>
  <script type="text/javascript">
    function win_verKardex() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#win').window('open'); // open a window
        $('#win').window('refresh', '{{ $_controller }}/ver_kardex?id=' + row.id);
      }
    }

    function win_printKardex() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        window.open('{{ $_controller }}/print_kardex?id=' + row.id);
      }
    }
  </script>

@endsection

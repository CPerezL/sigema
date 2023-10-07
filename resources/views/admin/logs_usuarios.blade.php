@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Valle: </b>
      <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="width:160,panelHeight:300,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLogs(rec,'valle');}">
        <option value="0" selected="selected"> Todos los valles</option>
        @foreach ($valles as $key => $vall)
          <option value="{{ $key }}">{{ $vall }}</option>
        @endforeach
      </select>
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;R:.L:.S:. : </b>
      @if (count($logias) > 1)
        <select id="taller{{ $_mid }}" name="taller{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLogs(rec,'taller');}">
          <option value="0" selected="selected">Todas la del valle</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }} Nro {{ $key }}</option>
          @endforeach
        </select>
      @else
        <select id="taller{{ $_mid }}" name="taller{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLogs(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }} Nro {{ $key }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg success" onclick="genPlanilla();">Exportar a Excel(pantalla)</a></div>
  </div>
  <!-- Funciones javascript -->
  <script type="text/javascript">
    /*funcnode filtro de datos*/
    function filtrarDatosLogs(value, campo) {
      $.post('{{ $_controller }}/filtrar', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function doSearchLogs(value) {
      filtrarDatosMie(value, 'palabra');
    }

    function clearSearchLogs() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosLogs('', 'palabra');
    }

    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        pagination: true,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: true,
        autoRowHeight: true,
        pageList: [20, 40, 50, 100],
        pageSize: '20',
        columns: [
          [{
              field: 'accion',
              title: 'Accion'
            },
            {
              field: 'nombre',
              title: 'Usuario'
            },
            {
              field: 'rol',
              title: 'Rol'
            },
            {
              field: 'logiatxt',
              title: 'Logia'
            },
            {
              field: 'valletxt',
              title: 'Valle'
            },
            {
              field: 'fechaLog',
              title: 'Fecha de Accion'
            }
          ]
        ]
      });
    });

    function genPlanilla() {
      $('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');
    }
  </script>

@endsection

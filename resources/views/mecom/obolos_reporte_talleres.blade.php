@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
        <select id="tramite" name="tramite" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'auto'" style="width:165px;">
          <option value="10" selected="selected"> PAGO QR DE OBOLOS </option>
          <option value="12"> INICIACIONES </option>
          <option value="13"> AUMENTOS DE S.</option>
          <option value="14"> EXALTACIONES </option>
          <option value="15"> AFILIACIONES </option>
          <option value="16"> REINCOPORACIONES</option>
          <option value="11"> PAGOS EXTRAS</option>
        </select>
      </div>
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="taller" name="taller" class="easyui-combobox">
          <option value="0" selected="selected">Todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="taller" name="taller" class="easyui-combobox">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>Desde: <input id="desde{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 00:00 -</b></div>
    <div style="float:left;"><b>Hasta: <input id="hasta{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 24:00</b></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit correcto" plain="false" onclick="r_pagosweb();">Ver reporte</a></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o excel" onclick="genPlanilla();">Exportar a Excel</a></div>
  </div>
  <!-- Funciones javascript -->
  <script>
    $('#desde{{ $_mid }}').datebox({
      required: true
    });
    $('#desde{{ $_mid }}').datebox('setValue', '{{ $desde }}');
    $('#hasta{{ $_mid }}').datebox({
      required: true
    });
    $('#hasta{{ $_mid }}').datebox('setValue', '{{ $hasta }}');
  </script>
  <script type="text/javascript">
    var datam = new Array();
    datam[0] = 'Regular';
    datam[1] = 'Honorario';
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        pagination: false,
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
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'logiaName',
              title: 'R:.L:.S:.'
            },
            {
              field: 'fechaModificacion',
              title: 'Fecha Pago'
            },
            {
              field: 'tipoTram',
              title: 'Tipo Tramite'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'nombreCompleto',
              title: 'Pago a Nombre de'
            },
            {
              field: 'montoTaller',
              title: 'LOG'
            },
            {
              field: 'montoGDR',
              title: 'GDR'
            },
            {
              field: 'montoGLB',
              title: 'GLB'
            },
            {
              field: 'montoCOMAP',
              title: 'COMAP'
            },
            {
              field: 'monto',
              title: 'Total Pago'
            },
            {
              field: 'periodo',
              title: 'Periodo'
            }
          ]
        ]
      });
    });
  </script>
  <!--filtros de datos -->
  <script type="text/javascript">
    function genPlanilla() {
      $('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls')
    }

    function r_pagosweb() {
      var desde = $('#desde{{ $_mid }}').val();
      var hasta = $('#hasta{{ $_mid }}').val();
      var taller=$('#taller').combobox('getValue');
      var tram = $('#tramite').combobox('getValue');
      $.post('{{ $_controller }}/set_datos', {
        desde: desde,
        hasta: hasta,
        log: taller,
        tipo: tram,
        _token: tokenModule
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function genBanco() {
      window.open('{{ $_controller }}/ver_archivo');
    }
  </script>
@endsection

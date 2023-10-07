@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'300'" style="width:165px;">
        @if (count($valles) > 1)
          <option value="0" selected="selected">TODOS LOS VALLES</option>
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}">{{ $vall }}</option>
          @endforeach
        @else
          @foreach ($valles as $key => $vall)
            <option value="{{ $key }}" selected="selected">{{ $vall }}</option>
          @endforeach
        @endif
      </select>
    </div>
    <div style="float:left;">
      <select id="tramite" name="tramite" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'auto'" style="width:165px;">
        <option value="12" selected="selected"> INICIACIONES </option>
        <option value="13"> AUMENTOS DE S.</option>
        <option value="14"> EXALTACIONES </option>
        <option value="15"> AFILIACIONES </option>
        <option value="16"> REINCOPORACIONES</option>
        <option value="11"> PAGOS EXTRAS</option>
        <option value="10"> PAGO QR DE OBOLOS </option>
      </select>
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Desde: <input id="desde{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 00:00</b></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Hasta: <input id="hasta{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 24:00</b></div>
    <div style="float:left;">&nbsp;&nbsp;<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit correcto" onclick="r_pagosweb();">Ver reporte</a></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o excel" onclick="genPlanilla();">Exportar a Excel(pantalla)</a></div>
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
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'post',
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
      var valle = $('#valle{{ $_mid }}').combobox('getValue');
      var desde = $('#desde{{ $_mid }}').val();
      var hasta = $('#hasta{{ $_mid }}').val();
      var tram = $('#tramite').combobox('getValue');
      if (tram >= 10) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valle,
          desde: desde,
          hasta: hasta,
          tipo: tram,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite a reportar'
        });
      }
    }

    function gen_formulario_wc() {
      window.open("{{ $_controller }}/gen_formulario");
    }
  </script>
@endsection

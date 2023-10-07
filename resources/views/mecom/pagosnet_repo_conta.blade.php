@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'400'" style="width:165px;">
        @if (count($valles) > 1)
          <option value="0" selected="selected"> SELECCIONAR VALLE </option>
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
    <div style="float:left;"><b>&nbsp;&nbsp;Desde: <input id="desde{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 00:00</b></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Hasta: <input id="hasta{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 24:00</b></div>
    <div style="float:left;">&nbsp;&nbsp;<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit correcto" onclick="r_pagosweb();">Ver reporte</a></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o excel" onclick="genPlanilla();">Exportar a Excel(pantalla)</a></div>
    <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print archivo" onclick="gen_formulario_wc();" id="darasis">Imprimir formulario</a></div>
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
        type: 'post',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        pagination: false,
        fitColumns: false,
        rownumbers: false,
        singleSelect: true,
        remoteFilter: false,
        nowrap: true,
        autoRowHeight: true,
        pageList: [20, 40, 50, 100],
        pageSize: '20',
        columns: [
          [{
              field: 'NombreCompleto',
              title: 'Nombre completo'
            },
            {
              field: 'Grado',
              title: 'Grado'
            },
            {
              field: 'Cat',
              title: 'Cat'
            },
            {
              field: 'cantidad',
              title: 'Cuotas'
            },
            {
              field: 'Periodo',
              title: 'Periodo'
            },
            {
              field: 'Logia',
              title: 'Logia'
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
      if (valle > 0 || taller > 0) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valle,
          desde: desde,
          hasta: hasta,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione un valle o taller para el reporte'
        });
      }
    }

    function gen_formulario_wc() {
      window.open("{{ $_controller }}/gen_formulario");
    }
  </script>
@endsection

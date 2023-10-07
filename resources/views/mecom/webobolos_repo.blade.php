@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%"" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'400'" style="width:165px;">
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
    <div style="float:left;"><b>&nbsp;R:.L:.S:. : </b>
      @if (count($logias) > 1)
        <select id="taller{{ $_mid }}" name="taller{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false">
          <option value="0" selected="selected">TODAS LAS DEL VALLE</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="taller{{ $_mid }}" name="taller{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Desde: <input id="desde{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 00:00</b></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Hasta: <input id="hasta{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 24:00</b></div>
    <div style="float:left;">&nbsp;&nbsp;<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg correcto" onclick="r_pagosweb();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg excel" onclick="genPlanilla();">Exportar a Excel(pantalla)</a></div>
  </div>
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
  <!-- Funciones javascript -->
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
        pagination: true,
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
              field: 'logia',
              title: 'R:.L:.S:.'
            },
            {
              field: 'LogiaActual',
              title: 'Nro.'
            },
            {
              field: 'Miembro',
              title: 'Miembro'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre completo'
            },
            {
              field: 'Periodo',
              title: 'Periodo'
            },
            {
              field: 'cantidad',
              title: 'Cant.'
            },
            {
              field: 'montoTaller',
              title: 'Logia'
            },
            {
              field: 'montoGDR',
              title: 'GDR'
            },
            {
              field: 'montoCOMAP',
              title: 'COMAP'
            },
            {
              field: 'montoGLB',
              title: 'GLB'
            },
            {
              field: 'monto',
              title: 'Monto'
            },
            {
              field: 'transaccion',
              title: 'Cod. Trans. Sintesis'
            },
            {
              field: 'fechaPago',
              title: 'Fecha de Trans.'
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
      var taller = $('#taller{{ $_mid }}').combobox('getValue');
      var desde = $('#desde{{ $_mid }}').val();
      var hasta = $('#hasta{{ $_mid }}').val();
      if (valle > 0 || taller > 0) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valle,
          taller: taller,
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
  </script>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Desde: <input id="desde{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 00:00 -</b></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Hasta: <input id="hasta{{ $_mid }}" type="text" style="width:106px;" required="required"> hr. 24:00</b></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;">&nbsp;&nbsp;<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit aqua" onclick="r_pagosweb();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o success" onclick="genPlanilla();">Exportar a Excel</a></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o danger" onclick="genBanco();">Exportar para Banco(reporte)</a></div>
  </div>
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
              field: 'jurisdiccion',
              title: 'Jurisdiccion'
            },
            {
              field: 'entidad',
              title: 'Entidad'
            },
            {
              field: 'numero',
              title: 'N. de pagos'
            },
            {
              field: 'numeroTrans',
              title: 'N. Transacciones'
            },
            {
              field: 'banco',
              title: 'Banco'
            },
            {
              field: 'cuenta',
              title: 'Cuenta'
            },
            {
              field: 'codigo',
              title: 'Codigo'
            },
            {
              field: 'montototal',
              title: 'Monto'
            }
          ]
        ]
      });
    });

    $('#desde{{ $_mid }}').datebox({
      required: true
    });
    $('#desde{{ $_mid }}').datebox('setValue', '{{ $desde }}');
    $('#hasta{{ $_mid }}').datebox({
      required: true
    });
    $('#hasta{{ $_mid }}').datebox('setValue', '{{ $hasta }}');

    function genPlanilla() {
      $('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls')
    }

    function r_pagosweb() {
      var desde = $('#desde{{ $_mid }}').val();
      var hasta = $('#hasta{{ $_mid }}').val();
      $.post('{{ $_controller }}/set_datos', {
        _token: tokenModule,
        desde: desde,
        hasta,
        hasta
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

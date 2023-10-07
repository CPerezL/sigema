@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#toolbar{{ $_mid }}"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>;Gestion: </b>
      <input id="gestionof" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2018,max:{{ $year }},editable:false,onChange: function(rec){filtrarEstadoAsis(rec,'gestion');}" value="{{ $year }}">
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Logia: </b>
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarEstadoAsis(rec,'taller');}">
          <option value="0" selected="selected">Todas las Logias</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarEstadoAsis(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o excel" onclick="genPlanilla();">Exportar a Excel</a></div>
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
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: true,
        autoRowHeight: true,
        pageList: [100],
        pageSize: '100',
        columns: [
          [{
              field: 'idLogia',
              title: 'R:.L:.S:.'
            },
            {
              field: 'fechaTenida',
              title: 'Fecha de Trabajo'
            },
            {
              field: 'numeroActa1',
              title: 'Acta 1er G.'
            },
            {
              field: 'numeroActa2',
              title: 'Acta 2do G.'
            },
            {
              field: 'numeroActa3',
              title: 'Acta 3er G.'
            },
            {
              field: 'numerovenes',
              title: 'Ex. V.M.'
            },
            {
              field: 'numeromaes',
              title: 'Maestros'
            },
            {
              field: 'numerocomp',
              title: 'Compañeros'
            },
            {
              field: 'numeroapre',
              title: 'Aprendices'
            },
            {
              field: 'numerototal',
              title: 'Asist. total'
            }
          ]
        ]
      });
    });
  </script>
  <!--filtros de datos -->
  <script type="text/javascript">
    function filtrarEstadoAsis(value, campo) {
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

    function genPlanilla() {
      $('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');
    }
  </script>
@endsection

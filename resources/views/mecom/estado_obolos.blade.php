@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" style="width:100%;height:100%;" toolbar="#toolbar{{ $_mid }}" fit="true"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}"  style="display:inline-block">
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarObolosMie(rec,'taller');}">
          <option value="0" selected="selected">Todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarObolosMie(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
      <select id="gradom" name="gradom" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarObolosMie(rec,'grado');}">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg" onclick="genPlanilla();">Exportar a Excel</a></div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:searchObolosMie,prompt:'Buscar apellido o nombre'" id="searchbox{{ $_mid }}" value="">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearsearchObolosMie();"></a>
    </div>
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
        autoRowHeight: false,
        pageList: [20, 50, 100, 200],
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
              field: 'ultimoPago',
              title: 'Ultimo mes pagado'
            }
          ]
        ],
        rowStyler: function(index, row) {
          if (row.pagoOk == '2') {
            return {
              class: 'activo'
            };
          } else if (row.pagoOk == '1') {
            return {
              class: 'alerta'
            };
          } else {
            return {
              class: 'inactivo'
            };
          }
        }
      });
    });
  </script>
  <!--filtros de datos -->
  <script type="text/javascript">
    function filtrarObolosMie(value, campo) {
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

    function searchObolosMie(value) {
      filtrarObolosMie(value, 'palabra');
    }

    function clearsearchObolosMie() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarObolosMie('', 'palabra');
    }

    function genPlanilla() {
      $('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls')
    }
  </script>
@endsection

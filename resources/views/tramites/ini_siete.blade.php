@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbar{{ $_mid }}',
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: true,
        nowrap: true,
        pageList: [20, 50, 100, 200],
        pageSize: '20',
        view: groupview,
        groupField: 'separador',
        groupFormatter: function(value, rows) {
          return 'Ceremonia ' + value + ' - ' + rows.length + ' Iniciados';
        },
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'fechaIniciacion',
              title: 'Fecha Ceremonia'
            },
            {
              field: 'idTramite',
              title: 'Tramite'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'Taller'
            },
            {
              field: 'numero',
              title: 'Nro'
            },
            {
              field: 'fInsinuacion',
              title: 'Fecha de Insinuacion'
            },
            {
              field: 'circular',
              title: 'Nro Circular'
            },
            {
              field: 'fechaCircular',
              title: 'F. Circular'
            },
            {
              field: 'apPaterno',
              title: 'Ap. Paterno'
            },
            {
              field: 'apMaterno',
              title: 'Ap. Materno'
            },
            {
              field: 'nombres',
              title: 'Nombres'
            },
            {
              field: 'foto',
              title: 'Foto',
              hidden: 'false'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block;">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload correcto" onclick="gen_repceremonia();"> Generar Autorizacion de ceremonia</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
     <script type="text/javascript">
    function filterDatos(value, campo) {
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

    function gen_repceremonia() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        window.open("{{ $_controller }}/gen_reporte?_token={{ csrf_token() }}&id=" + row.idTramite);
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }
  </script>
@endsection

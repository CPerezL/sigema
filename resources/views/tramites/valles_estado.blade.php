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
      columns: [
        [{
            field: 'ck',
            title: '',
            checkbox: true
          },
          {
            field: 'tipo',
            title: 'Tramite'
          },
          {
            field: 'numTramite',
            title: 'Numero'
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
            field: 'fechaDato',
            title: 'Fecha de pedido'
          },
          {
            field: 'NombreCompleto',
            title: 'Nombres'
          },
          {
            field: 'fechaCreacion',
            title: 'Creacion'
          },
          {
            field: 'fechaModificacion',
            title: 'Modificacion'
          }
        ]
      ]
    });
  });
</script>
<div class="easyui-layout" data-options="fit:true">
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="filtrot1" name="filtrot1" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarEstadoValle(rec,'taller');}">
        <option value="0">Ver todos los talleres</option>
        @foreach ($logias as $key => $logg)
          <option value="{{ $key }}">{{ $logg }}</option>
        @endforeach
      </select>
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:140px" data-options="searcher:doSearchEstadoValle,prompt:'Buscar tramite'" id="searchbox{{ $_mid }}" value="">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchEstadoValle();"></a>
    </div>
  </div>
</div>
<script type="text/javascript">
  function filtrarEstadoValle(value, campo) {
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

  function doSearchEstadoValle(value) {
    filtrarEstadoValle(value, 'palabra');
  }

  function clearSearchEstadoValle() {
    $('#searchbox{{ $_mid }}').searchbox('clear');
    filtrarEstadoValle('', 'palabra');
  }
</script>
@endsection

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
        pageList: [15, 20, 50, 100, 200],
        pageSize: '15',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'idTramite',
              title: 'Tramite',
              hidden: 'true'
            },
            {
              field: 'idMiembro',
              title: 'Tramite',
              hidden: 'true'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'R:.L:.S:.'
            },
            {
              field: 'numero',
              title: 'N.'
            },
            {
              field: 'NombreCompleto',
              title: 'M:.M:.'
            },
            {
              field: 'numeroCertificado',
              title: 'N. Certif.'
            },
            {
              field: 'fechaCertificado',
              title: 'F Certificado'
            },
            {
              field: 'fechaCeremonia',
              title: 'F Ceremonia.'
            },
            {
              field: 'estadotxt',
              title: 'Pago'
            },
            {
              field: 'okCeremonia',
              title: 'Ceremonia'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificado.'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}"  style="display:inline-block">
      <div style="float:left;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:'false',onChange: function(rec){filtrarDatosIniCert(rec,'valle',1);}">
            <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:'false',onChange: function(rec){filtrarDatosIniCert(rec,'valle',1);}">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:left;">
        <input id="flogias_144" name="flogias_144" class="easyui-combobox" data-options="width:340,valueField:'nlogia',textField:'logian',url:'{{ $_controller }}/get_logias?_token={{ csrf_token() }}',onChange: function(rec){filtrarDatosIniCert(rec,'taller');}" value="0">
      </div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="gen_certificado_145();">Imprimir certificado de Maestro</a></div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchIniCert,prompt:'Buscar nombre'" id="searchbox{{ $_mid }}" value="">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchIniCert();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function filtrarDatosIniCert(value, campo, opcion = 0) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          if (opcion == '1')
            $('#flogias_144').combobox('reload');

          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function doSearchIniCert(value) {
      filtrarDatosIniCert(value, 'palabra');
    }

    function clearSearchIniCert() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosIniCert('', 'palabra');
    }

    function gen_certificado_145() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        window.open("{{ $_controller }}/gen_reporte?id=" + row.idTramite);
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }
  </script>

@endsection

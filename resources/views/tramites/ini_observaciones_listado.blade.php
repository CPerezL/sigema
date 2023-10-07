@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    tramite = 0;
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
              field: 'idTramite',
              title: 'Tramite',
              hidden: true
            },
            {
              field: 'obstxt',
              title: 'Observaciones'
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
              field: 'documento',
              title: 'Documento'
            },
            {
              field: 'foto',
              title: 'Foto',
              hidden: 'false'
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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="ini_listaDatos();">Ver observaciones</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar R:.L:.S:.</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }} </option>
            @endforeach
          </select>
        @else
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchData,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchData();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_tramites?idt=' + row.idTramite);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Observaciones en tramite');
        url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite</div>'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->


  <div id="dlgini0-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="obs_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:90px">Cancelar/Cerrar</a>
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

    function doSearchData(value) {
      filterDatos(value, 'palabra');
    }

    function clearSearchData() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filterDatos('', 'palabra');
    }
    $(function() {
      var dgobs{{ $_mid }} = $('#dgobs{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_tramites?idt=' + tramite,
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
        nowrap: false,
        striped: true,
        autoRowHeight: true,
        pageList: [10],
        pageSize: '10',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'fechaRegistro',
              title: 'Fecha Denuncia'
            },
            {
              field: 'descripcion',
              title: 'Descripcion'
            },
            {
              field: 'tipotxt',
              title: 'Tipo'
            },
            {
              field: 'estadotxt',
              title: 'Estado'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
            },
          ]
        ]
      });
    });
  </script>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="url: '{{ $_controller }}/get_datos',
         queryParams:{
         _token: tokenModule
         },
         rowStyler: function (index, row) {
         if (row.obs == '0') {
         return {class:'activo'};
         } else {
         return {class:'inactivo'};
         }
         }
         "
    toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" hidden="true">ID</th>
        <th field="obstxt">Antecedentes</th>
        <th field="valle">Valle</th>
        <th field="logia">R:.L:.S:. Actual</th>
        <th field="numero">Nro.</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Miembro">Miembro</th>
        <th field="FechaIniciacion">F. Iniciacion</th>
        <th field="FechaAumentoSalario">F. Aumento S.</th>
        <th field="FechaExaltacion">F Exaltacion</th>
        <th field="ultimoPago">Ultimo Pago</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (Auth::user()->nivel > 2 && Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o fa-lg blue2" onclick="ini_listaDatos();">Ver Antecedentes</a></div>
    @endif
    <div style="float:left;">
      <div style="padding-bottom:2px;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'valle');}">
            <option value="0">Mostrar logia de todos los valles</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          @foreach ($valles as $key => $vogg)
            <input name="fvalle" id="fvalle" value="{{ $key }}" type="hidden">
            <a href="#" class="easyui-linkbutton" plain="true"><b>VALLE: {{ $vogg }}</b></a>
          @endforeach
        @endif
      </div>
    </div>
    <div style="float:left;">
      <div style="padding-bottom:2px;">
        @if (count($logias) > 1)
          <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
            <option value="0" selected="selected">Todas las Logias</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchMie,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchMie();"></a>
    </div>
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    /*funcnode filtro de datos*/
    function filtrarDatosMie(value, campo) {
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

    function doSearchMie(value) {
      filtrarDatosMie(value, 'palabra');
    }

    function clearSearchMie() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosMie('', 'palabra');
    }
  </script>
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
        url: '{{ $_controller }}/get_meritos?idt=0',
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
              title: 'F. Registro'
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

     function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_meritos?idt=' + row.id);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Antecedentes de miembro');
        url = '{{ $_controller }}/save_merito?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione miembro</div>'
        });
      }
    }

    </script>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
  </div>
@endsection

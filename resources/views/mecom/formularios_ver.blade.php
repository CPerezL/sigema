@extends('layouts.easyuitab')
@section('content')
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Formularios de pago de obolos',collapsed:false,split:false" style="width:650px;">
      <table id="dgdia{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%"
        data-options="url:'{{ $_controller }}/get_formularios?_token={{ csrf_token() }}',onLoadSuccess:function(){clearTallerasClsmf()},rowStyler: function(index,row){
           if (row.estado == '1') {return {class:'activo'};}
           else if(row.estado == '2') {return {class:'alerta'};}
           else {return {class:'correcto'};}}"
        toolbar="#toolbardia{{ $_mid }}" fitColumns="true" rownumbers="true" singleSelect="true" pagination="true" pageList="[20]" pageSize="20">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'idFormulario'" hidden="true">#</th>
            <th data-options="field:'numero'">Form</th>
            <th data-options="field:'taller'">Log.</th>
            <th data-options="field:'descripcion'">Estado</th>
            <th data-options="field:'numeroMiembros'">Aport.</th>
            <th data-options="field:'montoTotal'">Importe</th>
            <th data-options="field:'fechaEnvio'">F. Envio</th>
            <th data-options="field:'fechaAprobacion'">F. Aprobacion</th>
            <th data-options="field:'detail'" formatter="optionsEnviaMecom">Opciones</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          <input id="gestform{{ $_mid }}" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2021,max:{{ $year }},editable:false,onChange: function(rec){fMecomForms(rec,'gestion');}" value="{{ $year }}">
        </div>
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){fMecomForms(rec,'taller');}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fMecomForms(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
      </div>
    </div>
    <script type="text/javascript">
      function fMecomForms(value, campo) {
        $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
          _token: tokenModule,
          valor: value,
          filtro: campo
        }, function(result) {
          if (result.success) {
            $('#dgdia{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }

      function clearTallerasClsmf() {
        $('#dg{{ $_mid }}').datagrid('reload');
      }

      function optionsEnviaMecom(value, row) {
        return '<a href="javascript:void(0)" onclick="ver_aportantes(' + row.idFormulario + ',' + row.numero + ',' + row.taller + ');"><button>Seleccionar</button></a>';
      }

    </script>
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#tbptasis{{ $_mid }}" title="Formulario no Seleccionado"></table>
      <div class="datagrid-toolbar" id="tbptasis{{ $_mid }}">
        <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print fa-lg archivo" onclick="gen_formulario();" id="darasis">Imprimir formulario</a></div>
      </div>

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
            fitColumns: false,
            rownumbers: true,
            singleSelect: true,
            remoteFilter: false,
            nowrap: true,
            autoRowHeight: true,
            pageList: [20],
            pageSize: '20',
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
                  field: 'NombreCompleto',
                  title: 'Nombre completo'
                },
                {
                  field: 'GradoActual',
                  title: 'Grado'
                },
                {
                  field: 'Miembro',
                  title: 'Miembro'
                },
                {
                  field: 'monto',
                  title: 'Importe'
                },
                {
                  field: 'numeroCuotas',
                  title: 'Cuotas'
                },
                {
                  field: 'fechaPago',
                  title: 'Mes Anterior'
                },
                {
                  field: 'fechaNueva',
                  title: 'Mes pagado'
                },
                {
                  field: 'detail',
                  title: 'Opciones',
                  formatter: function(value, row) {
                    if (row.estado == '1')
                      return '<a href="javascript:void(0)" onclick="removeAporte(' + row.idRegistro + ');"><button style="color:red"><i class="fa fa-times"></i> Eliminar</button></a>';
                    else
                      return '';
                  },
                  width: 130
                }
              ]
            ]
          });
        });
      </script>
    </div>
  </div>
  <script type="text/javascript">

    function gen_formulario() {
      window.open("{{ $_controller }}/gen_formulario");
    }

    $(function() {
      var dgmie{{ $_mid }} = $('#dgmie{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_miembros',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbarmie{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: true,
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
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre'
            },
            {
              field: 'Miembro',
              title: 'Miembro'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo pago'
            }
          ]
        ]
      });
      dgmie{{ $_mid }}.datagrid('enableFilter', [{
        field: 'id',
        type: 'label'
      }, {
        field: 'ck',
        type: 'label'
      }, {
        field: 'Miembro',
        type: 'label'
      }, {
        field: 'GradoActual',
        type: 'label'
      }, {
        field: 'ultimoPago',
        type: 'label'
      }]);
    });
  </script>
  <script type="text/javascript">
    function doTallerobCls(value) {
      $.post('{{ $_controller }}/filter_taller', {
        taller: value
      }, function(result) {
        if (result.success) {
          $('#dgdia{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }
  </script>
  <script>
    function ver_aportantes(valued, valuenum, valuetall) {
      if (valued > 0) {
        $('#dg{{ $_mid }}').datagrid('reload', {
          key: 1,
          idform: valued,
          _token: tokenModule
        });
        $('#dg{{ $_mid }}').datagrid('getPanel').panel('setTitle', 'Formulario No. ' + valuenum + '-' + valuetall);
      }
    }
  </script>

@endsection

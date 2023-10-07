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
              field: 'idTramite',
              title: 'Tramite'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'estadopago',
              title: 'Estado Pago'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'logia',
              title: 'R:.L:.S:.'
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
        @if (count($logias) > 1)
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosDepos(rec,'taller');}">
            <option value="0">Todas las Logias</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosDepos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload fa-lg success" onclick="ini4_revDatos();"> Ver comprobantes registrados</a></div>
    </div>
  </div>
  <script type="text/javascript">
    function filtrarDatosDepos(value, campo) {
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

    function searchDepos(value) {
      filtrarDatosDepos(value, 'palabra');
    }

    function clearSearchDepos() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosDepos('', 'palabra');
    }

    function ini4_revDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.pagoQR == '0') {
          $('#fmini4{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
          urldoca = '{{ $_folder }}/comprobantes/' + row.docDepositoGDR;
          urldocb = '{{ $_folder }}/comprobantes/' + row.docDepositoGLB;
          urldocc = '{{ $_folder }}/comprobantes/' + row.docDepositoDer;
          $("a#comprogdr").attr('href', urldoca);
          $("a#comproglb").attr('href', urldocb);
          $("a#comproder").attr('href', urldocc);
          $('#dlgini4{{ $_mid }}').dialog('open').dialog('setTitle', 'Ver comprobantes');
          url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
        } else {
          $('#fmini66{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
          $('#dlgini66{{ $_mid }}').dialog('open').dialog('setTitle', 'Ver comprobantes');
          url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini4{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini4-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini4{{ $_mid }}" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite para Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="idTramite" style="width:100%" data-options="label:'<b>Numero de Tramite:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="nombre" style="width:100%" data-options="label:'<b>Profano:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numCircular" style="width:100%" data-options="label:'<b>Numero circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaCircular" style="width:100%" data-options="label:'<b>Fecha circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
      </div>
      <div class="easyui-panel" title="Deposito de pago de derechos" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaDepositoDer" style="width:100%" data-options="label:'<b>Fecha de deposito de derechos</b>',readonly:'true',editable:false" labelWidth="210"></div>
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comproder" class="enlace"> Ver Imagen/PDF de Comprobante de deposito de derechos</a></div>
      </div>
      <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaPagoDerechos" style="width:100%;padding:5px;" data-options="label:'<b>Fecha de deposito de derechos</b>',readonly:'true',editable:false" labelWidth="210"></div>
      <div class="easyui-panel" title="Deposito a GDR" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comprogdr" class="enlace"> Ver Imagen/PDF de Comprobante de deposito GDR</a></div>
      </div>
      <div class="easyui-panel" title="DepositoGLB" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comproglb" class="enlace"> Ver Imagen/PDF de Comprobante de deposito GLB</a></div>
      </div>
      <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaIniciacion" style="width:100%" data-options="label:'<b>Fecha de ceremonia de Iniciacion</b>',readonly:'true',editable:false" labelWidth="210"></div>
    </form>
  </div>
  <div id="dlgini4-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini4{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

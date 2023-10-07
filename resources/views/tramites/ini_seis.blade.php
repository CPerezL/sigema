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
              field: 'circular',
              title: 'Nro Circular'
            },
            {
              field: 'fechaCircular',
              title: 'F. Circular'
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
              field: 'fechaIniciacion',
              title: 'Fecha de Ceremonia'
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
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload correcto" onclick="ini4_revDatos();"> Revisar Informes de Tramites</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini4_revDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini4{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        urldoca = '{{ $_folder }}/comprobantes/' + row.docDepositoGDR;
        urldocb = '{{ $_folder }}/comprobantes/' + row.docDepositoGLB;
        urldocc = '{{ $_folder }}/comprobantes/' + row.docDepositoDer;
        $("a#comprogdr").attr('href', urldoca);
        $("a#comproglb").attr('href', urldocb);
        $("a#comproder").attr('href', urldocc);
        $('#dlgini4{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function tini4_saveDatos() {
      $('#fmini4{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fmini4{{ $_mid }}').form('clear');
            $('#dlgini4{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
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
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comproder"> Ver Imagen/PDF de Comprobante de deposito de derecho</a></div>
        <div style="margin-bottom:0px"><label for="okPagoDerechos3">
            <div style="width:220px;display: inline-block;"><b>Revisado: </b></div>
          </label><input class="easyui-checkbox" id="okPagoDerechos3" name="okPagoDerechos3" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
      </div>
      <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaPagoDerechos" style="width:100%" data-options="label:'<b>Fecha de deposito de derechos</b>',readonly:'true',editable:false" labelWidth="210"></div>
      <div class="easyui-panel" title="Deposito a GDR" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comprogdr"> Ver Imagen/PDF de Comprobante de deposito GDR</a></div>
        <div style="margin-bottom:0px"><label for="okPagoDerechos2">
            <div style="width:220px;display: inline-block;"><b>Revisado: </b></div>
          </label><input class="easyui-checkbox" id="okPagoDerechos2" name="okPagoDerechos2" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
      </div>
      <div class="easyui-panel" title="DepositoGLB" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><a href="" target="_blank" id="comproglb"> Ver Imagen/PDF de Comprobante de deposito GLB</a></div>
        <div style="margin-bottom:0px"><label for="okPagoDerechos1">
            <div style="width:220px;display: inline-block;"><b>Revisado: </b></div>
          </label><input class="easyui-checkbox" id="okPagoDerechos1" name="okPagoDerechos1" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
      </div>

      <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaIniciacion" style="width:100%" data-options="label:'<b>Fecha de ceremonia de Iniciacion</b>',readonly:'true',editable:false" labelWidth="230"></div>

    </form>
  </div>
  <div id="dlgini4-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini4_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini4{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function filtrarDatos(value, campo) {
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
  </script>
@endsection

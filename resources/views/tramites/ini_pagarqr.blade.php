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
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload correcto" onclick="pagarqr_{{ $_mid }}();">Realizar pago con QR</a>
      </div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot5" name="filtrot5" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot5" name="filtrot5" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function revdatos_133() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini5{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        $('#dlgini5{{ $_mid }}').dialog('open').dialog('setTitle', 'Realizar pago usando QR');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function savedatos_133() {
      $('#fmini5{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg +
                '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg +
                '</div>'
            });
            $('#fmini5{{ $_mid }}').form('clear');
            $('#dlgini5{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini5{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini5-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini5{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite para Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="idTramite" style="width:100%" data-options="label:'<b>Numero de Tramite:</b>',readonly:'true',editable:false" labelWidth="180">
        </div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="180">
        </div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="nombre" style="width:100%" data-options="label:'<b>Profano:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numCircular" style="width:100%" data-options="label:'<b>Numero circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaCircular" style="width:100%" data-options="label:'<b>Fecha circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
      </div>
      <div class="easyui-panel" title="Pago de derecho de tramite (35Bs.)" style="width:100%;padding:5px;">
        <div style="margin-bottom:10px"><input class="easyui-datebox" name="fechaDepositoDer" style="width:100%" data-options="label:'F. de deposito o transf. por derecho de tramite*:',required:true" labelWidth="320"></div>
        <div style="margin-top:10px;margin-bottom:5px">
          <label><b>Doc. de deposito o transf. por derecho de tramite*:</b></label>
          <input class="easyui-filebox" id="fileup" name="fileup" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Deposito de derechos'">
        </div>
      </div>
      <div class="easyui-panel" title="Pago de derechos" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><input class="easyui-datebox" name="fechaPagoDerechos" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*: </b>'" labelWidth="210">
        </div>
        <label><b>Doc. de deposito o transf. bancaria por derecho de ceremonia GDR*:</b></label>
        <input class="easyui-filebox" id="fileup1" name="fileup1" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Deposito de derechos GDR'">
        <label><b>Doc. de deposito o transf. bancaria por derecho de ceremonia GLB*:</b></label>
        <input class="easyui-filebox" id="fileup2" name="fileup2" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Deposito de derechos GLB'">
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaIniciacion" style="width:100%" data-options="label:'<b>Fecha de Iniciacion*: </b>',required:true" labelWidth="210"></div>
      </div>
    </form>
  </div>
  <div id="dlgini5-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="savedatos_133();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini5{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
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
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg_pagar{{ $_mid }}" class="easyui-dialog" style="width:980px;height:700px;" data-options="iconCls:'icon-save',resizable:false,modal:true" closed="true" closable="false">
    <div id="cc" class="easyui-layout" style="width:100%;height:100%;">
      <form id="form{{ $_mid }}" method="post" novalidate>
        <div data-options="region:'west',split:true" style="width:240px;font-size:11px;">
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="ceremonia" style="width:100%;" data-options="label:'<b>Tipo:</b>',readonly:'true',editable:false" labelWidth="40"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="50"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="50"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="nombre" style="width:100%;" data-options="label:'<b>Nombre:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="paterno" style="width:100%;" data-options="label:'<b>Paterno:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="materno" style="width:100%;" data-options="label:'<b>Materno:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoGLB2" style="width:100%;" data-options="label:'<b>Derecho tramite:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoGLB" style="width:100%;" data-options="label:'<b>Importe GLB:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoCOMAP" style="width:100%;" data-options="label:'<b>Importe COMAP:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoGDR" style="width:100%;" data-options="label:'<b>Importe GLD/GDR:</b>',readonly:'true',editable:false" labelWidth="130">
          </div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoTotal" style="width:100%;" data-options="label:'<b>Importe Total:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="padding:20px 2px 10px 2px;"><a href="#pagar" class="easyui-linkbutton c6" iconCls="icon-ok" style="width:100%" onclick="abrirPagoi_133();">Pagar derecho de ceremonia</a>
          </div>
          <div style="padding:10px 2px 10px 2px;"><a href="#cancelar" class="easyui-linkbutton c6" iconCls="icon-cancel" style="width:100%" onclick="cancelPagoi_133();">Cerrar/Cancelar transaccion</a>
          </div>
        </div>
        <div data-options="region:'center'" style="width:730px;height:auto;">
          <iframe id="modalpg_133" class="embed-responsive-item" src="" width="98%" height="650" style="border:none;"></iframe>
        </div>
      </form>
    </div>
  </div>
  <script>
    function pagarqr_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == 1) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>Ya tiene pago registrado</div>'
          });
        } else {
          $('#form{{ $_mid }}').form('load', '{{ $_controller }}/get_ceremonia?_token={{ csrf_token() }}&id=' + row.idTramite); // load from URL
          $('#dlg_pagar{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de derechos de Ceremonia');
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione ceremonia primero</div>'
        });
      }
    }

    function cancelPagoi_133() {
      var url = '';
      $("#modalpg_133").attr("src", url);
      $('#dg{{ $_mid }}').datagrid('reload');
      $('#dlg_pagar{{ $_mid }}').dialog('close');
    }

    function cerrarPagoi_133() {
      $('#modalpg_133').attr('src', '');
      $('#dg{{ $_mid }}').datagrid('reload');
      $("#mymodal").hide();
    }

    function abrirPagoi_133() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/get_datos_pagos', {
          _token: tokenModule,
          reg: row.idTramite
        }, function(result) {
          registro = result.ref;
          red = result.red;
          ok = result.ok;
          if (ok == 1) {
            var url = '{{ $linkiframe }}?entidad={{ $entidad }}&red={{ $linkaccion }}/codigo/' + red + '&ref=' + registro;
            $("#modalpg_133").attr("src", url);
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>No hay importe a cancelar</div>'
        });
      }
    }
  </script>

@endsection

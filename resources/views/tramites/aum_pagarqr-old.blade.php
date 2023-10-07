@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    var obolo = 10;
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
              field: 'idCeremonia',
              title: 'Ceremonia',
              hidden: 'true'
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
              field: 'fechaCeremonia',
              title: 'Fecha de Ceremonia'
            },
            {
              field: 'okCeremonia',
              title: 'Estado Ceremonia'
            },
            {
              field: 'numeroAum',
              title: 'Nro. H:.A:.'
            },
            {
              field: 'estadotxt',
              title: 'Estado Pago'
            },
            {
              field: 'fechaModificacion',
              title: 'Ult. Modificacion'
            }
          ]
        ]
      });
    });

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
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDatos_{{ $_mid }}();">Pagar derechos con Pago Electronico</a>
      </div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function revDatos_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == 1) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>Ya tiene pago registrado</div>'
          });
        } else {
          $('#form{{ $_mid }}').form('load', '{{ $_controller }}/get_ceremonia?_token={{ csrf_token() }}&id=' + row.idCeremonia + '&num=' + row.numeroAum); // load from URL
          $('#dlg_pagar{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de derechos de Ceremonia');
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione ceremonia primero</div>'
        });
      }
    }

    //-----------------------------  pagos qr

    function abrirPago_129() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/get_datos_pagos', {
          _token: tokenModule,
          reg: row.idCeremonia
        }, function(result) {
          registro = result.ref;
          red = result.red;
          ok = result.ok;
          if (ok == 1) {
            var url =
              '{{ $linkiframe }}?entidad={{ $entidad }}&red={{ $linkaccion }}/codigo/' +
              red + '&ref=' + registro;
            $("#modalpg_129").attr("src", url);
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>No hay monto a pagar</div>'
        });
      }
    }

    function cancelPago_129() {
      var url = '';
      $("#modalpg_129").attr("src", url);
      $('#dg{{ $_mid }}').datagrid('reload');
      $('#dlg_pagar{{ $_mid }}').dialog('close');
    }

    function cerrarPago_129() {
      $('#modalpg_129').attr('src', '');
      $('#dg{{ $_mid }}').datagrid('reload');
      $("#mymodal").hide();
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg_pagar{{ $_mid }}" class="easyui-dialog" style="width:960px;height:700px;" data-options="iconCls:'icon-save',resizable:false,modal:true" closed="true" closable="false">
    <div id="cc" class="easyui-layout" style="width:100%;height:100%;">
      <form id="form{{ $_mid }}" method="post" novalidate>
        <div data-options="region:'west',split:true" style="width:220px;font-size:11px;">
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="ceremonia" style="width:100%;" data-options="label:'<b>Ceremonia de:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="fechaCeremonia" style="width:100%;" data-options="label:'<b>Fecha Ceremonia:</b>',readonly:'true',editable:false" labelWidth="120">
          </div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="cantidad" style="width:100%;" data-options="label:'<b>Numero CC:.:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="monto" style="width:100%;" data-options="label:'<b>Importe por Comp.:</b>',readonly:'true',editable:false" labelWidth="130">
          </div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoTotal" style="width:100%;" data-options="label:'<b>Importe Total:</b>',readonly:'true',editable:false" labelWidth="130"></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#pagar" class="easyui-linkbutton c6" iconCls="icon-ok" style="width:210px" onclick="abrirPago_129();">Pagar derecho de ceremonia</a>
          </div>
          <div style="padding:10px 2px 10px 2px;"><a href="#cancelar" class="easyui-linkbutton c6" iconCls="icon-cancel" style="width:210px" onclick="cancelPago_129();">Cerrar/Cancelar
              transaccion</a>
          </div>
        </div>
        <div data-options="region:'center'" style="width:730px;height:auto;">
          <iframe id="modalpg_129" class="embed-responsive-item" src="" width="98%" height="650" style="border:none;"></iframe>
        </div>
      </form>
    </div>
  </div>

@endsection

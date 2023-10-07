@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    var obolo = 0;

    function doSearch_147(value) {
      $.post('{{ $_controller }}/search_datos', {
        _token: tokenModule,
        palabra: value
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Error en busqueda'
          });
        }
      }, 'json');
    }

    function clearSearchob_147() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      $.post('{{ $_controller }}/search_datos', {
        _token: tokenModule,
        palabra: ''
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
        }
      }, 'json');
    }


    function doTaller_147(value) {
      $.post('{{ $_controller }}/filter_taller', {
        taller: value
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }
  </script>
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
              field: 'id',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'logia',
              title: 'Logia'
            },
            {
              field: 'LogiaActual',
              title: 'Nro.'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre completo'
            },
            {
              field: 'monto',
              title: 'Monto'
            },
            {
              field: 'fechaCreacion',
              title: 'Pagado en'
            },
            {
              field: 'detail',
              title: 'Opciones',
              formatter: function(value, row) {
                var erow = row.id;
                if (row.estadopago > 0) {
                  return 'Pago ya realizado';
                } else {
                  if (row.pago > 0) {
                    if (row.habil == '1')
                      return '<a href="javascript:void(0)" onclick="verForm_147(' + erow + ');"><button><i class="fa fa-money"></i> Realizar pago QR</button></a>';
                    else
                      return 'No tiene habilitado este pago';
                  } else
                    return 'Pago sin seleccionar';
                }
              }
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
      <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:400,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){fPagosExtra(rec,'taller');}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fPagosExtra(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
        <div style="float:left;">
          <select id="filtropag{{ $_mid }}" name="filtropag{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:400,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){fPagosExtra(rec,'pago');}">
            <option value="0">Seleccionar pago</option>
            @foreach ($pagos as $key => $pagg)
              <option value="{{ $key }}">{{ $pagg }}</option>
            @endforeach
          </select>
        </div>
        <div class="datagrid-btn-separator"></div>
        <div style="float:left;"><input class="easyui-searchbox" style="width:140px" data-options="searcher:searchPagosExtra,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearPagosExtra();"></a>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      function fPagosExtra(value, campo) {
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

      function searchPagosExtra(value) {
        fPagosExtra(value, 'palabra');
      }

      function clearPagosExtra() {
        $('#searchbox{{ $_mid }}').searchbox('clear');
        fPagosExtra('', 'palabra');
      }

      function doPagosFil(value) {
        $.post('{{ $_controller }}/filter_pagos', {
          pago: value
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }

      function abrirPago_147() {
        var canti = $('#cantidad').numberspinner('getValue');
        if (canti > 0) {
          $.post('{{ $_controller }}/get_datos_pagos', {
            _token: tokenModule,
            reg: canti
          }, function(result) {
            registro = result.ref;
            red = result.red;
            ok = result.ok;
            if (ok == '1') {
              var url = '{{ $linkiframe }}?entidad={{ $entidad }}&red={{ $linkaccion }}/codigo/' + red + '&ref=' + registro;
              $("#modalpg").attr("src", url);
            }
          }, 'json');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>No hay monto a pagar'
          });
        }
      }

      function cancelPago_147() {
        var url = '';
        $("#modalpg").attr("src", url);
        $('#dlg_pagar{{ $_mid }}').dialog('close');
        $('#dg{{ $_mid }}').datagrid('reload');
      }

      function cerrarPago_147() {
        $('#modalpg').attr('src', '');
        $("#mymodal").hide();
      }

      function verForm_147(value) {
        $.post('{{ $_controller }}/exe_pago?_token={{ csrf_token() }}', {
          idm: value
        }, function(result) {
          if (result.success) {
            obolo = result.monto;
            window.document.getElementById("vv").innerText = 0;
            $('#cantidad').numberspinner('setValue', 0);
            window.document.getElementById("montof").innerText = "Monto de pago: " + result.monto + ' Bs.';
            window.document.getElementById("miembrof").innerText = result.miembro;
            window.document.getElementById("pagof").innerText = result.pago;
            $('#dlg_pagar{{ $_mid }}').dialog('open').dialog('setTitle', 'Realizar pago Extra');
          }
        }, 'json');
      }
    </script>
    <div id="dlg_pagar{{ $_mid }}" class="easyui-dialog" style="width:960px;height:700px;" data-options="iconCls:'icon-save',resizable:false,modal:true" closed="true" closable="false">
      <div id="cc" class="easyui-layout" style="width:100%;height:100%;">
        <div data-options="region:'west',split:true" style="width:220px;">
          <div style="padding:10px 2px 0px 2px;"><span id="theValue3" style="width:100%;font-weight: bold;">Miembro pagador:</span></div>
          <div style="padding:10px 2px 10px 2px;"><span id="miembrof" style="width:100%;font-weight: bold;">Nombre</span></div>
          <div style="padding:10px 2px 0px 2px;"><span id="theValue4" style="width:100%;font-weight: bold;">Destino de pago:</span></div>
          <div style="padding:10px 2px 10px 2px;"><span id="pagof" style="width:100%;font-weight: bold;">Pago</span></div>
          <div style="padding:5px 2px 10px 2px;"><span id="montof" style="width:100%;font-weight: bold;">Monto: 0 Bs.</span></div>
          <div style="padding:10px 2px 10px 2px;font-weight: bold;">
            <input class="easyui-numberspinner" id="cantidad" style="width:100%;" required="true" label="Cuotas a pagar:" labelPosition="left" labelWidth="110" data-options="min:0,max:1,value: 0,onChange: function(value){var vari=value*obolo;$('#vv').text(vari);}">
          </div>
          <div style="padding:10px 2px 10px 2px;">
            <h4>Monto a pagar: <span id="vv"></span> Bs.</h4>
          </div>
          <div style="padding:10px 2px 10px 2px;"><a href="#pagar" class="easyui-linkbutton c6" iconCls="icon-ok" style="width:210px" onclick="abrirPago_147();">Pagar monto calculado</a></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#cancelar" class="easyui-linkbutton c6" iconCls="icon-cancel" style="width:210px" onclick="cancelPago_147();">Cerrar/Cancelar transaccion</a></div>
        </div>
        <div data-options="region:'center'" style="width:730px;height:auto;">
          <iframe id="modalpg" class="embed-responsive-item" src="" width="98%" height="650" style="border:none;"></iframe>
        </div>
      </div>
    </div>
  </div>
@endsection

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
        fitColumns: true,
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
              field: 'estadotxt',
              title: 'Estado de tramite'
            },
            {
              field: 'casotxt',
              title: 'Tipo de tramite'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'Logia Actual'
            },
            {
              field: 'nLogiaNueva',
              title: 'Logia a Afiliar'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Miembro'
            },
            {
              field: 'fechaCreacion',
              title: 'F. de Solicitud'
            },
            {
              field: 'estadotxt',
              title: 'Estado pago'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
    });


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
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}"  style="display:inline-block">
      @if (Auth::user()->permisos == 1)
        @if (Auth::user()->nivel  == 1  || Auth::user()->nivel  > 4)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o correcto" onclick="afi_pagoqr_131();">Realizar pago QR</a></div>
        @endif
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'taller');}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
              @endforeach
            </select>
          @endif
      @endif
    </div>
  </div>
  </div>
  <!--   formulario de derechos registro  -->
  <div id="dlg_pagar{{ $_mid }}" class="easyui-dialog" style="width:960px;height:700px;" data-options="iconCls:'icon-save',resizable:false,modal:true" closed="true" closable="false">
    <div id="cc" class="easyui-layout" style="width:100%;height:100%;">
      <form id="form{{ $_mid }}" method="post" novalidate>
        <div data-options="region:'west',split:true" style="width:220px;">
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="ceremonia" style="width:100%;" data-options="label:'<b>Tramite de:</b>',readonly:'true',editable:false" labelWidth="90"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="fechaTramite" style="width:100%;" data-options="label:'<b>Ultimo Pago:</b>',readonly:'true',editable:false" labelWidth="100"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoGLB" style="width:100%;" data-options="label:'<b>Monto GLB:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoGDR" style="width:100%;" data-options="label:'<b>Monto GDR/GLD:</b>',readonly:'true',editable:false" labelWidth="120">
          </div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoCOMAP" style="width:100%;" data-options="label:'<b>Monto COMAP:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoTotal" style="width:100%;" data-options="label:'<b>Monto Total:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#pagar" class="easyui-linkbutton c6" iconCls="icon-ok" style="width:210px" onclick="abrirPagoafi_131();">Pagar tramite</a></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#cancelar" class="easyui-linkbutton c6" iconCls="icon-cancel" style="width:210px" onclick="cancelPagoafi_131();">Cerrar/Cancelar transaccion</a>
          </div>
        </div>
        <div data-options="region:'center'" style="width:730px;height:auto;">
          <iframe id="modalpg_131" class="embed-responsive-item" src="" width="98%" height="650" style="border:none;"></iframe>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function afi_pagoqr_131() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estadop == '0') {
          $('#form{{ $_mid }}').form('load',
            '{{ $_controller }}/get_values?_token={{ csrf_token() }}&id=' + row.id + '&num=' + row
            .numeroAum
          );
          $('#dlg_pagar{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de derechos de Tramite');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Pago realizado\n o falta aprobación'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite'
        });
      }
    }

    function cancelPagoafi_131() {
      var url = '';
      $("#modalpg_131").attr("src", url);
      $('#dlg_pagar{{ $_mid }}').dialog('close');
    }

    function cerrarPagoafi_131() {
      $('#modalpg_131').attr('src', '');
      $("#mymodal").hide();
    }

    function abrirPagoafi_131() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/get_pago_datos', {
          _token: tokenModule,
          reg: row.id
        }, function(result) {
          registro = result.ref;
          red = result.red;
          ok = result.ok;
          if (ok == 1) {
            var url =
              '{{ $linkiframe }}?entidad={{ $entidad }}&red={{ $linkaccion }}/codigo/' +
              red + '&ref=' + registro;
            $("#modalpg_131").attr("src", url);
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>No hay monto a pagar'
        });
      }
    }
  </script>
@endsection

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
              title: 'Valle',
              hidden: true
            },
            {
              field: 'nLogia',
              title: 'Logia Actual',
              hidden: true
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
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o correcto" onclick="afi_deposito();">Enviar datos de deposito</a></div>
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

    function afi_deposito(revisar) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '3') {
          $('#fmdeposito').form('load', row);
          $('#dlgdeposito').dialog('open').dialog('setTitle', 'Pago de tramite');
          url = '{{ $_controller }}/registra_pago?_token=' + tokenModule + '&id=' + row.id;
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>No se puede modificar ya fue enviado'
          });
        }

      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }
    function upPagorei() {
      $('#fmdeposito').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg
            });
            $('#fmdeposito').form('clear');
            $('#dlgdeposito').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>

  <!--   formulario de derechos registro  -->
  <div id="dlgdeposito" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#btndeposito" data-options="iconCls:'icon-save',modal:true">
    <form id="fmdeposito" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Miembro para afiliar" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="NombreCompleto" id="numero" label="Miembro:" labelPosition="left" labelWidth="100" class="easyui-textbox" style="width:95%" readonly="true"></div>
      </div>
      <div class="easyui-panel" title="Datos y Pagos de derechos de afiliacion (archivo no mayor a 2 megas)" style="width:100%;padding:5px;">
        <div style="margin-bottom:15px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*:</b>'" required="true" labelWidth="210"></div>
        <div style="margin-bottom:15px">
          <label><b>Documento de deposito o transf. bancaria a cuenta GLSP</b></label>
          <input class="easyui-filebox" id="fileup3" name="fileup2" style="width:100%" buttonText="Archivo PDF/Imagen"  required="true" accept=".pdf,.png,.jpeg,.jpg,.jfif" value="" data-options="prompt:'Documento de deposito GLSP'">
        </div>
      </div>
      <div style="margin-top:15px"><input class="easyui-datebox" name="fechaJuramento" style="width:100%" data-options="label:'<b>Fecha de Juramento en Logia*:</b>'"  required="true" labelWidth="230"></div>
    </form>
  </div>
  <div id="btndeposito">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="upPagorei();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdeposito').dialog('close');" style="width:90px">Cancelar</a>
  </div>

@endsection

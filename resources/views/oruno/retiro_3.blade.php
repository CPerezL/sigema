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
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'Logia Actual'
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
              field: 'fechaModificacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
    });

    function fDatosAfilia(value, campo) {
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
      @if (Auth::user()->permisos == 1)
        @if (Auth::user()->nivel > 2)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o warning" onclick="reti_deposito();">Enviar datos de deposito</a></div>
        @endif
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDatosAfilia(rec,'taller');}">
              <option value="0">Todas la Logias</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDatosAfilia(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
      @endif
    </div>
  </div>
  </div>
  <!-- aprobacion -->
  <script type="text/javascript">
 function reti_deposito(revisar) {
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
          <div class="easyui-panel" title="Miembro Quite Y Placet" style="width:100%;padding:5px;">
            <div style="margin-top:0px"><input name="NombreCompleto" id="numero" label="Miembro:" labelPosition="left" labelWidth="100" class="easyui-textbox" style="width:95%" readonly="true"></div>
          </div>
          <div class="easyui-panel" title="Datos de derechos de Quite Y Placet (archivo no mayor a 2Mb)" style="width:100%;padding:5px;">
            <div style="margin-bottom:15px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*:</b>'" labelWidth="210"></div>
            <div style="margin-bottom:15px">
              <label><b>Documento de deposito o transf. bancaria a cuenta GLSP</b></label>
              <input class="easyui-filebox" id="fileup3" name="fileup2" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif,.jfif" value="" data-options="prompt:'Documento de deposito GLSP'">
            </div>
          </div>
        </form>
      </div>
      <div id="btndeposito">
        @if (Auth::user()->permisos == 1)
          <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="upPagorei();" style="width:90px">Grabar</a>
        @endif
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdeposito').dialog('close');" style="width:90px">Cancelar</a>
      </div>
@endsection

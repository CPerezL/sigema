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
              field: 'idCeremonia',
              title: 'Ceremonia',
              hidden: 'true'
            },
            {
              field: 'valle',
              title: 'Valle',
              hidden: 'true'
            },
            {
              field: 'nLogia',
              title: 'R:.L:.S:.'
            },
            {
              field: 'numero',
              title: 'Nro'
            },
            {
              field: 'fechaExaltacion',
              title: 'Fecha de Ceremonia'
            },
            {
              field: 'NombreCompleto',
              title: 'H:.C:.'
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
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block;">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revdatos_133();">Enviar datos de pago de derechos</a></div>
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
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgexa3{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgexa3-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmexa3{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite para Exaltacion" style="width:100%;padding:5px;">
        {{-- <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div> --}}
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="140"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="apreName" style="width:100%" data-options="label:'<b>C:.M:.:<b>',readonly:'true',editable:false" labelWidth="140"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaExaltacion" style="width:100%" data-options="label:'<b>Fecha ceremonia:<b>',readonly:'true',editable:false" labelWidth="140"></div>
      </div>
      <div class="easyui-panel" title="Pago de derechos" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><input class="easyui-datebox" name="fechaDepoGDR" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*: </b>'" labelWidth="210">
        </div>
        <label><b>Doc. de deposito o transf. bancaria por derechos de Exaltacion:</b></label>
        <input class="easyui-filebox" id="depositoGDR" name="depositoGDR" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Deposito de derechos'">
      </div>
    </form>
  </div>
  <div id="dlgexa3-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="savedatos_133();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgexa3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function revdatos_133() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmexa3{{ $_mid }}').form('load', '{{ $_controller }}/get_ceremonia?_token={{ csrf_token() }}&id=' + row.idTramite); // load from URL
        $('#dlgexa3{{ $_mid }}').dialog('open').dialog('setTitle', 'Registro de deposito');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function savedatos_133() {
      $('#fmexa3{{ $_mid }}').form('submit', {
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
            $('#fmexa3{{ $_mid }}').form('clear');
            $('#dlgexa3{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
@endsection

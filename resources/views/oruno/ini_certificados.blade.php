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
        pageList: [15, 20, 50, 100, 200],
        pageSize: '15',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'idTramite',
              title: 'Tramite',
              hidden: 'true'
            },
            {
              field: 'idMiembro',
              title: 'Tramite',
              hidden: 'true'
            },
            {
              field: 'valle',
              title: 'Valle'
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
              field: 'NombreCompleto',
              title: 'Profano'
            },
            {
              field: 'numeroCertificado',
              title: 'N. Cert.'
            },
            {
              field: 'fechaCertificado',
              title: 'F. Cert.'
            },
            {
              field: 'fechaIniciacion',
              title: 'F. de Cere.'
            },
            {
              field: 'estadotxt',
              title: 'Pago'
            },
            {
              field: 'okCeremonia',
              title: 'Estado'
            },
            {
              field: 'fechaModificacion',
              title: 'Modif.'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:'false',onChange: function(rec){filtrarDatosIniCert(rec,'valle',1);}">
            <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:'false',onChange: function(rec){filtrarDatosIniCert(rec,'valle',1);}">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:left;">
        <input id="flogias_144" name="flogias_144" class="easyui-combobox" data-options="width:340,valueField:'nlogia',textField:'logian',url:'{{ $_controller }}/get_logias?_token={{ csrf_token() }}',onChange: function(rec){filtrarDatosIniCert(rec,'taller');}" value="0">
      </div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDatos_{{ $_mid }}();">Gestionar certificado de aprendiz</a></div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchIniCert,prompt:'Buscar nombre'" id="searchbox{{ $_mid }}" value="">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchIniCert();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function filtrarDatosIniCert(value, campo, opcion = 0) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          if (opcion == '1')
            $('#flogias_144').combobox('reload');

          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function doSearchIniCert(value) {
      filtrarDatosIniCert(value, 'palabra');
    }

    function clearSearchIniCert() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosIniCert('', 'palabra');
    }

    function revDatos_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.docDepositoDer !== null && row.docDepositoDer.length > 4) {
          $('#abrir_doc').linkbutton('enable');
          $('#abrir_doc').attr("target", "_blank");
          var newUrl = '{{ $_folder }}/media/tramites/' + row.docDepositoDer;
          $('#abrir_doc').attr("href", newUrl);
        } else {
          $('#abrir_doc').attr("target", "_self");
          $('#abrir_doc').linkbutton('disable');
        }
        $('#fmaum2{{ $_mid }}').form('clear');
        $('#fmaum2{{ $_mid }}').form('load', '{{ $_controller }}/get_tramites?_token=' + tokenModule + '&id=' + row.idTramite); // load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Datos del certificado');
        url = '{{ $_controller }}/update_ceremonia?_token=' + tokenModule + '&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveDatos_{{ $_mid }}() {
      $('#fmaum2{{ $_mid }}').form('submit', {
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
            $('#fmaum2{{ $_mid }}').form('clear');
            $('#dlgauun{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmaum2{{ $_mid }}" method="post" novalidate>
      <div class="easyui-panel" title="Certificado de Aprendiz para" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px;margin-left:5px">
          <input class="easyui-textbox" name="apreName1" style="width:100%;" data-options="label:'<b>A:.M:.:</b>',labelPosition:'left',editable:false" labelWidth="60">
        </div>
        <input type="hidden" name="idMiembro1" value="0" />
      </div>
      <div class="easyui-panel" title="Fecha del certificado" style="width:100%;padding:5px;">
        <label><b>El certificado saldra con la fecha de la Ceremonia, puede cambiarlo en el cuadro siguiente</b></label>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaCeremonia1" style="width:100%" data-options="label:'Fecha de Ceremonia:'" labelWidth="260"></div>
      </div>
      <div class="easyui-panel" title="Numero de Certificado" style="width:100%;padding:5px;">
        <label><b>El certificado necesita un numero</b></label>
        <div style="margin-top:0px"><input name="numero1" id="numero1" label="Numero:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div>
      <div class="easyui-panel" title="Revision de derechos de Ceremonia y Cetificación" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px">Documento de Deposito: <a href="javascript:void(0);" id="abrir_doc" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:250px;margin-left:10px;">Ver documento archivo</a></div>
        <div style="width:270px;display: inline-block;"><b>Revisado por Tesoreria:</b></div>
        <input class="easyui-checkbox" id="okPagoDerechos" name="okPagoDerechos" value="1" data-options="label:'<b>Revisado y aprobado<b>',labelPosition:'after'" labelWidth="80"></div>
      </div>

    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

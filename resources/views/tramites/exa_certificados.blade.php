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
              title: 'C:.F:.'
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
              field: 'fechaCeremonia',
              title: 'F. de Cere.'
            },
            {
              field: 'estadotxt',
              title: 'Pago'
            },
            {
              field: 'okCeremonia',
              title: 'Ceremonia'
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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:400,valueField: 'id',textField: 'text',editable:false,
                  onChange: function(rec){
                    filtrarDatosExaCert(rec,'valle');
                  }">
            <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fvalle" name="fvalle" width="150" class="easyui-combobox" data-options="width:160,panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,
          onChange: function(rec){
            filtrarDatosExaCert(rec,'valle');
          }">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:left;">
        <input id="flogias_142" name="flogias_142" class="easyui-combobox" data-options="width:340,valueField:'nlogia',textField:'logian',url:'{{ $_controller }}/get_logias?_token={{ csrf_token() }}',onChange: function(rec){filtrarDatosExaCert(rec,'taller');}" value="0">
      </div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDatos_{{ $_mid }}();">Gestionar certificado de Maestro</a></div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchExaCert,prompt:'Buscar nombre'" id="searchbox{{ $_mid }}" value="">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchExaCert();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function filtrarDatosExaCert(value, campo) {
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

    function doSearchExaCert(value) {
      filtrarDatosExaCert(value, 'palabra');
    }

    function clearSearchExaCert() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosExaCert('', 'palabra');
    }


    function revDatos_{{ $_mid }}() {
      var rows = $('#dg{{ $_mid }}').datagrid('getSelections');
      var count = $('#dg{{ $_mid }}').datagrid('getSelections').length;
      var i;
      var ids = '';
      for (i = 0; i < count; ++i) {
        ids += '&ids[]=' + rows[i].idTramite;
        //console.log(rows[i].idMiembro);
      }
      if (count > 0) {
        $('#fmexa2{{ $_mid }}').form('clear');
        $('#fmexa2{{ $_mid }}').form('load', '{{ $_controller }}/get_tramites?_token=' + tokenModule + ids); // load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Datos del certificado');
        url = '{{ $_controller }}/update_ceremonia?_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveDatos_{{ $_mid }}() {
      $('#fmexa2{{ $_mid }}').form('submit', {
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
            $('#fmexa2{{ $_mid }}').form('clear');
            $('#dlgauun{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmexa2{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Certificado de Maestro para" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px;margin-left:5px">
          <input class="easyui-textbox" name="apreName1" style="width:100%;" data-options="label:'<b>M:.M:.:</b>',labelPosition:'left',editable:false" labelWidth="60">
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
        <div style="margin-bottom:2px"><label for="certificado1">
            <div style="width:270px;display: inline-block;"><b>Revisado por Secretaria:</b></div>
          </label><input class="easyui-checkbox" id="certificado1" name="certificado1" value="1" data-options="label:'<b>Autorizado<b>',labelPosition:'after'" labelWidth="80"></div>
        <div style="width:270px;display: inline-block;"><b>Revisado por Tesoreria:</b></div>
        </label><input class="easyui-checkbox" id="certificado1" name="certificado1" value="1" data-options="label:'<b>Autorizado<b>',labelPosition:'after'" labelWidth="80">
      </div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

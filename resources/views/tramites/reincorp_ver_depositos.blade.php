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
            title: 'Taller'
          },
          {
            field: 'idLogia',
            title: 'Nro'
          },
          {
            field: 'fechaCreacion',
            title: 'F. Insinuacion'
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
            field: 'fechaModificacion',
            title: 'Modificacion'
          },
          {
            field: 'fechaDeposito',
            title: 'F. Deposito'
          },
        ]
      ]
    });
  });
</script>
<div class="easyui-layout" data-options="fit:true">
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:'auto';"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDeposReincorp(rec,'taller');}">
          <option value="0">Todas las Logias</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDeposReincorp(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    @if (Auth::user()->nivel > 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o correcto" onclick="rei_aprobar();">Ver comprobantes registrados</a></div>
    @endif
  </div>
</div>
<!--   formulario de derechos registro  -->
<script type="text/javascript">
  function fDeposReincorp(value, campo) {
    $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      },
      function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      },
      'json');
  }

  function searchDeposAum(value) {
    fDeposReincorp(value, 'palabra');
  }

  function clearSearchDeposAum() {
    $('#searchbox{{ $_mid }}').searchbox('clear');
    fDeposReincorp('', 'palabra');
  }
</script>
<!-- edicion y aporbqaion -->
<div id="dlg2{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons2{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
  <form id="fm2{{ $_mid }}" method="post" novalidate>
    <input name="id" id="id" type="hidden">
    <input name="tipo" type="hidden">
    <input name="especial" type="hidden">
    <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
    <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
    <div style="margin-top:2px"><input class="easyui-datebox" name="fAprobacionLogia" id="faprologia" style="width:100%" data-options="label:'Fecha de Aprobacion en Logia*:',required:true" labelWidth="200"></div>
    <div style="margin-top:2px"><input class="easyui-textbox" name="actaAprobacionLogia" id="actaprologia" style="width:100%" data-options="label:'No. Acta de Aprobacion en Logia*:',required:true" labelWidth="200"></div>

    <div style="margin-top:2px">
      <label class="textbox-label textbox-label-top" style="text-align: left;"><b>Comentarios</b></label>
      <input name="observaciones" class="easyui-textbox" multiline="true" onRead="onRead" style="width:100%; height:120px" />
    </div>
  </form>
</div>
<div id="dlgl-buttons2{{ $_mid }}">
  @if (Auth::user()->nivel > 1)
    <a href="javascript:void(0)" id="btn_formrecha" class="easyui-linkbutton c6" iconCls="icon-clear" onclick="no_cambiaItem();" style="width:150px">Rechazar tramite</a>
  @endif
  <a href="javascript:void(0)" id="btn_formtre" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="reigdr_continuar();" style="width:150px">Aprobar tramite</a>
  <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg2{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
</div>
<!-- aprobacion -->
<div id="dlg4{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons4{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
  <form id="fm4{{ $_mid }}" method="post" novalidate>
    <input name="id" id="id" type="hidden">
    <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
    <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
    <div style="margin-top:2px"><input class="easyui-datebox" name="fAprobacionLogia" style="width:100%" data-options="label:'Fecha de Aprobacion en Logia:'" labelWidth="200" readonly="true"></div>
    <div style="margin-top:2px"><input class="easyui-textbox" name="actaAprobacionLogia" style="width:100%" data-options="label:'No. Acta de Aprobacion:'" labelWidth="200" readonly="true"></div>
    <div style="margin-top:2px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'Fecha de deposito:'" labelWidth="200" readonly="true"></div>
    <div style="margin-top:0px">Deposito: <a href="javascript:void(0)" id="abrir_dep" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para COMAP</a></div>
    <div style="margin-top:2px">Deposito: <a href="javascript:void(0)" id="abrir_dep2" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para GLD/GDR</a></div>
    <div style="margin-top:2px">Deposito: <a href="javascript:void(0)" id="abrir_dep3" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para GLB</a></div>
    <div style="margin-top:2px"><input class="easyui-datebox" id='mesreincorp' name="mesreincorp" style="width:100%" data-options="label:'Mes de reincorporacion:(solo se usa el mes)'" labelWidth="260"></div>
    <div style="margin-top:2px">
      <label class="textbox-label textbox-label-top" style="text-align: left;"><b>Comentarios</b></label>
      <input name="observaciones" class="easyui-textbox" multiline="true" onRead="onRead" style="width:100%; height:120px" readonly="true" />
    </div>
  </form>
</div>
<div id="dlgl-buttons4{{ $_mid }}">
  <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg4{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
</div>
<script type="text/javascript">
  function rei_aprobar() {
    var row = $('#dg{{ $_mid }}').datagrid('getSelected');
    if (row) {
      if (row.estado != '2223') {
        $('#dlg4{{ $_mid }}').dialog('open').dialog('setTitle', 'Revisar depositos de reincorporacion');
        $('#faprologia').textbox('readonly', true);
        $('#actaprologia').textbox('readonly', true);
        $('#fm4{{ $_mid }}').form('clear');
        var newUrl = '{{ $_folder }}/media/tramites/' + row.archivo;
        $('#abrir_dep').attr("href", newUrl);
        var newUrl2 = '{{ $_folder }}/media/tramites/' + row.archivo2;
        $('#abrir_dep2').attr("href", newUrl2);
        var newUrl3 = '{{ $_folder }}/media/tramites/' + row.archivo3;
        $('#abrir_dep3').attr("href", newUrl3);
        $('#fm4{{ $_mid }}').form('load', row);
        $('#mesreincorp').datebox('setValue', '{{ $mesactual }}');
        url = '{{ $_controller }}/cambia_datos?task=4&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Ya esta aprobado o no se puede modificar</div>'
        });
      }
    } else {
      $.messager.show({
        title: 'Error',
        msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
      });
    }
  }
</script>
@endsection

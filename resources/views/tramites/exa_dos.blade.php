@extends('layouts.easyuitab')
@section('content')
<script type="text/javascript">
    $(function () {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {_token: tokenModule},
        toolbar: '#toolbar{{ $_mid }}',
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: false,
        nowrap: true,
        pageList: [20, 50, 100, 200],
        pageSize: '20',
        columns: [[
            {field: 'ck', title: '', checkbox: true},
            {field: 'idTramite', title: 'Tramite', hidden: 'true'},
            {field: 'idMiembro', title: 'Tramite', hidden: 'true'},
            {field: 'nivel', title: 'Estado de tramite'},
            {field: 'valle', title: 'Valle'},
            {field: 'nLogia', title: 'Taller'},
            {field: 'numero', title: 'Nro'},
            {field: 'fechaIniciacion', title: 'Fecha de Iniciacion'},
            {field: 'fechaAumento', title: 'Fecha de Aumento'},
            {field: 'NombreCompleto', title: 'C:.F:.'},
            {field: 'fCeremonia', title: 'Fecha de Ceremonia'},
            {field: 'okCeremonia', title: 'Ceremonia'},
            {field: 'fechaModificacion', title: 'Modificacion'}
          ]]
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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" ></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block;">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDatos_{{ $_mid }}();">Registrar Ceremonia</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
        <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
          <option value="0">Seleccionar talller</option>
          @foreach ( $logias as $key => $logg )
          <option value="{{$key}}">R:.L:.S:. {{$logg}}</option>
          @endforeach
        </select>
        @else
        <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
          @foreach ( $logias as $key => $logg )
          <option value="{{$key}}" selected="selected">R:.L:.S:. {{$logg}}</option>
          @endforeach
        </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function revDatos_{{ $_mid }}() {
      var rows = $('#dg{{ $_mid }}').datagrid('getSelections');
      var count = $('#dg{{ $_mid }}').datagrid('getSelections').length;
      var i;
      var ids = '';
      for (i = 0; i < count; ++i) {
        ids += '&ids[]=' + rows[i].idMiembro;
        //console.log(rows[i].idMiembro);
      }
      if (count > 0) {
        $('#fmini0{{ $_mid }}').form('clear');
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramites?_token={{ csrf_token() }}' + ids);	// load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Revision de tramite para Exaltacion');
        url = '{{ $_controller }}/update_ceremonia?_token={{ csrf_token() }}';
      } else
      {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }
    function saveDatos_{{ $_mid }}() {
      $('#fmini0{{ $_mid }}').form('submit', {url: url,
        onSubmit: function () {
          return $(this).form('validate');
        },
        success: function (result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fmini0{{ $_mid }}').form('clear');
            $('#dlgauun{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}"
       data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div class="easyui-panel" title="Lista de compañeros" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName1" style="width:100%;" data-options="label:'<b>1. C:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
        <input type="hidden" name="idMiembro1" value="0"/>
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName2" style="width:100%;" data-options="label:'<b>2. C:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
        <input type="hidden" name="idMiembro2" value="0"/>
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName3" style="width:100%;" data-options="label:'<b>3. C:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
        <input type="hidden" name="idMiembro3" value="0"/>
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName4" style="width:100%;" data-options="label:'<b>4. C:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
        <input type="hidden" name="idMiembro4" value="0"/>
      </div>
      <div class="easyui-panel" title="Información del tramite" style="width:100%;padding:5px;">
        <label><b>Registre la fecha de Ceremonia y despues puede pagar los derechos usando pago QR</b></label>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaCeremonia" style="width:100%" data-options="label:'Fecha de Ceremonia:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="monto" style="width:100%;" data-options="label:'<b>Monto total derechos(Bs):</b>',readonly:'true',editable:false" labelWidth="180"></div>
      </div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>

@endsection

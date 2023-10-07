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
              title: 'Nro. H:.C:.'
            },
            {
              field: 'fechaModificacion',
              title: 'Ult. Modificacion'
            }
          ]
        ]
      });
    });
  </script>
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:'auto';"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDeposExa{{ $_mid }}(rec,'taller');}">
          <option value="0">Todas las Logias</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDeposExa{{ $_mid }}(rec.'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square correcto" onclick="revDatos_{{ $_mid }}();">Ver datos de depositos</a></div>
  </div>

  <script type="text/javascript">
    function fDeposExa{{ $_mid }}(value, campo) {
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

    function searchDeposAum(value) {
      fDeposExa{{ $_mid }}(value, 'palabra');
    }

    function clearSearchDeposAum() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      fDeposExa{{ $_mid }}('', 'palabra');
    }

    function revDatos_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');

      if (row) {
        $('#fmini0{{ $_mid }}').form('clear');
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&id=' + row.idCeremonia); // load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Datos de ceremonia de Exaltacion');
        url = '{{ $_controller }}/update_ceremonia?_token={{ csrf_token() }}&id=' + row.idCeremonia;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione ceremonia primero</div>'
        });
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div class="easyui-panel" title="Lista de aumentados" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName1" style="width:100%;" data-options="readonly:'true',editable:false" labelWidth="0"></div>
        <input type="hidden" name="idMiembro1" value="0" />
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName2" style="width:100%;" data-options="readonly:'true',editable:false" labelWidth="0"></div>
        <input type="hidden" name="idMiembro2" value="0" />
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName3" style="width:100%;" data-options="readonly:'true',editable:false" labelWidth="0"></div>
        <input type="hidden" name="idMiembro3" value="0" />
        <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName4" style="width:100%;" data-options="readonly:'true',editable:false" labelWidth="0"></div>
        <input type="hidden" name="idMiembro4" value="0" />
      </div>
      <div class="easyui-panel" title="Revisar deposito de derechos" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="depositoGLB" style="width:100%" data-options="label:'<b>Deposito GLB(800) :</b>',required:true,readonly:'true',editable:false" labelWidth="210"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="depositoGDR" style="width:100%" data-options="label:'<b>Deposito GDR(875) :</b>',required:true,readonly:'true',editable:false" labelWidth="210"></div>
        <div style="margin-bottom:5px"><input class="easyui-datebox" name="fechaCeremonia" style="width:100%" data-options="label:'<b>Fecha ceremonia*: </b>',required:true,readonly:'true',editable:false" labelWidth="210"></div>
      </div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    {{-- <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a> --}}
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

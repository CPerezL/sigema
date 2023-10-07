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
              field: 'idTramite',
              title: 'Tramite'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'circular',
              title: 'Nro Circular'
            },
            {
              field: 'fechaCircular',
              title: 'F. Circular'
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
              field: 'fInsinuacion',
              title: 'Fecha de Insinuacion'
            },
            {
              field: 'apPaterno',
              title: 'Ap. Paterno'
            },
            {
              field: 'apMaterno',
              title: 'Ap. Materno'
            },
            {
              field: 'nombres',
              title: 'Nombres'
            },
            {
              field: 'foto',
              title: 'Foto',
              hidden: 'false'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload correcto" onclick="ini5_revDatos();"> Registrar ceremonia de Iniciacion</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot5" name="filtrot5" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot5" name="filtrot5" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini5_revDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini5{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token()}}&idTra=' + row.idTramite); // load from URL
        $('#dlgini5{{ $_mid }}').dialog('open').dialog('setTitle', 'Registrar depositos');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token()}}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function tini5_saveDatos() {
      $('#fmini5{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
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
            $('#fmini5{{ $_mid }}').form('clear');
            $('#dlgini5{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini5{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini5-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini5{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite para Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="idTramite" style="width:100%" data-options="label:'<b>Numero de Tramite:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="nombre" style="width:100%" data-options="label:'<b>Profano:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numCircular" style="width:100%" data-options="label:'<b>Numero circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaCircular" style="width:100%" data-options="label:'<b>Fecha circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
      </div>
      <div class="easyui-panel" title="Fecha ed cerermonia de Iniciacion" style="width:100%;padding:5px;">
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaIniciacion" style="width:100%" data-options="label:'<b>Fecha de Iniciacion*: </b>',required:true" labelWidth="210"></div>
      </div>
    </form>
  </div>
  <div id="dlgini5-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini5_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini5{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
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
@endsection

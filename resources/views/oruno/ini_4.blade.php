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
              title: 'Tramite',
              hidden: true
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
              title: 'Valle',
              hidden: true
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
              title: 'Fecha de Proposición'
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
      {{-- <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-upload correcto" onclick="ini3_revDatos();"> Cargar datos de informes</a></div> --}}
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-close danger" onclick="ini_observaDatos();"> Observar y terminar tramite</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot3" name="filtrot3" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot3" name="filtrot3" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini3_revDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini3{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        $('#dlgini3{{ $_mid }}').dialog('open').dialog('setTitle', 'Datos de informes aprobados');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function tini3_saveDatos() {
      $('#fmini3{{ $_mid }}').form('submit', {
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
            $('#fmini3{{ $_mid }}').form('clear');
            $('#dlgini3{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini3{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini3-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini3{{ $_mid }}" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite para Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="idTramite" style="width:100%" data-options="label:'<b>Numero de Tramite:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="nombre" style="width:100%" data-options="label:'<b>Profano:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numCircular" style="width:100%" data-options="label:'<b>Numero circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaCircular" style="width:100%" data-options="label:'<b>Fecha circular:<b>',readonly:'true',editable:false" labelWidth="180"></div>
      </div>
      <div class="easyui-panel" title="Informe Laboral" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-datebox" name="fechaInfLaboral" style="width:100%" data-options="label:'<b>Fecha de informe laboral*: </b>',required:true" labelWidth="210"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numActaInfLaboral" style="width:100%" data-options="label:'<b>Nro. Acta - Grado 1*:</b>',required:true" labelWidth="210"></div>
      </div>
      <div class="easyui-panel" title="Informe Social" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-datebox" name="fechaInfSocial" style="width:100%" data-options="label:'<b>Fecha de informe social*: </b>',required:true" labelWidth="210"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="numActaInfSocial" style="width:100%" data-options="label:'<b>No. Acta - Grado 1*:</b>',required:true" labelWidth="210"></div>
      </div>

    </form>
  </div>
  <div id="dlgini3-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini3_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
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
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlg_observa" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlg_observa-buttons" data-options="iconCls:'icon-save',modal:true">
    <form id="fm_observa" method="post" novalidate>
      <input type="hidden" name="idTramite" />
      <input type="hidden" name="idObservacion" />
      <div style="margin-top:5px"><input class="easyui-datebox" name="fechaRegistro" style="width:100%" data-options="label:'Fecha de Denuncia *:',required:true" labelWidth="170"></div>
      <div style="margin-top:5px">
        <select id="tipo" name="tipo" class="easyui-combobox" style="width:100%" label="Tipo de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          <option value="1" selected>Balota negra</option>
          <option value="2">Reporte de Miembro</option>
        </select>
      </div>
      <div style="margin-top:5px">
        <select id="estado" name="estado" class="easyui-combobox" style="width:100%" label="Estado de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          <option value="0" selected>Registrado</option>
          <option value="1">Descartado/Sin respaldo</option>
          <option value="2">Aprobado/Comprobado</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <textarea id="descripcion" name="descripcion" class="easyui-textbox" data-options="multiline:true,required:true" label="Observación:" labelPosition="top" style="width:100%;height:200px"></textarea>
      </div>
    </form>
  </div>
  <div id="dlg_observa-buttons">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="obs_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:140px">Cancelar/Cerrar</a>
  </div>
  <script type="text/javascript">
      function ini_observaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fm_observa').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        $('#dlg_observa').dialog('open').dialog('setTitle', 'Observar tramite');
        url = '{{ $_controller }}/save_observacion?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }
    function obs_saveDatos() {
      $('#fm_observa').form('submit', {
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
            $('#fm_observa').form('clear');
            $('#dlg_observa').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
@endsection

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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="ini1_revDatos();">Revisar documentos enviados</a></div>
      <div style="float:left;">
        <select id="filtrot1" name="filtrot1" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'taller');}">
          <option value="0">Ver todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      </div>
      <div class="datagrid-btn-separator"></div>
      <div style="float:left;"><input class="easyui-searchbox" style="width:140px" data-options="searcher:searchDatos,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra !!}">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearch();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini1_revDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini1{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        if (row.foto) {
          $('#photo{{ $_mid }}').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src')); //recraga imagen
        } else {
          $('#photo{{ $_mid }}').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src'));
        }
        $('#dlgini1{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite 1');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function tini1_saveDatos() {
      $('#fmini1{{ $_mid }}').form('submit', {
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
            $('#fmini1{{ $_mid }}').form('clear');
            $('#dlgini1{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function onOpenDialogMy() {
      if ($("#foto").val().length > 4) {
        $('#photoImage').attr('src', '{{ $_folder }}media/fotos/' + $("#foto").val());
      } else {
        $('#photoImage').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
      }
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini1{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini1-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini1{{ $_mid }}"  method="post" novalidate>
      <input type="hidden" name="idTramite" />
      <input type="hidden" name="foto" id="foto" />
      <div class="easyui-panel" title="Datos del tramite de Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="idTramite" style="width:100%" data-options="label:'<b>Numero de Tramite:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="180"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="nombre" style="width:100%" data-options="label:'<b>Profano:<b>',readonly:'true',editable:false,setBorder:'false'" labelWidth="180"></div>
        <div style="margin-bottom:0px"><img src='' id="photo{{ $_mid }}" height="120" /></div>
      </div>
      <div class="easyui-panel" title="Documentos del Profano" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="maestro" style="width:100%" data-options="label:'<b>Presentado por el Q.H. Maestro:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><label for="okcurriculump">
            <div style="width:270px;display: inline-block;"><b>Curriculum profano</b></div>
          </label><input class="easyui-checkbox" id="okcurriculump" name="okcurriculump" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
        <div style="margin-bottom:0px"><label for="okcompromisop">
            <div style="width:270px;display: inline-block;"><b>Compromiso padrino:</b></div>
          </label><input class="easyui-checkbox" id="okcompromisop" name="okcompromisop" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
        <div style="margin-bottom:0px"><label for="okinformefamiliar">
            <div style="width:270px;display: inline-block;"><b>Informe familiar:</b></div>
          </label><input class="easyui-checkbox" id="okinformefamiliar" name="okinformefamiliar" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fInsinuacion" style="width:80%" data-options="label:'<b>Fecha de Insinuación:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="actaInsinuacion" style="width:100%" data-options="label:'<b>No. Acta - Grado 1ro de insinuacion:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><label for="okactainsinuacion">
            <div style="width:270px;display: inline-block;"><b>Acta insinuación:</b></div>
          </label><input class="easyui-checkbox" id="okactainsinuacion" name="okactainsinuacion" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaAprobPase" style="width:80%" data-options="label:'<b>Fecha del acta de aprobación de pase:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="actaInformePase" style="width:100%" data-options="label:'<b>No. Acta - Grado 3ro de aprobación de pase:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><label for="okactaaprobacion">
            <div style="width:270px;display: inline-block;"><b>Acta aprobación:</b></div>
          </label><input class="easyui-checkbox" id="okactaaprobacion" name="okactaaprobacion" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="fechaActaInforme" style="width:80%" data-options="label:'<b>Fecha acta informe de pase:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><input class="easyui-textbox" name="actaAprobPase" style="width:100%" data-options="label:'<b>No. Acta - Grado 1ro de informe de pase:</b>',readonly:'true',editable:false" labelWidth="270"></div>
        <div style="margin-bottom:0px"><label for="okactainforme">
            <div style="width:270px;display: inline-block;"><b>Acta de informe: </b></div>
          </label><input class="easyui-checkbox" id="okactainforme" name="okactainforme" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="60"></div>
      </div>
    </form>
  </div>
  <div id="dlgini1-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini1_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini1{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
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

    function searchDatos(value) {
      filtrarDatos(value, 'palabra');
    }

    function clearSearch() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatos('', 'palabra');
    }
  </script>
@endsection

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
              field: 'logian',
              title: 'R:.L:.S:.'
            },
            {
              field: 'NombreCompleto',
              title: 'A:.M:.'
            },
            {
              field: 'antiguedad',
              title: 'Antiguedad'
            },
            {
              field: 'cubierto',
              title: 'Obolos'
            },
            {
              field: 'ntenidas',
              title: 'Nº Tenidas'
            },
             {
              field: 'asiste',
              title: 'Asistencia'
            },
            {
              field: 'ordinaria',
              title: '% Asistencia'
            },
            {
              field: 'pagoOk',
              title: 'pagoOk',
              hidden: 'true'
            },
            {
              field: 'antOk',
              title: 'antOk',
              hidden: 'true'
            },
            {
              field: 'FechaIniciacion',
              title: 'Fecha Iniciacion'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo Pago'
            },
            {
              field: 'tramite',
              title: 'Tramite'
            }
          ]
        ],
        rowStyler: function(index, row) {
          if (row.pagoOk == '1' && row.antOk == '1')
            return {class:'activo'};
          else
            return {class:'alerta'};;
        }
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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square fa-lg correcto"  onclick="newTramite_{{ $_mid }}();">Gestionar Tramite</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot{{ $_mid }}" name="filtrot{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function newTramite_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.pagoOk == '1' && row.antOk == '1') {
          $('#fmini0{{ $_mid }}').form('clear');
          $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&id=' + row.id); // load from URL
          $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Formulario de inicio de tramite para Aumento');
          url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>No cumple requisitos</div>'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function ini0_editDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function tini0_saveDatos() {
      $('#fmini0{{ $_mid }}').form('submit', {
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
            $('#fmini0{{ $_mid }}').form('clear');
            $('#dlgini0{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini0{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini0-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="idMiembro" />
      <input type="hidden" name="idTramite" value="0" />
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="apreName" style="width:100%;" data-options="label:'<b>A:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div class="easyui-panel" title="Información del tramite" style="width:100%;padding:5px;">
        <label style="color:red;"><b>Los datos  con (*) son necesarios</b></label>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaIniciacion" style="width:100%" data-options="label:'Fecha de Iniciacion*:',readonly:'true',editable:false" labelWidth="320"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaIniciacion" style="width:100%" data-options="label:'No. Acta 1er Grado - iniciacion:'" labelWidth="320"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaPase" style="width:100%" data-options="label:'Fecha acta de solicitud de aumento*:',required:true" labelWidth="320"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaPase" style="width:100%" data-options="label:'No. Acta 2do Gr. de solicitud de aumento*:',required:true" labelWidth="320"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaExamen" style="width:100%" data-options="label:'Fecha de examen de Aumento de Salario*:',required:true" labelWidth="320"></div>
        <div style="margin-bottom:10px"><input class="easyui-textbox" name="actaExamen" style="width:100%" data-options="label:'No. Acta 2do Gr. de examen de aumento*:',required:true" labelWidth="320"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaAumento" style="width:100%" data-options="label:'Fecha de Ceremonia de Aumento de Salario*:',required:true" labelWidth="320"></div>
      </div>
    </form>
  </div>
  <div id="dlgini0-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini0_saveDatos();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

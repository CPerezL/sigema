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
              field: 'idMiembro',
              title: 'Tramite',
              hidden: 'true'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'fechaExaltacion',
              title: 'Fecha de Ceremonia'
            },
            {
              field: 'valle',
              title: 'Valle',
              hidden: true
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
              field: 'fechaIniciacion',
              title: 'Fecha de Iniciacion'
            },
            {
              field: 'fechaAumento',
              title: 'Fecha de Aumento'
            },
            {
              field: 'NombreCompleto',
              title: 'Q:.H:.C:.'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
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
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display: inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDatos_{{ $_mid }}();">Revisar documentacion de tramite</a></div>
      <div style="float:left;">
        <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
          <option value="0">Ver todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function revDatos_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&id=' + row.idMiembro); // load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Revision de tramite para Exaltacion');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idMiembro;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function saveDatos_{{ $_mid }}() {
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
            $('#dlgauun{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="idMiembro" />
      <input type="hidden" name="idTramite" value="0" />
      {{-- <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="80"></div> --}}
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="compaName" style="width:100%;" data-options="label:'<b>C:.F:.M:.</b>',readonly:'true',editable:false" labelWidth="80"></div>
      <div class="easyui-panel" title="Información del tramite" style="width:100%;padding:5px;">
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaIniciacion" style="width:100%" data-options="label:'Fecha de Iniciacion:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaIniciacion" style="width:100%" data-options="label:'No. Acta 1er Grado - Iniciacion:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:2px"><label for="okPagoIniciacion">
            <div style="width:290px;display: inline-block;"><b>Iniciacion:</b></div>
          </label><input class="easyui-checkbox" id="okIniciacion" name="okIniciacion" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaAumento" style="width:100%" data-options="label:'Fecha de Aumento:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaAumento" style="width:100%" data-options="label:'No. Acta 2do Grado - Aumento:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:2px"><label for="okAumento">
            <div style="width:290px;display: inline-block;"><b>Aumento de Salario:</b></div>
          </label><input class="easyui-checkbox" id="okAumento" name="okAumento" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaPase" style="width:100%" data-options="label:'Fecha de acta de solicitud de pase:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaPase" style="width:100%" data-options="label:'No. Acta 3er Grado de solicitud de pase:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:2px"><label for="okSolicitud">
            <div style="width:290px;display: inline-block;"><b>Solicitud:</b></div>
          </label><input class="easyui-checkbox" id="okSolicitud" name="okSolicitud" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaExamen" style="width:100%" data-options="label:'Fecha de examen de Exaltacion:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaExamen" style="width:100%" data-options="label:'No. Acta 2do Grado de examen Exaltacion:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:2px"><label for="okExamen">
            <div style="width:290px;display: inline-block;"><b>Examen:</b></div>
          </label><input class="easyui-checkbox" id="okExamen" name="okExamen" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
      </div>
      <div class="easyui-panel" title="Datos adicionales" style="width:100%;padding:5px;">
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaExaltacion" style="width:100%" data-options="label:'Fecha de Ceremonia de Exaltacion:',required:true" labelWidth="260"></div>
        <div style="margin-bottom:2px"><label for="okTrabajos">
            <div style="width:290px;display: inline-block;"><b>Trabajos presentados:</b></div>
          </label><input class="easyui-checkbox" id="okTrabajos" name="okTrabajos" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
        <div style="margin-bottom:2px"><label for="okAsistencia">
            <div style="width:290px;display: inline-block;"><b>Asistencias:</b></div>
          </label><input class="easyui-checkbox" id="okAsistencia" name="okAsistencia" value="1" data-options="label:'<b>Revisado<b>',labelPosition:'after'" labelWidth="70"></div>
            <div style="margin-bottom:2px;margin-top:5px"><label for="okAsistencia">
                <div style="width:290px;display: inline-block;"><b>Aprobación Comisión Administrativa:</b></div>
              </label><input class="easyui-checkbox" id="okComision" name="okComision" value="1" data-options="label:'<b>Aprobado<b>',labelPosition:'after'" labelWidth="70"></div>
      </div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

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
        singleSelect: false,
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
              title: 'Fecha de Solictud'
            },
            {
              field: 'GradoActual',
              title: 'Grado Acred.'
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
      @if (Auth::user()->permisos == 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="ini2_genDatos();">Generar Circular</a></div>
      @endif
      <div style="float:left;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:400,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'valle');}">
            <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'valle');}">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function ini2_genDatos() {
      var ids = [];
      var rows = $('#dg{{ $_mid }}').datagrid('getSelections');
      if (rows.length > 0) {
        for (var i = 0; i < rows.length; i++) {
          ids.push(rows[i].idTramite);
        }
        $('#dlgini2{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite');
        url = '{{ $_controller }}/get_circular?_token={{ csrf_token() }}&idsTram=' + ids.join(",");
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione los tramites primero</div>'
        });
      }
    }

    function tini2_saveDatos() {
      $('#fmini2{{ $_mid }}').form('submit', {
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
            $('#fmini21{{ $_mid }}').form('clear');
            $('#dlgini2{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

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
  <div id="dlgini2{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgini2-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini2{{ $_mid }}" method="post" novalidate>
      <div class="easyui-panel" title="Datos del tramite de Iniciación" style="width:100%;padding:5px;">
        <div style="margin-bottom:2px"><label for="okcircular">
            <div style="width:270px;display: inline-block;"><b>Generar circular: </b></div>
          </label><input class="easyui-checkbox" id="okcircular" name="okcircular" value="1" data-options="label:'<b>Generar<b>',labelPosition:'after'" labelWidth="60"></div>
      </div>
    </form>
  </div>
  <div id="dlgini2-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="tini2_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini2{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

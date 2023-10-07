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
              field: 'estadotxt',
              title: 'Estado Pago'
            },
            {
              field: 'numeroAum',
              title: 'N.ro H:.A:.'
            },
            {
              field: 'fechaModificacion',
              title: 'Ult. Modificacion'
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
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square correcto" onclick="revDatos_{{ $_mid }}();">Revisar depositos y autorizar</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="fil_{{ $_mid }}" name="fil_{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function revDatos_{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.pagoQR == '1') {
          $('#fmini02{{ $_mid }}').form('clear');
          $('#fmini02{{ $_mid }}').form('load',
            '{{ $_controller }}/get_ceremonia?_token={{ csrf_token() }}&id=' + row.idCeremonia
          ); // load from URL
          $('#dlgauun2{{ $_mid }}').dialog('open').dialog('setTitle',
            'Datos de ceremonia para Aumento de salario');

        } else {

          $('#fmini0{{ $_mid }}').form('clear');
          $('#fmini0{{ $_mid }}').form('load',
            '{{ $_controller }}/get_ceremonia?_token={{ csrf_token() }}&id=' + row.idCeremonia
          ); // load from URL
          $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle',
            'Datos de ceremonia para Aumento de salario');
        }
        url = '{{ $_controller }}/update_ceremonia?_token={{ csrf_token() }}&id=' + row.idCeremonia;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione ceremonia primero</div>'
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
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg +
                '</div>'
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg +
                '</div>'
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
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:480px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
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
      <div class="easyui-panel" title="Revisar derechos" style="width:100%;padding:5px;">
        <div style="margin-bottom:5px"><input class="easyui-textbox" name="depositoGLB" style="width:100%;" data-options="label:'<b>Cod. Deposito GLB y GDR.:</b>',readonly:'true',editable:false" labelWidth="190"></div>
        <div style="margin-bottom:2px"><label for="okDepoGLB">
            <div style="width:270px;display: inline-block;"><b>¿Derechos pagados?:</b></div>
          </label><input class="easyui-checkbox" id="okDepoGLB" name="okDepoGLB" value="1" data-options="label:'<b>Pagado<b>',labelPosition:'after',readonly:'true'" labelWidth="80"></div>
        <div style="margin-bottom:5px"><input class="easyui-datebox" name="fechaCeremonia" style="width:100%" data-options="label:'<b>Fecha de Ceremonia*: </b>',required:true,readonly:'true',editable:false" labelWidth="220"></div>
        <div style="margin-bottom:2px"><label for="okCeremonia">
            <div style="width:270px;display: inline-block;"><b>Autorizar Ceremonia:</b></div>
          </label><input class="easyui-checkbox" id="okCeremonia" name="okCeremonia" value="1" data-options="label:'<b>Autorizado<b>',labelPosition:'after'" labelWidth="80"></div>
      </div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

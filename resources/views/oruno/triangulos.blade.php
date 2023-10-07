@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="
    url: '{{ $_controller }}/get_datos',
    queryParams:{
    _token: tokenModule
    },
    " toolbar="#toolbar{{ $_mid }}" pagination="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="oriente" hidden="true">Oriente</th>
        <th field="nvalle">Valle</th>
        <th field="numero">Numero</th>
        <th field="logia">Nombre Triangulo</th>
        <th field="nrito">Rito</th>
        <th field="ndiatenida">Dia</th>
        <th field="fechaModificacion">Fecha de Modificacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($oris) > 1)
        <select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:500,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLog(rec,'oriente');}">
          <option value="0">Mostrar triangulos del Orientes</option>
          @foreach ($oris as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLog(rec,'oriente');}">
          @foreach ($oris as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:500,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLog(rec,'valle');}">
          <option value="0">Mostrar Triangulos de todos los valles</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosLog(rec,'valle');}">
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square blue" onclick="logia_newItem();">Nuevo Triangulo</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit teal" onclick="logia_editItem();">Editar Triangulo</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square blue2" onclick="logia_destroyItem();" id="borrarbtn{{ $_mid }}">Mover a la papelera</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-trash-alt red" onclick="logia_showPapelera();" id="papelera{{ $_mid }}">Mostrar papelera</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square blue2" onclick="logia_convertir();" id="borrarbtn{{ $_mid }}">Convertir a Logia</a></div>

    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchLog,prompt:'Buscar logia'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchLog();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    var vista{{ $_mid }} = 0;
    function logia_convertir() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang('mess.question')Confirm', '¿Esta seguro de Convertir este Triangulo en Logia?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/convertir', {
              _token: tokenModule,
              id: row.idLogia,
            }, function(result) {
              if (result.errorMsg) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.errorMsg + '</div>'
                });
              } else {
                $.messager.show({
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      }
      else{
        $.messager.show({
            title: 'Error',
              msg: '<div class="messager-icon messager-info"></div>Seleccione dato'
            });
      }
    }

    /*funcnode filtro de datos*/
    function filtrarDatosLog(value, campo) {
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

    function doSearchLog(value) {
      filtrarDatosLog(value, 'palabra');
    }

    function clearSearchLog() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosLog('', 'palabra');
    }

    function logia_newItem() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Triangulo');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function logia_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        fillComboValle(row.idOriente)
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Triangulo');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idLogia;
      }
    }

    function logia_saveItem() {
      $('#fm{{ $_mid }}').form('submit', {
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
              title: '@lang('mess.ok')',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function logia_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang('mess.question')Confirm', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idLogia,
              flag: row.borrado
            }, function(result) {
              if (result.errorMsg) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.errorMsg + '</div>'
                });
              } else {
                $.messager.show({
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      }
    }


    function logia_showPapelera() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      if (vista{{ $_mid }} == '1') {
        $('#papelera{{ $_mid }}').linkbutton({
          iconCls: 'far fa-trash-alt red',
          text: 'Mostrar papelera',
          toggle: 'true'
        });
        $('#borrarbtn{{ $_mid }}').linkbutton({
          iconCls: 'far fa-minus-square blue2',
          text: 'Mover a la papelera',
          toggle: 'true'
        });
        vista{{ $_mid }} = 0;
      } else {
        $('#papelera{{ $_mid }}').linkbutton({
          iconCls: 'fa fa-trash teal',
          text: 'Mostrra activos',
          toggle: 'true'
        });
        $('#borrarbtn{{ $_mid }}').linkbutton({
          iconCls: 'far fa-minus-square orange',
          text: 'Sacar de la Papelera',
          toggle: 'true'
        });
        vista{{ $_mid }} = 1;
      }
      $.post('{{ $_controller }}/show_papelera', {
        _token: tokenModule,
        values: 1
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: 'Error en papelera'
          });
        }
      }, 'json');
    }
  </script>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="idLogia">
      <div style="margin-top:0px"><input name="numero" id="numero" label="Numero del Triangulo:" labelPosition="left" labelWidth="160" class="easyui-textbox" style="width:100%" required="true"></div>
      <div style="margin-top:0px"><input name="logia" id="logia" label="Nombre corto del Triangulo (Sin numero):" labelPosition="top" class="easyui-textbox" style="width:100%" required="true"></div>
      <div style="margin-top:0px"><input name="nombreCompleto" id="nombreCompleto" label="Nombre completo del Triangulo:" labelPosition="top" class="easyui-textbox" style="width:100%" required="true"></div>
      <div style="margin-top:4px">
        <input id="orientecb" name="idOriente" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <input id="vallecb" name="valle" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <select id="rito" class="easyui-combobox" name="rito" style="width:100%" label="Rito :" labelWidth="130" labelPosition="left" editable="false">
          @foreach ($ritos as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="diatenida" class="easyui-combobox" name="diatenida" style="width:100%" label="Dia de Trabajo :" labelWidth="130" labelPosition="left" editable="false">
          <option value="1">Lunes</option>
          <option value="2">Martes</option>
          <option value="3">Miercoles</option>
          <option value="4">Jueves</option>
          <option value="5">Viernes</option>
          <option value="6">Sabado</option>
          <option value="7">Domingo</option>
        </select>
      </div>
    </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="logia_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    $('#orientecb').combobox({
      url: '{{ $_controller }}/get_orientes?_token=' + tokenModule,
      panelHeight: '350',
      required: true,
      valueField: 'idOriente',
      textField: 'oriente',
      method: 'get',
      label: 'Oriente:',
      labelWidth: '70',
      labelPosition: 'left',
      onChange: function(rec) {
        $('#vallecb').combobox('reload', '{{ $_controller }}/get_valles?_token=' + tokenModule + '&oriente=' + rec);
      },
    });

    function fillComboValle(oriente) {
      $('#vallecb').combobox('reload', '{{ $_controller }}/get_valles?_token=' + tokenModule + '&oriente=' + oriente);
    }
    $('#vallecb').combobox({
      url: '',
      panelHeight: '350',
      required: true,
      valueField: 'idValle',
      textField: 'valle',
      method: 'get',
      label: 'Valle:',
      labelWidth: '70',
      labelPosition: 'left',
    });
  </script>
@endsection

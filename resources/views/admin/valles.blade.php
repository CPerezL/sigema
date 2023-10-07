@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options=" url:'{{ $_controller }}/get_datos', queryParams:{ _token: tokenModule },
    " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idValle" hidden="true">ID</th>
        <th field="oriente">Oriente</th>
        <th field="valle">Nombre de Valle</th>
        <th field="tipotxt">Tipo de Valle</th>
        <th field="numero">Nº Logias</th>
        <th field="fechaModificacion">Fecha de Modificacion</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($oris) == 1)
        @foreach ($oris as $key => $ogg)
          <input name="foriente" id="foriente" value="{{ $key }}" type="hidden">
          <a href="#" class="easyui-linkbutton" plain="true"><b>ORIENTE: {{ $ogg }}</b></a>
        @endforeach
      @else
        <select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosVal(rec,'oriente');}">
          <option value="0" selected="selected">Mostrar todos los Orientes &nbsp;&nbsp;&nbsp;</option>
          @foreach ($oris as $key => $ogg)
            <option value="{{ $key }}">{{ $ogg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square blue" onclick="valle_newItem();">Nuevo Valle</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit teal" onclick="valle_editItem();">Editar Valle</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit danger" onclick="quitar_valle();">Borrar Valle</a>
    </div>
    {{-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square blue2"  onclick="valle_destroyItem();" id="borrarbtn{{ $_mid }}">Mover a la papelera</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-trash-alt red"  onclick="valle_showPapelera();" id="papelera{{ $_mid }}">&nbsp;Mostrar papelera</a> --}}
    <div style="float:right;"><input class="easyui-searchbox" style="width:120px" data-options="searcher:doSearchVal,prompt:'Buscar rol'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchVal();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    var vista{{ $_mid }} = 0;
    function quitar_valle() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.numero > 0) {
          $.messager.show({
            title: '@lang('mess.alert')',
            msg: '<div class="messager-icon messager-warning"></div></div>Primero tiene que quitar todas las logias en el Valle y despues podra eliminarlo'
          });
        } else {
          $.messager.confirm('Confirmar', '¿Esta seguro de borrar este valle?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_valle', {
                _token: tokenModule,
                id: row.idValle
              }, function(result) {
                if (!result.success) {
                  $.messager.show({ // show error message
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div>' + result.Msg
                  });
                } else {
                  $.messager.show({
                    title: 'Correcto',
                    msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                  });
                  $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
                }
              }, 'json');
            }
          });
        }
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Valle'
        });
      }
    }
    /*funcnode filtro de datos*/
    function filtrarDatosVal(value, campo) {
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

    function doSearchVal(value) {
      filtrarDatosVal(value, 'palabra');
    }

    function clearSearchVal() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filtrarDatosVal('', 'palabra');
    }

    function filtraOrientes(value) {
      $.post('{{ $_controller }}/filter_oriente?_token={{ csrf_token() }}', {
        oriente: value,
        _token: tokenModule
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function valle_newItem() {
      $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Valle');
      $('#fm{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function valle_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Valle');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idValle;
      }
    }

    function valle_saveItem() {
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
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function valle_destroyItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.borrado == '1')
          var mensaje = 'Esta seguro de sacar de la papelera este Dato?';
        else
          var mensaje = 'Esta seguro de llevar a la papelera este Dato?';
        $.messager.confirm('Confirm', mensaje, function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.idValle,
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

    function valle_showPapelera() {
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
          text: 'Ver Items activos',
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
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <select id="idOriente" class="easyui-combobox" name="idOriente" style="width:100%" label="Oriente Padre:" panelHeight="auto" labelWidth="130" labelPosition="left" required="true">
          @foreach ($oris as $key => $levs)
            <option value="{{ $key }}">{{ $levs }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px"><input name="valle" id="valle" label="Nombre del valle:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:4px">
        <select id="tipo" class="easyui-combobox" name="tipo" style="width:100%" label="Tipo de Valle:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          @foreach ($distritos as $ey => $evs)
            <option value="{{ $ey }}">{{ $evs }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
  <div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="valle_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

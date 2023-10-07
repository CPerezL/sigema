@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url: '{{ $_controller }}/get_datos',
    queryParams:{
    _token: tokenModule
    }
    " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idControl" hidden="true">ID</th>
        <th field="valletxt">Valle</th>
        <th field="taller">Nro. Logia Destino</th>
        <th field="accion">Tipo de tramite</th>
        <th field="modificacion">Nombre nuevo Miembro</th>
        <th field="estadotxt">Estado</th>
        <th field="username">Usuario Cambio</th>
        <th field="fechaModificacion">Fecha</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o correcto" onclick="new_afint();">Nueva Afiliacion/Regularizacion</a></div>
      {{-- <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg" onclick="edit_afint();">Editar Datos</a></div> --}}
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus danger" onclick="del_afint();">Eliminar</a></div>
    @endif
    <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:searchRegularizacion,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchRegularizacion();"></a>
    </div>
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    function filtrarRegularizacion(value, campo) {
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

    function searchRegularizacion(value) {
      filtrarRegularizacion(value, 'palabra');
    }

    function clearSearchRegularizacion() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarRegularizacion('', 'palabra');
    }
  </script>
  <script type="text/javascript">
    function del_afint() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '1') {
          $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_datos', {
                _token: tokenModule,
                id: row.idControl
              }, function(result) {
                if (!result.success) {
                  $.messager.show({ // show error message
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
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
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-warning"></div>Este tramite ya fue aprobado o rechazado'
          });
        }
      }
    }
  </script>
  <!--                                                                                DATOS NUEVOS DE USUARIO                                                                    --->
  <div id="dmiedn{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bmiedn{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmiedn{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" />
      <div style="margin-top:0px"><input name="Paterno" id="Paterno" label="Ap. Paterno:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      <div style="margin-top:0px"><input name="Materno" id="Materno" label="Ap. Materno:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      <div style="margin-top:0px"><input name="Nombres" id="Nombres" label="Nombres:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" required="required"></div>
      <div style="margin-top:4px">
        <select id="jurisdiccion" class="easyui-combobox" name="jurisdiccion" style="width:95%" label="Jurisdiccion:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="2">Regularizacion No Jurisdiccionales</option>
          <option value="3">Afliacion Internacional</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <select id="LogiaActual" class="easyui-combobox" name="LogiaActual" style="width:95%" label="Logia Destino:" labelWidth="130" labelPosition="left" required="required">
          @foreach ($logias as $key => $llog)
            <option value="{{ $key }}">{{ $llog }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:2px">
        <select id="Grado" class="easyui-combobox" name="Grado" style="width:95%" label="Grado Acreditado:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="0">Ninguno</option>
          <option value="1">Aprendiz</option>
          <option value="2">Compañero</option>
          <option value="3">Maestro</option>
          <option value="4">V:.M:. o Ex V:.M:.</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <textarea id="params" name="params" class="easyui-textbox" data-options="multiline:true" label="Datos adicionales:" labelPosition="top" style="width:100%;height:160px"></textarea>
      </div>
    </form>
  </div>
  <div id="bmiedn{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_afint();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dmiedn{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function new_afint() {
      $('#dmiedn{{ $_mid }}').dialog('open').dialog('setTitle', 'Nueva afiliacion/regularizacoin de miembro');
      url = '{{ $_controller }}/save_datos?task=7&_token=' + tokenModule;
    }

    function save_afint() {
      $('#fmiedn{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg
            });
            $('#fmiedn{{ $_mid }}').form('clear');
            $('#dmiedn{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
@endsection

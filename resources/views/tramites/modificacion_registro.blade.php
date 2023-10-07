@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       }
       " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true" nowrap="false">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" hidden="true">ID</th>
        <th field="nlogia">R:.L:.S:. Actual</th>
        <th field="LogiaAfiliada" hidden="true">Afiliado</th>
        <th field="GradoActual">Grado</th>
        <th field="Miembro">Miembro</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Ingreso" hidden="true">Ingreso</th>
        <th field="FechaIniciacion">Iniciacion</th>
        <th field="FechaAumentoSalario">Aumento</th>
        <th field="FechaExaltacion">Exaltacion</th>
        <th field="tipo">Modificacion hecha</th>
        <th field="fechaModificacion">Fecha Modificacion</th>
        <th field="estadotxt">Estado</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
   <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarModificacion(rec,'taller');}">
          <option value="0" selected="selected">Todos los talleres</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
          <option value="1000">Sin Taller Asignado</option>
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarModificacion(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;">
        <a href="javascript:void(0)" id="mb" class="easyui-menubutton" data-options="menu:'#mmoed',iconCls:'fa fa-edit success'" plain="false">Modificar Datos de Miembro</a>
        <div id="mmoed" style="width:220px;">
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_namec();" style="width:198px;text-align: left;">Nombre de Miembro</a></div>
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_grado();" style="width:198px;text-align: left;">Cambiar Grado</a></div>
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_fecha(3, 'Editar Fecha de Iniciacion');" style="width:198px;text-align: left;">Fecha iniciacion</a></div>
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_fecha(4, 'Editar Fecha de Aumento');" style="width:198px;text-align: left;">Fecha Aumento</a></div>
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_fecha(5, 'Editar Fecha de Exaltacion');" style="width:198px;text-align: left;">Fecha Exaltacion</a></div>
          <div data-options="iconCls:'fa fa-pencil correcto'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="edit_miembro();" style="width:198px;text-align: left;">Modificar Tipo de Miembro</a></div>
        </div>
      </div>

    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-times danger" onclick="descartar{{ $_mid }}();">Descartar cambio</a></div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:searchModificacion,prompt:'Buscar nombre'" id="searchbox{{ $_mid }}" value="">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchModificacion();"></a>
      </div>
  </div>
  <script>
    function filtrarModificacion(value, campo) {
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

    function searchModificacion(value) {
      filtrarModificacion(value, 'palabra');
    }

    function clearSearchModificacion() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarModificacion('', 'palabra');
    }


    function descartar{{ $_mid }}() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idControl > 0) {
          $.messager.confirm('Confirm', '¿Esta seguro de descartar el cambio pedido?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/estado', {
                _token: tokenModule,
                id: row.idControl
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
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>No tiene modificaciones pendientes</div>'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <!--                                                                                DATOS PERSONALES                                                                    --->
  <div id="edatos{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bedatos{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fedatos{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" />
      <div style="margin-top:4px"><input name="Paterno" id="Paterno" label="Ap. Paterno:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:4px"><input name="Materno" id="Materno" label="Ap. Materno:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:4px"><input name="Nombres" id="Nombres" label="Nombres:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
    </form>
  </div>
  <div id="bedatos{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_namec();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#edatos{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function edit_namec() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#edatos{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar datos');
        urlform = '{{ $_controller }}/get_form?task=1&id=' + row.id + '&_token=' + tokenModule;
        $('#fedatos{{ $_mid }}').form('load', urlform);
        url = '{{ $_controller }}/update_datos?task=1&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function save_namec() {
      $('#fedatos{{ $_mid }}').form('submit', {
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
            $('#fedatos{{ $_mid }}').form('clear');
            $('#edatos{{ $_mid }}').dialog('close');
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }
      });
    }
  </script>
  <!--                                                                                DATOS MASONICOS                                                                    --->
  <div id="edgrado{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bedgrado{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fedgrado{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" /><input type="hidden" name="control" value="" />
      <div style="margin-top:2px">
        <select id="Grado" class="easyui-combobox" name="Grado" style="width:95%" label="Grado Actual:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="0">Ninguno</option>
          <option value="1">Aprendiz</option>
          <option value="2">Compañero</option>
          <option value="3">Maestro</option>
          <option value="4">V:.M:. o Ex V:.M:.</option>
        </select>
      </div>
    </form>
  </div>
  <div id="bedgrado{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="f_save_grado();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#edgrado{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function edit_grado() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#edgrado{{ $_mid }}').dialog('open').dialog('setTitle', 'Modificar datos');
        urlform = '{{ $_controller }}/get_form?task=2&id=' + row.id + '&_token=' + tokenModule;
        $('#fedgrado{{ $_mid }}').form('load', urlform);
        url = '{{ $_controller }}/update_datos?task=2&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function f_save_grado() {
      $('#fedgrado{{ $_mid }}').form('submit', {
        url: url,
        _token: tokenModule,
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
            $('#fedgrado{{ $_mid }}').form('clear');
            $('#edgrado{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>

  <!--                                                                                DATOS NUEVOS DE USUARIO                                                                    --->
  <div id="edfecha{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bedfecha{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fedfecha{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" />
      <div style="margin-top:2px"><input name="Fecha" id="Fecha" label="Fecha:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:95%"></div>
    </form>
  </div>
  <div id="bedfecha{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="f_save_fecha();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#edfecha{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function edit_fecha(cere, texto) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#edfecha{{ $_mid }}').dialog('open').dialog('setTitle', texto);
        urlform = '{{ $_controller }}/get_form?task=' + cere + '&id=' + row.id + '&_token=' + tokenModule;
        $('#fedfecha{{ $_mid }}').form('load', urlform);
        url = '{{ $_controller }}/update_datos?task=' + cere + '&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function f_save_fecha() {
      $('#fedfecha{{ $_mid }}').form('submit', {
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
            $('#fedfecha{{ $_mid }}').form('clear');
            $('#edfecha{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--                                                                                TIPO DE MIEMBRO                                                                    --->
  <div id="edmiembro{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#edmiembro{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fedmiembro{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" /><input type="hidden" name="control" value="" />
      <div style="margin-top:2px">
        <select id="Miembro" class="easyui-combobox" name="Miembro" style="width:95%" label="Tipo de Miembro:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="Regular">Regular</option>
          <option value="Honorario">Honorario</option>
          <option value="Ad-Vitam">Ad-Vitam</option>
        </select>
      </div>
      <div style="margin-top:4px"><input name="Decreto" id="Decreto" label="Decreto de cambio:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      <div style="margin-top:2px"><input name="Fecha" id="Fecha" label="Fecha de decreto:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:95%"></div>
    </form>
  </div>
  <div id="edmiembro{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="mo_save_miembro();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#edmiembro{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function edit_miembro() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fedmiembro{{ $_mid }}').form('load', row);
        $('#edmiembro{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar Tipo de Miembro');
        url = '{{ $_controller }}/update_datos?task=6&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function mo_save_miembro() {
      $('#fedmiembro{{ $_mid }}').form('submit', {
        _token: tokenModule,
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
            $('#fedmiembro{{ $_mid }}').form('clear');
            $('#edmiembro{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
@endsection

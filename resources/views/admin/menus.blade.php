@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    $(function() {
      var dg = $('#dgmenu{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        rowStyler: function(index, row) {
          if (row.activo == '1') {
            if (row.idMenu == '0') {
              return {
                class: 'titulo'
              };
            } else if (row.idModulo > 0) {
              return {
                class: 'activo'
              };
            } else {
              return {
                class: 'alerta'
              };
            }
          } else {
            if (row.idMenu == '0') {
              return {
                class: 'alerta'
              };
            } else {
              return {
                class: 'inactivo'
              };
            }
          }
        },
        toolbar: '#toolbaruu{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        pageList: [100, 200],
        pageSize: '100',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID'
            },
            {
              field: 'orden',
              title: 'Orden'
            },
            {
              field: 'name',
              title: 'Nombre'
            },
            {
              field: 'modulotxt',
              title: 'Modulo'
            },
            {
              field: 'enlace',
              title: 'Enlace'
            },
            {
              field: 'niveltxt',
              title: 'Nivel Minimo'
            },
            {
              field: 'icono',
              title: 'Icono'
            },
            {
              field: 'modulook',
              title: 'Observaciones'
            },
            {
              field: 'estado',
              title: 'Estado'
            },
            {
              field: 'fechaCreacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
      dg.datagrid('enableFilter');
    });
  </script>
  <table id="dgmenu{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
  <div class="datagrid-toolbar" id="toolbaruu{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <select id="level" name="level" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMenu(rec,'menusel');}">
        <option value="0" selected="selected">Todos los menus padres</option>
        @foreach ($padres as $key => $rrr)
          <option value="{{ $key }}">{{ $rrr }}</option>
        @endforeach
      </select>
    </div>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="addMenu_1();">Nuevo Item</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editMenu_1();">Editar Item</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyMenu_1();">Borrar Item</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-large-clipart" onclick="editRoles_1();">Roles asignados</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-large-chart" onclick="fixMenuOrden_1();">Arreglar orden</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-large-chart" onclick="fixMenuPadres_1();">Arreglar Padres</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-reload" onclick="cambiaEstado();">Cambiar estado</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-reload" onclick="menurevisado();">Marcar revisado</a>
  </div>
  <script type="text/javascript">
    var url;

    function addMenu_1() {
      $('#dlgmenu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.nuevo', ['job' => 'Menu'])');
      $('#fmmenu{{ $_mid }}').form('clear');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function filtrarDatosMenu(value, campo) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          $('#dgmenu{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function editMenu_1() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlgmenu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.editar', ['job' => 'Menu'])');
        $('#fmmenu{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')',
        });
      }
    }

    function saveMenu_1() {
      $('#fmmenu{{ $_mid }}').form('submit', {
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
              title: '@lang('mess.ok')',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg
            });
            $('#fmmenu{{ $_mid }}').form('clear');
            $('#dlgmenu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyMenu_1() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang('mess.question')', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.id
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
                $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <div id="dlgmenu{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlg-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmmenu{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:0px"><input name="nombre" id="name" label="Nombre:" labelPosition="left" labelWidth="120" class="easyui-textbox" style="width:100%" required="true"></div>
      <div style="margin-top:4px">
        <select id="idModulo" class="easyui-combobox" name="idModulo" style="width:100%" label="Modulo asignado:" labelWidth="120" labelPosition="left">
          <option value="0">Ninguno (o no requiere)</option>
          @foreach ($modulos as $key => $opc)
            <option value="{{ $key }}">{{ $opc }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="idMenu" class="easyui-combobox" name="idMenu" style="width:100%" label="Item Padre:" labelWidth="120" labelPosition="left">
          <option value="0">Este es el item principal</option>
          @foreach ($padres as $key => $op)
            <option value="{{ $key }}">{{ $op }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <input name="icono" id="icono" label="Icono del menu(ej: fa -fa-user):" labelPosition="left" labelWidth="200" class="easyui-textbox" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <input name="descripcion" id="descripcion" class="easyui-textbox" label="Descripcion:" labelPosition="top" multiline="true" value="" style="width:100%;height:80px">
      </div>
      <div style="margin-top:4px">
        <input id="orden" name="orden" class="easyui-numberspinner" label="Posicion/Orden:" labelPosition="left" labelWidth="120" data-options="min:1,max:40,editable:false" style="width:100%;">
      </div>
      <div style="margin-top:4px">
        <select id="activo" class="easyui-combobox" name="activo" style="width:100%" label="Estado:" labelWidth="120" labelPosition="left" data-options="panelHeight:'auto',editable:false">
          <option value="0">No habilitado</option>
          <option value="1">Habilitado</option>
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="nivel" class="easyui-combobox" name="nivel" style="width:100%" label="Nivel de usuario minimo:" panelHeight="auto" labelWidth="160" labelPosition="left" required="true">
          @foreach ($levels as $key => $levs)
            <option value="{{ $key }}">{{ $levs }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
  <div id="dlg-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMenu_1();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgmenu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function editRoles_1() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idModulo > 0) {
          url = '{{ $_controller }}/get_roles?id=' + row.idModulo + '&_token={{ csrf_token() }}';
          $('#modlist').datalist('load', url);
          $('#dlgmods{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.editar', ['job' => 'Roles'])');
          url = '{{ $_controller }}/update_roles?_token=' + tokenModule + '&id=' + row.idModulo;
        } else {
          $.messager.show({
            title: 'No tiene modulo asignado',
            msg: '<div class="messager-icon messager-info"></div>Asigne un modulo si es necesario'
          });
        }
      } else {
        $.messager.show({
          title: 'Usuario no seleccionado',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveRoles_1() {
      var ids = [];
      $("form input:checkbox").each(function() {
        if (this.checked === true) {
          ids.push(this.value);
        }
      });
      if (ids.length > 0) {
        lista = ids.join(',');
        $('input[name=roles]').val(lista);
        $('#fmmods{{ $_mid }}').form('submit', {
          url: url,
          onSubmit: function() {
            return $(this).form('validate');
          },
          success: function(result) {
            var result = eval('(' + result + ')');
            if (!result.success) {
              $.messager.show({
                title: '@lang('mess.error')',
                msg: '<div class="messager-icon messager-error"></div>' + result.Msg
              });
            } else {
              $.messager.show({
                title: '@lang('mess.ok')',
                msg: '<div class="messager-icon messager-info"></div>' + result.Msg
              });
              //$('#fmmods{{ $_mid }}').form('clear');
              $('#dlgmods{{ $_mid }}').dialog('close'); // close the dialog
              $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
            }
          }
        });
      } else {
        $.messager.show({
          title: 'Correcto',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function fixMenuOrden_1() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/fixmenu_datos', {
          idm: row.id,
          _token: tokenModule,
        }, function(result) {
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg,
              icon: 'info'
            });
            $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          }
        }, 'json');
      } else {
        $.messager.alert({ // show error message
          title: 'Error',
          msg: '<div class="messager-icon messager-info"></div>@lang('mess.alertdata')',
        });
      }
    }

    function fixMenuPadres_1() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/fixmenu_padres', {
          idm: row.id,
          _token: tokenModule,
        }, function(result) {
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg,
              icon: 'info'
            });
            $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          }
        }, 'json');
      } else {
        $.messager.alert({ // show error message
          title: 'Error',
          msg: '<div class="messager-icon messager-info"></div>@lang('mess.alertdata')',
        });
      }
    }
    function cambiaEstado() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/menu_estado', {
          idm: row.id,
          est: row.activo,
          _token: tokenModule,
        }, function(result) {
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg,
              icon: 'info'
            });
            $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          }
        }, 'json');
      } else {
        $.messager.alert({ // show error message
          title: 'Error',
          msg: '<div class="messager-icon messager-info"></div>@lang('mess.alertdata')',
        });
      }
    }
    function menurevisado() {
      var row = $('#dgmenu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ $_controller }}/menu_revisado', {
          idm: row.idModulo,
          _token: tokenModule,
        }, function(result) {
          if (result.success) {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg,
              icon: 'info'
            });
            $('#dgmenu{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          }
        }, 'json');
      } else {
        $.messager.alert({ // show error message
          title: 'Error',
          msg: '<div class="messager-icon messager-info"></div>@lang('mess.alertdata')',
        });
      }
    }
  </script>
  <div id="dlgmods{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:0px" closed="true" buttons="#dlgmods-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmmods{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="roles">
      <div class="easyui-datalist" id="modlist" style="width:100%;height:250px"
        data-options="
                url: '',
                method: 'get',
                lines:true,
                valueField: 'id',
                textField: 'name',
                checkbox: true,
                selectOnCheck: false,
                checkOnSelect:true,
                onBeforeSelect: function(){return false;},
                ">
      </div>
    </form>
  </div>
  <div id="dlgmods-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveRoles_1();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgmods{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

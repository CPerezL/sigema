@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
         queryParams:{
         _token: tokenModule
         } " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" pageList="[20,40,50,100]"
    pageSize="20" rownumbers="true" fitColumns="true" singleSelect="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idRito" hidden="true">ID</th>
        <th field="nombreCompleto">Nombre Completo</th>
        <th field="rito">Nombre corto</th>
        <th field="nombreTexto">Nombre para reportes</th>
        <th field="textoPlanillas">Nombre para planillas de Asistencia</th>
        <th field="cantidad">Cant. de Oficiales</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (Auth::user()->nivel > 2 && Auth::user()->permisos == 1)
      <div style="float:left;margin-right:10px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit alerta" onclick="rito_gestion();">Gestionar Oficiales de Rito</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus teal" onclick="rito_nuevo();">Adicionar Rito</a></div>
      <div style="float:left;;margin-right:5px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit teal" onclick="rito_editar();">Editar Rito</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit danger" onclick="quitar_rito();">Eliminar Rito</a></div>
    @endif
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    function rito_editar() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg-ritosexe').dialog('open').dialog('setTitle', 'Editar Rito');
        $('#fm-ritosexe').form('load', row);
        url = '{{ $_controller }}/update_rito?id=' + row.idRito + '&_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Seleccione dato',
          msg: '<div class="messager-icon messager-warning"></div>Seleccion Rito'
        });
      }
    }

    function quitar_rito() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.cantidad > 0) {
          $.messager.show({
            title: '@lang('mess.alert')',
            msg: '<div class="messager-icon messager-warning"></div></div>Debe Primero quitar todos los oficiales y despues podra Eliminar'
          });
        } else {
          $.messager.confirm('Confirm', '@lang('mess.questdel')', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_rito', {
                _token: tokenModule,
                id: row.idRito
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
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Rito'
        });
      }
    }

    function rito_nuevo() {
      $('#fm-ritosexe').form('clear');
      $('#dlg-ritosexe').dialog('open').dialog('setTitle', 'Nuevo Rito');
      url = '{{ $_controller }}/save_rito?_token={{ csrf_token() }}';
    }

    function save_rito() {
      $('#fm-ritosexe').form('submit', {
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
            $('#fm-ritosexe').form('clear');
            $('#dlg-ritosexe').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function rito_gestion() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        urll = '{{ $_controller }}/get_oficiales?id=' + row.idRito + '&_token={{ csrf_token() }}';
        $('#dg-ofisver').datagrid('reload', urll);
        $('#dlg-ofisver').dialog('open').dialog('setTitle', 'Cargos del Rito');

      } else {
        $.messager.show({
          title: 'Seleccion dato',
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Rito'
        });
      }
    }
  </script>
  <div id="dlg-ofisver" class="easyui-dialog" style="width:500px;height:350px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true" closable="false">
    <div class="datagrid-toolbar" id="tb-ofisver" style="display:inline-block;width:100%">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square teal" onclick="otro_oficial();">Nuevo Oficial</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit teal" onclick="editar_oficial();">Editar Oficial</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="quitar_oficial();">Quitar Oficial</a></div>
      <div style="float:right;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-close danger" onclick="cerrar_dlg()">Cerrar</a></div>
    </div>
    <table id="dg-ofisver" class="easyui-datagrid" style="width:100%;height:100%;" toolbar="#tb-ofisver" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageSize="20">
      <thead>
        <tr>
          <th data-options="field:'ck',checkbox:true"></th>
          <th field="id" hidden="true">ID</th>
          <th field="rito">Rito</th>
          <th field="oficial">Cargo</th>
          <th field="orden">Orden</th>
        </tr>
      </thead>
    </table>
  </div>
  <div id="dlg-oficialexe" class="easyui-dialog" style="width:400px;height:auto;padding:5px;" closed="true" buttons="#btn-logiaexe" data-options="iconCls:'icon-save',modal:true">
    <form id="fm-oficialexe" method="post" novalidate>
      <div><input name="oficial" id="oficial" label="Cargo o Definicion de Oficial" labelPosition="top" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="orden" id="orden" label="Orden" labelPosition="left" labelWidth="80" class="easyui-numberspinner" style="width:60%;" required="required" data-options="min:1,max:100" value="1"></div>
    </form>
  </div>
  <div id="btn-logiaexe">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_oficial();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="cerrar_dlgof();" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function cerrar_dlg() {
      $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
      $('#dlg-ofisver').dialog('close');
    }

    function cerrar_dlgof() {
      $('#dg-ofisver').datagrid('reload'); // reload the user data
      $('#dlg-oficialexe').dialog('close');
    }

    function otro_oficial() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fm-oficialexe').form('clear');
        $('#dlg-oficialexe').dialog('open').dialog('setTitle', 'Editar Cargo');
        url = '{{ $_controller }}/save_oficial?id=' + row.idRito + '&_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Seleccione dato',
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Rito'
        });
      }
    }

    function editar_oficial() {
      var row = $('#dg-ofisver').datagrid('getSelected');
      if (row) {
        $('#dlg-oficialexe').dialog('open').dialog('setTitle', 'Editar Cargo');
        $('#fm-oficialexe').form('load', row);
        url = '{{ $_controller }}/update_oficial?id=' + row.id + '&_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Seleccione dato',
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Rito'
        });
      }
    }

    function save_oficial() {
      $('#fm-oficialexe').form('submit', {
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
            $('#fm-oficialexe').form('clear');
            $('#dlg-oficialexe').dialog('close'); // close the dialog
            $('#dg-ofisver').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function quitar_oficial() {
      var row = $('#dg-ofisver').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_oficial', {
              _token: tokenModule,
              id: row.id
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
                $('#dg-ofisver').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-warning"></div>Seleccione Dato'
        });
      }
    }
  </script>

  <div id="dlg-ritosexe" class="easyui-dialog" style="width:450px;height:auto;padding:5px" closed="true" buttons="#btn-ritosexe" data-options="iconCls:'icon-save',modal:true">
    <form id="fm-ritosexe" method="post" novalidate>
      <div style="margin-top:0px"><input name="rito" id="rito" label="Nombre corto" labelPosition="top" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="nombreCompleto" id="nombreCompleto" label="Nombre Completo" labelPosition="top" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="nombreTexto" id="nombreTexto" label="Nombre en reportes" labelPosition="top" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px"><input name="textoPlanillas" id="textoPlanillas" label="Nombre para Planillas de Asistencia" labelPosition="top" class="easyui-textbox" style="width:100%" required="required"></div>
    </form>
  </div>
  <div id="btn-ritosexe">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_rito();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-ritosexe').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    /*funciones filtro de datos*/
    function filtrarDatosMie(value, campo) {
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

    function doSearchMie(value) {
      filtrarDatosMie(value, 'palabra');
    }

    function clearSearchMie() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosMie('', 'palabra');
    }
  </script>
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="url: '{{ $_controller }}/get_datos',
         queryParams:{
         _token: tokenModule
         },
         rowStyler: function (index, row) {
         if (row.Estado === 1) {
         return {class:'activo'};
         } else {
         return {class:'inactivo'};
         }
         }
         "
    toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" hidden="true">ID</th>
        <th field="valle">Valler</th>
        <th field="nlogia">R:.L:.S:.</th>
        <th field="estadolog">Tipo</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Miembro">Miembro</th>
        <th field="texto">Estado</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (Auth::user()->nivel > 2 && Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit teal" onclick="log_editar();">Gestionar Logias</a></div>
    @endif
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'valle');}">
          <option value="0">Mostrar miembros de todos los valles</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        @foreach ($valles as $key => $vogg)
          <input name="fvalle" id="fvalle" value="{{ $key }}" type="hidden">
          <a href="#" class="easyui-linkbutton" plain="true"><b>VALLE: {{ $vogg }}</b></a>
        @endforeach
      @endif
    </div>
    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
          <option value="0" selected="selected">Lista de miembros completp</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
      <select id="gradom" name="gradom" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'grado');}">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Estado: </b>
      <select id="estado" name="estado" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'estado');}">
        <option value="0">Inactivo</option>
        <option value="1">Activo</option>
        <option value="2">Fallecido</option>
        <option value="3">Ninguno</option>
        <option value="4" selected="selected">Todos</option>
        <option value="5">Retiro Voluntario</option>
      </select>
    </div>
    <div style="float:left;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchMie,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchMie();"></a>
    </div>
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    function log_editar() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.id > 0) {
          urll = '{{ $_controller }}/get_logias?id=' + row.id + '&_token={{ csrf_token() }}';
          $('#dg-logiasver').datagrid('reload', urll);
          $('#dlg-logiasver').dialog('open').dialog('setTitle', 'Logias Asignadas');
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

    function quitar_logia() {
      var row = $('#dg-logiasver').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
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
                $('#dg-logiasver').datagrid('reload');
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <div id="dlg-logiasver" class="easyui-dialog" style="width:500px;height:350px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true" closable="false">
    <div class="datagrid-toolbar" id="tb-logiasver" style="display:inline-block;width:100%">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square teal" onclick="nueva_logia();">Asigna nueva Logia</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="quitar_logia();">Quitar Logia</a></div>
      <div style="float:right;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-close danger" onclick="javascript:$('#dlg-logiasver').dialog('close');">Cerrar</a></div>
    </div>
    <table id="dg-logiasver" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_logias',queryParams:{_token: tokenModule}" toolbar="#tb-logiasver" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true"
      pageSize="20">
      <thead>
        <tr>
          <th data-options="field:'ck',checkbox:true"></th>
          <th field="id" hidden="true">ID</th>
          <th field="valle">Valle</th>
          <th field="nlogia">R:.L:.S:.</th>
          <th field="estadolog">Tipo</th>
        </tr>
      </thead>
    </table>
  </div>
  <script type="text/javascript">
    function nueva_logia() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dlg-logiasexe').dialog('open').dialog('setTitle', 'Asignar Logia');
        url = '{{ $_controller }}/save_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $.messager.show({
          title: 'Miembro no seleccionado',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function save_logia() {
      $('#fm-logiasexe').form('submit', {
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
            $('#fm-logiasexe').form('clear');
            $('#dlg-logiasexe').dialog('close'); // close the dialog
            $('#dg-logiasver').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function cerrar_dlg() {
      $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
      $('#dlg-logiasexe').dialog('close');
    }
  </script>
  <div id="dlg-logiasexe" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#btn-logiaexe" data-options="iconCls:'icon-save',modal:true">
    <form id="fm-logiasexe" method="post" novalidate>
      <div style="margin-top:4px">
        <select id="nulogia" name="nulogia" style="width:100%" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false" label="<b>Logia para asignar:</b>" labelPosition="top">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <select id="tipo" class="easyui-combobox" name="tipo" style="width:100%" label="Tipo Logia para asignar:" labelWidth="170" labelPosition="left" data-options="panelHeight:'auto',editable:false">
          <option value="0">Logia Actual</option>
          <option value="1">Afiliado a Logia</option>
        </select>
      </div>
    </form>
  </div>
  <div id="btn-logiaexe">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_logia();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="cerrar_dlg();" style="width:90px">Cancelar</a>
  </div>
@endsection

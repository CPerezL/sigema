@extends('layouts.easyuitab')
@section('content')
  <table id="dguu{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="
       url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       rowStyler: function (index, row) {
        if (row.permisos == '1')
        {return {class:'activo'};}
        else if (row.permisos == '0')
        {return {class:'inactivo'};}
        else
        {return {class:'alerta'};}
        }
       "
    toolbar="#toolbaruu{{ $_mid }}" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="username">Usuario</th>
        <th field="name">Nombre</th>
        <th field="email">EMail</th>
        <th field="roltxt">Rol Usuario</th>
        <th field="orientetxt">Or:.</th>
        <th field="valletxt">Valle asignado</th>
        <th field="logiatxt">Logia asignada</th>
        <th field="permisostxt">Configuración</th>
        <th field="fecha">Ultimo acceso</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbaruu{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($oris) == 1)
        @foreach ($oris as $key => $ogg)
          <input name="foriente" id="foriente" value="{{ $key }}" type="hidden">
          <a href="#" class="easyui-linkbutton" plain="true"><b>{{ $ogg }}</b></a>
        @endforeach
      @else
        <select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'oriente');}">
          <option value="0" selected="selected">Mostrar todos los Orientes &nbsp;&nbsp;&nbsp;</option>
          @foreach ($oris as $key => $ogg)
            <option value="{{ $key }}">{{ $ogg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;">
      @if (count($valles) == 1)
        @foreach ($valles as $key => $logg)
          <input name="fvalle" id="fvalle" value="{{ $key }}" type="hidden">
          <a href="#" class="easyui-linkbutton" <b>VALLE: {{ $logg }}</b></a>
        @endforeach
      @else
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'valle');}">
          <option value="0" selected="selected">Mostrar todos los Valles &nbsp;&nbsp;&nbsp;</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;">
      <select id="roles" name="roles" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos(rec,'rol');}">
        <option value="0" selected="selected">Todos los Roles</option>
        @foreach ($roles as $key => $rrr)
          <option value="{{ $key }}">{{ $rrr }}</option>
        @endforeach
      </select>
    </div>
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="newUser();">Nuevo Usuario</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="editUser();">Editar Usuario</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="destroyUser();">Borrar Usuario</a>
    @endif
    @if (Auth::user()->nivel > 4)
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-man" onclick="changeToUser();">Ingresar como</a>
    @endif
    <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchUser,prompt:'Buscar usuario'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchUser();"></a>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    /*funcnode filtro de datos*/
    function filtrarDatos(value, campo) {
      $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
        _token: tokenModule,
        valor: value,
        filtro: campo
      }, function(result) {
        if (result.success) {
          $('#dguu{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }

    function doSearchUser(value) {
      filtrarDatos(value, 'palabra');
    }

    function clearSearchUser() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filtrarDatos('', 'palabra');
    }

    function newUser() {
      $('#fmuu{{ $_mid }}').form('clear');
      $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo usuario');
      url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
    }

    function editUser() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        // $('#orientecb').combobox('reload', '{{ $_controller }}/get_orientes?_token=' + tokenModule);
        fillComboValle(row.oriente)
        fillComboLogia(row.valle);
        $('#fmuu{{ $_mid }}').form('load', row);
        $('#dlguu{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar usuario');
        url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.id;
      } else {
        $('#fmuu{{ $_mid }}').form('clear');
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveUser() {
      roltxt = $('#idRol option:selected').text();
      $('input[name=rol]').val(roltxt);
      $('#fmuu{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (!result.success) {
            $.messager.show({
              title: '@lang('mess.error')',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
            });
          } else {
            $.messager.show({
              title: '@lang('mess.ok')',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
            });
            $('#fmuu{{ $_mid }}').form('clear');
            $('#dlguu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function destroyUser() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.id
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: '@lang('mess.error')',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: '@lang('mess.ok')',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dguu{{ $_mid }}').datagrid('reload'); // reload the user data
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
  <div id="dlguu{{ $_mid }}" class="easyui-dialog" style="width:460px;height:auto;padding:5px 5px" closed="true" buttons="#dlguu-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmuu{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input type="hidden" name="rol">
        <select id="idRol" class="easyui-combobox" name="idRol" style="width:100%" label="Rol del usuario:" panelHeight="auto" labelWidth="110" labelPosition="left" required="true" editable="false">
          @foreach ($roles as $key => $uas)
            <option value="{{ $key }}">{{ $uas }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <input id="orientecb" name="oriente" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <input id="vallecb" name="valle" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <input id="logiacb" name="logia" style="width:100%">
      </div>
      <div style="margin-top:4px">
        <select id="permisos" class="easyui-combobox" name="permisos" style="width:100%" label="Configuración de usuario:" panelHeight="auto" labelWidth="180" labelPosition="left" required="true" editable="false">
          @foreach ($permisos as $key => $uas)
            <option value="{{ $key }}">{{ $uas }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:4px">
        <hr>
      </div>
      <div style="margin-top:4px">
        <input name="name" id="name" label="Nombre del usuario:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="true">
      </div>
      <div style="margin-top:4px">
        <input name="username" id="username" label="Usuario/Username:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="required:true,validType:['text','length[5,20]']">
      </div>
      <div style="margin-top:4px">
        <input name="email" id="email" label="Email:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="prompt:'Email valido',validType:'email'">
      </div>
      <div style="margin-top:4px;">
        <input name="clave" id="clave" label="Clave del usuario:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="prompt:'Conservar la actual'">
      </div>
    </form>
  </div>

  <div id="dlguu-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser();" style="width:90px">Grabar</a>
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlguu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
    @endif
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
        // fillComboValle(rec.idOriente);
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
      onChange: function(rec) {
        fillComboLogia(rec);
      },
    });

    function fillComboLogia(valleid) {
      $('#logiacb').combobox('reload', '{{ $_controller }}/get_logias?_token=' + tokenModule + '&valleid=' + valleid);
    }
    $('#logiacb').combobox({
      url: '',
      panelHeight: '350',
      required: true,
      valueField: 'nlogia',
      textField: 'logian',
      method: 'get',
      label: 'R:.L:.S:. :',
      labelWidth: '60',
      labelPosition: 'left'
    });

    function changeToUser() {
      var row = $('#dguu{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.post('{{ url('/') }}/change_user', {
          _token: '{{ csrf_token() }}',
          iduser: row.id
        }, function(result) {
          if (result.success) {
            window.top.location.href = "{{ url('/') }}";
          } else {
            $.messager.show({ // show error message
              title: '@lang('mess.error')',
              msg: '@lang('mess.error')'
            });
          }
        }, 'json');
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
@endsection

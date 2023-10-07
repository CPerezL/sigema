@extends('layouts.easyuitab')
@section('content')

  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',queryParams:{_token: tokenModule}" toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idOficial" sortable="false" hidden="true">ID</th>
        <th field="idGestion" sortable="false" hidden="true">Gestion</th>
        <th field="cargo"sortable="false">Cargo</th>
        <th field="logia" sortable="false">R:.L:.S:.</th>
        <th field="GradoActual" sortable="false">Grado</th>
        <th field="Paterno" sortable="false">Paterno</th>
        <th field="Materno" sortable="false">Materno</th>
        <th field="Nombres" sortable="false">Nombres</th>
        @if (Auth::user()->nivel > 1)
          <th field="detail" width="120" formatter="formatDetailof">Opciones</th>
        @endif
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (count($valles) > 1)
      <div style="float:left;"><b>&nbsp;&nbsp;VALLE: </b>
        <input id="fcomision" class="easyui-combobox"
          data-options="
      panelHeight:300,
      editable:false,
      valueField: 'id',
      textField: 'text',
      url: '{{ $_controller }}/get_valles?_token={{ csrf_token() }}',
      onSelect: function(rec){
      var url = '{{ $_controller }}/get_gestiones?_token={{ csrf_token() }}&id='+rec.id;
      $('#fgestion').combobox('reload', url);
      }"
          style="width:200px;">
      </div>
      <div style="float:left;"><b>&nbsp;&nbsp;Gestion: </b>
        <input id="fgestion" class="easyui-combobox" data-options="valueField:'id',textField:'text',panelHeight:300,editable:false,onChange: function(rec){filtrarDatos_20(rec,'gestion');}" style="width:200px;">
      </div>
    @else
      <div style="float:left;"><b>&nbsp;&nbsp;
          @foreach ($valles as $key => $vvv)
            <a href="#" class="easyui-linkbutton" plain="true">VALLE: {{ $vvv }}</a>
          @endforeach
        </b></div>
      <div style="float:left;"><b>&nbsp;&nbsp;GESTION: </b>
        <select id="fgestion" name="fgestion" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatos_20(rec,'gestion');}">
          <option value="0" selected>SELECCIONAR GESTION</option>
          @foreach ($_gg as $key => $gg)
            <option value="{{ $key }}">{{ $gg }}</option>
          @endforeach
        </select>
      </div>
    @endif
  </div>
  <script type="text/javascript">
    $(function() {
      var dg2{{ $_mid }} = $('#dg2{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_miembros',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbar2{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: true,
        nowrap: true,
        autoRowHeight: true,
        pageList: [10],
        pageSize: '10',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID',
              hidden:'true'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'Paterno',
              title: 'Paterno'
            },
            {
              field: 'Materno',
              title: 'Materno'
            },
            {
              field: 'Nombres',
              title: 'Nombres'
            },
            {
              field: 'Miembro',
              title: 'Miembro'
            }
          ]
        ]
      });
      dg2{{ $_mid }}.datagrid('enableFilter', [{
        field: 'id',
        type: 'label'
      }, {
        field: 'ck',
        type: 'label'
      }, {
        field: 'Miembro',
        type: 'label'
      }, {
        field: 'GradoActual',
        type: 'label'
      }]);
    });
  </script>
  <!-- Formulario de datos-->
  <div id="dlg2{{ $_mid }}" class="easyui-dialog" style="width:630px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dg2{{ $_mid }}" style="width:auto;height:456px"></table>
    <input name="idcargo" id="idcargo" type="hidden">
    <div class="datagrid-toolbar" id="toolbar2{{ $_mid }}">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg" onclick="oficialv_saveItem();">Asignar cargo</a></div>
    </div>
  </div>
  <script type="text/javascript">
    var url;
    /*funcnode filtro de datos*/
    function filtrarDatos_20(value, campo) {
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

    function formatDetailof(value, row) {
      var erow = row.idCargo;
      if ({{ Auth::user()->permisos }} == '1') {
        return '<a href="javascript:void(0)" onclick="oficialv_asignar(' + erow + ');"><button><i class="fa fa-cube"></i> Asignar cargo</button></a> <a href="javascript:void(0)" onclick="oficialv_quitar(' + row.id + ');"><button><i class="fa fa-cube"></i> Limpiar cargo</button></a>';
      } else {
        return '';
      }
    }

    /*funciones nuevas*/

    function oficialv_asignar(valuer) {
      if (valuer > 0) {
        $('#idcargo').val(valuer);
        $('#dg2{{ $_mid }}').datagrid('removeFilterRule', 'Paterno');
        $('#dg2{{ $_mid }}').datagrid('removeFilterRule', 'Materno');
        $('#dg2{{ $_mid }}').datagrid('removeFilterRule', 'Nombres');
        $('#dg2{{ $_mid }}').datagrid('doFilter');
        $('#dg2{{ $_mid }}').datagrid('reload');
        $('#dlg2{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.alertmiembro")');
      }
    }

    function oficialv_saveItem() {

      $('#dlg2{{ $_mid }}').dialog('close'); // close the dialog
      var row = $('#dg2{{ $_mid }}').datagrid('getSelected');
      var vcar = $("#idcargo").val();
      $.post('{{ $_controller }}/update_cargo', {
        _token: tokenModule,
        id: row.id,
        cargo: vcar
      }, function(result) {
        if (result.success) {
          $.messager.show({ // show error message
            title: 'Correcto',
            msg: '<div class="messager-icon messager-info"></div>' + result.Msg
          });
          $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>' + result.Msg
          });
        }
      }, 'json');
    }
  </script>
  <script>
    function oficialv_quitar(valuer) {
      if (valuer > 0) {
        $.messager.confirm('@lang("mess.question")', '@lang("mess.questdel")?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_cargo', {
              _token: tokenModule,
              ido: valuer
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
    }
  </script>


@endsection

@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url: '{{ $_controller }}/get_datos',
    queryParams:{
    _token: tokenModule
    }
    " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true"
    nowrap="false" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="idControl" hidden="true">ID</th>
        <th field="valletxt">Valle</th>
        <th field="taller">Logia Nro.</th>
        <th field="tipo">Tipo de modificacion/tramite</th>
        <th field="NombreCompleto">Miembro</th>
        <th field="estadotxt">Estado</th>
        <th field="username">Usuario Solicitante</th>
        <th field="fechaModificacion">Fecha</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight:'auto',editable:false,onChange: function(rec){filtrarControl(rec,'valle');}">
          <option value="0">Todos los valles &nbsp;&nbsp;&nbsp;</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight:'auto',editable:false,onChange: function(rec){filtrarControl(rec,'valle');}">
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>

    <div style="float:left;"><b>&nbsp;&nbsp;Tipo de cambio: </b>
      <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight:'auto',editable:false,onChange: function(rec){filtrarControl(rec,'filtro');}">
        <option value="1" selected="selected">Todos los cambios</option>
        <option value="2">Desechados</option>
        <option value="3">Rechazados</option>
        <option value="4">Aprobados</option>
      </select>
    </div>
    {{-- <div class="datagrid-btn-separator"></div> --}}
    @if (Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o success" onclick="doEstado{{ $_mid }}(4);">Aprobar cambio/tramite</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o warning" onclick="doEstado{{ $_mid }}(3);">Rechazar cambio/tramite</a></div>
    @endif
  </div>
  <!-- Funciones javascript -->
  <!--filtros de datos -->
  <script type="text/javascript">
    /*funcnode filtro de datos*/
    function filtrarControl(value, campo) {
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

    function doEstado{{ $_mid }}(value) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (value == '4') {
        var txtm = '¿Esta seguro aprobar este cambio?';
      } else {
        var txtm = '¿Esta seguro rechazar el cambio pedido?';
      }
      if (row) {
        $.messager.confirm('Confirm', txtm, function(r) {
          if (r) {
            $.post('{{ $_controller }}/estado', {
              _token: tokenModule,
              id: row.idControl,
              tipo: value
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
          title: '@lang("mess.alert")',
          msg: '<div class="messager-icon messager-info"></div>@lang("mess.alertdata")'
        });
      }
    }
  </script>
@endsection

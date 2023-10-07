@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos?_token={{ csrf_token() }}',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbar{{ $_mid }}',
        pagination: true,
        fitColumns: false,
        rownumbers: true,
        singleSelect: true,
        nowrap: true,
        pageList: [20, 50, 100, 200],
        pageSize: '20',
        columns: [
          [{
              field: 'ck',
              title: '',
              checkbox: true
            },
            {
              field: 'id',
              title: 'ID',
              hidden: 'true'
            },
            {
              field: 'LogiaActual',
              title: 'Logia'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre completo'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'Miembro',
              title: 'Miembro'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo pago'
            },
            {
              field: 'detail',
              title: 'Opciones',
              formatter: function(value, row) {
                var erow = row.id;
                var frow = row.fechaPago;
                return '<a href="javascript:void(0)" onclick="verObolos(' + erow + ');"><button><i class="fa fa-eye"></i> Ver Obolos</button></a> <a href="javascript:void(0)" onclick="addObolos(' + erow + ');"><button><i class="fa fa-money"></i> Cambiar fecha de Ult. Obolo</button></a>';
              }
            }
          ]
        ],
        queryParams: {
          _token: tokenModule
        },
        rowStyler: function(index, row) {
          if (row.upago == '1') {
            return {
              class: 'activo'
            };
          } else if (row.upago == '2') {
            return {
              class: 'alerta'
            };
          } else {
            return {
              class: 'inactivo'
            };
          }
        }
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
      <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fMecomObolos(rec,'taller');}">
            <option value="0">Seleccionar Logia</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        </div>
        <div class="datagrid-btn-separator"></div>
        <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
          <select id="gradom" name="gradom" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight: 'auto',editable:false,onChange: function(rec){fMecomObolos(rec,'grado');}">
            <option value="0" selected="selected">Todos</option>
            <option value="1">Aprendiz</option>
            <option value="2">Compañero</option>
            <option value="3">Maestro</option>
            <option value="4">V:.M:. o Ex V:.M:.</option>
          </select>
        </div>
        <div class="datagrid-btn-separator"></div>
        <div style="float:left;"><b>&nbsp;&nbsp;Estado: </b>
          <select id="estado" name="estado" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',panelHeight: 'auto',editable:false,onChange: function(rec){fMecomObolos(rec,'estado');}">
            @foreach ($estados as $keye => $eee)
              @if ($eee == 1)
                <option value="{{ $keye }}" selected="selected">{{ $eee }}</option>
              @else
                <option value="{{ $keye }}">{{ $eee }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="datagrid-btn-separator"></div>
        <div style="float:left;"><input class="easyui-searchbox" style="width:140px" data-options="searcher:searchNameObolo,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchName();"></a>
        </div>
      </div>
      <script type="text/javascript">
        function fMecomObolos(value, campo) {
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

        function searchNameObolo(value) {
          fMecomObolos(value, 'palabra');
        }

        function clearSearchName() {
          $('#searchbox{{ $_mid }}').searchbox('clear');
          fMecomObolos('', 'palabra');
        }

        function verObolos(value) {
          $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
            _token: tokenModule,
            valor: value,
            filtro: 'idm'
          }, function(result) {
            if (result.success) {
              $('#dgobolos{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function addObolos(value) {
          $('#fm3{{ $_mid }}').form('clear');
          $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
            _token: tokenModule,
            valor: value,
            filtro: 'idm'
          }, function(result) {
            var urlf = '{{ $_controller }}/get_obolo?_token={{ csrf_token() }}&idm=' + value;
            $('#fm3{{ $_mid }}').form('load', urlf);
            if (result.success) {
              $('#dgobolos{{ $_mid }}').datagrid('reload');
              $('#dlg3{{ $_mid }}').dialog('open').dialog('setTitle', 'Cambiar fecha de ultimo obolo');
            }
          }, 'json');
        }
      </script>
    </div>
    <div data-options="region:'east',collapsed:false" style="width:380px;">
      <table id="dgobolos{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url:'{{ $_controller }}/get_obolos?_token={{ csrf_token() }}',fitColumns:true,singleSelect:true,pagination:false" toolbar="#toolbarobol{{ $_mid }}" rownumbers="true">
        <thead>
          <tr>
            {{-- <th data-options="field:'ck',checkbox:true"></th> --}}
            <th data-options="field:'monto'" hidden="true">Monto</th>
            <th data-options="field:'numeroCuotas'">Pagos</th>
            <th data-options="field:'ultimoPago'">Ant. Pago</th>
            <th data-options="field:'fechaCreacion'">F. registro</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbarobol{{ $_mid }}">
        @if ($level > 4)
          <!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg" onclick="ob_deletePago();" id="darasisfe">Quitar ultimo Pago</a>-->
        @endif
      </div>
      <div id="dlg3{{ $_mid }}" class="easyui-dialog" style="width:370px;height:auto;padding:5px 5px" closed="true" buttons="#dlgo-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fm3{{ $_mid }}" method="post" novalidate>
          <div style="margin-bottom:20px"><b>Ultimo Obolo hasta el mes de: </b> <input id="fechaultimo" name="fechaultimo" type="text" class="easyui-datebox" required="required"><br><b>* solo el mes es tomado en cuenta</b></div>
        </form>
      </div>
      <div id="dlgo-buttons{{ $_mid }}">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveObolos();" style="width:90px">Grabar</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
      </div>
    </div>
    <script type="text/javascript">
      function saveObolos() {
        url = '{{ $_controller }}/set_obolo?_token={{ csrf_token() }}';
        $('#fm3{{ $_mid }}').form('submit', {
          url: url,
          onSubmit: function() {
            return $(this).form('validate');
          },
          success: function(result) {
            var result = eval('(' + result + ')');
            if (result.errorMsg) {
              $.messager.show({
                title: 'Error',
                msg: '<div class="messager-icon messager-error"></div>' + result.Msg
              });
            } else {
              $.messager.show({
                title: 'Success',
                msg: '<div class="messager-icon messager-info"></div>' + result.Msg
              });
              $('#dlg3{{ $_mid }}').dialog('close'); // close the dialog
              $('#dgobolos{{ $_mid }}').datagrid('reload');
              $('#dg{{ $_mid }}').datagrid('reload');
            }
          }
        });
      }

      function ob_deletePago() {
        var row = $('#dgobolos{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $.messager.confirm('Confirm', 'Esta seguro de borrar este pago, solo se borrara si es el ultimo pago registrado y si es el unico la fecha se cambiara a enero-2017?', function(r) {
            $.post('{{ $_controller }}/destroy_last_pago', {
              _token: tokenModule,
              idp: row.id
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
                $('#dgobolos{{ $_mid }}').datagrid('reload');
                $('#dg{{ $_mid }}').datagrid('reload');
              }
            }, 'json');
          });
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Seleccione el ultimo pago'
          });
        }
      }
    </script>
  </div>
@endsection

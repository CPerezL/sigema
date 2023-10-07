@extends('layouts.easyuitab')
@section('content')
  <script>
    $(function() {
      var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_datos',
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
        nowrap: false,
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
              field: 'valle',
              title: 'Valle'
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
              field: 'nBenes',
              title: 'N. Benf.'
            },
            {
              field: 'sumBenes',
              title: '% asig.'
            },
            {
              field: 'ultimoPago',
              title: 'Ult. pago'
            },
            {
              field: 'detail',
              title: 'Opciones',
              formatter: function(value, row) {
                var erow = row.id;
                return '<a href="javascript:void(0)" onclick="verBenes(' + erow + ');"><button><i class="fa fa-eye blue"></i> Benef.</button></a>';
              }
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"></table>
      <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatoscomap(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        </div>
        <div style="float:right;"><input class="easyui-searchbox" style="width:140px" data-options="searcher:doSearchComap,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" onclick="clearSearchComap();"></a>
        </div>
      </div>
    </div>
    <div data-options="region:'east',collapsed:false" style="width:700px;">
      <table id="dgbenes{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url:'{{ $_controller }}/get_beneficiarios?_token={{ csrf_token() }}',fitColumns:true,singleSelect:true,pagination:false,showFooter: true" toolbar="#toolbarco3{{ $_mid }}"
        rownumbers="true">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'nombreBeneficiario'">Beneficiario</th>
            <th data-options="field:'parentesco'">Parentesco</th>
            <th data-options="field:'porcentaje'">%</th>
            <th data-options="field:'fechaModificacion'">Modificacion</th>
          </tr>
        </thead>
      </table>
      <div id="dlg3{{ $_mid }}" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#dlgo-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fm3{{ $_mid }}" method="post" novalidate>
          <div style="margin-top:0px"><input name="nombreBeneficiario" id="nombreBeneficiario" label="Nombre Beneficiario:" labelPosition="top" class="easyui-textbox" style="width:99%" required="required"></div>
          <div style="margin-top:0px"><input name="parentesco" id="parentesco" label="Parentesco:" labelPosition="top" class="easyui-textbox" style="width:99%" required="required"></div>
          <div style="margin-top:10px"><input name="porcentaje" id="porcentaje" class="easyui-numberspinner" style="width:99%" label="Porcentaje asignado:" labelWidth="150" labelPosition="left" required="required" data-options="min:5,max:100,editable:true"></div>
        </form>
      </div>
      <div id="dlgo-buttons{{ $_mid }}">
        @if (Auth::user()->permisos == 1)
          <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveBcomap();" style="width:90px">Grabar</a>
        @endif
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
      </div>
    </div>
    <script type="text/javascript">
      function filtrarDatoscomap(value, campo) {
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

      function doSearchComap(value) {
        filtrarDatoscomap(value, 'palabra');
      }

      function clearSearchComap() {
        $('#searchbox{{ $_mid }}').searchbox('clear');
        filtrarDatoscomap('', 'palabra');
      }

      function saveBcomap() {
        $('#fm3{{ $_mid }}').form('submit', {
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
              $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              $('#fm3{{ $_mid }}').form('clear');
              $('#dlg3{{ $_mid }}').dialog('close'); // close the dialog
              $('#dgbenes{{ $_mid }}').datagrid('reload'); // reload the user data
            }
          }
        });
      }

      function verBenes(value) {
        $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
          _token: tokenModule,
          valor: value,
          filtro: 'idm'
        }, function(result) {
          if (result.success) {
            $('#dgbenes{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }

      function addBenes(value) {
        $('#dlg3{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.nuevo",["job"=>"Beneficiario"])');
        $('#fm3{{ $_mid }}').form('clear');
        url = '{{ $_controller }}/save_datos?_token=' + tokenModule;
      }

      function editBenes() {
        var row = $('#dgbenes{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $('#dlg3{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.editar",["job"=>"Beneficiario"])');
          $('#fm3{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&id=' + row.idComap;
        }
      }

      function deleteBenes() {
        var row = $('#dgbenes{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_datos', {
                _token: tokenModule,
                id: row.idComap
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
                  $('#dgbenes{{ $_mid }}').datagrid('reload'); // reload the user data
                }
              }, 'json');
            }
          });
        }
      }
    </script>
  </div>

  <div class="datagrid-toolbar" id="toolbarco3{{ $_mid }}">
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o fa-lg blue2" onclick="addBenes();">Adicionar beneficiario</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="editBenes();">Editar beneficiario</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus fa-lg danger" onclick="deleteBenes();">Eliminar beneficiario</a></div>
  </div>
@endsection

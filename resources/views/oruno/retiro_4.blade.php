@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
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
        fitColumns: true,
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
              field: 'estadotxt',
              title: 'Estado de tramite'
            },
            {
              field: 'valle',
              title: 'Valle'
            },
            {
              field: 'nLogia',
              title: 'Logia Actual'
            },
            {
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Miembro'
            },
            {
              field: 'miembro',
              title: 'Estado'
            },
            {
              field: 'fechaCreacion',
              title: 'F. de Solicitud'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
    });

    function fDatosAfilia(value, campo) {
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
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      @if (Auth::user()->permisos == 1)
        @if (Auth::user()->nivel > 2)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o warning" onclick="afi_aprobar();">Revisar Pago de Tramite</a></div>
        @endif
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDatosAfilia(rec,'taller');}">
              <option value="0">Todas la Logias</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fDatosAfilia(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
      @endif
    </div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-level-up fa-lg correcto" onclick="actualizaDatos();">Actualizar Datos de Retiro</a></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="gen_certificado();">Imprimir certificado</a></div>
  </div>
  </div>
  <!-- aprobacion -->
  <div id="dlg4{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons4{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm4{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <div style="margin-top:0px"><input name="nLogia" label="R:.L:.S:.:" labelPosition="left" labelWidth="100" class="easyui-textbox" style="width:100%" readonly="true"></div>
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="100" class="easyui-textbox" style="width:100%" readonly="true"></div>
      <div style="margin-top:4px">Deposito: <a href="javascript:void(0)" id="abrir_dep" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento</a></div>
      <div style="margin:5px"><label for="okEnviar">
          <div style="width:220px;display: inline-block;"><b>Deposito revisado: </b></div>
        </label><input class="easyui-checkbox" name="okEnviar" value="1" data-options="label:'<b>Si<b>',labelPosition:'after'" labelWidth="60"></div>
    </form>
  </div>
  <div id="dlgl-buttons4{{ $_mid }}">
    <a href="javascript:void(0)" id="btn_formtre" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="afigdr_listo();" style="width:150px">Aprobar tramite</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg4{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function afi_aprobar() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '4') {
          $('#dlg4{{ $_mid }}').dialog('open').dialog('setTitle', 'Revisar y Aprobar Afiliacion');
          $('#fm4{{ $_mid }}').form('clear');
          var newUrl = '{{ $_folder }}/media/tramites/' + row.archivo2;
          $('#abrir_dep').attr("href", newUrl);
          $('#fm4{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/cambia_datos?task=4&_token=' + tokenModule;
        } else {
          $.messager.show({
            title: 'Alert',
            msg: '<div class="messager-icon messager-error"></div>Ya esta aprobado o no se puede modificar'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }

    function afigdr_listo() {
      $('#fm4{{ $_mid }}').form('submit', {
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
            $('#fm4{{ $_mid }}').form('clear');
            $('#dlg4{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function gen_certificado() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == 6) {
          window.open("{{ $_controller }}/gen_certificado?id=" + row.id);
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>El tramite no esta aprobado'
          });
        }

      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }

    function actualizaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estadomie == 1 && row.estado == 6) {
          $.messager.confirm('Confirm', '¿Esta seguro Actualizar Estado a Retirado?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/update_data', {
                _token: '{{ csrf_token() }}',
                id: row.id
              }, function(result) {
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
                if (result.success) {
                  $.messager.show({
                    title: 'Actualizado',
                    msg: '<div class="messager-icon messager-info"></div>' + result.Msg,
                  });
                } else {
                  $.messager.show({
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div>' + result.Msg,
                  });
                }
              }, 'json');

            }
          });
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>No esta aprobado o no hay nada que actualizar'
          });

        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione item primero'
        });
      }
    }
  </script>
@endsection

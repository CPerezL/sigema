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
              field: 'casotxt',
              title: 'Tipo de tramite'
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
              field: 'nLogiaNueva',
              title: 'Logia a Afiliar'
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

    function dummy(iden) {
      window.open("{{ $_controller }}/gen_certificado?id=" + iden);
    }

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
      @if (Auth::user()->permisos == 1 && Auth::user()->nivel > 2)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o warning" onclick="afi_aprobar();">Revisar Tramite</a></div>
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
      </div>
    </div>
  </div>
  <!--   formulario de derechos registro  -->

  <script type="text/javascript">
    function upPagorei() {
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
              msg: '<div class="messager-icon messager-error"></div>' + result.Msg
            });
          } else {
            $.messager.show({
              title: 'Correcto',
              msg: '<div class="messager-icon messager-info"></div>' + result.Msg
            });
            $('#fm3{{ $_mid }}').form('clear');
            $('#dl3{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>

  <script type="text/javascript">
    function no_afiliaItem() {
      url = '{{ $_controller }}/cambia_datos?task=5&_token=' + tokenModule;
      $('#fm2{{ $_mid }}').form('submit', {
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
            $('#fm2{{ $_mid }}').form('clear');
            $('#dlg2{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!-- aprobacion -->
  <div id="dlg4{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons4{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm4{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:4px">Documento de : <a href="javascript:void(0)" id="abrir_dep" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento</a></div>
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
        if (row.estado == '6') {
          $('#dlg2{{ $_mid }}').dialog('open').dialog('setTitle', 'Aprobar tramite de reincorporacion');
          $('#faprologia').textbox('readonly', true);
          $('#actaprologia').textbox('readonly', true);
          $('#fm2{{ $_mid }}').form('clear');
          $('#fm2{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/cambia_datos?task=2&_token=' + tokenModule;
        } else if (row.estado == '2') {
          $('#dlg4{{ $_mid }}').dialog('open').dialog('setTitle', 'Revisar y Aprobar Afiliacion');
          $('#faprologia').textbox('readonly', true);
          $('#actaprologia').textbox('readonly', true);
          $('#fm4{{ $_mid }}').form('clear');
          var newUrl = '{{ $_folder }}/media/tramites/' + row.archivo;
          $('#abrir_dep').attr("href", newUrl);
          var newUrl2 = '{{ $_folder }}/media/tramites/' + row.archivo2;
          $('#abrir_dep2').attr("href", newUrl2);
          var newUrl3 = '{{ $_folder }}/media/tramites/' + row.archivo3;
          $('#abrir_dep3').attr("href", newUrl3);
          $('#fm4{{ $_mid }}').form('load', row);
          $('#mesreincorp').datebox('setValue', '{{ $mesactual }}');
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

    function afigdr_continuar() {
      $('#fm2{{ $_mid }}').form('submit', {
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
            $('#fm2{{ $_mid }}').form('clear');
            $('#dlg2{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
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
  </script>
@endsection

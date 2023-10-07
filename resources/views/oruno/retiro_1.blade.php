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
              title: 'Valle',
              hidden: true
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
              field: 'fechaSolicitud',
              title: 'F. de Solicitud'
            },
            {
              field: 'fechaAprobacionLogia',
              title: 'F. Aprobacion Logia'
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
      @if (Auth::user()->permisos == 1)
        @if ((Auth::user()->nivel == 1 && Auth::user()->permisos == 1) || Auth::user()->nivel > 4)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square correcto" onclick="lafi_sel();">Nuevo Tramite</a></div>
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus danger" onclick="tafilog_delItem();">Eliminar Tramite</a></div>
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o excel" onclick="reti_completar();">Registrar Datos de Retiro</a></div>
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
  </div>
  </div>
  <!--   formulario de derechos registro  -->

  <div id="regder_but{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="upDatosReti();" style="width:120px">Enviar datos</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dl3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function reti_completar(revisar) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '0' || row.estado == '1'|| row.estado == '2') {
          $('#fm{{ $_mid }}').form('load', row);
          $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Correcion de datos');
          url = '{{ $_controller }}/update_tramite?_token=' + tokenModule + '&id=' + row.id;
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>No se puede modificar ya fue enviado'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }

    function upDatosReti() {
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
  <div id="dlg_selm{{ $_mid }}" class="easyui-dialog" style="width:750px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dg_reisel" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="tba_selm">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg correcto" onclick="afi_asignar();">Selecccionar Miembro</a></div>
    </div>
  </div>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <input name="LogiaActual" id="LogiaActual" type="hidden">
      <input name="Grado" type="hidden">
      <input name="ultimoPagoDate" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="GradoActual" label="Grado:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-bottom:5px;margin-top:10px"><input class="easyui-datebox" name="fechaSolicitud" style="width:100%" data-options="label:'<b>Fecha de Solicitud*:</b>'" labelWidth="230"></div>
      <div style="margin-bottom:10px;margin-top:15px">
        <label><b>Carta de solicitud de retito</b></label>
        <input class="easyui-filebox" id="fileup" name="fileup" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.jfif" value="" data-options="prompt:'Carta de solicitud'">
      </div>
      <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaAprobacionLogia" style="width:100%" data-options="label:'<b>No. Acta - de Aprobacion en Logia*:</b>',required:true" labelWidth="290"></div>
      <div style="margin-bottom:15px"><input class="easyui-datebox" name="fechaAprobacionLogia" style="width:100%" data-options="label:'<b>Fecha de Aprobacion Logia*:</b>'" labelWidth="230"></div>
  </div>
  </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="afina_saveItem();" style="width:140px">Registrar tramite</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    $(function() {
      var dg_reisel{{ $_mid }} = $('#dg_reisel').datagrid({
        url: null,
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#tba_selm',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: true,
        nowrap: false,
        autoRowHeight: true,
        pageList: [20],
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
              field: 'GradoActual',
              title: 'Grado'
            },
            {
              field: 'NombreCompleto',
              title: 'Nombre'
            },
            {
              field: 'Miembro',
              title: 'Tipo'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo Obolo'
            },
            {
              field: 'talltxt',
              title: 'Log Actual'
            },
            {
              field: 'obstxt',
              title: 'Observacion'
            }
          ]
        ]
      });
      dg_reisel{{ $_mid }}.datagrid('enableFilter', [{
        field: 'id',
        type: 'label'
      }, {
        field: 'ultimoPago',
        type: 'label'
      }, {
        field: 'Miembro',
        type: 'label'
      }, {
        field: 'ck',
        type: 'label'
      }, {
        field: 'GradoActual',
        type: 'label'
      }, {
        field: 'tipotxt',
        type: 'label'
      }, {
        field: 'obstxt',
        type: 'label'
      }, {
        field: 'talltxt',
        type: 'label'
      }]);
    });

    function lafi_sel() {
      logg = $("#filtrot0").val();
      if (logg > 0) {
        $('#dg_reisel').datagrid('options').url = '{{ $_controller }}/get_miembros?id=' + logg;
        $('#dg_reisel').datagrid('reload');
        $('#dlg_selm{{ $_mid }}').dialog('open').dialog('setTitle', 'Seleccionar Miembro Regular');
        url = '{{ $_controller }}/update_datos?task=1&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione logia</div>'
        });
      }
    }

    function afi_asignar() {
      var row = $('#dg_reisel').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Tramite');
        $('#fm{{ $_mid }}').form('clear');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/save_tramite?_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione miembro</div>'
        });
      }
    }

    function afina_saveItem() {
      $('#fm{{ $_mid }}').form('submit', {
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
            $('#fm{{ $_mid }}').form('clear');
            $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
            $('#dlg_selm{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function tafilog_delItem() {
      var rowm = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (rowm) {
        if (rowm.estado < 3) {
          $.messager.confirm('Confirm', 'Esta seguro eliminar el tramite?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/unset_tramite', {
                id: rowm.id,
                _token: tokenModule
              }, function(result) {
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
                }
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              }, 'json');
            }
          });
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>No se puede modificar'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }
  </script>
@endsection

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
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}"  style="display:inline-block">
      @if (Auth::user()->permisos == 1)
          @if ((Auth::user()->nivel == 1 && Auth::user()->permisos == 1) || Auth::user()->nivel > 4)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square correcto" onclick="lafi_sel();">Nuevo Tramite</a></div>
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus danger" onclick="tafilog_delItem();">Eliminar Tramite</a></div>
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o excel" onclick="afi_regpago();">Registrar Datos de Afiliacion</a></div>
        @endif
        @if (Auth::user()->nivel > 2)
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
      @endif
    </div>
  </div>
  </div>
  <!--   formulario de derechos registro  -->
  <div id="dl3{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#regder_but{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm3{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Miembro para afiliar" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="NombreCompleto" id="numero" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      </div>
      <div style="margin-top:2px">
        <select id="Miembro" class="easyui-combobox" name="tipo" style="width:95%" label="Tipo de Afiliacion:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="1">Afiliacion y cambio de Logia actual</option>
          <option value="2">Afiliacion como logia Actual y la otra logia como afiliada</option>
          <option value="3">Afiliacion como segunda Logia</option>
        </select>
      </div>
      <div class="easyui-panel" title="Datos y Pagos de derechos de afiliacion (archivo no mayor a 2 megas)" style="width:100%;padding:5px;">
        <div style="margin-bottom:15px">
          <label><b>Carta de aprobacion o comunicacion de Logia Madre</b></label>
          <input class="easyui-filebox" id="fileup" name="fileup" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif,.jfif" value="" data-options="prompt:'Carta de consentimiento o aviso'">
        </div>
        <div style="margin-bottom:15px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*:</b>'" labelWidth="210"></div>
        {{-- <div style="margin-bottom:15px">
          <label><b>Documento de deposito o transf. bancaria a cuenta GLB</b></label>
          <input class="easyui-filebox" id="fileup2" name="fileup2" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif,.jfif" value="" data-options="prompt:'Documento de deposito GLB'">
        </div>
        <div style="margin-bottom:15px">
          <label><b>Documento de deposito o transf. bancaria a cuenta GDR</b></label>
          <input class="easyui-filebox" id="fileup3" name="fileup3" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif,.jfif" value="" data-options="prompt:'Documento de deposito GLD/GDR'">
        </div> --}}
      </div>
      <div style="margin:5px"><label for="okEnviar">
          <div style="width:220px;display: inline-block;"><b>Enviar tramite para aprobacion: </b></div>
        </label><input class="easyui-checkbox" name="okEnviar" value="1" data-options="label:'<b>Si<b>',labelPosition:'after'" labelWidth="60"></div>
    </form>
  </div>
  <div id="regder_but{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="upPagorei();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dl3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function afi_regpago(revisar) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '1') {
          $('#fm3{{ $_mid }}').form('load', row);
          $('#dl3{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de tramite');
          url = '{{ $_controller }}/registra_pago?_token=' + tokenModule + '&id=' + row.id;
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
  <div id="dlg_selm{{ $_mid }}" class="easyui-dialog" style="width:750px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dg_reisel" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="tba_selm">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg correcto" onclick="afi_asignar();">Selecccionar Miembro</a></div>
    </div>
  </div>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <input name="LogiaActual" id="LogiaActual" type="hidden">
      <input name="Grado" type="hidden">
      <input name="ultimoPagoDate" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Nombre de Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="GradoActual" label="Grado:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px">
        <select id="Miembro" class="easyui-combobox" name="tipo" style="width:95%" label="Tipo de Afiliacion:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
          <option value="1">Afiliacion y cambio de Logia actual</option>
          <option value="2">Afiliacion como logia Actual y la otra logia como afiliada</option>
          <option value="3">Afiliacion como segunda Logia</option>
        </select>
      </div>
      <b>a) Para Maestros necesitara:</b><br>
      <ol>
        <li>Carta de comunicacion a Logia madre o Logia Actual</li>
        <li>Documento de deposito a cuenta de la GLD/GDR</li>
        <li>Documento de deposito a cuenta de la GLB</li>
      </ol>
      <b>b) Para Compañero y Aprendices necesitara:</b><br>
      <ol>
        <li>Carta de aprobacion de Logia Madre para afiliarce a otra Logia</li>
        <li>Documento de deposito a cuenta de la GLD/GDR</li>
        <li>Documento de deposito a cuenta de la GLB</li>
      </ol>
  </div>
  </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="afina_saveItem();" style="width:140px">Registrar tramite</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
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

    function tafilog_editItem() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '1') {
          $("#btn_formtre").linkbutton({
            text: 'Actualizar'
          });
          $('#dlg2{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite');
          $('#fm2{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/cambia_tramite?_token=' + tokenModule + '&id=' + row.id;
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
  </script>
  <!-- aprobacion -->
  <div id="dlg4{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons4{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm4{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'Fecha de deposito:'" labelWidth="200" readonly="true"></div>
      <div style="margin-top:4px">Documento de : <a href="javascript:void(0)" id="abrir_dep" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento</a></div>
      <div style="margin-top:4px">Deposito GLB: <a href="javascript:void(0)" id="abrir_dep2" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito GLB</a></div>
      <div style="margin-top:4px">Deposito GDR: <a href="javascript:void(0)" id="abrir_dep3" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito GLB</a></div>
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

    function tafilog_delItem() {
      var rowm = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (rowm) {
        if(rowm.estado<2)
        {
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
        }
        else
        {
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

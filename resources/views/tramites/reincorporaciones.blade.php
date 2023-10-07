@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}"  style="display:inline-block">
    @if (Auth::user()->permisos == 1)
      @if (Auth::user()->permisos == 1 || Auth::user()->nivel > 4)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square correcto" onclick="lrei_sel();">Nuevo Tramite</a></div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit alerta" onclick="trrelog_editItem();">Editar Tramite</a></div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus danger" onclick="trrelog_delItem();">Eliminar Tramite</a></div>
      @endif
      @if ((Auth::user()->nivel == 1 && Auth::user()->permisos == 2) || Auth::user()->nivel > 4)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o fa-lg correcto" onclick="rei_regpago();">Registrar Pago</a></div>
      @endif
      @if (Auth::user()->nivel > 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square-o fa-lg activo" onclick="rei_aprobar();">Revisar Tramite</a></div>
      @endif
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarObolosMiembro(rec,'taller');}">
            <option value="0">Seleccionar Logia</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}"> {{ $logg }} </option>
            @endforeach
          </select>
        @else
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarObolosMiembro(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected"> {{ $logg }} </option>
            @endforeach
          </select>
        @endif
      </div>
    @endif
  </div>

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
              title: 'Taller'
            },
            {
              field: 'idLogia',
              title: 'Nro'
            },
            {
              field: 'fechaCreacion',
              title: 'F. Insinuacion'
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
              field: 'fechaModificacion',
              title: 'Modificacion'
            },
            {
              field: 'detail',
              title: 'Observaciones',
              formatter: function(value, row) {
                if (row.estado == '4' || row.pagoQR>0) {
                  return '<a href="javascript:void(0)" onclick="verArchivo(' + row.id + ',0);"><button><i class="fa fa-check-square"></i> Certificado de pago.</button></a>';
                } else {
                  if (row.tipo == '1')
                    return 'Necesita aprobacion simple GDR/GLD';
                  else if (row.tipo == '2')
                    return 'Necesita aprobacion del Consejo Distrital';
                  else if (row.tipo == '3')
                    return 'Necesita aprobacion de la GLB primero';
                  else if (row.tipo == '4')
                    return 'Necesita aprobacion de la GLB y del Consejo Distrital';
                  else
                    return 'En revision';
                }
              },
              width: 130
            }
          ]
        ]
      });
    });

    function verArchivo(iden) {
      window.open("{{ $_controller }}/gen_certificado?id=" + iden);
    }
  </script>

  <script>
    function filtrarObolosMiembro(value, campo) {
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

    function searchObolosMiembro(value) {
      filtrarObolosMiembro(value, 'palabra');
    }

    function clearsearchObolosMiembro() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarObolosMiembro('', 'palabra');
    }
  </script>
  <!--   formulario de derechos registro  -->
  <div id="dl3{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#regder_but{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm3{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <div class="easyui-panel" title="Miembro para regularizar" style="width:100%;padding:5px;">
        <div style="margin-top:0px"><input name="NombreCompleto" id="numero" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      </div>
      <div class="easyui-panel" title="Pago de derechos de reincorporacion" style="width:100%;padding:5px;">
        <div style="margin-bottom:15px"><input class="easyui-datebox" name="fDeposito" style="width:100%" data-options="label:'<b>Fecha de Pago de Derechos*: </b>'" labelWidth="210"></div>
        <div style="margin-bottom:15px">
          <label><b>Documento de deposito o transf. bancaria al COMAP (504 Bs.)</b></label>
          <input class="easyui-filebox" id="fileup" name="fileup" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Documento de deposito'">
        </div>
        <div style="margin-bottom:15px">
          <label><b>Documento de deposito o transf. bancaria a la GDR/GLD (Consultar en la GDR / GLD)</b></label>
          <input class="easyui-filebox" id="fileup1" name="fileup1" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Documento de deposito'">
        </div>
        <div style="margin-bottom:10px">
          <label><b>Documento de deposito o transf. bancaria a la GLB (324 Bs.)</b></label>
          <input class="easyui-filebox" id="fileup2" name="fileup2" style="width:100%" buttonText="Archivo PDF/Imagen" accept=".pdf,.png,.jpeg,.jpg,.gif" value="" required="required" data-options="prompt:'Documento de deposito'">
        </div>
      </div>
    </form>
  </div>
  <div id="regder_but{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="upPagorei();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dl3{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function rei_regpago(revisar) {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '2') {
          $('#fm3{{ $_mid }}').form('clear');
          $('#fm3{{ $_mid }}').form('load', row);
          $('#dl3{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de tramite');
          url = '{{ $_controller }}/registra_pago?_token=' + tokenModule + '&id=' + row.id;
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>@lang('mess.nomodify')'
          });
        }

      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
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
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg" onclick="rei_asignar();">Seleccionar postulante</a></div>
    </div>
  </div>
  <div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <input name="LogiaActual" id="LogiaActual" type="hidden">
      <input name="Grado" id="Grado" type="hidden">
      <input name="caso" id="caso" type="hidden">
      <input name="ultimoPagoDate" id="Grado" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px"><input name="casotxt" label="Tipo de tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-datebox" name="fechaAprobacionLogia" style="width:100%" data-options="label:'Fecha de Aprobacion en Logia*:',required:true" labelWidth="220"></div>
      <div style="margin-top:2px"><input class="easyui-textbox" name="actaAprobacionLogia" style="width:100%" data-options="label:'No. Acta de Aprobacion en Logia*:',required:true" labelWidth="220"></div>
      <div style="margin-top:2px">
        <label class="textbox-label textbox-label-top" style="text-align: left;"><b>Observaciones</b></label>
        <input name="observaciones" class="easyui-textbox" multiline="true" onRead="onRead" style="width:100%; height:120px" />
      </div>
    </form>
  </div>
  <div id="dlgl-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="reina_saveItem();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function no_cambiaItem() {
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

    function trrelog_editItem() {
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
            msg: '<div class="messager-icon messager-error"></div>@lang('mess.nomodify')'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
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
        nowrap: true,
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
              field: 'ultimoPago',
              title: 'Ult. Obolo'
            },
            {
              field: 'LogiaActual',
              title: 'Logia'
            },
            {
              field: 'tipotxt',
              title: 'En sueño'
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
        field: 'ck',
        type: 'label'
      }, {
        field: 'LogiaActual',
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
      }]);
    });

    function lrei_sel() {

      logg = $("#filtrot0").val();
      if (logg > 0) {
        $('#dg_reisel').datagrid('options').url = '{{ $_controller }}/get_miembros?id=' + logg;
        $('#dg_reisel').datagrid('reload');
        $('#dlg_selm{{ $_mid }}').dialog('open').dialog('setTitle', 'Seleccionar Miembro en Sueño');
        url = '{{ $_controller }}/update_datos?task=1&_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function rei_asignar() {
      var row = $('#dg_reisel').datagrid('getSelected');
      if (row) {
        $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Tramite');
        $('#fm{{ $_mid }}').form('clear');
        $('#fm{{ $_mid }}').form('load', row);
        url = '{{ $_controller }}/save_tramite?_token=' + tokenModule;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function reina_saveItem() {
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
  <!-- edicion y aporbqaion -->
  <div id="dlg2{{ $_mid }}" class="easyui-dialog" style="width:480px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons2{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm2{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <input name="tipo" type="hidden">
      <input name="especial" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-datebox" name="fAprobacionLogia" id="faprologia" style="width:100%" data-options="label:'Fecha de Aprobacion en Logia*:',required:true" labelWidth="200"></div>
      <div style="margin-top:2px"><input class="easyui-textbox" name="actaAprobacionLogia" id="actaprologia" style="width:100%" data-options="label:'No. Acta de Aprobacion en Logia*:',required:true" labelWidth="200"></div>

      <div style="margin-top:2px">
        <label class="textbox-label textbox-label-top" style="text-align: left;"><b>Comentarios</b></label>
        <input name="observaciones" class="easyui-textbox" multiline="true" onRead="onRead" style="width:100%; height:120px" />
      </div>
    </form>
  </div>
  <div id="dlgl-buttons2{{ $_mid }}">
    @if (Auth::user()->nivel > 2)
      <a href="javascript:void(0)" id="btn_formrecha" class="easyui-linkbutton c6" iconCls="icon-clear" onclick="no_cambiaItem();" style="width:150px">Rechazar tramite</a>
    @endif
    <a href="javascript:void(0)" id="btn_formtre" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="reigdr_continuar();" style="width:150px">Aprobar tramite</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg2{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <!-- aprobacion -->
  <div id="dlg4{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgl-buttons4{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fm4{{ $_mid }}" method="post" novalidate>
      <input name="id" id="id" type="hidden">
      <div style="margin-top:0px"><input name="NombreCompleto" label="Miembro:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:0px"><input name="casotxt" label="Tramite:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-datebox" name="fAprobacionLogia" style="width:100%" data-options="label:'Fecha de Aprobacion en Logia:'" labelWidth="200" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-textbox" name="actaAprobacionLogia" style="width:100%" data-options="label:'No. Acta de Aprobacion:'" labelWidth="200" readonly="true"></div>
      <div style="margin-top:2px"><input class="easyui-datebox" name="fDeposito" style="width:95%" data-options="label:'Fecha de deposito:'" labelWidth="200" readonly="true"></div>
      <div style="margin-top:0px">Deposito: <a href="javascript:void(0)" id="abrir_dep" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para COMAP</a></div>
      <div style="margin-top:0px">Deposito: <a href="javascript:void(0)" id="abrir_dep2" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para GLD/GDR</a></div>
      <div style="margin-top:0px">Deposito: <a href="javascript:void(0)" id="abrir_dep3" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:300px;margin-left:10px;">Ver documento de deposito para GLB</a></div>
      <div style="margin-top:2px"><input class="easyui-datebox" id='mesreincorp' name="mesreincorp" style="width:100%" labelPosition="top" label="Mes de reincorporacion:(solo se usa el mes)"></div>
      <div style="margin-top:2px">
        <label class="textbox-label textbox-label-top" style="text-align: left;"><b>Comentarios</b></label>
        <input name="observaciones" class="easyui-textbox" multiline="true" onRead="onRead" style="width:100%; height:120px" readonly="true" />
      </div>
    </form>
  </div>
  <div id="dlgl-buttons4{{ $_mid }}">
    <a href="javascript:void(0)" id="btn_formtre" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="reigdr_listo();" style="width:150px">Aprobar tramite</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg4{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function rei_aprobar() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '1') {
          $('#dlg2{{ $_mid }}').dialog('open').dialog('setTitle', 'Aprobar tramite de reincorporacion');
          $('#faprologia').textbox('readonly', true);
          $('#actaprologia').textbox('readonly', true);
          $('#fm2{{ $_mid }}').form('clear');
          $('#fm2{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/cambia_datos?task=2&_token=' + tokenModule;
        } else if (row.estado == '3') {
          $('#dlg4{{ $_mid }}').dialog('open').dialog('setTitle', 'Revisar y Aprobar pago de derechos');
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
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Ya esta aprobado o no se puede modificar'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function reigdr_continuar() {
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

    function reigdr_listo() {
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

    function trrelog_delItem() {
      $.messager.confirm('Confirm', 'Esta seguro eliminar el tramite?', function(r) {
        if (r) {
          if (row.estado == '1') {
            var rowm = $('#dg{{ $_mid }}').datagrid('getSelected');
            $.post('{{ $_controller }}/unset_tramite?_token=' + tokenModule, {
              id: rowm.id
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
          } else {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>Ya esta aprobado o no se puede modificar</div>'
            });
          }
        }
      });
    }
  </script>
@endsection

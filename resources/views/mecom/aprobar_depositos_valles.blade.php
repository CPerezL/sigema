@extends('layouts.easyuitab')
@section('content')
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Depositos GDR para revisar (Ya aprobados por la GLB) ',collapsed:false,split:false" style="width:850px;">
      <table id="dgdia{{ $_mid }}" class="easyui-datagrid" style="width:100%;"
        data-options="url:'{{ $_controller }}/get_formularios', queryParams:{ _token: tokenModule },onLoadSuccess:function(){clearTallerasClsmf()},rowStyler: function(index,row){
           if (row.estado == '1')
           {return {class:'normal'};}
           else if(row.aprobadogdr == '4')
           {return {class:'correcto'};}
           else
           {return {class:'alerta'};}}"
        toolbar="#toolbardia{{ $_mid }}" fitColumns="true" rownumbers="true" singleSelect="true" pagination="true" pageList="[20]" pageSize="20" nowrap="false">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'idFormulario'" hidden="true">#</th>
            <th data-options="field:'numero'">Form</th>
            <th data-options="field:'tallertxt'">R:.L:.S:.</th>
            <th data-options="field:'documento'">Codigo</th>
            <th data-options="field:'montoGDR'">Monto GDR</th>
            <th data-options="field:'numeroMiembros'">HH</th>
            <th data-options="field:'aprobadogdrtxt'">Estado</th>
            <th data-options="field:'fechaAprobacion'">F. Aprob.</th>
            <th data-options="field:'fechagdr'">F. Revision</th>
            <th data-options="field:'detail'" formatter="optionsEnviaMecom">Opciones</th>
          </tr>
        </thead>
      </table>
      <script type="text/javascript">
        function optionsEnviaMecom(value, row) {
          var namet = row.tallertxt.replace(/["']/g, "`");
          return '<a href="javascript:void(0)" onclick="ver_aportantes(' + row.idFormulario + ',' + row.numero + ',\'' + namet + '\');"><button>Seleccionar</button></a>';
        };
      </script>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){filtrarDeposValle(rec,'taller');}">
              <option value="0">Todos los tallleres </option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }} </option>
              @endforeach
            </select>
          @else
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDeposValle(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }} </option>
              @endforeach
            </select>
          @endif
        </div>
        <div style="float:left;">
          <select id="filtrotipo" name="filtrotipo" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:true,onChange: function(rec){filtrarDeposValle(rec,'tipo');}">
            <option value="0">Para revisar </option>
            <option value="4">Revisados </option>
            <!--     <option value="5">Anulados</option>-->
          </select>
        </div>
        <div style="float:left;">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" onclick="show_baucher(1);">Ver Deposito GDR</a>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      function filtrarDeposValle(value, campo) {
        $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
          _token: tokenModule,
          valor: value,
          filtro: campo
        }, function(result) {
          if (result.success) {
            $('#dgdia{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }

      function clearTallerasClsmf() {
        $('#dg{{ $_mid }}').datagrid('reload');
      }

      function npago_mform() {
        taller = $("#filtro option:selected").val();
        if (taller > 0) {
          $('#fmecfecha{{ $_mid }}').form('clear');
          $('#dlg_fmecfecha{{ $_mid }}').dialog('open').dialog('setTitle', 'Crear formulario de aportes');
          url = '{{ $_controller }}/create_formaporte?_token=' + tokenModule;
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertlogia')'
          });
        }
      }

      function ver_aportantes(valued, valuenum, titulo) {
        if (valued > 0) {
          $('#dg{{ $_mid }}').datagrid('reload', {
            key: 1,
            idform: valued,
            _token: tokenModule
          });
          var dgPanel = $('#dg{{ $_mid }}').datagrid('getPanel');
          dgPanel.panel('setTitle', 'Formulario N. ' + valuenum + ' de R.L.S. ' + titulo);
        }
      }

      function show_archivo(archivo) {
        urlfile = '{{ $_folder }}' + archivo;
        window.open(urlfile);
      }

      function show_baucher(tipo) {
        var row = $('#dgdia{{ $_mid }}').datagrid('getSelected');
        if (row) {
          switch (tipo) {
            case 1:
              archivo = row.archivoGDR;
              break;
            case 2:
              archivo = row.archivoGLB;
              break;
            case 3:
              archivo = row.archivoCOMAP;
              break;
          }
          if (archivo.length > 10) {
            urlfile = '{{ $_folder }}' + archivo;
            window.open(urlfile);
          } else {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>  @lang('mess.nofile')'
            });
          }
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>  @lang('mess.alertform')'
          });
        }
      }
    </script>
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#tbptasis{{ $_mid }}" title="No Seleccionado"></table>
      <div class="datagrid-toolbar" id="tbptasis{{ $_mid }}" style="border-bottom:1px solid #ddd;height:32px;padding:2px 5px;">
        <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-share-square fa-lg success" onclick="enviar_mform();" id="darasis">Marcar revisado</a></div>
        <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print fa-lg archivo" onclick="gen_formulario();" id="darasis">Ver formulario</a></div>
      </div>
      <script type="text/javascript">
        var datam = new Array();
        datam[0] = 'Regular';
        datam[1] = 'Honorario';
        $(function() {
          var dg{{ $_mid }} = $('#dg{{ $_mid }}').datagrid({
            url: '{{ $_controller }}/get_datos',
            type: 'get',
            dataType: 'json',
            queryParams: {
              _token: tokenModule
            },
            pagination: false,
            fitColumns: false,
            rownumbers: true,
            singleSelect: true,
            remoteFilter: false,
            nowrap: true,
            autoRowHeight: true,
            pageList: [20],
            pageSize: '20',
            columns: [
              [{
                  field: 'id',
                  title: 'ID',
                  hidden: 'true'
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
                  field: 'montoGDR',
                  title: 'Importe'
                },
                {
                  field: 'numeroCuotas',
                  title: 'N. Cuotas'
                },
                {
                  field: 'fechaPago',
                  title: 'Ultimo Ob.'
                },
                {
                  field: 'fechaNuevo',
                  title: 'Nuevo Ob.'
                }
              ]
            ]
          });
        });
      </script>
    </div>
  </div>
  <script type="text/javascript">
    function gen_formulario() {
      window.open("{{ $_controller }}/gen_formulario");
    }

    function gen_reporte() {
      window.open("{{ $_controller }}/gen_reporte");
    }
  </script>


  <div id="dlg_enviarf{{ $_mid }}" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#enviar-buttons{{ $_mid }}" data-options="iconCls:'icon-man',modal:true">
    <form id="enviarf{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input name="documento" id="documento" label="Nro de comprobante:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" required="required">
      </div>
    </form>
    <div id="enviar-buttons{{ $_mid }}">
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="enviando_mform();" style="width:90px">Enviar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_enviarf{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
    </div>
  </div>
  <script type="text/javascript">
    function enviar_mform() {
      $.messager.confirm('Confirm', '¿El deposito esta revisado y es correcto?, se marcara como revisado', function(r) {
        $.post('{{ $_controller }}/send_formulario', {
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
          $('#dlg_enviarf{{ $_mid }}').dialog('close'); // close the dialog
          $('#dgdia{{ $_mid }}').datagrid('reload');
          $('#dg{{ $_mid }}').datagrid('reload', {
            key: 1,
            idform: 0,
            _token: tokenModule
          });
          var dgPanel = $('#dg{{ $_mid }}').datagrid('getPanel');
          dgPanel.panel('setTitle', 'No Seleccionado');
        }, 'json');
      });
    }

    function enviando_mform() {
      ndocs = $('#documento').val();
      fdepo = $('#fechaDeposito').val();
      $.post('{{ $_controller }}/send_formulario', {
        _token: tokenModule,
        fechaDeposito: fdepo,
        documento: ndocs
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
        $('#dlg_enviarf{{ $_mid }}').dialog('close'); // close the dialog
        $('#dgdia{{ $_mid }}').datagrid('reload');
        $('#dg{{ $_mid }}').datagrid('reload', {
          key: 1,
          idform: 0,
          _token: tokenModule
        });
        var dgPanel = $('#dg{{ $_mid }}').datagrid('getPanel');
        dgPanel.panel('setTitle', 'No Seleccionado');
      }, 'json');
    }

    function recal_mform() {
      $.post('{{ $_controller }}/recal_formulario', {
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
        var dgPanel = $('#dg{{ $_mid }}').datagrid('getPanel');
        dgPanel.panel('setTitle', 'No Seleccionado');
      }, 'json');
    }

    function anula_mform() {
      $.post('{{ $_controller }}/anula_formulario', {
        _token: tokenModule
      }, function(result) {
        if (!result.success) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>' + result.Msg
          });
        } else {
          $('#dgdia{{ $_mid }}').datagrid('reload');
          $('#dg{{ $_mid }}').datagrid('reload', {
            key: 1,
            idform: 0,
            _token: tokenModule
          });
          $.messager.show({
            title: 'Correcto',
            msg: '<div class="messager-icon messager-info"></div>' + result.Msg
          });
        }
        var dgPanel = $('#dg{{ $_mid }}').datagrid('getPanel');
        dgPanel.panel('setTitle', 'No Seleccionado');
      }, 'json');
    }
  </script>
@endsection

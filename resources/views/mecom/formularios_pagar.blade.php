@extends('layouts.easyuitab')
@section('content')
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Formularios de pago de obolos',collapsed:false,split:false" style="width:650px;">
      <table id="dgfqr{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%"
        data-options="url:'{{ $_controller }}/get_formularios?_token={{ csrf_token() }}',onLoadSuccess:function(){clearqrTallerasClsmf()},rowStyler: function(index,row){
         if (row.estado == '1') {return {class:'activo'};}
         else if(row.estado == '2') {return {class:'alerta'};}
         else if(row.estado == '5') {return {class:'archivo'};}
         else {return {class:'correcto'};}}"
        toolbar="#toolbardia{{ $_mid }}" fitColumns="true" rownumbers="true" singleSelect="true" pagination="true" pageList="[20]" pageSize="20">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'idFormulario'" hidden="true">#</th>
            <th data-options="field:'numero'">Form</th>
            <th data-options="field:'taller'">Tall</th>
            <th data-options="field:'descripcion'">Estado</th>
            <th data-options="field:'numeroMiembros'">N. Hnos</th>
            <th data-options="field:'montoTotal'">Importe</th>
            <th data-options="field:'fechaEnvio'">F. Envio</th>
            <th data-options="field:'fechaAprobacion'">F. Aprobacion</th>
            <th data-options="field:'detail'" formatter="optionsEnviaMecom">Opciones</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="display:inline-block">
        <div style="float:left;"><b>Ges: </b>
          <input id="gestform{{ $_mid }}" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2021,max:{{ $year }},editable:false,onChange: function(rec){fMecomFormsQR(rec,'gestion');}" value="{{ $year }}">
        </div>
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){fMecomFormsQR(rec,'taller');}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}
                </option>
              @endforeach
            </select>
          @else
            <select id="filtro{{ $_mid }}" name="filtro{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){fMecomFormsQR(rec,'taller');}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
        <div style="float:right;">
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg aqua" onclick="npago_mqrform();" id="darasisfe">Nuevo formulario</a>
        </div>
      </div>
    </div>
    <div id="dlg_fmecfecha{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:5px 5px" closed="true" buttons="#dlgfmfecha-buttons{{ $_mid }}" data-options="iconCls:'icon-man',modal:true">
      <form id="fmecfecha{{ $_mid }}" method="post" novalidate>
        <div style="margin-top:4px">
          <label>Crear un formulario de aportes nuevo?<br>Solo se puede tener uno activo</label>
        </div>
      </form>
      <div id="dlgfmfecha-buttons{{ $_mid }}">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="createqr_formpago();" style="width:90px">Crear</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_fmecfecha{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
      </div>
    </div>
    <script type="text/javascript">
      function fMecomFormsQR(value, campo) {
        $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
          _token: tokenModule,
          valor: value,
          filtro: campo
        }, function(result) {
          if (result.success) {
            $('#dgfqr{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }

      function clearqrTallerasClsmf() {
        $('#dgqr{{ $_mid }}').datagrid('reload');
      }

      function optionsEnviaMecom(value, row) {
        return '<a href="javascript:void(0)" onclick="ver_qraportantes(' + row.idFormulario + ',' + row.numero + ',' +
          row.taller + ');"><button>Seleccionar</button></a>';
      }

      function npago_mqrform() {
        taller = $("#filtro{{ $_mid }} option:selected").val();
        if (taller > 0) {
          $('#fmecfecha{{ $_mid }}').form('clear');
          $('#dlg_fmecfecha{{ $_mid }}').dialog('open').dialog('setTitle', 'Crear formulario de aportes');
          url = '{{ $_controller }}/createqr_formaporte?_token=' + tokenModule;
        } else {
          $.messager.show({ // show error message
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>Seleccione Taller primero</div>'
          });
        }
      }

      function createqr_formpago() {
        $('#fmecfecha{{ $_mid }}').form('submit', {
          url: url,
          onSubmit: function() {
            return $(this).form();
          },
          success: function(result) {
            var result = eval('(' + result + ')');
            if (result.success) {
              $.messager.show({ // show error message
                title: 'Correcto',
                msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg +
                  '</div>'
              });
              $('#dgfqr{{ $_mid }}').datagrid('reload');
            } else {
              $.messager.show({ // show error message
                title: 'Error en datos',
                msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg +
                  '</div>'
              });
            }
            $('#fmecfecha{{ $_mid }}').form('clear');
            $('#dlg_fmecfecha{{ $_mid }}').dialog('close'); // close the dialog
          }
        });
      }
    </script>
    <div data-options="region:'center'">
      <table id="dgqr{{ $_mid }}" style="width:100%;height:100%" toolbar="#tbptasis{{ $_mid }}" title="Formulario no Seleccionado"></table>
      <div class="datagrid-toolbar" id="tbptasis{{ $_mid }}">
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg teal" onclick="setNuevoObolo();" id="darasis">Adicionar aportante</a>
        </div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-print fa-lg purple" onclick="gen_qrformulario();" id="darasis">Imprimir formulario</a>
        </div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-share-square fa-lg orange" onclick="pagoqr{{ $_mid }}();" id="darasis">Pagar formulario con QR</a></div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-file fa-lg excel" onclick="gen_recibo();" id="darasis">Recibo de tesorero</a></div>
        <div style="float:right;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-reply fa-lg red" onclick="anular_mqrform();" id="darasis">Anular formulario</a></div>
      </div>
      <div id="dlgaddobolo{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
        <table id="dgqrmie{{ $_mid }}" style="width:auto;height:456px"></table>
        <div class="datagrid-toolbar" id="toolbarmie{{ $_mid }}">
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square activo" onclick="miembroqr_saveItem();">Asignar aporte</a></div>
        </div>
      </div>
      <script type="text/javascript">
        var datam = new Array();
        datam[0] = 'Regular';
        datam[1] = 'Honorario';
        $(function() {
          var dgqr{{ $_mid }} = $('#dgqr{{ $_mid }}').datagrid({
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
                  field: 'monto',
                  title: 'Importe'
                },
                {
                  field: 'numeroCuotas',
                  title: 'Cuotas'
                },
                {
                  field: 'fechaPago',
                  title: 'Mes Anterior'
                },
                {
                  field: 'fechaNuevo',
                  title: 'Mes pagado'
                },
                {
                  field: 'detail',
                  title: 'Opciones',
                  formatter: function(value, row) {
                    if (row.estado == '1')
                      return '<a href="javascript:void(0)" onclick="removeqrAporte(' +
                        row.idRegistro +
                        ');"><button style="color:red"><i class="fa fa-times"></i> Eliminar</button></a>';
                    else
                      return '';
                  },
                  width: 130
                }
              ]
            ]
          });
        });

        function removeqrAporte(valued) {
          if (valued > 0) {
            $.messager.confirm('Confirm', 'Esta seguro de eliminar este aporte?', function(r) {
              if (r) {
                $.post('{{ $_controller }}/remove_obolo', {
                  _token: tokenModule,
                  id: valued
                }, function(result) {
                  if (!result.success) {

                    $.messager.show({ // show error message
                      title: 'Error',
                      msg: '<div class="messager-icon messager-error"></div><div>' +
                        result.Msg + '</div>'
                    });
                  } else {
                    $.messager.show({
                      title: 'Correcto',
                      msg: '<div class="messager-icon messager-info"></div><div>' +
                        result.Msg + '</div>'
                    });
                    $('#dgqrmie{{ $_mid }}').datagrid(
                      'reload'); // reload the user data
                    $('#dgfqr{{ $_mid }}').datagrid('reload');
                  }
                }, 'json');
              }
            });
          }
        }
      </script>
    </div>
  </div>
  <div id="dlg_nqrcuotas{{ $_mid }}" class="easyui-dialog" style="width:350px;height:auto;padding:5px 5px" closed="true" buttons="#dlgnqrcuotas-buttons{{ $_mid }}" data-options="iconCls:'icon-man',modal:true">
    <form id="fmecnqrcuotas{{ $_mid }}" method="post" novalidate>
      <div style="margin-top:4px">
        <input id="numbercuotas" name="numerocuotas" value="1" class="easyui-numberspinner" style="width:260px;" required="required" data-options="min:1,max:180,editable:false" label="N. de obolos">
      </div>
    </form>
    <div id="dlgnqrcuotas-buttons{{ $_mid }}">
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="miembroqr_savePago(0);" style="width:145px">Asignar y cerrar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="miembroqr_savePago(1);" style="width:160px">Asignar y continuar</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_nqrcuotas{{ $_mid }}').dialog('close');" style="width:90px">Cerrar</a>
    </div>
  </div>
  <script type="text/javascript">
    function miembroqr_saveItem() {
      $('#fmecnqrcuotas{{ $_mid }}').form('reset');
      $('#dlg_nqrcuotas{{ $_mid }}').dialog('open').dialog('setTitle', 'Numero de cuotas');
    }

    function miembroqr_savePago(opcion) {
      var rowm = $('#dgqrmie{{ $_mid }}').datagrid('getSelected');
      //var ncuot = $('#numbercuotas').val();
      var ncuot = $('#numbercuotas').numberbox('getValue');
      $.post('{{ $_controller }}/set_pagomiembro', {
        idmiembro: rowm.id,
        nqrcuotas: ncuot,
        _token: tokenModule
      }, function(result) {
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
        }
        $('#dlg_nqrcuotas{{ $_mid }}').dialog('close'); // close the dialog
        $('#dgqr{{ $_mid }}').datagrid('reload'); // reload the user data
        $('#dgfqr{{ $_mid }}').datagrid('reload');
        if (opcion == '0')
          $('#dlgaddobolo{{ $_mid }}').dialog('close'); // close the dialog
      }, 'json');
    }

    function setNuevoObolo() {
      $('#dlgaddobolo{{ $_mid }}').dialog('open').dialog('setTitle', 'Buscar miembro para pagar obolo');
      $('#dgqrmie{{ $_mid }}').datagrid('removeFilterRule', 'NombreCompleto');
      $('#dgqrmie{{ $_mid }}').datagrid('reload');
    }

    function gen_qrformulario() {
      window.open("{{ $_controller }}/gen_qrformulario");
    }
    function gen_recibo() {
      window.open("{{ $_controller }}/gen_recibo");
    }
    $(function() {
      var dgqrmie{{ $_mid }} = $('#dgqrmie{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_miembros',
        type: 'get',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbarmie{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: false,
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
              title: 'Miembro'
            },
            {
              field: 'ultimoPago',
              title: 'Ultimo pago'
            }
          ]
        ]
      });
      dgqrmie{{ $_mid }}.datagrid('enableFilter', [{
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
      }, {
        field: 'ultimoPago',
        type: 'label'
      }]);
    });
  </script>
  <script type="text/javascript">
    function doTallerqrbCls(value) {
      $.post('{{ $_controller }}/filter_taller', {
        taller: value
      }, function(result) {
        if (result.success) {
          $('#dgfqr{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }
  </script>
  <script>
    function ver_qraportantes(valued, valuenum, valuetall) {
      if (valued > 0) {
        $('#dgqr{{ $_mid }}').datagrid('reload', {
          key: 1,
          idform: valued,
          _token: tokenModule
        });
        $('#dgqr{{ $_mid }}').datagrid('getPanel').panel('setTitle', 'Formulario No. ' + valuenum + '-' +
          valuetall);
      }
    }
  </script>
  <script type="text/javascript">
    function enviar_mqrform{{ $_mid }}() {
      var index = 0;
      var row = $('#dgqr{{ $_mid }}').datagrid('getRows')[index];
      if (row) {
        if (row.estado == '1') {
          $('#enviarf{{ $_mid }}').form('clear');
          $('#dlg_enviarf{{ $_mid }}').dialog('open').dialog('setTitle', 'Enviar formulario');
        } else if (row.estado == '2') {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>El formulario ya fue enviado</div>'
          });
        } else if (row.estado == '4') {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>El formulario ya fue aprobado</div>'
          });
        } else if (row.estado == '5') {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>El formulario fue anulado</div>'
          });
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div><div>No se puede enviar</div>'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione formulario o esta vacio el formulario</div>'
        });
      }
    }

    function enviar_mqrformol(valued) {
      if (valued > 0) {
        $('#dlg_enviarf{{ $_mid }}').dialog('open').dialog('setTitle', 'Enviar formulario');
      } else {
        $.messager.show({ // show error message
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione Taller primero'
        });
      }
    }

    function enviando_mqrform{{ $_mid }}(opcion = 0) {
      url = '{{ $_controller }}/send_formulario?_token=' + tokenModule;
      $('#enviarf{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          if (result.errorMsg) {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg +
                '</div>'
            });
          } else {
            $.messager.show({
              title: 'Success',
              msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg +
                '</div>'
            });
            $('#dlg_enviarf{{ $_mid }}').dialog('close'); // close the dialog
            $('#dgqr{{ $_mid }}').datagrid('reload'); // reload the user data
            $('#dgfqr{{ $_mid }}').datagrid('reload');
            if (opcion == '0')
              $('#dlgaddobolo{{ $_mid }}').dialog('close'); // close the dialog
          }
        }
      });
    }

    function anular_mqrform() {
      $.messager.confirm('Confirm',
        'Esta seguro de anular este formulario, se anulara y tendra que crear otro formulario?',
        function(r) {
          if (r) {
            $.post('{{ $_controller }}/anular_formulario', {
              _token: tokenModule
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' +
                    result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result
                    .Msg + '</div>'
                });
                $('#dgqr{{ $_mid }}').datagrid('reload'); // reload the user data
                $('#dgfqr{{ $_mid }}').datagrid('reload');
              }
            }, 'json');
          }
        });
    }

    function doGestqrForm{{ $_mid }}(value) {
      valuel = $('#gestform{{ $_mid }}').val();
      $.post('{{ $_controller }}/filter_gestion', {
        gestion: valuel
      }, function(result) {
        if (result.success) {
          $('#dgfqr{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    }
  </script>
  <!--   formulario de derechos registro  -->
  <div id="dlg_pagar{{ $_mid }}" class="easyui-dialog" style="width:960px;height:700px;" data-options="iconCls:'icon-save',resizable:false,modal:true" closed="true" closable="false">
    <div id="cc" class="easyui-layout" style="width:100%;height:100%;">
      <form id="formqr{{ $_mid }}" method="post" novalidate>
        <div data-options="region:'west',split:true" style="width:220px;">
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="valle" style="width:100%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="logiaName" style="width:100%;" data-options="label:'<b>Taller:</b>',readonly:'true',editable:false" labelWidth="60"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="formulario" style="width:100%;" data-options="label:'<b>Nro. Form:</b>',readonly:'true',editable:false" labelWidth="90"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="cantidad" style="width:100%;" data-options="label:'<b>Nro. aportantes:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="montoTotal" style="width:100%;" data-options="label:'<b>Monto Total:</b>',readonly:'true',editable:false" labelWidth="120"></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#pagar" class="easyui-linkbutton c6" iconCls="icon-ok" style="width:210px" onclick="pagoQRForm();">Pagar Formulario con QR</a></div>
          <div style="padding:10px 2px 10px 2px;"><a href="#cancelar" class="easyui-linkbutton c6" iconCls="icon-cancel" style="width:210px" onclick="cancelPagoForm();">Cerrar/Cancelar transaccion</a></div>
        </div>
        <div data-options="region:'center'" style="width:730px;height:auto;">
          <iframe id="modalpg{{ $_mid }}" class="embed-responsive-item" src="" width="98%" height="650" style="border:none;"></iframe>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function pagoqr{{ $_mid }}() {
      var row = $('#dgfqr{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.estado == '1') {
          $('#formqr{{ $_mid }}').form('load',
            '{{ $_controller }}/get_datos_qr?_token={{ csrf_token() }}&id=' + row.idFormulario);
          $('#dlg_pagar{{ $_mid }}').dialog('open').dialog('setTitle', 'Pago de formulario con QR');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Pago realizado\n o falta aprobacion'
          });
        }
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite'
        });
      }
    }

    function cancelPagoForm() {
      var url = '';
      $("#modalpg{{ $_mid }}").attr("src", url);
      $('#dlg_pagar{{ $_mid }}').dialog('close');
    }

    function pagoQRForm() {
      $.messager.confirm('Confirm', '¿Esta seguro de enviar el formulario?, ya no podra añadir mas aportantes despues de generar el pago QR',
        function(r) {
          if (r) {
            var row = $('#dgfqr{{ $_mid }}').datagrid('getSelected');
            if (row) {
              $.post('{{ $_controller }}/get_pago_qr', {
                _token: tokenModule,
                reg: row.idFormulario
              }, function(result) {
                registro = result.ref;
                red = result.red;
                ok = result.ok;
                if (ok == '1') {
                  var url =
                    '{{ $linkiframe }}?entidad={{ $entidad }}&red={{ $linkaccion }}/codigo/' +
                    red + '&ref=' + registro;
                  $("#modalpg{{ $_mid }}").attr("src", url);
                  $('#dgfqr{{ $_mid }}').datagrid('reload');
                }
              }, 'json');
            } else {
              $.messager.show({
                title: 'Error',
                msg: '<div class="messager-icon messager-error"></div>No hay monto a pagar'
              });
            }

          }
        });
    }
  </script>

@endsection

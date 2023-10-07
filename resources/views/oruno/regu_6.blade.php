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
              field: 'fechaCircular',
              title: 'F. Circular'
            },
            {
              field: 'fechaJuramento',
              title: 'F. Juramento'
            },
            {
              field: 'numeroCertificado',
              title: 'Nº Cert.',
              hidden: true
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'valle',
              title: 'Valle',
              hidden: true
            },
            {
              field: 'nLogia',
              title: 'Logia'
            },
            {
              field: 'numero',
              title: 'Nro'
            },
            {
              field: 'GradoActual',
              title: 'Grado Acred.'
            },
            {
              field: 'apPaterno',
              title: 'Ap. Paterno'
            },
            {
              field: 'apMaterno',
              title: 'Ap. Materno'
            },
            {
              field: 'nombres',
              title: 'Nombres'
            },
            {
              field: 'estadoPago',
              title: 'Estado Pago'
            },
            {
              field: 'estadotxt',
              title: 'Pago'
            },
            {
              field: 'estado',
              title: 'Estado Miembro'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block;">
      {{-- <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-print correcto" onclick="gen_certificado_145();">Imprimir Certificado</a></div> --}}
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg correcto" onclick="revDeposito();">Revisar deposito</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-user danger" onclick="copiar_datos();">Registrar en el RUM</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar R:.L:.S:.</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
          <select id="filtrot4" name="filtrot4" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div style="float:right;"><input class="easyui-searchbox" style="width:200px" data-options="searcher:doSearchUser,prompt:'Buscar Apellido'" id="searchboxuu{{ $_mid }}" value="{!! $palabra ?? '' !!}">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchUser();"></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function doSearchUser(value) {
      filterDatos(value, 'palabra');
    }

    function clearSearchUser() {
      $('#searchboxuu{{ $_mid }}').searchbox('clear');
      filterrDatos('', 'palabra');
    }

    function filterDatos(value, campo) {
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
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgdatos{{ $_mid }}" class="easyui-dialog" style="width:480px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fdatos{{ $_mid }}" method="post" novalidate>
      <div style="margin-bottom:10px;margin-top:5px;margin-left:50px"><img id='foto' width="140"><label id="labelfoto" style="margin-left:10px"></label></div>
      <div style="margin-bottom:0px;margin-left:5px"><input class="easyui-textbox" name="nLogia" style="width:100%;" data-options="label:'<b>Logia:</b>',readonly:'true',editable:false" labelWidth="80"></div>
    </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveDatos_rum();" style="width:180px">Copiar Datos al RUM</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdatos{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <form id="fasignar" method="post"><input type="hidden" name="task" value="3" /></form>
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="toolbarobs{{ $_mid }}">
      @if (Auth::user()->permisos == 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square activo" onclick="asignarRUM();">Asignar RUM</a></div>
      @endif
    </div>
  </div>
  <script type="text/javascript">
    $(function() {
      var dgobs{{ $_mid }} = $('#dgobs{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_nombres?id=0',
        type: 'post',
        dataType: 'json',
        queryParams: {
          _token: tokenModule
        },
        toolbar: '#toolbarobs{{ $_mid }}',
        pagination: false,
        fitColumns: true,
        rownumbers: true,
        singleSelect: true,
        remoteFilter: false,
        nowrap: false,
        striped: true,
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
              field: 'Paterno',
              title: 'Paterno'
            },
            {
              field: 'Materno',
              title: 'Materno'
            },
            {
              field: 'Nombres',
              title: 'Nombres'
            },
            {
              field: 'logia',
              title: 'R:.L:.S:.'
            }
          ]
        ]
      });
    });

    function copiar_datos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idMiembro > 0) {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Ya tiene Registro creado'
          });
        } else {
          if (row.okPagoDerechos == 1) {
            $('#fdatos{{ $_mid }}').form('clear');
            $('#fdatos{{ $_mid }}').form('load', row);
            if (row.foto) {
              $('#foto').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
              $('#foto').attr('src', $('#foto').attr('src')); //recraga imagen
            } else {
              $('#foto').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
              $('#foto').attr('src', $('#foto').attr('src'));
            }
            var nombre = row.nombres + ' ' + row.apPaterno + ' ' + row.apMaterno;
            $("#labelfoto").text(nombre);
            $('#dlgdatos{{ $_mid }}').dialog('open').dialog('setTitle', 'Registrar datos en RUM');
          } else {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div>El deposito de derechos debe estar aprobado'
            });
          }
        }
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&task=1&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione ceremonia primero'
        });
      }
    }

    function copiar_foto() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.idMiembro > 0) {
          $('#fdatos{{ $_mid }}').form('clear');
          $('#fdatos{{ $_mid }}').form('load', row);
          if (row.foto) {
            $('#foto').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
            $('#foto').attr('src', $('#foto').attr('src')); //recraga imagen
          } else {
            $('#foto').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
            $('#foto').attr('src', $('#foto').attr('src'));
          }
          var nombre = row.nombres + ' ' + row.apPaterno + ' ' + row.apMaterno;
          $("#labelfoto").text(nombre);
          $('#dlgdatos{{ $_mid }}').dialog('open').dialog('setTitle', 'Copiar foto');
        } else {
          $.messager.show({
            title: 'Error',
            msg: '<div class="messager-icon messager-error"></div>Dbe asignar RUM primero</div>'
          });
        }
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&task=2&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione ceremonia primero'
        });
      }
    }

    function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_nombres?id=' + row.idTramite);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Lista de nombres');
        url = '{{ $_controller }}/procesar_datos?_token={{ csrf_token() }}&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione Miembro</div>'
        });
      }
    }

    function asignarRUM() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      $('#fasignar').form('submit', {
        url: url,
        onSubmit: function(param) {
          param.idm = row.id;
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
            $('#dlgobs{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function saveDatos_rum() {
      $('#fdatos{{ $_mid }}').form('submit', {
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
            $('#fdatos{{ $_mid }}').form('clear');
            $('#dlgdatos{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function gen_certificado_145() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        window.open("{{ $_controller }}/gen_reporte?id=" + row.idTramite);
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>Seleccione tramite primero'
        });
      }
    }

    function revDeposito() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        if (row.docDepositoDer !== null && row.docDepositoDer.length > 4) {
          $('#abrir_doc').linkbutton('enable');
          $('#abrir_doc').attr("target", "_blank");
          var newUrl = '{{ $_folder }}/media/tramites/' + row.docDepositoDer;
          $('#abrir_doc').attr("href", newUrl);
        } else {
          $('#abrir_doc').attr("target", "_self");
          $('#abrir_doc').linkbutton('disable');
        }
        $('#fmaum2{{ $_mid }}').form('clear');
        $('#fmaum2{{ $_mid }}').form('load', '{{ $_controller }}/get_tramites?_token=' + tokenModule + '&id=' + row.idTramite); // load from URL
        $('#dlgauun{{ $_mid }}').dialog('open').dialog('setTitle', 'Datos del certificado');
        url = '{{ $_controller }}/update_ceremonia?_token=' + tokenModule + '&id=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div>@lang('mess.alertdata')'
        });
      }
    }

    function saveDatos_{{ $_mid }}() {
      $('#fmaum2{{ $_mid }}').form('submit', {
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
            $('#fmaum2{{ $_mid }}').form('clear');
            $('#dlgauun{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgauun{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;" closed="true" buttons="#dlgauun-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmaum2{{ $_mid }}" method="post" novalidate>
      <div class="easyui-panel" title="Revisar pago de derechos de Regularizacion" style="width:100%;padding:5px;">
        <div style="margin-bottom:0px;margin-left:5px">
          <input class="easyui-textbox" name="apreName1" style="width:100%;" data-options="label:'<b>Nombre:</b>',labelPosition:'left',editable:false" labelWidth="70">
        </div>
        <input type="hidden" name="idMiembro1" value="0" />
      </div>
      {{-- <div class="easyui-panel" title="Fecha del certificado" style="width:100%;padding:5px;">
        <label><b>El certificado saldra con la fecha de la Ceremonia, puede cambiarlo en el cuadro siguiente</b></label>
        <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaCeremonia1" style="width:100%" data-options="label:'Fecha de Ceremonia:'" labelWidth="260"></div>
      </div>
      <div class="easyui-panel" title="Numero de Certificado" style="width:100%;padding:5px;">
        <label><b>El certificado necesita un numero</b></label>
        <div style="margin-top:0px"><input name="numero1" id="numero1" label="Numero:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%"></div>
      </div> --}}
      <div class="easyui-panel" title="Revision de derechos de Ceremonia y Cetificación" style="width:100%;padding:5px;">
        <div style="margin-bottom:10px">Documento de Deposito: <a href="javascript:void(0);" id="abrir_doc" class="easyui-linkbutton c6" iconCls="icon-print" target="_blank" style="width:250px;margin-left:10px;">Ver documento archivo</a></div>
        <div style="width:220px;display: inline-block;"><b>Revisado por Tesoreria:</b></div>
        <input class="easyui-checkbox" id="okPagoDerechos" name="okPagoDerechos" value="1" data-options="label:'<b>Deposito revisado<b>',labelPosition:'after'" labelWidth="140">
      </div>
  </div>

  </form>
  </div>
  <div id="dlgauun-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDatos_{{ $_mid }}();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgauun{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
@endsection

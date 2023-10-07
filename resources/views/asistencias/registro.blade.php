@extends('layouts.easyuitab')
@section('content')
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Fechas de las tenidas',collapsed:false,split:false" style="width:500px;">
      <table id="dgdia{{ $_mid }}" class="easyui-datagrid" style="width:100%;" data-options="url:'{{ $_controller }}/get_dias?_token={{ csrf_token() }}',nowrap:false,fitColumns:false,singleSelect:true,onLoadSuccess:function(){clearTalleras()}" toolbar="#toolbardia{{ $_mid }}"
        rownumbers="false" fitColumns="true">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'dia',width:25" hidden="true">Dia</th>
            <th data-options="field:'fechadia',width:60">Fecha</th>
            <th data-options="field:'acta1',width:30">Act 1er</th>
            <th data-options="field:'acta2',width:30">Act 2do</th>
            <th data-options="field:'acta3',width:30">Act 3ro</th>
            <th data-options="field:'gradot',width:25">Grado</th>
            <th field="detail" formatter="formatDetailTenida">Opciones</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="display:inline-block">
        <div style="float:left;"><b>Gestion:</b><input id="gestionas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2019,max:{{ $year }},editable:false,onChange: function(rec){fDatosAsistencia(rec,'gestion');}" value="{{ $year }}">
        </div>
        <div style="float:left;"><b>Mes:</b><select name="month2" id="month2" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onSelect: function(rec){fDatosAsistencia(rec.id,'mes');}">
            @foreach ($meses as $key => $mmm)
              @if ($key == $month)
                <option value="{{ $key }}" selected>{{ $mmm }}</option>
              @else
                <option value="{{ $key }}">{{ $mmm }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){doTalleras(rec);}">
              <option value="0" selected>Seleccionar taller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onSelect: function(rec){doTalleras(rec.id);}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
      </div>
      <script type="text/javascript">
        function gradofun(vari) {
          if (vari == 3) {
            $('#acta001').numberspinner('enable');
            $('#acta002').numberspinner('enable');
            $('#acta003').numberspinner('enable');
          }
          else if(vari == 2) {
            $('#acta001').numberspinner('enable');
            $('#acta002').numberspinner('enable');
            $('#acta003').numberspinner('disable');
          }
          else {
            $('#acta001').numberspinner('enable');
            $('#acta002').numberspinner('disable');
            $('#acta003').numberspinner('disable');
          }
        }
      </script>
      <div id="dlgdatasis{{ $_mid }}" class="easyui-dialog" style="width:620px;height:auto;padding:2px 2px" closed="true" buttons="#dlgdatasis-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fdatasis{{ $_mid }}" method="post" novalidate>
          <input name="idAsistenciaData" id="idAsistenciaData" type="hidden">
          <div style="margin-bottom:5px"><span style="color:red"> * Seleccionar el maximo grado al que se subio en la tenida</span></div>
          <div style="margin-bottom:5px" class="column-1">
            <select id="grado" class="easyui-combobox" name="grado" style="width:50%" label="Grado de la tenida:" labelWidth="130" labelPosition="left" data-options="panelHeight:'auto',editable:false,onChange: function(rec){gradofun(rec);}">
              <option value="0">No seleccionado</option>
              <option value="1">Primero</option>
              <option value="2">Segundo</option>
              <option value="3">Tercero</option>
              {{-- <option value="4">Maestro Instalado</option> --}}
            </select>
          </div>
          <div style="margin-bottom:5px"><span style="color:red">** Si no se abrio dejar en cero el numero de acta</span></div>
          <div style="margin-bottom:5px" class="column-3">
            <input class="easyui-numberspinner" name="numeroActa1" style="width:98%" data-options="min:0,editable:true" label="Acta 1er grado" labelWidth="120" id="acta001">
          </div>
          <div style="margin-bottom:5px" class="column-3">
            <input class="easyui-numberspinner" name="numeroActa2" style="width:98%" data-options="min:0,editable:true" label="Acta 2do grado" labelWidth="120" id="acta002">
          </div>
          <div style="margin-bottom:5px" class="column-3">
            <input class="easyui-numberspinner" name="numeroActa3" style="width:98%" data-options="min:0,editable:true" label="Acta 3er grado" labelWidth="120" id="acta003">
          </div>

        </form>
        <div id="dlgdatasis-buttons{{ $_mid }}">
          @if (Auth::user()->permisos == 1)
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_dataAsis();" style="width:90px">Grabar</a>
          @endif
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdatasis{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
        </div>
      </div>
      <center>
        <div id="titleftenida" style="font-size:16px;padding-top:15px;font-weight:bold;"></div>
        <span id="bplanilla" style="display: none;"><br><a href="javascript:void(0)" onClick="gen_planillaas(1);"><button><i class="fa fa-file-text-o"></i> Generar acta de asistencia de primer grado</button></a></span>
        <span id="bplanilla2" style="display: none;"><br><a href="javascript:void(0)" onClick="gen_planillaas(2);"><button><i class="fa fa-file-text-o"></i> Generar acta de asistencia de segundo grado</button></a></span>
        <span id="bplanilla3" style="display: none;"><br><a href="javascript:void(0)" onClick="gen_planillaas(3);"><button><i class="fa fa-file-text-o"></i> Generar acta de asistencia de tercer grado</button></a></span>
      </center>
    </div>
    <div data-options="region:'center'">
      <div id="tt" class="easyui-tabs" style="width:100%;height:99%;">
        <div title="Lista de HH del taller" style="padding:0px;display:none;" data-options="iconCls:'fa fa-users correcto'">
          <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#tbptasis{{ $_mid }}"></table>
        </div>
        <div title="Lista de Visitantes a la tenida" data-options="closable:false,iconCls:'fa fa-users correcto'" style="overflow:auto;padding:0px;display:none;">
          <table id="dg6{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options=" url:'{{ $_controller }}/get_visitas', queryParams:{ _token: tokenModule },
               " toolbar="#tbvisita{{ $_mid }}" pagination="false" fitColumns="true"
            rownumbers="true" fitColumns="true" singleSelect="true">
            <thead>
              <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="idExtra" hidden="true">ID</th>
                <th field="LogiaActual">Taller Nro</th>
                <th field="GradoActual">Grado</th>
                <th field="NombreCompleto">Nombre</th>
              </tr>
            </thead>
          </table>
          <div class="datagrid-toolbar" id="tbvisita{{ $_mid }}" style="display: inline-block">
            @if (Auth::user()->permisos == 1)
              <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-tag fa-lg aqua" onclick="visita_asignar(1);" id="addvisita">Adicionar visitante de otra logia</a></div>
              <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg danger" onclick="quitarVisita();" id="quitarvis">Quitar visita</a></div>
            @endif
          </div>
        </div>
      </div>
      <!--                             finde tabs                                          -->
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
            pageList: [50],
            pageSize: '50',
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
                  field: 'pagoOk',
                  title: 'pagoOk',
                  hidden: 'true'
                },
                {
                  field: 'FechaValida',
                  title: 'FechaValida',
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
                  field: 'EstadoAsis',
                  title: 'Asistencia',
                  width: 75
                },
                {
                  field: 'oficial',
                  title: 'Oficial/Oficial PT*'
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
                    var grow = row.Grado;
                    if (row.FechaValida == '2') {
                      $('#darpt').linkbutton('enable');
                      $('#quitarpt').linkbutton('enable');
                      if ({{ Auth::user()->permisos }} == '1') {
                        if (row.idAsistencia == null)
                          return '<a href="javascript:void(0)" onclick="updateAsis(' + erow + ',1,' + grow + ');"><button ><i class="fa fa-check"></i> Asistencia</button></a>';
                        else
                          return '<a href="javascript:void(0)" onclick="updateAsis(' + erow + ',0,' + grow + ');"><button style="color:red"><i class="fa fa-times"></i> Quitar asis.</button></a>';
                      } else {
                        return '';
                      }
                    } else if (row.FechaValida == '1') {
                      $('#darpt').linkbutton('disable');
                      $('#quitarpt').linkbutton('disable');
                      return 'No se realizo';
                    } else {
                      $('#darpt').linkbutton('disable');
                      $('#quitarpt').linkbutton('disable');
                      return 'Fecha Pasada';
                    }
                  },
                  width: 130
                }
              ]
            ],
            rowStyler: function(index, row) {
              if (row.EstadoAsis == 'Asistio') {
                if (row.pagoOk == '1')
                  return {
                    class: "activo"
                  };
                else
                  return {
                    class: 'alerta'
                  };;
              } else if (row.pagoOk == '1') {
                return {
                  class: 'alerta'
                };;
              } else {
                return {
                  class: 'inactivo'
                };
              }
            }
          });
          dg{{ $_mid }}.datagrid('enableFilter', [{
              field: 'id',
              type: 'label'
            }, {
              field: 'ck',
              type: 'label'
            }, {
              field: 'oficial',
              type: 'label'
            }, {
              field: 'Miembro',
              type: 'label'
            }, {
              field: 'ultimoPago',
              type: 'label'
            }, {
              field: 'detail',
              type: 'label'
            }, {
              field: 'GradoActual',
              type: 'combobox',
              options: {
                panelHeight: 'auto',
                data: [{
                  value: '',
                  text: 'Todos'
                }, {
                  value: 'Aprendiz',
                  text: 'Aprendiz'
                }, {
                  value: 'Compañero',
                  text: 'Compañero'
                }, {
                  value: 'Maestro',
                  text: 'Maestro'
                }, {
                  value: 'V.M./Ex V.M.',
                  text: 'V.M./Ex V.M.'
                }],
                onChange: function(value) {
                  if (value == '') {
                    dg{{ $_mid }}.datagrid('removeFilterRule', 'GradoActual');
                  } else {
                    dg{{ $_mid }}.datagrid('addFilterRule', {
                      field: 'GradoActual',
                      op: 'equal',
                      value: value
                    });
                  }
                  dg{{ $_mid }}.datagrid('doFilter');
                }
              }
            }, {
              field: 'UltimoPago',
              type: 'label'
            },
            {
              field: 'EstadoAsis',
              type: 'combobox',
              options: {
                panelHeight: 'auto',
                data: [{
                  value: '',
                  text: 'Todos'
                }, {
                  value: 'No asistio',
                  text: 'No asistio'
                }, {
                  value: 'Asistio',
                  text: 'Asistio'
                }],
                onChange: function(value) {
                  if (value == '') {
                    dg{{ $_mid }}.datagrid('removeFilterRule', 'EstadoAsis');
                  } else {
                    dg{{ $_mid }}.datagrid('addFilterRule', {
                      field: 'EstadoAsis',
                      op: 'equal',
                      value: value
                    });
                  }
                  dg{{ $_mid }}.datagrid('doFilter');
                }
              }
            }
          ]);
        });
      </script>
      <div class="datagrid-toolbar" id="tbptasis{{ $_mid }}" style="display:inline-block">
        @if (Auth::user()->permisos == 1)
          <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square correcto" onclick="setOficialPT();" id="darpt">Asignar oficialidad PT</a></div>
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="cleanOficialPT();" id="quitarpt">Quitar oficialidad PT</a></div>
          <!--      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-tag fa-lg" onclick="visita_asignar(1);" id="quitarpt">Adicionar visitante de otra logia</a></div>     -->
        @endif
      </div>
      <div id="dlgasispt{{ $_mid }}" class="easyui-dialog" style="width:300px;height:450px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
        <table id="dgasispt{{ $_mid }}" class="easyui-datagrid" style="height:410px;" data-options="url: '{{ $_controller }}/get_oficiales',queryParams:{_token: tokenModule}," toolbar="#toolbarasispt2{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
          fitColumns="true" singleSelect="true">
          <thead>
            <tr>
              <th data-options="field:'ck',checkbox:true"></th>
              <th field="id" sortable="false" hidden="true">ID</th>
              <th field="oficial"sortable="false">Cargo</th>
            </tr>
          </thead>
        </table>
        <div class="datagrid-toolbar" id="toolbarasispt2{{ $_mid }}" style="display:inline-block">
          @if (Auth::user()->permisos == 1)
            <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square correcto" plain="true" onclick="saveOficialPT();">Asignar cargo</a></div>
          @endif
        </div>
      </div>
      <script type="text/javascript">
        function cleanOficialPT() {

          $.messager.confirm('Confirm', 'Esta seguro quitar el cargo PT?', function(r) {
            if (r) {
              var rowm = $('#dg{{ $_mid }}').datagrid('getSelected');
              $.post('{{ $_controller }}/unset_oficialpt?_token={{ csrf_token() }}', {
                idmiembro: rowm.id
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

        function setOficialPT() {
          var row = $('#dg{{ $_mid }}').datagrid('getSelected');
          if (row) {
            $('#dgasispt{{ $_mid }}').datagrid('reload'); // reload the user data
            $('#dlgasispt{{ $_mid }}').dialog('open').dialog('setTitle', 'Asignar cargo PT en la tenida');
          } else {
            $.messager.show({
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>Seleccione miembro</div>'
            });
          }
        }

        function saveOficialPT() {
          var rowc = $('#dgasispt{{ $_mid }}').datagrid('getSelected');
          var rowm = $('#dg{{ $_mid }}').datagrid('getSelected');
          $.post('{{ $_controller }}/set_oficialpt?_token={{ csrf_token() }}', {
            idmiembro: rowm.id,
            idcargo: rowc.id
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
            $('#dlgasispt{{ $_mid }}').dialog('close'); // close the dialog
          }, 'json');
        }

        function clearTalleras() {
          $.post('{{ $_controller }}/filter_diatenida?_token={{ csrf_token() }}', {
            ftenida: "0"
          }, function(result) {
            if (result.success) {
              $('#dg{{ $_mid }}').datagrid('reload');
              $('#bplanilla').css({
                'display': 'none'
              });
              $('#bplanilla2').css({
                'display': 'none'
              });
              $('#bplanilla3').css({
                'display': 'none'
              });
              $('#titleftenida').text('');
            }
          }, 'json');
        }

        function doTalleras(value) {
          $.post('{{ $_controller }}/filter_taller?_token={{ csrf_token() }}', {
            taller: value
          }, function(result) {
            if (result.success) {
              $('#dgdia{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function fDatosAsistencia(value, campo) {
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

        function doGestionas(value) {
          valuel = $('#gestionas').val();
          $.post('{{ $_controller }}/filter_gestion?_token={{ csrf_token() }}', {
            gestion: valuel
          }, function(result) {
            if (result.success) {
              $('#dgdia{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }
      </script>
      <script>
        /*funciones nuevas*/
        function formatDetailTenida(value, row) {
          var erow = row.dia;
          if (row.valido == '1') {
            return '<a href="javascript:void(0)" onclick="edit_datoasis(' + erow + ');"><button><i class="fa fa-tag"></i> Datos</button></a><a href="javascript:void(0)" onclick="ver_asistencia(' + erow + ');"><button><i class="fa fa-bars"></i> Asist.</button></a>';
          } else if (row.valido == '2') {
            return 'Fecha futura';
          } else {
            return 'Fecha de receso';
          }
        }

        function edit_datoasis(valuer) {
          if (valuer > 0) {
            $('#fdatasis{{ $_mid }}').form('clear');
            $('#fdatasis{{ $_mid }}').form('load', '{{ $_controller }}/get_datasis?id=' + valuer);
            $('#dlgdatasis{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar datos de Tenida');
            url = '{{ $_controller }}/update_datasis?_token={{ csrf_token() }}&id=' + valuer;
          }
        }

        function save_dataAsis() {
          $('#fdatasis{{ $_mid }}').form('submit', {
            url: url,
            onSubmit: function() {
              return $(this).form('validate');
            },
            success: function(result) {
              var result = eval('(' + result + ')');
              if (result.success) {
                $.messager.show({ // show error message
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                });
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              } else {
                $.messager.show({ // show error message
                  title: 'Error en datos',
                  msg: '<div class="messager-icon messager-error"></div>' + result.Msg
                });
              }
              $('#fdatasis{{ $_mid }}').form('clear');
              $('#titleftenida').html(result.Ret);
              $('#dgdia{{ $_mid }}').datagrid('reload');
              $('#dlgdatasis{{ $_mid }}').dialog('close'); // close the dialog
            }
          });
        }

        function ver_asistencia(valuer) {
          $('#bplanilla').css({
            'display': 'none'
          });
          $('#bplanilla2').css({
            'display': 'none'
          });
          $('#bplanilla3').css({
            'display': 'none'
          });
          if (valuer > 0) {
            $.post('{{ $_controller }}/filter_diatenida?_token={{ csrf_token() }}', {
              ftenida: valuer
            }, function(result) {
              if (result.success) {
                $('#titleftenida').html(result.Msg);
                if (result.tres > 0)
                  $('#bplanilla3').css({
                    'display': 'block'
                  });
                if (result.dos > 0)
                  $('#bplanilla2').css({
                    'display': 'block'
                  });
                if (result.uno > 0)
                  $('#bplanilla').css({
                    'display': 'block'
                  });
                $('#dg{{ $_mid }}').datagrid('reload');
                $('#dg6{{ $_mid }}').datagrid('reload');
              }
            }, 'json');
          }
        }
        /**/
        function updateAsis(valueid, opcion, gradom) {
          if (valueid > 0) {
            $.post('{{ $_controller }}/update_asis', {
              idmiembro: valueid,
              idgrado: gradom,
              _token: tokenModule
            }, function(result) {
              if (result.success) {
                $.messager.show({ // show error message
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                });
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              } else {
                $.messager.show({ // show error message
                  title: 'Error en datos',
                  msg: '<div class="messager-icon messager-error"></div>' + result.Msg
                });
              }
            }, 'json');
          }
        }

        function gen_planillaas(grado) {
          window.open("{{ $_controller }}/gen_planilla?grado=" + grado);
        }
      </script>
    </div>
    <script type="text/javascript">
      $(function() {
        var dg7{{ $_mid }} = $('#dg7{{ $_mid }}').datagrid({
          url: '{{ $_controller }}/get_miembros',
          type: 'get',
          dataType: 'json',
          queryParams: {
            _token: tokenModule
          },
          toolbar: '#toolbar7{{ $_mid }}',
          pagination: false,
          fitColumns: true,
          rownumbers: true,
          singleSelect: true,
          remoteFilter: true,
          nowrap: true,
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
                hidden: true
              },
              {
                field: 'GradoActual',
                title: 'Grado'
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
                field: 'Miembro',
                title: 'Miembro'
              },
              {
                field: 'ultimoPago',
                title: 'Obolos'
              }
            ]
          ]
        });
        dg7{{ $_mid }}.datagrid('enableFilter', [{
          field: 'id',
          type: 'label'
        }, {
          field: 'ck',
          type: 'label'
        }, {
          field: 'Miembro',
          type: 'label'
        }, {
          field: 'ultimoPago',
          type: 'label'
        }, {
          field: 'GradoActual',
          type: 'label'
        }]);
      });
      /**/
      function visita_asignar(valuer) {
        if (valuer > 0) {
          $('#idcargo').val(valuer);
          $('#dg7{{ $_mid }}').datagrid('removeFilterRule', 'Paterno');
          $('#dg7{{ $_mid }}').datagrid('removeFilterRule', 'Materno');
          $('#dg7{{ $_mid }}').datagrid('removeFilterRule', 'Nombres');
          $('#dg7{{ $_mid }}').datagrid('doFilter');
          $('#dg7{{ $_mid }}').datagrid('reload');
          $('#dlg7{{ $_mid }}').dialog('open').dialog('setTitle', 'Buscar miembro para asignar visita');
        }
      }

      function visita_saveItem() {
        $('#dlg7{{ $_mid }}').dialog('close'); // close the dialog
        var row = $('#dg7{{ $_mid }}').datagrid('getSelected');
        $.post('{{ $_controller }}/add_visita', {
          _token: tokenModule,
          id: row.id
        }, function(result) {
          if (result.success) {
            $.messager.show({ // show error message
              title: 'Correcto',
              msg: result.Msg
            });
            $('#dg6{{ $_mid }}').datagrid('reload'); // reload the user data
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: result.Msg
            });
          }
        }, 'json');
      }

      function quitarVisita() {
        $.messager.confirm('Confirm', 'Esta seguro quitar Visita?', function(r) {
          if (r) {
            var rowm = $('#dg6{{ $_mid }}').datagrid('getSelected');
            $.post('{{ $_controller }}/quitar_visita', {
              id: rowm.idExtra,
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
              $('#dg6{{ $_mid }}').datagrid('reload'); // reload the user data
            }, 'json');
          }
        });
      }
    </script>
  </div>
  <div id="dlg7{{ $_mid }}" class="easyui-dialog" style="width:650px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
    <table id="dg7{{ $_mid }}" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="toolbar7{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg correcto" plain="true" onclick="visita_saveItem();">Asignar visita</a></div>
    </div>
  </div>
@endsection

@extends('layouts.easyuitab')
@section('content')
  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Asistencia extratemplos',collapsed:false,split:false" style="width:500px;">
      <table id="dgdia{{ $_mid }}" class="easyui-datagrid" style="width:100%;" data-options="url:'{{ $_controller }}/get_dias?_token={{ csrf_token() }}',nowrap:false,fitColumns:false,singleSelect:true,onLoadSuccess:function(){clearTalleret()}" toolbar="#toolbardia{{ $_mid }}"
        rownumbers="true" fitColumns="true">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'temaExtraTemplo',width:90">Tema</th>
            <th data-options="field:'GradoActual',width:30">Grado</th>
            <th data-options="field:'fechaExtraTemplo',width:36">Fecha</th>
            <th field="detail" formatter="formatDetailTenidaet">Opciones</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="display:inline-block">
        <div style="float:left;">
          <input id="gestionet" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2015,max:{{ $year }},editable:false,onChange: function(rec){filtrarDatos(rec,'gestion','dgdia');}" value="{{ $year }}">
        </div>
        <div style="float:left;">
          @if (count($logias) > 1)
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){doTalleret(rec);}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){doTalleret(rec);}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
        @if (Auth::user()->permisos == 1)
          <div style="float:left;">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newExtraTemp();">Nueva fecha</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editExtraTemp();">Editar fecha</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="delExtraTemp();">Borrar fecha</a>
          </div>
        @endif
      </div>
      <center>
        <div id="titleftenidat" style="font-size:16px;padding-top:15px;font-weight:bold;"></div>
        <span id="bplanillaet" style="display: none;"><br><a href="javascript:void(0)" onClick="gen_planillaet();"><button><i class="fa fa-file-text-o"></i> Generar planilla de asistencia</button></a></span>
      </center>
    </div>
    <div id="dlgnewextra{{ $_mid }}" class="easyui-dialog" style="width:500px;height:auto;padding:5px 5px" closed="true" buttons="#dlg-buttons" data-options="iconCls:'icon-save',modal:true">
      <form id="fmnewextra{{ $_mid }}" method="post" novalidate>
        <div style="margin-top:0px">
          <input name="temaExtraTemplo" id="tema" label="Tema del extratemplo:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%" required="required">
        </div>
        <div style="margin-top:4px">
          <select id="gradoet" name="gradoet" class="easyui-combobox" style="width:95%" label="Grado del Extra Temp:" labelWidth="130" labelPosition="left" required="required" data-options="panelHeight: 'auto',editable:false">
            <option value="1">Primero</option>
            <option value="2">Segundo</option>
            <option value="3">Tercero</option>
          </select>
        </div>
        <div style="margin-top:4px">
          <input id="fechaet" name="fechaet" label="Fecha del mes de:" labelWidth="130" labelPosition="left" type="text" class="easyui-datebox" required="required" style="width:95%">
        </div>
        <div style="margin-top:4px">
          <input name="instructor1" id="instructor1" label="Instructor 1:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%">
        </div>
        <div style="margin-top:4px">
          <input name="instructor2" id="instructor2" label="Instructor 2:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%">
        </div>
        <div style="margin-top:4px">
          <input name="instructor3" id="instructor3" label="Instructor 3:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:95%">
        </div>
      </form>
      <div id="dlg-buttons{{ $_mid }}">
        @if (Auth::user()->permisos == 1)
          <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveExtraTemp();" style="width:90px">Grabar</a>
        @endif
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgnewextra{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
      </div>
    </div>

    <script type="text/javascript">
      function delExtraTemp() {
        var row = $('#dgdia{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $.messager.confirm('Confirm', 'Esta seguro de borrar esta fecha?', function(r) {
            if (r) {
              $.post('{{ $_controller }}/destroy_dataet', {
                _token: tokenModule,
                idet: row.idExtraTemplo
              }, function(result) {
                if (!result.success) {
                  $.messager.show({ // show error message
                    title: 'Error',
                    msg: '<div class="messager-icon messager-error"></div>' + result.Msg
                  });
                } else {
                  $.messager.show({
                    title: 'Correcto',
                    msg: '<div class="messager-icon messager-info"></div>' + result.Msg
                  });
                  $('#dgdia{{ $_mid }}').datagrid('reload'); // reload the user data
                }
              }, 'json');
            }
          });
        }
      }

      function editExtraTemp() {
        var row = $('#dgdia{{ $_mid }}').datagrid('getSelected');
        if (row) {
          $('#dlgnewextra{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar fecha de extra-templo');
          $('#fmnewextra{{ $_mid }}').form('load', row);
          url = '{{ $_controller }}/update_datasiset?_token={{ csrf_token() }}&id=' + row.idExtraTemplo;
        }
      }

      function newExtraTemp() {
        $('#dlgnewextra{{ $_mid }}').dialog('open').dialog('setTitle', 'Nuevo Extratemplo');
        $('#fmnewextra{{ $_mid }}').form('clear');
        url = '{{ $_controller }}/save_extra?_token=' + tokenModule;
      }

      function saveExtraTemp() {
        $('#fmnewextra{{ $_mid }}').form('submit', {
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
              $('#fmnewextra{{ $_mid }}').form('clear');
              $('#titleftenidat').html(result.Ret);
              $('#dgdia{{ $_mid }}').datagrid('reload'); // reload the user data
              $('#dlgnewextra{{ $_mid }}').dialog('close'); // close the dialog
            }
          }
        });
      }

      function formatDetailTenidaet(value, row) {
        var erow = row.idExtraTemplo;
        return '<a href="javascript:void(0)" onclick="ver_asistenciaet(' + erow + ');"><button><i class="fa fa-thumbs-o-up"></i> Asistencia</button></a>';
      }

      function ver_asistenciaet(valuer) {
        $.post('{{ $_controller }}/filter_diatenida', {
          fextrat: valuer,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#titleftenidat').html(result.Msg);
            $('#bplanillaet').css({
              'display': 'block'
            });
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      }
    </script>
    <div data-options="region:'center'">
      <table id="dg{{ $_mid }}" style="width:100%;height:100%"></table>
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
                  field: 'Miembro',
                  title: 'Miembro'
                },
                {
                  field: 'ultimoPago',
                  title: 'Ultimo pago'
                },
                {
                  field: 'EstadoAsis',
                  title: 'Asistencia'
                },
                {
                  field: 'detail',
                  title: 'Opciones',
                  formatter: function(value, row) {
                    var erow = row.id;
                    if (row.FechaValida == '2') {
                      if ({{ Auth::user()->permisos }} == '1') {
                        if (row.idExtraTemploAsis == null)
                          return '<a href="javascript:void(0)" onclick="updateAsisET(' + erow + ');"><button><i class="fa fa-check"></i> Dar asistencia</button></a>';
                        else
                          return '<a href="javascript:void(0)" onclick="updateAsisET(' + erow + ');"><button><i class="fa fa-times"></i> Quitar asis.</button></a>';
                      } else {
                        return '';
                      }
                    } else if (row.FechaValida == '1') {
                      return 'No se realizo';
                    } else {
                      return 'Fecha Pasada';
                    }
                  },
                  width: 140
                }
              ]
            ],
            rowStyler: function(index, row) {
              if (row.EstadoAsis == 'Asistio') {
                if (row.pagoOk == '1')
                  return {
                    class: 'activo'
                  };
                else
                  return {
                    class: 'alerta'
                  };
              } else if (row.pagoOk == '1') {
                return {
                  class: 'normal'
                };
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
              field: 'Miembro',
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

        $(function() {
          $('#fechaet').datebox().datebox('calendar').calendar({
            validator: function(date) {
              var now = new Date();
              var d1 = new Date(now.getFullYear(), now.getMonth() - {{ $lmes }}, now.getDate());
              var d2 = new Date(now.getFullYear(), now.getMonth(), now.getDate());
              return d1 <= date && date <= d2;
            }
          });
        });

        function clearTalleret() {
          $.post('{{ $_controller }}/filter_diatenida', {
            fextrat: 0,
            _token: tokenModule,
          }, function(result) {
            if (result.success) {
              $('#dg{{ $_mid }}').datagrid('reload');
              $('#bplanillaet').css({
                'display': 'none'
              });
              $('#titleftenida').text('');
            }
          }, 'json');
        }

        function doTalleret(value) {
          $.post('{{ $_controller }}/filter_taller', {
            taller: value,
            _token: tokenModule,
          }, function(result) {
            if (result.success) {
              $('#dgdia{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function doGestionet(value) {
          valuel = $('#gestionet').val();
          $.post('{{ $_controller }}/filter_gestion', {
            _token: tokenModule,
            gestion: valuel
          }, function(result) {
            if (result.success) {
              $('#dgdia{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function updateAsisET(valueid) {
          if (valueid > 0) {
            $.post('{{ $_controller }}/update_asiset', {
              _token: tokenModule,
              idmiembro: valueid
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

        function gen_planillaet() {
          window.open("{{ $_controller }}/gen_planilla");
        }

        function filtrarDatos(value, campo, dtgrid = 'dg') {
          $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
            _token: tokenModule,
            valor: value,
            filtro: campo
          }, function(result) {
            if (result.success) {
              $('#' + dtgrid + '{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }
      </script>
    </div>
  </div>

@endsection

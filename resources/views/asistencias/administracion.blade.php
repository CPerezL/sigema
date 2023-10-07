@extends('layouts.easyuitab')
@section('content')

  <div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',title:'Fechas de los trabajos',collapsed:false,split:false" style="width:650px;">
      <table id="dgdia{{ $_mid }}" class="easyui-datagrid" style="width:100%;" data-options="url:'{{ $_controller }}/get_dias?_token={{ csrf_token() }}',nowrap:false,fitColumns:false,singleSelect:true,onLoadSuccess:function(){clearTallerasCls()}" toolbar="#toolbardia{{ $_mid }}" rownumbers="true"
        fitColumns="true">
        <thead>
          <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'dia',width:25" hidden="true">Dia</th>
            <th data-options="field:'fechaTrabajo',width:42">F. de trabajo</th>
            <th data-options="field:'fechaCierre',width:42">F. de cierre</th>
            <th data-options="field:'numeroActa1',width:30">A. 1er</th>
            <th data-options="field:'numeroActa2',width:30">A. 2do</th>
            <th data-options="field:'numeroActa3',width:30">A. 3ro</th>
            <th data-options="field:'grado',width:25">Grado</th>
            <th field="detail" formatter="formatDetailTenidaCls">Opciones</th>
          </tr>
        </thead>
      </table>
      <div class="datagrid-toolbar" id="toolbardia{{ $_mid }}" style="padding:5px;height:auto;display:inline-block">

        <div style="margin-bottom:5px">
          @if (Auth::user()->permisos == 1)
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg activo" onclick="nfecha_asis();" id="darasisfe">Adicionar fecha</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square fa-lg inactivo" onclick="nfecha_delete();" id="darasisfe">Quitar fecha</a>
          @endif
        </div>
        <div>
          <b>&nbsp;&nbsp;Gestion: </b><input id="gestionas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2019,max:{{ $year }},editable:false,onChange: function(rec){filtrarAsisAdm(rec,'gestion',2);}" value="{{ $year }}">
          @if (count($logias) > 1)
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:true,onChange: function(rec){doTallerasCls(rec);}">
              <option value="0">Seleccionar talller</option>
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}">{{ $logg }}</option>
              @endforeach
            </select>
          @else
            <select id="filtro" name="filtro" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){doTallerasCls(rec);}">
              @foreach ($logias as $key => $logg)
                <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
              @endforeach
            </select>
          @endif
        </div>
      </div>
      <div id="dlgdatasis{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:5px 5px" closed="true" buttons="#dlgdatasis-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fdatasis{{ $_mid }}" method="post" novalidate>
          <input name="idAsistenciaData" id="idAsistenciaData" type="hidden">
          <div style="margin-bottom:5px">
            <span style="color:indianred"> * Colocar el maximo grado al que se subio en la tenida</span>
          </div>
          <div style="margin-bottom:5px">
            <select id="grado" class="easyui-combobox" name="grado" style="width:95%" label="Grado de la tenida:" labelWidth="130" labelPosition="left" data-options="panelHeight:'auto',editable:false">
              <option value="0">No seleccionado</option>
              <option value="1">Primero</option>
              <option value="2">Segundo</option>
              <option value="3">Tercero</option>
              <option value="4">Maestro Instalado</option>
            </select>
          </div>
          <div style="margin-top:4px">
            <input class="easyui-datebox" name="fechaCierreForm" style="width:95%" label="Fecha de cierre:" labelPosition="left" type="text" required="required" labelWidth="130">
          </div>
          <div style="margin-bottom:5px">
            <span style="color:indianred"> ** Usar solo numero</span><br>
            <span style="color:indianred"> *** Si no se abrio dejar en cero el numero de acta</span>
          </div>
          <div style="margin-bottom:5px">
            <input class="easyui-numberspinner" name="numeroActa1" style="width:100%" data-options="min:0,max:50,editable:true" label="Nro acta 1er grado" labelWidth="130">
          </div>
          <div style="margin-bottom:5px">
            <input class="easyui-numberspinner" name="numeroActa2" style="width:100%" data-options="min:0,max:50,editable:true" label="Nro acta 2do grado" labelWidth="130">
          </div>
          <div style="margin-bottom:5px">
            <input class="easyui-numberspinner" name="numeroActa3" style="width:100%" data-options="min:0,max:50,editable:true" label="Nro acta 3er grado" labelWidth="130">
          </div>
        </form>
        <div id="dlgdatasis-buttons{{ $_mid }}">
          @if (Auth::user()->permisos == 1)
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="save_dataAsisCls();" style="width:90px">Grabar</a>
          @endif
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgdatasis{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
        </div>
      </div>
    </div>
    <div data-options="region:'center'">

      <table id="dg{{ $_mid }}" style="width:100%;height:100%" toolbar="#tbptasis{{ $_mid }}"></table>
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
                  field: 'oficial',
                  title: 'Oficialidad'
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
                    if ({{ Auth::user()->permisos }} == '1') {
                      return '<a href="javascript:void(0)" onclick="updateAsisCls(' + erow + ',0);"><button style="color:red"><i class="fa fa-times"></i> Quitar asis.</button></a>';
                    } else {
                      return '';
                    }
                  },
                  width: 130
                }
              ]
            ],
            rowStyler: function(index, row) {
              if (row.EstadoAsis == 'Asistio') {
                if (row.pagoOk == '1')
                  return {class:'activo'};
                else
                  return {class:'alerta'};
              } else if (row.pagoOk == '1') {
                return {class:'normal'};
              } else {
                return {class:'inactivo'};
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
              type: 'label'
            }
          ]);
        });
      </script>
      <div class="datagrid-toolbar" id="tbptasis{{ $_mid }}">
        @if (Auth::user()->permisos == 1)
          <div style="float:left;padding-right: 10px;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg aqua" onclick="setMiembroAsis();" id="darasis">Adicionar asistente</a></div>
        @endif
      </div>
      <div id="dlgmie{{ $_mid }}" class="easyui-dialog" style="width:650px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true">
        <table id="dgmie{{ $_mid }}" style="width:auto;height:456px"></table>
        <div class="datagrid-toolbar" id="toolbarmie{{ $_mid }}">
          @if (Auth::user()->permisos == 1)
            <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square fa-lg teal" onclick="miembro_saveItem();">Asignar asistencia</a></div>
          @endif
        </div>
      </div>
      <script type="text/javascript">
        function filtrarAsisAdm(value, campo,grid=1) {
          $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
            _token: tokenModule,
            valor: value,
            filtro: campo
          }, function(result) {
            if (result.success) {
                if(grid==='2')
              $('#dgdia{{ $_mid }}').datagrid('reload');
              else
              $('#dg{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function setMiembroAsis() {
          $('#dlgmie{{ $_mid }}').dialog('open').dialog('setTitle', 'Buscar miembro para asignar asistencia');
          $('#dgmie{{ $_mid }}').datagrid('removeFilterRule', 'NombreCompleto');
          $('#dgmie{{ $_mid }}').datagrid('reload');
        }

        function miembro_saveItem() {
          var rowm = $('#dgmie{{ $_mid }}').datagrid('getSelected');
          $.post('{{ $_controller }}/set_asistencia?_token={{ csrf_token() }}', {
            idmiembro: rowm.id
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
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
            $('#dlgmie{{ $_mid }}').dialog('close'); // close the dialog
          }, 'json');
        }
        $(function() {
          var dgmie{{ $_mid }} = $('#dgmie{{ $_mid }}').datagrid({
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
                  title: 'ID'
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
          dgmie{{ $_mid }}.datagrid('enableFilter', [{
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
        function clearTallerasCls() {
          $.post('{{ $_controller }}/filter_diatenida?_token={{ csrf_token() }}', {
            ftenida: "0"
          }, function(result) {
            if (result.success) {
              $('#dg{{ $_mid }}').datagrid('reload');
              $('#titleftenida').text('');
            }
          }, 'json');
        }

        function doTallerasCls(value) {
          $.post('{{ $_controller }}/filter_taller?_token={{ csrf_token() }}', {
            taller: value
          }, function(result) {
            if (result.success) {
              $('#dgdia{{ $_mid }}').datagrid('reload');
            }
          }, 'json');
        }

        function doGestionasCls(value) {
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
        function formatDetailTenidaCls(value, row) {
          return '<a href="javascript:void(0)" onclick="edit_datoasisCls(' + row.dia + ',' + row.mes + ');"><button><i class="fa fa-tag"></i> Datos</button></a><a href="javascript:void(0)" onclick="ver_asistenciaCls(' + row.dia + ',' + row.mes +
            ');"><button><i class="fa fa-thumbs-o-up"></i> Asist.</button></a>';
        }

        function edit_datoasisCls(valued, valuem) {
          $('#fdatasis{{ $_mid }}').form('clear');
          $('#fdatasis{{ $_mid }}').form('load', '{{ $_controller }}/get_datasis?_token={{ csrf_token() }}&id=' + valued + '&mes=' + valuem);
          $('#dlgdatasis{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar datos de Tenida');
          url = '{{ $_controller }}/update_datasis?_token=' + tokenModule;
        }

        function save_dataAsisCls() {
          $('#fdatasis{{ $_mid }}').form('submit', {
            url: url,
            onSubmit: function() {
              return $(this).form();
            },
            success: function(result) {
              var result = eval('(' + result + ')');
              if (result.success) {
                $.messager.show({ // show error message
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              } else {
                $.messager.show({ // show error message
                  title: 'Error en datos',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              }
              $('#fdatasis{{ $_mid }}').form('clear');
              $('#titleftenida').html(result.Ret);
              $('#dgdia{{ $_mid }}').datagrid('reload');
              $('#dlgdatasis{{ $_mid }}').dialog('close'); // close the dialog
            }
          });
        }

        function ver_asistenciaCls(valued, valuem) {
          if (valued > 0) {
            $.post('{{ $_controller }}/filter_diatenida?_token={{ csrf_token() }}', {
              ftenida: valued,
              mtenida: valuem
            }, function(result) {
              if (result.success) {
                $('#titleftenida').html(result.Msg);
                $('#dg{{ $_mid }}').datagrid('reload');
              }
            }, 'json');
          }
        }
        /**/
        function updateAsisCls(valueid, opcion) {
          $.messager.confirm('Confirm', 'Esta seguro quitar esta asistencia?', function(r) {
            if (r) {
              if (valueid > 0) {
                $.post('{{ $_controller }}/update_asis?_token={{ csrf_token() }}', {
                  idmiembro: valueid
                }, function(result) {
                  if (result.success) {
                    $.messager.show({ // show error message
                      title: 'Correcto',
                      msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                    });
                    $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
                  } else {
                    $.messager.show({ // show error message
                      title: 'Error en datos',
                      msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                    });
                  }
                }, 'json');
              }
            }
          });
        }

        function nfecha_asis() {
          taller = $("#filtro option:selected").val();
          if (taller > 0) {
            $('#fnfecha{{ $_mid }}').form('clear');
            $('#dlgnfecha{{ $_mid }}').dialog('open').dialog('setTitle', 'Adicionar fecha excepcional');
            url = '{{ $_controller }}/save_fechan?_token=' + tokenModule;
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>Seleccione Taller primero</div>'
            });
          }
        }

        function savenfecha_asis() {
          $('#fnfecha{{ $_mid }}').form('submit', {
            url: url,
            onSubmit: function() {
              return $(this).form();
            },
            success: function(result) {
              var result = eval('(' + result + ')');
              if (result.success) {
                $.messager.show({ // show error message
                  title: 'Correcto',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dgdia{{ $_mid }}').datagrid('reload');
              } else {
                $.messager.show({ // show error message
                  title: 'Error en datos',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              }
              $('#fnfecha{{ $_mid }}').form('clear');
              $('#dlgnfecha{{ $_mid }}').dialog('close'); // close the dialog
            }
          });
        }

        function nfecha_delete() {
          var row = $('#dgdia{{ $_mid }}').datagrid('getSelected');
          if (row) {
            $.messager.confirm('Confirm', 'Esta seguro de borrar este dato, se borrara todos los asistentes?', function(r) {
              if (r) {
                $.post('{{ $_controller }}/destroy_fechan?_token={{ csrf_token() }}', {
                  _token: tokenModule,
                  id: row.idAsistenciaData
                }, function(result) {
                  if (!result.success) {
                    $.messager.show({ // show error message
                      title: 'Error',
                      msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                    });
                  } else {
                    $.messager.show({
                      title: 'Correcto',
                      msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                    });
                    $('#dgdia{{ $_mid }}').datagrid('reload'); // reload the user data
                  }
                }, 'json');
              }
            });
          } else {
            $.messager.show({ // show error message
              title: 'Error',
              msg: '<div class="messager-icon messager-error"></div><div>Seleccione fecha primero</div>'
            });
          }
        }
      </script>
      <div id="dlgnfecha{{ $_mid }}" class="easyui-dialog" style="width:400px;height:auto;padding:5px 5px" closed="true" buttons="#dlgnfecha-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
        <form id="fnfecha{{ $_mid }}" method="post" novalidate>
          <div style="margin-bottom:5px">
            <select id="grado" class="easyui-combobox" name="gradofn" style="width:95%" label="Grado de la tenida:" labelWidth="130" labelPosition="left" data-options="panelHeight:'auto',editable:false">
              <option value="0">No seleccionado</option>
              <option value="1">Primero</option>
              <option value="2">Segundo</option>
              <option value="3">Tercero</option>
              <option value="4">Maestro Instalado</option>
            </select>
          </div>
          <div style="margin-top:4px">
            <input class="easyui-datebox" name="fechaTrabajofn" style="width:95%" label="Fecha de Trabajo:" labelPosition="left" type="text" required="required" labelWidth="130">
          </div>
          <div style="margin-top:4px">
            <input class="easyui-datebox" name="fechaCierrefn" style="width:95%" label="Fecha de cierre:" labelPosition="left" type="text" required="required" labelWidth="130">
          </div>
        </form>
        <div id="dlgnfecha-buttons{{ $_mid }}">
          @if (Auth::user()->permisos == 1)
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="savenfecha_asis();" style="width:90px">Grabar</a>
          @endif
          <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgnfecha{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
        </div>
      </div>
    </div>
  </div>
  </div>

@endsection

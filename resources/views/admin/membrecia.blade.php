@extends('layouts.easyuitab')
@section('content')
  <script type="text/javascript">
    $.extend($.fn.validatebox.defaults.rules, {
      justText: {
        validator: function(value, param) {
          var pattern = /^[0-9A-Z@_-]+$/;
          return value.match(pattern);
        },
        message: 'Solo Mayusculas y numeros.'
      },
      telefono: {
        validator: function(value, param) {
          var pattern = /^[0-9+]+$/;
          return value.match(pattern);
        },
        message: 'Solo Numeros, sin espacios.'
      },
      minLength: {
        validator: function(value, param){
            return value.length >= param[0];
        },
        message: 'Numero mayor a {0} digitos.'
    }
    });
  </script>
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="url: '{{ $_controller }}/get_datos',
         queryParams:{
         _token: tokenModule
         },
         rowStyler: function (index, row) {
         if (row.upago == '1') {
         return {class:'activo'};
         } else if (row.upago == '2') {
         return {class:'alerta'};
         } else {
         return {class:'inactivo'};
         }
         }
         "
    toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
    <thead>
      <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th field="id" hidden="true">ID</th>
        <th field="nlogia">R:.L:.S:. Actual</th>
        <th field="LogiaAfiliada" hidden="true">Afiliado</th>
        <th field="estadotxt">Estado</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Miembro">Miembro</th>
        <th field="Ingreso">Ingreso</th>
        <th field="FechaIniciacion">F. Iniciacion</th>
        <th field="FechaAumentoSalario">F. Aumento S.</th>
        <th field="FechaExaltacion">F Exaltacion</th>
        <th field="ultimoPago">Ultimo Pago</th>
        <th field="lastlogin">Ultimo Ingreso</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">
      <a href="javascript:void(0)" id="mb" class="easyui-menubutton" data-options="menu:'#mmedit',iconCls:'fa fa-edit fa-lg teal'" plain="false">Editar Miembro</a>
      <div id="mmedit" style="width:160px;">
        <div data-options="iconCls:'fa fa-birthday-cake fa-lg teal'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="mie_edit_dp();">Datos personales</a></div>
        @if (Auth::user()->nivel > 2)
          <div data-options="iconCls:'fa fa-dot-circle-o fa-lg teal'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="mie_edit_dm();">Datos Masonicos</a></div>
        @endif
        @if (Auth::user()->nivel > 3)
          <div data-options="iconCls:'fa fa-users fa-lg teal'"><a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="mie_edit_du();">Datos de Usuario</a></div>
        @endif
      </div>
    </div>
    @if (Auth::user()->nivel > 2 && Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o fa-lg blue2" onclick="mie_new_dn();">Adicionar Miembro</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-minus fa-lg red" onclick="me_deleteDatos();">Eliminar</a></div>
    @endif
    <div style="float:left;">
      @if (count($oris) == 1)
        @foreach ($oris as $key => $ogg)
          <input name="foriente" id="foriente" value="{{ $key }}" type="hidden">
        @endforeach
      @else
        <select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'oriente');}">
          <option value="0" selected="selected">Todos los Orientes</option>
          @foreach ($oris as $key => $ogg)
            <option value="{{ $key }}">{{ $ogg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;">
      @if (count($valles) > 1)
        <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'valle');}">
          <option value="0">Logias de todos los valles</option>
          @foreach ($valles as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        @foreach ($valles as $key => $vogg)
          <input name="fvalle" id="fvalle" value="{{ $key }}" type="hidden">
          <a href="#" class="easyui-linkbutton" plain="true"><b>VALLE: {{ $vogg }}</b></a>
        @endforeach
      @endif
    </div>

    <div style="float:left;">
      @if (count($logias) > 1)
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
          <option value="0" selected="selected">Todas las Logias</option>
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @else
        <select id="filtro" name="taller" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'taller');}">
          @foreach ($logias as $key => $logg)
            <option value="{{ $key }}">{{ $logg }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div style="float:left;"><b>Grado:</b>
      <select id="gradom" name="gradom" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'grado');}">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div style="float:left;"><b>&nbsp;&nbsp;Estado: </b>
      <select id="estado" name="estado" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'estado');}">
        @foreach ($estados as $keye => $esta)
          <option value="{{ $keye }}">{{ $esta }}</option>
        @endforeach
        <option value="4" selected="selected">Todos</option>
      </select>
    </div>

    <div style="float:left;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchMie,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-no" plain="true" onclick="clearSearchMie();"></a>
    </div>
  </div>
  <!--filtros de datos -->
  <script type="text/javascript">
    /*funcnode filtro de datos*/
    function filtrarDatosMie(value, campo) {
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

    function doSearchMie(value) {
      filtrarDatosMie(value, 'palabra');
    }

    function clearSearchMie() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filtrarDatosMie('', 'palabra');
    }
  </script>
  <script type="text/javascript">
    function me_deleteDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('@lang('mess.question')', '@lang('mess.questdel')', function(r) {
          if (r) {
            $.post('{{ $_controller }}/destroy_datos', {
              _token: tokenModule,
              id: row.id
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
                $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }
  </script>
  <!--                                                                                DATOS PERSONALES                                                                    --->
  <div id="dmiedp{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bmiedp{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmiedp{{ $_mid }}" method="post" enctype="multipart/form-data" novalidate><input type="hidden" name="id" value="" />
      <div id="tt" class="easyui-tabs" style="width:100%;height:auto;">
        <div title="Datos Principales" style="padding:5px;display:none;">
          <div style="margin-top:4px;margin-left:20px"><img src='' id="photo{{ $_mid }}" height="90" /></div>
          <div style="margin-top:2px"><input class="easyui-filebox" style="width:100%" name="foto" data-options="buttonText: 'Elegir foto',accept: 'image/*'"></div>
          <div style="margin-top:2px">
            <select id="Estado" name="Estado" class="easyui-combobox" style="width:100%" label="Estado:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
              @foreach ($estados as $keye => $esta)
                <option value="{{ $keye }}">{{ $esta }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:2px"><input name="Paterno" id="Paterno" label="Primer Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"{{ $editable }}></div>
          <div style="margin-top:2px"><input name="Materno" id="Materno" label="Segundo Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"{{ $editable }}></div>
          <div style="margin-top:2px"><input name="Nombres" id="Nombres" label="Nombres:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required" {{ $editable }}></div>
          <div style="margin-top:2px"><input name="FechaNacimiento" id="FechaNacimiento" label="Fecha de Nacimiento:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%"></div>
          <div style="margin-top:2px">
            <select name="Pais" label="Pais Nacimiento:" labelPosition="left" labelWidth="130" class="easyui-combobox" style="width:100%" required="required" panelHeight="300px" editable="false">
              @foreach ($paises as $ey => $pass)
                <option value="{{ $pass }}">{{ $pass }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:2px"><input name="LugarNacimiento" label="Lugar de Nac.:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px"><input name="Celular" label="Celular:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="prompt:'Ej:02116082 o +585540001',validType:['telefono','minLength[5]']"></div>
          <div style="margin-top:2px"><input name="CI" label="Cedula de Id.:" data-options="validType:'justText'" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" class="solo-numero"></div>
          <div style="margin-top:2px"><input name="email" label="Correo Email:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="prompt:'Email valido',validType:'email'"></div>
        </div>
        <div title="Datos Secundarios" style="padding:5px;display:none;">
          <div style="margin-top:2px">
            <select id="ProfesionOficio" name="ProfesionOficio" class="easyui-combobox" style="width:100%" label="Profesion:" labelWidth="130" labelPosition="left" required="required" panelHeight="300px" editable="false">
              @foreach ($profes as $ey => $profs)
                <option value="{{ $profs }}">{{ $profs }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:2px"><input name="Trabajo" label="Oficina/trabajo:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px"><input name="TelefonoOficina" label="Telefono Oficina:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px"><input name="Cargo" label="Cargo:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px"><input name="NombreMadre" label="Nombre Madre:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px"><input name="NombrePadre" label="Nombre Padre:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px">
            <select id="EstadoCivil" name="EstadoCivil" class="easyui-combobox" style="width:100%" label="Estado Civil:" labelWidth="130" labelPosition="left" panelHeight="auto" editable="false" required="required">
              <option value="Soltero">Soltero</option>
              <option value="Casado">Casado</option>
              <option value="Viudo">Viudo</option>
              <option value="Divorciado">Divorciado</option>
              <option value="union libre">Concubinato</option>
            </select>
          </div>
          <div style="margin-top:2px"><input name="NombreEsposa" label="Nombre Esposa:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px">
            <textarea id="Domicilio" name="Domicilio" class="easyui-textbox" data-options="multiline:true" label="Domicilio:" labelPosition="left" style="width:100%;height:60px"></textarea>
          </div>
          <div style="margin-top:2px"><input name="TelefonoDomicilio" label="Telefono Domicilio" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
        </div>
      </div>
    </form>
  </div>
  <div id="bmiedp{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="mie_save_dp();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dmiedp{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function mie_edit_dp() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dmiedp{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.editar', ['job' => 'Miembro'])');
        urlform = '{{ $_controller }}/get_form?_token={{ csrf_token() }}&task=1&id=' + row.id;
        $('#fmiedp{{ $_mid }}').form('load', urlform);
        if (row.foto) {
          $('#photo{{ $_mid }}').attr('src', 'media/miembros/' + row.foto);
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src')); //recraga imagen
          //console.log('Image:' + $('#photoImage0').attr('src'));
        } else {
          $('#photo{{ $_mid }}').attr('src', 'media/miembros/foto.jpg');
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src'));
        }
        url = '{{ $_controller }}/update_datos?_token={{ csrf_token() }}&task=1';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function mie_save_dp() {
      $('#fmiedp{{ $_mid }}').form('submit', {
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
            $('#fmiedp{{ $_mid }}').form('clear');
            $('#dmiedp{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--                                                                                DATOS MASONICOS                                                                    --->
  <div id="dmiedm{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bmiedm{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmiedm{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" /><input type="hidden" name="control" value="" />
      {{-- <div id="tt" class="easyui-tabs" style="width:100%;height:auto;">
        <div title="Datos de ingreso" style="padding:20px;display:none;"> --}}
      <div style="margin-top:4px">
        <select id="jurisdiccion" class="easyui-combobox" name="jurisdiccion" style="width:100%" label="Jurisdiccion:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
          <option value="0">Sin datos</option>
          <option value="1">Regular G.L.S.P.</option>
          <option value="2">Regularizado G.L.S.P.</option>
          <option value="3">Regular de otro oriente</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <select class="easyui-combobox" name="LogiaIniciacion" style="width:100%" label="Logia Iniciacion:" labelWidth="130" labelPosition="left" editable="false">
          <option value="0">Sin datos</option>
          @foreach ($logias as $key => $llog)
            <option value="{{ $key }}">{{ $llog }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:2px"><input name="FechaIniciacion" id="FechaIniciacion" label="Fecha Iniciacion:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%" {{ $editable }}></div>
      <div style="margin-top:2px">
        <select class="easyui-combobox" name="LogiaAumento" style="width:100%" label="Logia Aumento:" labelWidth="130" labelPosition="left" editable="false">
          <option value="0">Sin datos</option>
          @foreach ($logias as $key => $llog)
            <option value="{{ $key }}">{{ $llog }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:2px"><input name="FechaAumentoSalario" id="FechaAumentoSalario" label="Fecha Aumento Salario:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%" {{ $editable }}></div>
      <div style="margin-top:2px">
        <select class="easyui-combobox" name="LogiaExaltacion" style="width:100%" label="Logia Exaltacion:" labelWidth="130" labelPosition="left" editable="false">
          <option value="0">Sin datos</option>
          @foreach ($logias as $key => $llog)
            <option value="{{ $key }}">{{ $llog }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:2px"><input name="FechaExaltacion" id="FechaExaltacion" label="Fecha Exaltacion:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%" {{ $editable }}></div>
      <div style="margin-top:2px">
        <select id="LogiaActual" class="easyui-combobox" name="LogiaActual" style="width:100%" label="Logia Actual:" labelWidth="130" labelPosition="left" editable="false">
          @foreach ($logias as $key => $llog)
            <option value="{{ $key }}">{{ $llog }}</option>
          @endforeach
        </select>
      </div>
      {{-- <div style="margin-top:2px">
            <select id="LogiaAfiliada" class="easyui-combobox" name="LogiaAfiliada" style="width:100%" label="Logia Afiliada:" labelWidth="130" labelPosition="left" editable="false">
              <option value="0">Ninguna</option>
              @foreach ($logias as $key => $llog)
                <option value="{{ $key }}">{{ $llog }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:4px"><input name="DecretoAfiliacion" id="DecretoAdVitam" label="Decreto Afiliacion:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:2px">
            <select id="LogiaInspector" class="easyui-combobox" name="LogiaInspector" style="width:100%" label="Inspector de Rito en:" labelWidth="130" labelPosition="left" editable="false">
              <option value="0">No es inspector</option>
              @foreach ($logias as $key => $llog)
                <option value="{{ $key }}">{{ $llog }}</option>
              @endforeach
            </select>
          </div> --}}
      <div style="margin-top:2px">
        <select id="Grado" class="easyui-combobox" name="Grado" style="width:100%" label="Grado Actual:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto"{{ $editable }} editable="false">
          <option value="0">Ninguno</option>
          <option value="1">Aprendiz</option>
          <option value="2">Compañero</option>
          <option value="3">Maestro</option>
          <option value="4">V:.M:. o Ex V:.M:.</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <select id="Miembro" class="easyui-combobox" name="Miembro" style="width:100%" label="Tipo miembro:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto"{{ $editable }} editable="false">
          @foreach ($miembros as $timie)
            <option value="{{ $timie }}">{{ $timie }}</option>
          @endforeach
        </select>
      </div>
      {{-- <div style="margin-top:2px">
            <select id="socio" name="socio" class="easyui-combobox" style="width:100%" label="<b>Tipo Socio:</b>" labelWidth="130" labelPosition="left" required="required" panelHeight="auto">
              <option value="1" selected="selected">Iniciado Ordinario</option>
              <option value="2">Reincorporado Tipo 1</option>
              <option value="3">Reincorporado Tipo 2</option>

            </select>
          </div> --}}
      <div style="margin-top:4px"><input type="text" name="mesesprofano" id="mesesprofano" label="Meses en sueño:" labelPosition="left" labelWidth="130" class="easyui-numberspinner" data-options="min:0,editable:true" style="width:100%"></div>
  </div>
  {{-- <div title="Datos de permanencia" style="padding:20px;display:none;">
           <div style="margin-top:0px"><input name="FechaHonorario" id="FechaHonorario" label="Fecha Honorario:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%"></div>
          <div style="margin-top:4px"><input name="DecretoHonorario" id="DecretoHonorario" label="Decreto Honorario:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:4px">
            <select id="LogiaHonorario" class="easyui-combobox" name="LogiaHonorario" style="width:100%" label="Logia Honorario:" labelWidth="130" labelPosition="left" editable="false">
              <option value="0">Ninguna</option>
              @foreach ($logias as $key => $llog)
                <option value="{{ $key }}">{{ $llog }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:4px"><input name="FechaAdMeritum" id="FechaAdMeritum" label="Fecha Ad Meritum:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%"></div>
          <div style="margin-top:4px"><input name="DecretoAdMeritum" id="DecretoAdMeritum" label="Decreto Ad Meritum:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:4px">
            <select id="LogiaAdMeritum" class="easyui-combobox" name="LogiaAdMeritum" style="width:100%" label="Logia Ad Meritum:" labelWidth="130" labelPosition="left" editable="false">
              <option value="0">Ninguna</option>
              @foreach ($logias as $key => $llog)
                <option value="{{ $key }}">{{ $llog }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:4px"><input name="FechaAdVitam" id="FechaAdVitam" label="Fecha Ad Vitam:" labelPosition="left" labelWidth="130" class="easyui-datebox" style="width:100%"></div>
          <div style="margin-top:4px"><input name="DecretoAdVitam" id="DecretoAdVitam" label="Decreto Ad Vitam:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
          <div style="margin-top:4px">
            <select id="LogiaAdVitam" class="easyui-combobox" name="LogiaAdVitam" style="width:100%" label="Logia Ad Vitam:" labelWidth="130" labelPosition="left" editable="false">
              <option value="0">Ninguna</option>
              @foreach ($logias as $key => $llog)
                <option value="{{ $key }}">{{ $llog }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:2px">
            <select id="Estado" name="Estado" class="easyui-combobox" style="width:100%" label="Estado:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
              @foreach ($estados as $keye => $esta)
                <option value="{{ $keye }}">{{ $esta }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-top:2px">
            <textarea id="observaciones" name="observaciones" class="easyui-textbox" data-options="multiline:true" label="Observaciones:" labelPosition="top" style="width:100%;height:160px"></textarea>
          </div>
        </div>
      </div> --}}
  </form>
  </div>
  <div id="bmiedm{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="mie_save_dm();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dmiedm{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function mie_edit_dm() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dmiedm{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar datos masonicos');
        urlf = '{{ $_controller }}/get_form?_token={{ csrf_token() }}&task=2&id=' + row.id;
        $('#fmiedm{{ $_mid }}').form('load', urlf);
        url = '{{ $_controller }}/update_datos?_token={{ csrf_token() }}&task=2';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function mie_save_dm() {
      $('#fmiedm{{ $_mid }}').form('submit', {
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
            $('#fmiedm{{ $_mid }}').form('clear');
            $('#dmiedm{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--                                                                                DATOS DE USUARIO                                                                    --->
  <div id="dmiedu{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bmiedu{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmiedu{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" />
      <div style="margin-top:0px"><input name="Paterno" id="Paterno" label="Primer Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"{{ $editable }} required="required"></div>
      <div style="margin-top:0px"><input name="Materno" id="Materno" label="Segundo Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"{{ $editable }}></div>
      <div style="margin-top:0px"><input name="Nombres" id="Nombres" label="Nombres:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"{{ $editable }}></div>
      <div style="margin-top:0px"><input name="username" id="username" data-options="validType:'justText'" label="ID de Usuario:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"{{ $editable }}></div>
      <div style="margin-top:0px"><input name="email" id="email" label="Email de Usuario:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" data-options="prompt:'Email valido',validType:'email'"></div>
      <div style="margin-top:0px"><input name="claven" id="claven" label="Clave de acceso:" prompt="Ingresar clave si quiere cambiar" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:0px"><input name="lastlogin" label="Ultimo Acceso:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" disabled="true"></div>
    </form>
  </div>
  <div id="bmiedu{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="mie_save_du();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dmiedu{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function mie_edit_du() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dmiedu{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.editar', ['job' => 'Datos Usuario'])');
        urlf = '{{ $_controller }}/get_form?_token={{ csrf_token() }}&task=3&id=' + row.id;
        $('#fmiedu{{ $_mid }}').form('load', urlf);
        url = '{{ $_controller }}/update_datos?_token={{ csrf_token() }}&task=3&id=' + row.id;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-warning"></div>@lang('mess.alertdata')'
        });
      }
    }

    function mie_save_du() {
      $('#fmiedu{{ $_mid }}').form('submit', {
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
            $('#fmiedu{{ $_mid }}').form('clear');
            $('#dmiedu{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--                                                                                DATOS NUEVOS DE USUARIO                                                                    --->
  <div id="dmiedn{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#bmiedn{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmiedn{{ $_mid }}" method="post" novalidate><input type="hidden" name="id" value="" />
      <div style="margin-top:0px"><input name="Paterno" id="Paterno" label="Primer Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:0px"><input name="Materno" id="Materno" label="Segundo Apellido:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
      <div style="margin-top:0px"><input name="Nombres" id="Nombres" label="Nombres:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%" required="required"></div>
      <div style="margin-top:4px">
        <input name="username" id="username" style="width:100%;" class="easyui-textbox" data-options="validType:'justText'" label="Cedula de Identidad*:" labelPosition="left" labelWidth="150" required="required">
        <div style="margin-top:4px">
          <select id="jurisdiccion" class="easyui-combobox" name="jurisdiccion" style="width:100%" label="Jurisdiccion:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
            <option value="0">Sin datos</option>
            <option value="1">Regular G.L.S.P.</option>
            <option value="2">Regularizado G.L.S.P.</option>
            <option value="3">Regular de otro oriente</option>
          </select>
        </div>
        <div style="margin-top:2px">
          <select id="LogiaActual" class="easyui-combobox" name="LogiaActual" style="width:100%" label="Logia Actual:" labelWidth="130" labelPosition="left" required="required" editable="false">
            @foreach ($logias as $key => $llog)
              <option value="{{ $key }}">{{ $llog }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-top:2px">
          <select id="Grado" class="easyui-combobox" name="Grado" style="width:100%" label="Grado Actual:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
            <option value="0">Ninguno</option>
            <option value="1">Aprendiz</option>
            <option value="2">Compañero</option>
            <option value="3">Maestro</option>
            <option value="4">V:.M:. o Ex V:.M:.</option>
          </select>
        </div>
        <div style="margin-top:2px">
          <select id="Miembro" class="easyui-combobox" name="Miembro" style="width:100%" label="Tipo miembro:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
            @foreach ($miembros as $timie)
              <option value="{{ $timie }}">{{ $timie }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-top:2px">
          <select id="Estado" class="easyui-combobox" name="Estado" style="width:100%" label="Estado miembro:" labelWidth="130" labelPosition="left" required="required" panelHeight="auto" editable="false">
            @foreach ($estados as $keyes => $estas)
              <option value="{{ $keyes }}">{{ $estas }}</option>
            @endforeach
          </select>
        </div>
    </form>
  </div>
  <div id="bmiedn{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="mie_save_dn();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dmiedn{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
    function mie_new_dn() {
      $('#dmiedn{{ $_mid }}').dialog('open').dialog('setTitle', '@lang('mess.nuevo', ['job' => 'Miembro'])');
      url = '{{ $_controller }}/save_datos?_token={{ csrf_token() }}&task=7';
    }

    function mie_save_dn() {
      $('#fmiedn{{ $_mid }}').form('submit', {
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
            $('#fmiedn{{ $_mid }}').form('clear');
            $('#dmiedn{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
@endsection

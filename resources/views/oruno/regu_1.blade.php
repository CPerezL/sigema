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
              field: 'idTramite',
              title: 'Tramite',
              hidden:'true'
            },
            {
              field: 'nivel',
              title: 'Estado de tramite'
            },
            {
              field: 'valle',
              title: 'Valle',
              hidden:'true'
            },
            {
              field: 'nLogia',
              title: 'R:.L:.S:.'
            },
            {
              field: 'numero',
              title: 'Nro'
            },
            {
              field: 'fInsinuacion',
              title: 'Fecha de Proposición'
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
              field: 'documento',
              title: 'Documento'
            },
            {
              field: 'foto',
              title: 'Foto',
              hidden: 'false'
            },
            {
              field: 'fechaModificacion',
              title: 'Modificacion'
            }
          ]
        ]
      });
    });
  </script>
  <div class="easyui-layout" data-options="fit:true">
    <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"></table>
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square fa-lg correcto" onclick="treg0_newTramite();">Tramite Nuevo</a></div>
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-check-square fa-lg alerta" onclick="reg0_editDatos();">Editar Tramite</a></div>
      <div style="float:left;">
        @if (count($logias) > 1)
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            <option value="0">Seleccionar talller</option>
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}">R:.L:.S:. {{ $logg }} </option>
            @endforeach
          </select>
        @else
          <select id="filtrot0" name="filtrot0" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filterDatos(rec,'taller');}">
            @foreach ($logias as $key => $logg)
              <option value="{{ $key }}" selected="selected">R:.L:.S:. {{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function reg0_editDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite); // load from URL
        if (row.foto) {
          $('#photo{{ $_mid }}').attr('src', '{{ $_folder }}media/fotos/' + row.foto);
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src')); //recraga imagen
          //console.log('Image:' + $('#photoImage0').attr('src'));
        } else {
          $('#photo{{ $_mid }}').attr('src', '{{ $_folder }}media/fotos/foto.jpg');
          $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src'));
        }
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Editar tramite');
        url = '{{ $_controller }}/update_tramite?_token={{ csrf_token() }}&idTra=' + row.idTramite;
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione tramite primero</div>'
        });
      }
    }

    function treg0_newTramite() {
      taller = $('#filtrot0').combobox('getValue');
      if (taller > 0) {
        $('#fmini0{{ $_mid }}').form('load', '{{ $_controller }}/get_nlogia?_token={{ csrf_token() }}'); // load from URL
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Formulario de registro de tramite para Regularizacion');
        url = '{{ $_controller }}/save_tramite?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Taller no seleccionado</div>'
        });
      }
    }

    function treg0_saveDatos() {
      $('#fmini0{{ $_mid }}').form('submit', {
        url: url,
        onSubmit: function() {
          return $(this).form('validate');
        },
        success: function(result) {
          var result = eval('(' + result + ')');
          //alert(result.success);
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
            $('#fmini0{{ $_mid }}').form('clear');
            $('#dlgini0{{ $_mid }}').dialog('close'); // close the dialog
            $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }
  </script>
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini0{{ $_mid }}" class="easyui-dialog" style="width:650px;height:650px;" closed="true" buttons="#dlgini0-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" enctype="multipart/form-data" method="post" novalidate>
      <input type="hidden" name="idTramite" />
      <input type="hidden" name="foto" id="foto" />
      <div style="width:98%;padding:5px;">
        <div style="margin-bottom:0px">
          {{-- <input class="easyui-textbox" name="valle" style="width:25%;" data-options="label:'<b>Valle:</b>',readonly:'true',editable:false" labelWidth="45"> --}}
          <input class="easyui-textbox" name="logiaName" style="width:93%;" data-options="label:'<b>R:.L:.S:.:</b>',readonly:'true',editable:false" labelWidth="70">
        </div>
      </div>
      <div id="tt" class="easyui-tabs" style="width:100%;height:auto;">
        <div title="1.- Información Personal" style="padding:8px;display:none;">
          <div style="margin-bottom:0px"><input class="easyui-filebox" name="fotografia" style="width:100%" data-options="label:'Fotografia*:',required:false,buttonText: 'Escoger foto'" labelWidth="210" accept=".jpg,.jpeg,.png"></div>
          <div style="margin-bottom:5px"><img src='' id="photo{{ $_mid }}" height="120" /></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="documento" style="width:100%" data-options="label:'Nro. Doc. de Identificación*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="nombres" style="width:100%" data-options="label:'Nombres*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="apPaterno" style="width:100%" data-options="label:'Apellido Paterno*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="apMaterno" style="width:100%" data-options="label:'Apellido Materno:'" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-datebox" name="fechaNac" style="width:80%" data-options="label:'Fecha de Nacimiento*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px">
            <select name="nacionalidad" label="Pais Nacimiento:*" labelPosition="left" labelWidth="210" class="easyui-combobox" style="width:100%" required="required" panelHeight="300px" editable="false">
              @foreach ($paises as $ey => $pass)
                <option value="{{ $pass }}">{{ $pass }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="lugarNac" style="width:100%" data-options="label:'Lugar de nacimiento*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px">
            <select id="profesion" name="profesion" class="easyui-combobox" style="width:100%" label="Profesion:" labelWidth="210" labelPosition="left" required="required" panelHeight="300px" editable="false">
              @foreach ($profes as $ey => $profs)
                <option value="{{ $profs }}">{{ $profs }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="domicilio" style="width:100%" data-options="label:'Dirección de domicilio*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="fonoDomicilio" style="width:100%" data-options="label:'Telefono domicilio:',required:false" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="celular" style="width:100%" data-options="label:'Telefono celular*:',required:true" labelWidth="210"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="email" style="width:100%" data-options="label:'Correo electronico*:',required:true,validType:'email'" labelWidth="210"></div>
          <div style="margin-bottom:5px">
            <div style="display:inline-block;width:120px;"><span><label>Estado Civil: </label></span></div>
            <input class="easyui-radiobutton" name="estadoCivil" value="0" label="Soltero" data-options="labelPosition:'after'" labelWidth="50" checked="checked">
            <input class="easyui-radiobutton" name="estadoCivil" value="1" label="Casado" data-options="labelPosition:'after'" labelWidth="55">
            <input class="easyui-radiobutton" name="estadoCivil" value="2" label="Viudo" data-options="labelPosition:'after'" labelWidth="50">
            <input class="easyui-radiobutton" name="estadoCivil" value="3" label="Divorciado" data-options="labelPosition:'after'" labelWidth="80">
            <input class="easyui-radiobutton" name="estadoCivil" value="4" label="Concubinato" data-options="labelPosition:'after'" labelWidth="100">
          </div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="esposa" style="width:100%" data-options="label:'Nombres y apellidos de la Esposa:',required:false" labelWidth="240"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="padre" style="width:100%" data-options="label:'Nombres y apellidos del Padre*:',required:true" labelWidth="240"></div>
          <div style="margin-bottom:5px"><input class="easyui-textbox" name="madre" style="width:100%" data-options="label:'Nombres y apellidos de la Madre*:',required:true" labelWidth="240"></div>
        </div>
        <div title="2.- Información del tramite" style="padding:8px;display:none;">
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="maestro" style="width:100%" data-options="label:'Presentado por Q.H. Maestro 1*:',required:true" labelWidth="240"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="maestro2" style="width:100%" data-options="label:'Presentado por Q.H. Maestro 2*:',required:true" labelWidth="240"></div>
            <div style="margin-bottom:2px"><input class="easyui-datebox" name="fInsinuacion" style="width:80%" data-options="label:'Fecha de Proposición*:',required:true" labelWidth="290"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaInsinuacion" style="width:100%" data-options="label:'No. Acta - Grado 1ro de proposición*:',required:true" labelWidth="290"></div>
            <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaAprobPase" style="width:80%" data-options="label:'Fecha acta aprobacion de pase*:',required:true" labelWidth="290"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="actaAprobPase" style="width:100%" data-options="label:'No. Acta - Grado 1ro de informe de pase*:',required:true" labelWidth="290"></div>
            <div style="margin-bottom:5px">
              <select id="grado" class="easyui-combobox" name="grado" style="width:100%" label="Maximo grado que acreditara:" labelWidth="200" labelPosition="left" required="required" panelHeight="auto">
                <option value="0">Ninguno</option>
                <option value="1">Aprendiz</option>
                <option value="2">Compañero</option>
                <option value="3">Maestro</option>
                <option value="4">V:.M:. o Ex V:.M:.</option>
              </select>
            </div>
            <div style="margin-bottom:5px"><input class="easyui-filebox" name="cuadernillo" style="width:100%" data-options="label:'Doc. de Respaldo de (los) grado(s)*:',required:false,buttonText: 'Escoger PDF'" labelWidth="240" accept=".pdf,.PDF"></div>
            <div style="margin-bottom:10px"><input class="easyui-filebox" name="antecedentes" style="width:100%" data-options="label:'Antecedentes Penales*:',required:false,buttonText: 'Escoger PDF'" labelWidth="210" accept=".pdf,.PDF"></div>
            <div style="margin-bottom:10px"><input class="easyui-filebox" name="antecedentes2" style="width:100%" data-options="label:'Antecedentes Policiales*:',required:false,buttonText: 'Escoger PDF'" labelWidth="210" accept=".pdf,.PDF"></div>
            <div style="margin-bottom:2px"><input class="easyui-datebox" name="fechaJuramento" style="width:80%" data-options="label:'Fecha de Juramentación*:',required:true" labelWidth="290"></div>
          </div>
        <div title="3.- Información Extra" style="padding:8px;display:none;">
          <div class="easyui-panel" title="Información laboral" style="width:100%;padding:5px;">
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="empresa" style="width:100%" data-options="label:'Empresa',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="direccionEmpresa" style="width:100%" data-options="label:'Dirección:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="fonoEmpresa" style="width:100%" data-options="label:'Telefono:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="cargo" style="width:100%" data-options="label:'Cargo:',required:false" labelWidth="210"></div>
          </div>
          <div class="easyui-panel" title="¿ Es candidato extranjero ?" style="width:100%;padding:5px;">
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="resideBolivia" style="width:100%" data-options="label:'Tiempo de residencia en el pais (en meses):',required:false" labelWidth="300"></div>
            <center><label><h4>QQ.HH. que lo avalan (Si es candidato extranjero)</h4></label></center>
            <div style="margin-bottom:2px"><input class="easyui-textbox" name="aval1" style="width:100%" data-options="label:'Q:.H:.:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="aval1Logia" style="width:100%" data-options="label:'Resp:. Log:. No.:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:2px"><input class="easyui-textbox" name="aval2" style="width:100%" data-options="label:'Q:.H:.:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="aval2Logia" style="width:100%" data-options="label:'Resp:. Log:. No.:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:2px"><input class="easyui-textbox" name="aval3" style="width:100%" data-options="label:'Q:.H:.:',required:false" labelWidth="210"></div>
            <div style="margin-bottom:5px"><input class="easyui-textbox" name="aval3Logia" style="width:100%" data-options="label:'Resp:. Log:. No.:',required:false" labelWidth="210"></div>
          </div>
        </div>

      </div>
    </form>
  </div>
  <div id="dlgini0-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="treg0_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
  </div>
  <script type="text/javascript">
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
@endsection

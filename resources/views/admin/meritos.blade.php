@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;"
    data-options="url: '{{ $_controller }}/get_datos',
         queryParams:{
         _token: tokenModule
         },
         rowStyler: function (index, row) {
         if (row.obs == '0') {
         return {class:'activo'};
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
        <th field="obstxt">Antecedentes</th>
        <th field="valle">Valle</th>
        <th field="logia">R:.L:.S:. Actual</th>
        <th field="numero">Nro.</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="Miembro">Miembro</th>
        <th field="FechaIniciacion">F. Iniciacion</th>
        <th field="FechaAumentoSalario">F. Aumento S.</th>
        <th field="FechaExaltacion">F Exaltacion</th>
        <th field="ultimoPago">Ultimo Pago</th>
      </tr>
    </thead>
  </table>
  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    @if (Auth::user()->nivel > 2 && Auth::user()->permisos == 1)
      <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o fa-lg blue2" onclick="ini_listaDatos();">Gestionar Antecedentes</a></div>
    @endif
    <div style="float:left;">
      <div style="padding-bottom:2px;">
        @if (count($valles) > 1)
          <select id="fvalle" name="fvalle" class="easyui-combobox" data-options="panelHeight:600,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosMie(rec,'valle');}">
            <option value="0">Mostrar logia de todos los valles</option>
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
    </div>
    <div style="float:left;">
      <div style="padding-bottom:2px;">
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
    </div>
    <div style="float:right;"><input class="easyui-searchbox" style="width:150px" data-options="searcher:doSearchMie,prompt:'Buscar apellido'" id="searchbox{{ $_mid }}" value="{!! $palabra ?? '' !!}">
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
  <!--   formulario de modificacion de datos de tramite  -->
  <div id="dlgini0{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;" closed="true" buttons="#dlgini0-buttons{{ $_mid }}" data-options="iconCls:'icon-save',modal:true">
    <form id="fmini0{{ $_mid }}" method="post" novalidate>
      <input type="hidden" name="id" />
      <input type="hidden" name="idMerito" />
      <div style="margin-top:5px"><input class="easyui-datebox" name="fechaRegistro" style="width:100%" data-options="label:'Fecha de Denuncia *:',required:true" labelWidth="170"></div>
      <div style="margin-top:5px">
        <select id="idTipoMerito" name="idTipoMerito" class="easyui-combobox" style="width:100%" label="Tipo de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          @foreach ($tmeritos as $key => $mmm)
            <option value="{{ $key }}">{{ $mmm }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-top:5px">
        <select id="estado" name="estado" class="easyui-combobox" style="width:100%" label="Estado de observación:" labelWidth="170" labelPosition="left" panelHeight="auto" required="required">
          <option value="0" selected>Registrado</option>
          <option value="1">Descartado/Sin respaldo</option>
          <option value="2">Aprobado/Comprobado</option>
        </select>
      </div>
      <div style="margin-top:2px">
        <textarea id="descripcion" name="descripcion" class="easyui-textbox" data-options="multiline:true,required:true" label="Observación:" labelPosition="top" style="width:100%;height:200px"></textarea>
      </div>
    </form>
  </div>
  </div>
  </div>
  <div id="dlgini0-buttons{{ $_mid }}">
    @if (Auth::user()->permisos == 1)
      <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="merito_saveDatos();" style="width:90px">Grabar</a>
    @endif
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgini0{{ $_mid }}').dialog('close');" style="width:90px">Cancelar/Cerrar</a>
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

    function doSearchData(value) {
      filterDatos(value, 'palabra');
    }

    function clearSearchData() {
      $('#searchbox{{ $_mid }}').searchbox('clear');
      filterDatos('', 'palabra');
    }
    $(function() {
      var dgobs{{ $_mid }} = $('#dgobs{{ $_mid }}').datagrid({
        url: '{{ $_controller }}/get_meritos?idt=0',
        type: 'get',
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
              field: 'fechaRegistro',
              title: 'Fecha'
            },
            {
              field: 'descripcion',
              title: 'Descripcion'
            },
            {
              field: 'tipotxt',
              title: 'Tipo'
            },
            {
              field: 'estadotxt',
              title: 'Estado'
            }
          ]
        ]
      });
    });

    function merito_saveDatos() {
      $('#fmini0{{ $_mid }}').form('submit', {
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
            $('#fmini0{{ $_mid }}').form('clear');
            $('#dlgini0{{ $_mid }}').dialog('close'); // close the dialog
            $('#dgobs{{ $_mid }}').datagrid('reload'); // reload the user data
          }
        }
      });
    }

    function delete_merito() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $.messager.confirm('Confirm', 'Esta seguro de borrar este dato?', function(r) {
          if (r) {
            $.post('{{ $_controller }}/delete_merito', {
              _token: tokenModule,
              id: row.idMerito
            }, function(result) {
              if (!result.success) {
                $.messager.show({ // show error message
                  title: 'Error',
                  msg: '<div class="messager-icon messager-error"></div><div>' + result.Msg + '</div>'
                });
              } else {
                $.messager.show({
                  title: '@lang('mess.ok')',
                  msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                });
                $('#dgobs{{ $_mid }}').datagrid('reload'); // reload the user data
              }
            }, 'json');
          }
        });
      } else {
        $.messager.show({
          title: '@lang('mess.alert')',
          msg: '<div class="messager-icon messager-alert"></div>@lang('mess.alertdata')'
        });
      }
    }

    function ini_listaDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#dgobs{{ $_mid }}').datagrid('reload', '{{ $_controller }}/get_meritos?idt=' + row.id);
        $('#dlgobs{{ $_mid }}').dialog('open').dialog('setTitle', 'Antecedentes de miembro');
        url = '{{ $_controller }}/save_merito?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione Miembro</div>'
        });
      }
    }

    function obs_close(value) {
      $('#dlgobs{{ $_mid }}').dialog('close')
      $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
    }

    function ini_meritoDatos() {
      var row = $('#dg{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', row);
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Formulario de registro de antecedente');
        url = '{{ $_controller }}/save_merito?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione Miembro</div>'
        });
      }
    }

    function ini_meritoEdita() {
      var row = $('#dgobs{{ $_mid }}').datagrid('getSelected');
      if (row) {
        $('#fmini0{{ $_mid }}').form('load', row);
        $('#dlgini0{{ $_mid }}').dialog('open').dialog('setTitle', 'Modificar antecedente');
        url = '{{ $_controller }}/save_merito?_token={{ csrf_token() }}';
      } else {
        $.messager.show({
          title: 'Error',
          msg: '<div class="messager-icon messager-error"></div><div>Seleccione antecedente</div>'
        });
      }
    }
  </script>
  <div id="dlgobs{{ $_mid }}" class="easyui-dialog" style="width:660px;height:500px;padding:0px" closed="true" data-options="iconCls:'icon-save',modal:true,closable:false">
    <table id="dgobs{{ $_mid }}" style="width:auto;height:456px"></table>
    <div class="datagrid-toolbar" id="toolbarobs{{ $_mid }}">
      @if (Auth::user()->permisos == 1)
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square activo" onclick="ini_meritoDatos();">Adicionar antecedente</a></div>
        <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-plus-square alerta" onclick="ini_meritoEdita();">Modificar antecedente</a></div>
        @if (Auth::user()->nivel > 4)
          <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="far fa-minus-square danger" onclick="delete_merito();">Borrar antecedente</a></div>
        @endif
      @endif
      <div style="float:right;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-remove danger" onclick="obs_close();">Cerrar</a></div>
    </div>
  </div>
@endsection

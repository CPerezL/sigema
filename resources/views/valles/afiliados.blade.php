@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
  queryParams:{
  _token: tokenModule
  }
  " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true" fitColumns="true"
    singleSelect="true" pageList="[20,40,50,100]" pageSize="100" enableFilter="true">
    <thead>
      <tr>
        <th field="nlogia" sortable="false">R:.L:.S:. Afilada</th>
        <th field="LogiaActual" sortable="false">Log Actual</th>
        <th field="GradoActual" sortable="false">Grado</th>
        <th field="NombreCompleto" sortable="false">Nombre completo</th>
        <th field="Miembro" sortable="false">Miembro</th>
        <th field="Ingreso" sortable="false">Ingreso</th>
        <th field="ultimoPago" sortable="false">Ultimo Pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"> Valles:
      <input id="idVallecomap" name="idVallecomap" class="easyui-combobox"
        data-options="
        queryParams: {
          _token: tokenModule
         },
        panelHeight:'auto',
        valueField: 'idValle',
        textField: 'valle',
        url: '{{ $_controller }}/get_valles',
        onChange: function(rec){
        fillCombocomap(rec);
        }"
        value='0'>
    </div>

    <div style="float:left;">Taller:<input id="logiacomap" name="logiacomap" style="width:300px;"></div>
    <script type="text/javascript">
      var prueba = 0;
      $('#logiacomap').combobox({
        url: '{{ $_controller }}/get_logias?_token={{ csrf_token() }}&valleid=' + 0,
        panelHeight: '400',
        valueField: 'nlogia',
        textField: 'logian',
        method: 'get'
      });

      function fillCombocomap(valleid) {
        $('#logiacomap').combobox('options').url = '{{ $_controller }}/get_logias?_token={{ csrf_token() }}&valleid=' + valleid;
        $('#logiacomap').combobox('reload');
      }
    </script>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Grado: </b>
      <select id="grado{{ $_mid }}" name="grado{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><b>&nbsp;&nbsp;Miembro: </b>
      <select id="estado{{ $_mid }}" name="estado{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Regular</option>
        <option value="2">Honorario</option>
        <option value="3">Ad-Vitam</option>
        <option value="4">Ad-Meritum</option>
        <option value="5">Fallecido</option>
      </select>
    </div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-laptop correcto" onclick="r_listacom{{ $_mid }}();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg excel" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
  </div>
  <!-- Funciones javascript -->
  <!--filtros de datos -->
  <script type="text/javascript">
    function r_listacom{{ $_mid }}() {
      var valle = $('#idVallecomap').combobox('getValue');
      var logia = $('#logiacomap').combobox('getValue');
      var grado = $('#grado{{ $_mid }}').combobox('getValue');
      var estado = $('#estado{{ $_mid }}').combobox('getValue');
      if (valle > 0 || logia > 0) {
        $.post('{{ $_controller }}/set_datos', {
          _token: tokenModule,
          valle: valle,
          taller: logia,
          estado: estado,
          grado: grado
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        alert('seleccione un valle o logia para el reporte');
      }
    }
  </script>
@endsection

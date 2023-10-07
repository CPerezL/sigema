@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="100">
    <thead>
      <tr>
        <th field="valletxt">Valle</th>
        <th field="logia">R:.L:.S:.</th>
        <th field="taller">Nro</th>
        <th field="GradoActual">Grado</th>
        <th field="miembro">Miembro</th>
        <th field="GradoActual">Grado</th>
        <th field="NombreCompleto">Nombre completo</th>
        <th field="cuotaInicial">Cuotas Pag.</th>
        <th field="mesesDescuento">Cuotas Desc.</th>
        <th field="numeroCuotas">Total</th>
        <th field="mesesRango">Meses Descuento</th>
        <th field="ultimoPago">U. Pago</th>
        <th field="fechaPagoNuevo">N. Pago</th>
        <th field="estadotxt">Estado</th>
        <th field="fechaCreacion">Fecha Creacion</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;Gestion: </b><input id="gestionreas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year }},editable:false" value="{{ $year }}"></div>
    <div style="float:left;"><b>&nbsp;Taller: </b>
      <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'500'">
        <option value="0" selected="selected"> SELECCIONAR VALLE </option>
        @foreach ($valles as $key => $vall)
          <option value="{{ $key }}">{{ $vall }}</option>
        @endforeach
      </select>
    </div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg correcto" onclick="r_habilitados{{ $_mid }}();">Ver reporte</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o excel" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print archivo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>

  </div>
  <!-- Funciones javascript actuales -->
  <script type="text/javascript">
    function r_habilitados{{ $_mid }}() {
      var valleid = $('#valle{{ $_mid }}').combobox('getValue');
      var gesreas = $('#gestionreas').val();
      if (valleid > 0) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valleid,
          gestion: gesreas,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        alert('seleccione un valle para el reporte');
      }
    }
  </script>
@endsection

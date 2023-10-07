@extends('layouts.easyuitab')
@section('content')
<table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="true"
  fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="100">
  <thead>
    <tr>
      <th field="numero" sortable="false">#</th>
      <th field="taller" sortable="false">RLS</th>
      <th field="GradoActual" sortable="false">Grado</th>
      <th field="nombre" sortable="false">Nombre completo H:.</th>
      <th field="ntenidas" sortable="false">Nro Ten.</th>
      <th field="ordinaria" sortable="false">Ten. Ord.</th>
      <th field="extratemplo" sortable="false">Extra Tem.</th>
      <th field="ultimoPago" sortable="false">Ult. pago</th>
    </tr>
  </thead>
</table>

<div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
  <div style="float:left;"><b>&nbsp;&nbsp;Gestion: </b><input id="gestionreas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year }},editable:false" value="{{ $year }}"></div>
  <div class="datagrid-btn-separator"></div>
  <div style="float:left;"><b>&nbsp;&nbsp;Taller: </b>
    <select id="logia{{ $_mid }}" name="logia{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'500'">
      <option value="0" selected="selected"> SELECCIONAR TALLER </option>
      @foreach ($logias as $key => $logg)
        <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
      @endforeach
    </select>
  </div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="r_habilitados{{ $_mid }}();">Ver reporte</a></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg red" onclick="toPdf{{ $_mid }}();">Exportar a PDF</a></div>
</div>
<!-- Funciones javascript actuales -->
<script type="text/javascript">
  function r_habilitados{{ $_mid }}() {
    var logia = $('#logia{{ $_mid }}').combobox('getValue');
    var gesreas = $('#gestionreas').val();
    if (logia > 0) {
      $.post('{{ $_controller }}/set_datos', {
        _token: tokenModule,
        taller: logia,
        gestion: gesreas
      }, function(result) {
        if (result.success) {
          $('#dg{{ $_mid }}').datagrid('reload');
        }
      }, 'json');
    } else {
      alert('seleccione un logia para el reporte');
    }
  }

  function toPdf{{ $_mid }}() {
    var nnlogia = $('#logia{{ $_mid }}').combobox('getText');
    var body = $('#dg{{ $_mid }}').datagrid('toArray');
    var docDefinition = {
      pageSize: 'LETTER',
      pageMargins: [40, 130, 40, 90],
      content: [{
          margin: [0, 0, 0, 0],
          alignment: 'center',
          text: nnlogia,
          style: 'header'
        },
        {
          margin: [0, 0, 0, 10],
          alignment: 'center',
          text: 'Reporte de asistencia y obolos',
          style: 'header'
        },
        {
          table: {
            headerRows: 1,
            widths: ['7%', '4%', '7%', '*', '7%', '7%', '7%', '9%'],
            body: body
          }
        }
      ],
      defaultStyle: {
        fontSize: 10,
        bold: false
      },
      footer: function(currentPage, pageCount) {
        return {
          alignment: 'center',
          text: currentPage.toString() + ' de ' + pageCount,
          fontSize: 8
        };
      }
    };
    pdfMake.createPdf(docDefinition).download();
  }
</script>
@endsection

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
      <th field="numero">#</th>
      <th field="Miembro">Tipo</th>
      <th field="GradoActual">Grado</th>
      <th field="nombre">Nombre completo</th>
      <th field="ordinaria">Asist</th>
      <th field="extratemplo">Extra Temp</th>
      <th field="ultimoPago">Obolos Pago</th>
      <th field="edadnac">Edad</th>
      <th field="edadmas">Años MM.</th>
      <th field="ParaVM">V.M.</th>
      <th field="ParaVig">Vig.</th>
      <th field="ParaOf">Of.</th>
    </tr>
  </thead>
</table>

<div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
  <div style="float:left;"><b>&nbsp;&nbsp;Gestion: </b><input id="gestionreha" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year }},editable:false" value="{{ $year }}"></div>
  <div style="float:left;"><b>&nbsp;&nbsp;Taller: </b>
    <select id="logiaha" name="logiaha" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'500'">
      <option value="0" selected="selected"> SELECCIONAR TALLER </option>
      @foreach ($logias as $key => $logg)
        <option value="{{ $key }}">R:.L:.S:. {{ $logg }}</option>
      @endforeach
    </select>
  </div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg teal" onclick="rc_elegibles();">Ver reporte</a></div>
  <div class="datagrid-btn-separator"></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
  <div class="datagrid-btn-separator"></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
  <div class="datagrid-btn-separator"></div>
  <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg red" onclick="toPdfeleg();">Exportar a PDF</a></div>
</div>
<!-- Funciones javascript actuales -->
<script type="text/javascript">
  function rc_elegibles() {
    var logia = $('#logiaha').combobox('getValue');
    var gesreas = $('#gestionreha').val();
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
      alert('seleccione un taller para el reporte');
    }
  }

  function toPdfeleg() {
    var nnlogia = $('#logiaha').combobox('getText');
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
          text: 'Hermanos Maestros habilitados para ser elegidos Oficiales',
          style: 'header'
        },
        {
          table: {
            headerRows: 1,
            widths: ['3%', '6%', '10%', '*', '6%', '7%', '8%', '6%', '6%', '5%', '5%', '4%', '4%'],
            body: body
          }
        }
      ],
      defaultStyle: {
        fontSize: 9,
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

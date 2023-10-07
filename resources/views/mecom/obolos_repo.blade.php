@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%" data-options="url: '{{ $_controller }}/get_datos?_token={{ csrf_token() }}',
queryParams:{
_token: tokenModule
},
" toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="false" nowrap="false"
    fitColumns="true" singleSelect="true" pageList="[200]" pageSize="200">
    <thead>
      <tr>
        <th field="numero" sortable="false">#</th>
        <th field="gestion" sortable="false">GES</th>
        <th field="taller" sortable="false">RLS</th>
        <th field="nombre" sortable="false">Nombre completo H:.</th>
        <th field="ene" sortable="false">ENE</th>
        <th field="feb" sortable="false">FEB</th>
        <th field="mar" sortable="false">MAR</th>
        <th field="abr" sortable="false">ABR</th>
        <th field="may" sortable="false">MAY</th>
        <th field="jun" sortable="false">JUN</th>
        <th field="jul" sortable="false">JUL</th>
        <th field="ago" sortable="false">AGO</th>
        <th field="sep" sortable="false">SEP</th>
        <th field="oct" sortable="false">OCT</th>
        <th field="nov" sortable="false">NOV</th>
        <th field="dic" sortable="false">DIC</th>
        <th field="ultimoPago" sortable="false">Ult. pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Gestion: </b><input id="gestion_meclog" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year + 5 }},editable:false" value="{{ $year }}"></div>
    <div style="float:left;"><b>&nbsp;&nbsp;R:.L:.S:.</b>
      <select id="logia{{ $_mid }}" name="logia{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'500'">
        @if (count($logias) > 1)
        <option value="0" selected="selected"> SELECCIONAR TALLER </option>
        @foreach ($logias as $key => $logg)
          <option value="{{ $key }}">{{ $logg }}</option>
        @endforeach
        @else
        @foreach ($logias as $key => $logg)
          <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
        @endforeach

        @endif
      </select>
    </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg aqua" onclick="r_habilitados{{ $_mid }}();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg aquamarine" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg red" onclick="toPdf{{ $_mid }}();">Exportar a PDF</a></div>
  </div>
  <!-- Funciones javascript actuales -->
  <script type="text/javascript">
    function r_habilitados{{ $_mid }}() {
      var logia = $('#logia{{ $_mid }}').combobox('getValue');
      var gesreas = $('#gestion_meclog').val();
      if (logia > 0) {
        $.post('{{ $_controller }}/set_datos?_token={{ csrf_token() }}', {
          taller: logia,
          gestion: gesreas
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        $.messager.show({
          title: 'Atencion',
          msg: '<div class="messager-icon messager-info"></div>seleccione una Logia para el reporte'
        });
      }
    }

    function toPdf{{ $_mid }}() {
      var nnlogia = $('#logia{{ $_mid }}').combobox('getText');
      var body = $('#dg{{ $_mid }}').datagrid('toArray');
      var f = new Date();
      var fecha = f.getDate() + "/" + (f.getMonth() + 1) + "/" + f.getFullYear();

      var docDefinition = {
        pageSize: 'LETTER',
        pageOrientation: 'landscape',
        pageMargins: [20, 40, 20, 40],
        content: [{
            margin: [0, 0, 0, 0],
            alignment: 'center',
            text: nnlogia,
            style: 'header',
            fontSize: 9
          },
          {
            margin: [0, 0, 0, 10],
            alignment: 'center',
            text: 'Reporte de Obolos ' + fecha,
            style: 'header',
            fontSize: 9
          },
          {
            table: {
              headerRows: 1,
              body: body
            }
          }
        ],
        defaultStyle: {
          fontSize: 6,
          bold: false
        },
        footer: function(currentPage, pageCount) {
          return {
            alignment: 'center',
            text: currentPage.toString() + ' de ' + pageCount,
            fontSize: 7
          };
        }
      };
      pdfMake.createPdf(docDefinition).download();
    }
  </script>
@endsection

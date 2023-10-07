@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options="url: '{{ $_controller }}/get_datos',
       queryParams:{
       _token: tokenModule
       },
       " toolbar="#toolbar{{ $_mid }}" pagination="false" fitColumns="true" rownumbers="false"
    fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="100">
    <thead>
      <tr>
        <th field="numero" sortable="false">#</th>
        <th field="gestion" sortable="false">Gestion</th>
        <th field="taller" sortable="false">R:.L:.S:.</th>
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
        <th field="sumaPagos" sortable="false">Suma</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;"><b>&nbsp;&nbsp;Gestion: </b><input id="gestionreas" class="easyui-numberspinner" style="width:80px;" required="required" data-options="min:2010,max:{{ $year + 5 }},editable:false" value="{{ $year }}"></div>

    <div style="float:left;">
        @if (count($valles) > 1)
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'400'" style="width:165px;">
            <option value="0"> SELECCIONAR VALLE</option>
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}">{{ $logg }}</option>
            @endforeach
          </select>
        @else
        <select id="valle{{ $_mid }}" name="valle{{ $_mid }}" class="easyui-combobox" data-options="valueField: 'id',textField: 'text',editable:false,panelHeight:'auto'" style="width:165px;">
            @foreach ($valles as $key => $logg)
              <option value="{{ $key }}" selected="selected">{{ $logg }}</option>
            @endforeach
          </select>
        @endif
      </div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg correcto" onclick="r_habilitados{{ $_mid }}();">Ver reporte</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg excel" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg archivo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>
    <div class="datagrid-btn-separator"></div>
    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg danger" onclick="toPdf{{ $_mid }}();">Exportar a PDF</a></div>
  </div>
  <!-- Funciones javascript actuales -->
  <script type="text/javascript">
    function r_habilitados{{ $_mid }}() {
      var valle = $('#valle{{ $_mid }}').combobox('getValue');
      var gesreas = $('#gestionreas').val();
      if (valle > 0) {
        $.post('{{ $_controller }}/set_datos', {
          _token: tokenModule,
          valle: valle,
          gestion: gesreas
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        alert('seleccione un valle para el reporte');
      }
    }

    function toPdf{{ $_mid }}() {
      var nnlogia = 'Valle de ' + $('#valle{{ $_mid }}').combobox('getText');
      var body = $('#dg{{ $_mid }}').datagrid('toArray');
      var docDefinition = {
        pageSize: 'LETTER',
        pageOrientation: 'landscape',
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
            text: 'Reporte de Obolos',
            style: 'header'
          },
          {
            table: {
              headerRows: 1,
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

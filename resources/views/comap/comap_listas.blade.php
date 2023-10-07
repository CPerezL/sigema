@extends('layouts.easyuitab')
@section('content')
  <table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;"
    data-options="url: '{{ $_controller }}/get_datos',
queryParams:{
_token: tokenModule
},
rowStyler: function (index, row) {
if (row.pagoOk == '2')
{return {class:'activo'};}
else if (row.pagoOk == '1')
{ return {class:'alerta'};}
else
{return {class:'inactivo'};}
}
" toolbar="#toolbar{{ $_mid }}"
    pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[200]" pageSize="200">
    <thead>
      <tr>
        <th field="nlogia" sortable="false">R:.L:.S:. Actual</th>
        <th field="GradoActual" sortable="false">Grado</th>
        <th field="NombreCompleto" sortable="false">Nombre completo</th>
        <th field="Miembro" sortable="false">Miembro</th>
        <th field="ultimoPago" sortable="false">Ultimo Pago</th>
      </tr>
    </thead>
  </table>

  <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <div style="float:left;">Valle: <input id="vallecb" name="valle" value='0'> </div>
    <div style="float:left;"> <input id="logiacb" name="logia" value='0' style="width:300px"> </div>
    <script>
      $('#vallecb').combobox({
        url: '{{ $_controller }}/get_valles?_token=' + tokenModule,
        panelHeight: '350',
        valueField: 'idValle',
        textField: 'valle',
        method: 'get',
        onSelect: function(rec) {
          fillComboLogia(rec.idValle);
        },
      });

      function fillComboLogia(valleid) {
        $('#logiacb').combobox('reload', '{{ $_controller }}/get_logias?_token=' + tokenModule + '&valleid=' + valleid);
      }
      $('#logiacb').combobox({
        url: '',
        panelHeight: '350',
        valueField: 'nlogia',
        textField: 'logian',
        method: 'get',
        label: 'R:.L:.S:.:',
        labelWidth: '70',
        labelPosition: 'left'
      });
    </script>

    <div style="float:left;"><b> Grado: </b>
      <select id="grado{{ $_mid }}" name="grado{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Aprendiz</option>
        <option value="2">Compañero</option>
        <option value="3">Maestro</option>
        <option value="4">V:.M:. o Ex V:.M:.</option>
      </select>
    </div>

    <div style="float:left;"><b> Estado: </b>
      <select id="estado{{ $_mid }}" name="estado{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false">
        <option value="0" selected="selected">Todos</option>
        <option value="1">Regular</option>
        <option value="2">Honorario</option>
        <option value="3">Ad-Vitam</option>
        <option value="4">Ad-Meritum</option>
        <option value="5">Fallecido</option>
      </select>
    </div>
    <div style="float:left;"><b> Obolos: </b>
      <select id="resul{{ $_mid }}" name="resul{{ $_mid }}" class="easyui-combobox" data-options="panelHeight:'auto',valueField: 'id',textField: 'text',editable:false">
        <option value="0">Pagos al dia</option>
        <option value="1">Por entrar en mora</option>
        <option value="2">En mora</option>
        <option value="3" selected="selected">Todos</option>
      </select>
    </div>

    <div style="float:left;"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-plus-square-o fa-lg blue2" onclick="r_listacom{{ $_mid }}();">Ver reporte</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-excel-o fa-lg green" onclick="$('#dg{{ $_mid }}').datagrid('toExcel', 'reporte.xls');">Exportar a Excel</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-print fa-lg indigo" onclick="$('#dg{{ $_mid }}').datagrid('print', 'DataGrid');">Imprimir</a></div>

    <div style="float:left;"><a href="javascript:void(0);" class="easyui-linkbutton" iconCls="fa fa-file-pdf-o fa-lg red" onclick="toPdfeleg();">Exportar a PDF</a></div>
  </div>
  <!-- Funciones javascript -->
  <!--filtros de datos -->
  <script type="text/javascript">
    function r_listacom{{ $_mid }}() {
      var valle = $('#vallecb').combobox('getValue');
      var logia = $('#logiacb').combobox('getValue');
      var grado = $('#grado{{ $_mid }}').combobox('getValue');
      var estado = $('#estado{{ $_mid }}').combobox('getValue');
      var resul = $('#resul{{ $_mid }}').combobox('getValue');

      if (valle > 0) {
        $.post('{{ $_controller }}/set_datos', {
          valle: valle,
          taller: logia,
          grado: grado,
          estado: estado,
          resul: resul,
          _token: tokenModule
        }, function(result) {
          if (result.success) {
            $('#dg{{ $_mid }}').datagrid('reload');
          }
        }, 'json');
      } else {
        $.messager.alert('@lang("mess.alert")','@lang("mess.alerttext",["text"=>"Valle"])','warning');
      }
    }

    function toPdfeleg() {
      var body = $('#dg{{ $_mid }}').datagrid('toArray');
      var docDefinition = {
        pageSize: 'LETTER',
        pageMargins: [40, 130, 40, 90],
        content: [{
            margin: [0, 0, 0, 10],
            alignment: 'center',
            text: 'Lista de Hermanos COMAP',
            style: 'header'
          },
          {
            table: {
              headerRows: 1,
              widths: ['20%', '11%', '*', '8%', '9%'],
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

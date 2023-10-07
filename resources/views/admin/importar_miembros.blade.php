@extends('layouts.easyuitab')
@section('content')
    <table id="dgimp{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:auto;" data-options="url: '{{ $_controller }}/get_datos',fitColumns:false,singleSelect:true,
    queryParams:{
    _token: tokenModule
    }" toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true"
        rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20" enableFilter="true">
        <thead>
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="estado">Estado</th>
                <th field="oriente">Oriente</th>
                <th field="valle">Valle</th>
                <th field="logia">R:.L:.S:.</th>
                <th field="LogiaActual">Nro.</th>
                <th field="GradoActual">Grado</th>
                <th field="NombreCompleto">Nombres</th>
                <th field="username">Usuario</th>
                <th field="FechaCreacion">F. importacion</th>
            </tr>
        </thead>
    </table>
    <!--   formulario de carga de archivo    -->
    <div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
        <form name="ffupload" id="ffupload" enctype="multipart/form-data" method="post" action="">
            @csrf
            <div style="float:left;">
                @if (count($oris) == 1)
                    @foreach ($oris as $key => $ogg)
                        <input name="foriente" id="foriente" value="{{ $key }}" type="hidden">
                        <a href="#" class="easyui-linkbutton" ><b>ORIENTE: {{ $ogg }}</b></a>
                    @endforeach
                @else
                <b>ORIENTE: </b><select id="foriente" name="foriente" class="easyui-combobox" data-options="panelHeight:450,valueField: 'id',textField: 'text',editable:false,onChange: function(rec){filtrarDatosImp(rec,'oriente');}">
                        <option value="0" selected="selected">Mostrar todos los Orientes &nbsp;&nbsp;&nbsp;</option>
                        @foreach ($oris as $key => $ogg)
                            <option value="{{ $key }}">{{ $ogg }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div style="float:left; width:500px;padding-left:10px; ">
                <input name="archivoLee" id="archivoLee" class="easyui-filebox" style="width:100%;height:30px;" accept=".xlsx,.csv,.xls"
                    data-options="
          buttonText: 'Seleccione un archivo',
          label: 'Archivo con miembros:',
          labelPosition: 'left',
          labelWidth: 150,
          prompt:'Archivo Excel...'" />
            </div>
            <div style="float:left;"><button class="easyui-linkbutton" id="btn_upload" type="submit" iconCls="fa fa-edit orange">Cargar datos al sistema</button></div>
        </form>
    </div>
    <!-- Dialogo de cargado de archivo -->
    <div id="ddwork" class="easyui-dialog" title="Espere por favor, cargando archivo..." style="width:335px;height:270px;" data-options="iconCls:'icon-save',resizable:false,modal:true,closable:false" closed="true">
        <center><img src="{{ asset('media/working.gif') }}"></center>
    </div>
    <!--filtros de datos -->
    <script type="text/javascript">
            function filtrarDatosImp(value, campo) {
            $.post('{{ $_controller }}/filtrar?_token={{ csrf_token() }}', {
                _token: tokenModule,
                valor: value,
                filtro: campo
            }, function(result) {
                if (result.success) {
                    $('#dgimp{{ $_mid }}').datagrid('reload');
                }
            }, 'json');
        }
        $('#ffupload').submit(function(e) {
            e.preventDefault();
            ori = $('#foriente').combobox('getValue');
            if (ori > 0) {
                $('#ddwork').dialog('open');
                $.ajax({
                    url: '{{ $_controller }}/upload_file',
                    type: "post",
                    data: new FormData(this), //this is formData
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: true,
                    success: function(result) {
                        $('#ddwork').dialog('close');
                        if (result.success) {
                            $('#dgimp{{ $_mid }}').datagrid('reload'); // reload the user data
                            mes = 'Datos cargados ' + result.cargados + ', Duplicados ' + result.duplicados + ', Total ' + result.sinid
                            // alert(result.Msg);
                            $.messager.alert({
                                title: 'Error',
                                msg: mes,
                                icon: 'info'
                            });
                        } else {
                            $.messager.alert({
                                title: 'Error',
                                msg: result.Msg,
                                icon: 'warning'
                            });
                            // alert(result.Msg);
                        }

                        $('#archivoLee').filebox('clear');
                        // $("#ffupload").trigger('reset');
                    }
                });
            } else {
                $.messager.alert({
                    title: 'Error',
                    msg: '@lang("mess.alertoriente")',
                    icon: 'warning'
                });
            }
        });
    </script>

@endsection

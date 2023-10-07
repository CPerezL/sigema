@extends('layouts.easyuitab')
@section('content')
<table id="dg{{ $_mid }}" class="easyui-datagrid" style="width:100%;height:100%;" data-options=" url:'{{ $_controller }}/get_datos', queryParams:{ _token: tokenModule },
       " toolbar="#toolbar{{ $_mid }}" pagination="true" fitColumns="true" rownumbers="true" fitColumns="true" singleSelect="true" pageList="[20,40,50,100]" pageSize="20">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th field="idValle" hidden="true">ID</th>
            <th field="valle">Nombre de Valle</th>
            <th field="tipotxt">Tipo de Valle</th>
            <th field="fechaModificacion">Fecha de Modificacion</th>
            <th field="fechaCreacion">Fecha de creacion</th>
        </tr>
    </thead>
</table>

<div class="datagrid-toolbar" id="toolbar{{ $_mid }}" style="display:inline-block">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="fa fa-edit fa-lg green"  onclick="vvdt_edit();">Editar Datos de Valle</a>
</div>

<script type="text/javascript">
    var url;
        var vista{{ $_mid }} = 0;
        function vvdt_edit() {
            var row = $('#dg{{ $_mid }}').datagrid('getSelected');
            if (row) {
                $('#dlg{{ $_mid }}').dialog('open').dialog('setTitle', '@lang("mess.editar",["job"=>"valle"])');
                urlform = '{{ $_controller }}/get_form?_token=' + tokenModule + '&task=1&id=' + row.idValle;
                $('#fmvd{{ $_mid }}').form('load', urlform);
                if (row.logo) {
                    $('#photo{{ $_mid }}').attr('src', '{{ URL::to('/media/') }}/' + row.logo);
                    $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src')); //recraga imagen
                } else {
                    $('#photo{{ $_mid }}').attr('src', '{{ URL::to('/media/') }}/glsp-150.png');
                    $('#photo{{ $_mid }}').attr('src', $('#photo{{ $_mid }}').attr('src'));
                }
                url = '{{ $_controller }}/update_datos?_token=' + tokenModule + '&task=1';
            }
        }

        function vvdat_save() {
            $('#fmvd{{ $_mid }}').form('submit', {
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
                            title: '@lang("mess.ok")',
                            msg: '<div class="messager-icon messager-info"></div><div>' + result.Msg + '</div>'
                        });
                        $('#fm{{ $_mid }}').form('clear');
                        $('#dlg{{ $_mid }}').dialog('close'); // close the dialog
                        $('#dg{{ $_mid }}').datagrid('reload'); // reload the user data
                    }
                }
            });
        }
</script>
<!--                                                                                DATOS PERSONALES                                                                    --->
<div id="dlg{{ $_mid }}" class="easyui-dialog" style="width:450px;height:auto;padding:5px 5px" closed="true" buttons="#dlgv-buttons{{ $_mid }}"
    data-options="iconCls:'icon-save',modal:true">
    <form id="fmvd{{ $_mid }}" method="post" enctype="multipart/form-data" novalidate><input type="hidden" name="idValle" value="" />
        <div id="tt" class="easyui-tabs" style="width:100%;height:auto;">
            <div title="Datos Principales" style="padding:5px;display:none;">
                <div style="margin-top:4px;margin-left:20px"><img src='' id="photo{{ $_mid }}" height="150" /></div>
                <div style="margin-top:2px"><input class="easyui-filebox" style="width:100%" name="foto" data-options="required:false,buttonText: 'Elegir logo',accept: 'image/*'">
                </div>
                <div style="margin-top:2px"><input name="nombreCompleto" label="Nombre completo:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%">
                </div>
                <div style="margin-top:2px"><input name="fundacion" id="fundacion" label="Fecha de creacion:" labelPosition="left" labelWidth="130" class="easyui-datebox"
                        style="width:100%"></div>
                <div style="margin-top:2px"><input name="departamento" label="Departamento:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
                <div style="margin-top:2px"><input name="direccion" label="Direccion:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
                <div style="margin-top:2px"><input name="localidad" label="Localidad:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
                <div style="margin-top:2px"><input name="telefonos" label="Telefonos:" labelPosition="left" labelWidth="130" class="easyui-textbox" style="width:100%"></div>
            </div>
        </div>
    </form>
</div>
<div id="dlgv-buttons{{ $_mid }}">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="vvdat_save();" style="width:90px">Grabar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg{{ $_mid }}').dialog('close');" style="width:90px">Cancelar</a>
</div>

@endsection

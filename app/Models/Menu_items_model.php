<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Menu_items_model extends Model
{
    public $timestamps = false;
    protected $table = 'sgm2_menu_items';
    private static function getModulos($rol)
    {
        $mods = DB::table('sgm2_modulos_roles')->select('idModulo')->where('idRol', '=', $rol)->orderby('idModulo', 'ASC')->pluck('idModulo');
        return $mods;
    }

    public static function getMenuUI($rol = 0, $nivel = 1)
    {
        $mm = self::getModulos($rol);
        $ret = '';
        $menu1 = self::select('id', 'nombre', 'tipo', 'icono')->where('activo', '=', 1)->where('idMenu', '=', 0)->orderby('orden', 'ASC')->get();
        foreach ($menu1 as $mm1) {
            if (strlen($mm1->icono) > 6) {
                $ico = $mm1->icono;
            } else {
                $ico = 'fa fa-bars';
            }
            $aux = 0;
            $grupo = '';
            $menu2 = self::select('sgm2_menu_items.id', 'sgm2_menu_items.nombre', 'sgm2_menu_items.tipo', 'sgm2_menu_items.icono', 'sgm2_modulos.link as enlace', 'sgm2_menu_items.nivel')
                ->join('sgm2_modulos', 'sgm2_menu_items.idModulo', '=', 'sgm2_modulos.id')
                ->wherein('sgm2_menu_items.idModulo', $mm)
                ->where('sgm2_menu_items.activo', '=', 1)
                ->where('sgm2_menu_items.idMenu', '=', $mm1->id)
                ->orderby('sgm2_menu_items.orden', 'ASC')
                ->get();
            $grupo .= '<div title="' . $mm1->nombre . '" style="padding:0px;" data-options="iconCls:\'' . $ico . ' fa-lg cyan\'">'; //----
            $grupo .= ' <ul class="easyui-tree tree tree-lines" data-options="animate:true,dnd:true" style="display:block">'; //------
            foreach ($menu2 as $mm2) {
                if ($nivel >= $mm2->nivel) {
                    if (strlen($mm2->icono) > 6) {
                        $ico2 = $mm2->icono;
                    } else {
                        $ico2 = 'fa fa-bars';
                    }
                    $aux++;
                    $grupo .= '<li class="easyui-linkbutton"><a href="#" style="color:inherit; text-decoration: none;" onclick="agregarTabs(\'' . $mm2->nombre . '\',\'' . $mm2->enlace . '\',\'' . $ico2 . ' fa-lg blue2\');" >
                <span style="display:block;width:220px;">' . $mm2->nombre . '</span></a></li>';
                }
            }
            $grupo .= '</ul></div>';
            if ($aux > 0) {
                $ret .= $grupo;
            }
        }
        return $ret;
    }
}

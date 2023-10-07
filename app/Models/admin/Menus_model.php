<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menus_model extends Model
{
    protected $table = 'sgm2_menu_items';
    static $cantidad = 20;
    static $pagina = 1;
    static $ordenpor = 'orden ASC';
    static $level = 1;
    public $timestamps = FALSE;
    public static function getModulosArray()
    {
        $qry = self::select('id', 'name')->orderBy('name', 'asc')->pluck('name', 'id');
        return $qry;
    }
    public static function getItems($palabra = '', $level = 0)
    {
        $cond = 'A.id>0 ';
        $inicio = self::$cantidad * (self::$pagina - 1);
        if (strlen($palabra) > 0)
            $cond .= " AND A.name Like '%$palabra%'";
        if ($level > 0)
            $cond .= " AND A.level=$level";

        $qry = "SELECT A.*,B.nombre AS niveltxt FROM sgm2_modulos A JOIN sgm_parametros B  ON A.level=B.valor AND B.tipo=2 WHERE $cond  ORDER BY " . self::$ordenpor . " Limit $inicio," . self::$cantidad;
        $results = \DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $level = 0)
    {
        $cond = 'id>0 ';
        if (strlen($palabra) > 0)
            $cond .= " AND name Like '%$palabra%'";
        if ($level > 0)
            $cond .= " AND level=$level";

        $count = self::select('id')->whereraw($cond)->count();
        return $count;
    }
    public static function getRoles($mod = 1)
    {
        $qry = "SELECT A.id,A.name,case when B.idRol IS NULL then '' ELSE 'checked' end as checked ,A.id AS _ck  FROM sgm2_roles A LEFT outer JOIN sgm2_modulos_roles B ON A.id=B.idRol AND idModulo=$mod WHERE A.id>0 ORDER BY A.level,A.name";
        $results = \DB::select($qry);
        return $results;
    }
    public static function getRolesArray($mod)
    {

        $qry = \DB::table('sgm2_modulos_roles')->select('idRol')->where('idModulo', '=', $mod)->pluck('idRol')->toArray();
        return $qry;
    }
    /*************************************           funciones de menus          *******************************************/
    public static function getMenus($menusel = 0)
    {
        if ($menusel > 0)
            $menu1 = self::select('id', 'orden')->where('id', '=', $menusel)->orderBy('orden', 'asc')->orderBy('nombre', 'asc')->get();
        else
            $menu1 = self::select('id', 'orden')->where('idMenu', '=', 0)->orderBy('orden', 'asc')->orderBy('nombre', 'asc')->get();
        $menu = array();
        //dd($menu);
        foreach ($menu1 as $val) {
            $menu1 = self::get_Menu($val->id);
            $menu2 = self::get_subMenu($val->id);
            $menu = array_merge($menu, $menu1, $menu2);
        }
        return $menu;
    }
    private static function get_Menu($id)
    {
        $qry = "SELECT A.*,A.nombre AS name,B.link as enlace, B.name AS modulotxt,case when A.activo=1 then 'Activo' ELSE 'Inactivo' end as estado,N.nombre AS niveltxt,'-' as modulook
        FROM sgm2_menu_items A JOIN sgm_parametros N ON A.nivel=N.valor AND N.tipo=2 left join sgm2_modulos B on A.idModulo=B.id WHERE A.id=$id";
        $results = \DB::select($qry);
        $data = (array) $results;
        return $data;
    }

    private static function get_subMenu($padre)
    {
        $qry = "SELECT A.*,CONCAT('|--> ',A.nombre) as name,B.link as enlace,B.name AS modulotxt,case when A.activo=1 then 'Activo' ELSE 'Inactivo' end as estado,N.nombre AS niveltxt,
        case when A.activo=1 then (case B.estado when 0 then 'No Rev' when 1 then 'Para Rev' when 2 then 'Revisado' else 'S/D' end) else '-' end as modulook
        FROM sgm2_menu_items A JOIN sgm_parametros N ON A.nivel=N.valor AND N.tipo=2 left join sgm2_modulos B on A.idModulo=B.id WHERE A.idMenu=$padre ORDER BY orden ASC,nombre ASC";
        $results = \DB::select($qry);
        $data = (array) $results;
        return $data;
    }
    public static function corregirMenu($id)
    {
        $menus = self::select('id')->orderBy('orden', 'asc')->orderBy('nombre', 'asc')->where('idMenu', '=', $id)->get();
        $numero = 0;
        foreach ($menus as $item) {
            $numero++;
            self::where("id", $item->id)->update(['orden' => $numero]);
        }
        if ($numero > 0)
            return true;
        else
            return false;
    }
    public static function corregirPadres($id)
    {
        $menus = self::select('id')->orderBy('orden', 'asc')->orderBy('nombre', 'asc')->where('idMenu', '=', 0)->get();
        $numero = 0;
        foreach ($menus as $item) {
            $numero++;
            self::where("id", $item->id)->update(['orden' => $numero]);
        }
        if ($numero > 0)
            return true;
        else
            return false;
    }
    public static function getMenusArray()
    {
        $qry = self::select('id', 'nombre')->orderBy('orden', 'asc')->where('idMenu', '=', 0)->pluck('nombre', 'id');
        return $qry;
    }
}

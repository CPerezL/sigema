<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Modulos_model extends Model
{
    protected $table = 'sgm2_modulos';
    public $timestamps = false;
    public static function getModulosArray()
    {
        $qry = self::select('id', 'name')->orderBy('name', 'asc')->pluck('name', 'id');
        return $qry;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $level = 0, $sort = '', $order = '')
    {
        $cond = 'A.id>0 ';
        $inicio = $cantidad * ($pagina - 1);
        if (strlen($palabra) > 0) {
            $cond .= " AND A.name Like '%$palabra%'";
        }

        if ($level > 0) {
            $cond .= " AND A.level=$level";
        }
        if (strlen($sort) > 1) {
            $ordenar = ' A.' . $sort . ' ' . $order;
        } else {
            $ordenar = 'A.id';
        }

        $qry = "SELECT A.*,B.nombre AS niveltxt,CASE WHEN A.estado=1 THEN 'Para revisar' WHEN A.estado=2 THEN 'Revisado' else 'No revisado' end AS estadotxt FROM sgm2_modulos A JOIN sgm_parametros B  ON A.level=B.valor AND B.tipo=2 WHERE $cond  ORDER BY $ordenar  Limit $inicio," . $cantidad;
        $results = \DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $level = 0)
    {
        $cond = 'id>0 ';
        if (strlen($palabra) > 0) {
            $cond .= " AND name Like '%$palabra%'";
        }

        if ($level > 0) {
            $cond .= " AND level=$level";
        }

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

}

<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles_model extends Model
{
    use HasFactory;
    protected $table = 'sgm2_roles';
    public static function getRolesArray($nivel=0)
    {
        $qry = self::select('id', 'name')->where('level','<=',$nivel)->orderBy('name', 'asc')->pluck('name', 'id');
        return $qry;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $level = 0)
    {
        $cond = 'A.id>0 ';
        $inicio = $cantidad * ($pagina - 1);
        if (strlen($palabra) > 0)
            $cond .= " AND A.name Like '%$palabra%'";
        if ($level > 0)
            $cond .= " AND A.level=$level";

        $qry = "SELECT A.*,B.nombre AS niveltxt FROM sgm2_roles A JOIN sgm_parametros B  ON A.level=B.valor AND B.tipo=2 WHERE $cond  ORDER BY level ASC,name ASC Limit $inicio," . $cantidad;
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
}

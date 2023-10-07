<?php

namespace App\Models\admin;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_logs_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_users_logs';
    protected static $idlast=0;
    public static function getItems($pagina, $cantidad, $valle, $taller, $palabra = '')
    {
        $last=self::$idlast;
        $inicio = $cantidad * ($pagina - 1);

        DB::select("SET lc_time_names = 'es_ES'");
        $cond = "WHERE A.id>$last AND A.rol not in(9,10,11) ";
        if ($taller > 0) {
            $cond .= " AND A.logia = '$taller'";
        }
        if ($valle > 0) {
            $cond .= " AND A.valle = $valle";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.usuario Like '%$palabra%'";
        }
        $qry = "SELECT A.id,A.nombre,A.accion,B.name as rol,case when A.logia>0 then CONCAT('R.L.S. ',C.logia,' Nro ',C.numero) ELSE 'Todas' END AS logiatxt,
        case when A.valle>0 then D.valle ELSE 'Todos' END AS valletxt,A.fechaLog
        FROM sgm_users_logs A LEFT JOIN sgm2_roles B ON A.rol=B.id LEFT JOIN sgm_logias C ON A.logia=C.numero LEFT JOIN sgm_valles D ON A.valle=D.idValle
          $cond ORDER BY A.fechaLog DESC  Limit $inicio,$cantidad";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($valle, $taller, $palabra = '')
    {
        $last=self::$idlast;
        $cond = "WHERE A.id>$last AND A.rol not in(9,10,11) ";
        if ($taller > 0) {
            $cond .= " AND A.logia = '$taller'";
        }
        if ($valle > 0) {
            $cond .= " AND A.valle = $valle";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.usuario Like '%$palabra%'";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm_users_logs A $cond";
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
}

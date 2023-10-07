<?php

namespace App\Models\admin;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logias_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idLogia';
    protected $table = 'sgm_logias';
    public static function getLogias($oriente, $valle = 0, $sulogia = 0)
    {
        $cond = 'borrado=0';
        if ($sulogia > 0) {
            $cond .= ' AND numero=' . $sulogia;
        }

        if ($valle > 0) {
            $cond .= ' AND valle=' . $valle;
        }

        if ($oriente > 0) {
            $cond .= " AND idOriente=$oriente";
        }
        $qry = DB::table('sgm_logias')->select('numero as nlogia', DB::raw("CONCAT(logia,' Nro. ' ,numero) AS logian"))->whereraw($cond)->orderBy('numero', 'ASC')->get();
        if ($sulogia == 0) {
            $qry[] = (object) ['logian' => 'Todas las Logias', 'nlogia' => 0];
        }

        return $qry;
    }
    public static function getlogiasArray($oriente, $valle = 0, $sulogia = 0)
    {
        $cond = 'borrado=0';
        if ($sulogia > 0) {
            $cond .= ' AND numero=' . $sulogia;
        }

        if ($valle > 0) {
            $cond .= ' AND valle=' . $valle;
        }

        if ($oriente > 0) {
            $cond .= " AND idOriente=$oriente";
        }
        $qry = self::select('numero as nlogia', DB::raw("CONCAT(CASE WHEN tipo=2 THEN 'T:. ' ELSE '' END,logia,' Nro. ' ,numero) AS logian"))->whereraw($cond)->orderBy('numero', 'asc')->pluck('logian', 'nlogia');
        return $qry;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $estado = 0, $oriente = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.borrado='$estado' and A.tipo=1";
        if ($oriente > 0) {
            $cond .= " AND A.idOriente=$oriente ";
        }

        if ($valle > 0) {
            $cond .= " AND A.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.logia Like '%$palabra%' ";
        }

        $qry = "SELECT  A.*,B.valle AS nvalle, C.rito AS nrito,D.nombre AS ndiatenida,E.oriente FROM sgm_logias A JOIN sgm_valles B ON A.valle=B.idValle
        JOIN sgm_ritos C ON A.rito=C.idRito JOIN sgm_parametros D ON  A.diatenida=D.valor AND D.tipo=10 JOIN sgm_orientes E ON A.idOriente=E.idOriente
        WHERE $cond ORDER BY E.oriente,A.numero Limit " . $inicio . "," . $cantidad . ' ';

        $results = DB::select($qry);
        return $results;
    }

    public static function getNumItems($palabra = '', $estado = 0, $oriente = 0, $valle = 0)
    {
        $cond = "borrado='$estado' and tipo=1";
        if ($oriente > 0) {
            $cond .= " AND idOriente=$oriente ";
        }

        if ($valle > 0) {
            $cond .= " AND valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND logia Like '%$palabra%' ";
        }

        $count = self::select('idLogia')->whereraw($cond)->count();
        return $count;
    }
}

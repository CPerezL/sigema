<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Valles_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idValle';
    protected $table = 'sgm_valles';
    public static function getVallesArray($or, $valle = 0, $borrado = 0)
    {
        if ($valle > 0)
            $qry = self::select('idValle', 'valle')->where('borrado', '=', $borrado)->where('idValle', '=', $valle)->orderBy('valle', 'asc')->pluck('valle', 'idValle');
        elseif ($or > 0)
            $qry = self::select('idValle', 'valle')->where('borrado', '=', $borrado)->where('idOriente', '=', $or)->orderBy('valle', 'asc')->pluck('valle', 'idValle');
        else
            $qry = self::select('idValle', 'valle')->where('borrado', '=', $borrado)->orderBy('valle', 'asc')->pluck('valle', 'idValle');
        return $qry;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $estado = 0, $sel = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.borrado = '$estado'";
        if ($sel > 0)
            $cond .= ' AND A.idOriente=' . $sel;
        if (strlen($palabra) > 2)
            $cond .= "AND A.valle Like '%$palabra%'";
        // $qry = "SELECT A.*,B.oriente ,nombre AS tipotxt FROM sgm_valles A
        // JOIN sgm_orientes B ON A.idOriente=B.idOriente JOIN sgm2_distritos C ON A.tipo=C.idTipo WHERE $cond ORDER BY B.oriente ASC,A.valle ASC Limit " . $inicio . "," . $cantidad . ' ';

$qry="SELECT A.idValle,A.idOriente,A.valle,A.tipo,A.nombreCompleto,A.autoridad,A.logo,A.fechaCreacion,A.fechaModificacion,A.borrado,B.oriente ,nombre AS tipotxt, COUNT(D.idLogia) AS numero
FROM sgm_valles A JOIN sgm_orientes B ON A.idOriente=B.idOriente JOIN sgm2_distritos C ON A.tipo=C.idTipo LEFT JOIN sgm_logias D ON D.valle=A.idValle
WHERE $cond
GROUP BY B.oriente ,C.nombre,A.idValle,A.idOriente,A.valle,A.tipo,A.nombreCompleto,A.autoridad,A.logo,A.fechaCreacion,A.fechaModificacion,A.borrado
ORDER BY B.oriente ASC,A.valle ASC Limit $inicio,$cantidad";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $estado = 0, $sel = 0)
    {
        $cond = "borrado = '$estado'";
        if ($sel > 0)
            $cond .= ' AND idOriente=' . $sel;
        if (strlen($palabra) > 2)
            $cond .= "AND valle Like '%$palabra%'";
        $count = self::select('idValle')->whereraw($cond)->count();
        return $count;
    }
}

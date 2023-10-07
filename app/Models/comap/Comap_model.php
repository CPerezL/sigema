<?php

namespace App\Models\comap;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comap_model extends Model
{
    private static $regular = 6;
    public $timestamps = false;
    protected $primaryKey = 'idComap';
    protected $table = 'sgm_comap';
    use HasFactory;
    public static function setMesesMora($meses)
    {
        self::$regular = $meses;
    }
    public static function getItemsComap($pagina, $cantidad, $palabra, $taller)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id >0 ";
        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago, A.Grado, A.Miembro, A.Estado, D.valle, F.GradoActual,A.Grado, COUNT(C.idComap) AS nBenes, SUM(C.porcentaje) AS sumBenes, B.logia
    FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero JOIN sgm_valles D ON B.valle=D.idValle JOIN sgm_grados F ON (A.Grado=F.Grado)
    LEFT JOIN sgm_comap C ON C.idMiembro=A.id WHERE $cond  GROUP BY A.id, A.LogiaActual, A.NombreCompleto,A.Grado, A.Miembro, A.Estado, A.Grado, B.logia , D.valle,F.GradoActual
             ORDER BY A.NombreCompleto ASC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getTotalItemsComap($palabra, $taller)
    {
        $cond = "sgm_miembros.id >0 ";
        if ($taller > 0) {
            $cond .= " AND sgm_miembros.LogiaActual = '$taller'";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND sgm_miembros.NombreCompleto Like '%$palabra%'";
        }
        $results = self::from('sgm_miembros')->join('sgm_logias', 'sgm_logias.numero', '=', 'sgm_miembros.LogiaActual')->select('sgm_miembros.id')->whereraw($cond)->count();
        return $results;
    }
    public static function getBeneficiarios($idm)
    {
        if ($idm > 0) {
            $qry = "SELECT * FROM sgm_comap WHERE idMiembro=$idm Order by porcentaje DESC";
            $results = DB::select($qry);
            return $results;
        } else {return null;}
    }
}

<?php

namespace App\Models\valles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Afiliados_model extends Model
{
    use HasFactory;
    private static $regular = 6;
    public static function getListas($valle = 0, $taller, $grado, $estado, $resul) 
    {
        if ($valle > 0 || $taller > 0) {
            $hoy = date("Y-m-d");
            $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01'; //fecha de corte de obolo
            $anterior = self::$regular - 1;
            $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
            //--
            if($valle>0)
            $cond = "A.id>0 AND B.valle=$valle ";
            else
            $cond = "A.id>0 ";
            if ($taller > 0) {
                $cond .= " AND A.LogiaAfiliada=$taller";
            } else {
                $cond .= " AND A.LogiaAfiliada>0";
            }

            if ($grado > 0) {
                $cond .= " AND A.Grado=$grado";
            }

            switch ($estado) {
                case 1:
                    $cond .= " AND A.Miembro='Regular'";
                    $cond .= " AND A.Estado <>2";
                    break;
                case 2:
                    $cond .= " AND A.Miembro='Honorario'";
                    $cond .= " AND A.Estado <>2";
                    break;
                case 3:
                    $cond .= " AND A.Miembro='Ad-Vitam'";
                    $cond .= " AND A.Estado <>2";
                    break;
                case 4:
                    $cond .= " AND A.Miembro='Ad-Meritum'";
                    $cond .= " AND A.Estado <>2";
                    break;
                case 5:
                    $cond .= " AND A.Estado=2";
                    break;
            }
            switch ($resul) {
                case 0:
                    $cond .= " AND (A.ultimoPago>='$newdate' OR ((A.miembro='Ad-Vitam') OR (A.miembro='Ad-Meritum')))";
                    break;
                case 1:
                    $cond .= " AND A.ultimoPago>='$newdate' AND A.ultimoPago<'$newdate2' AND ((A.miembro<>'Ad-Vitam') OR (A.miembro<>'Ad-Meritum'))";
                    break;
                case 2:
                    $cond .= " AND A.ultimoPago<'$newdate' AND ((A.miembro<>'Ad-Vitam') OR (A.miembro<>'Ad-Meritum'))";
                    break;
                case 3:
                    $cond .= " ";
                    break;
            }
            $qry = "SELECT A.id,A.ultimoPago,A.LogiaActual,A.LogiaAfiliada,A.NombreCompleto, A.Grado, A.Miembro, A.Estado,CONCAT(B.logia,' Nro. ',A.LogiaAfiliada) AS nlogia, "
                . "CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, "
                . "CASE A.jurisdiccion WHEN 0 THEN 'ND' WHEN 1 THEN 'G.L.B.' WHEN 2 THEN 'Reg.' WHEN 3 THEN 'Otro O.' END AS Ingreso, "
                . "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam')) THEN 2 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk "
                . "FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaAfiliada=B.numero WHERE $cond "
                . " ORDER BY A.NombreCompleto ASC ";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }

    }
}

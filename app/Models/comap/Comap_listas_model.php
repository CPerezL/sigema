<?php

namespace App\Models\comap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Comap_listas_model extends Model
{
    private static $regular = 6;
    use HasFactory;
    public static function setMesesMora($meses)
    {
        self::$regular = $meses;
    }
    public static function getListas($valle = 0, $taller, $grado, $estado, $resul)
    {
        if ($valle > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $hoy = date("Y-m-d");
            $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01'; //fecha de corte de obolo
            $anterior = self::$regular - 1;
            $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
            //--
            $cond = "A.id>0 AND B.valle=$valle ";
            if ($taller > 0)
                $cond .= " AND A.LogiaActual=$taller";
            else
                $cond .= " AND A.LogiaActual>0";
            if ($grado > 0)
                $cond .= " AND A.Grado=$grado";

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
            $qry = "SELECT A.id,DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago,A.LogiaActual,A.LogiaAfiliada,A.NombreCompleto, A.Grado, A.Miembro, A.Estado,CONCAT(B.logia,' Nro. ',A.LogiaActual) AS nlogia, "
                . "CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, "
                . "CASE A.jurisdiccion WHEN 0 THEN 'ND' WHEN 1 THEN 'G.L.B.' WHEN 2 THEN 'Reg.' WHEN 3 THEN 'Otro O.' END AS Ingreso, "
                . "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam')) THEN 2 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk "
                . "FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond "
                . " ORDER BY A.NombreCompleto ASC ";

            $results = DB::select($qry);
            return $results;
        } else
            return '';
    }
    public static function getReporte($valle, $resul)
    {
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01'; //fecha de corte de obolo
        $anterior = self::$regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        //--
        $cond = ' ';
        switch ($resul) {
            case 0://aldia
                $cond .= " AND (A.ultimoPago>='$newdate' OR (A.Miembro='Ad-Vitam' OR A.Miembro='Ad-Meritum') OR A.Estado=2 )";
                break;
            case 1://casi mora
                $cond .= " AND A.ultimoPago>='$newdate' AND  A.ultimoPago<'$newdate2' AND (A.Miembro<>'Ad-Vitam' AND A.Miembro<>'Ad-Meritum' AND A.Estado<>2 ) ";
                break;
            case 2://en mora
                $cond .= " AND (A.ultimoPago<'$newdate') AND (A.Miembro<>'Ad-Vitam' AND A.Miembro<>'Ad-Meritum') AND A.Estado<>2";
                break;
            case 3:
                $cond .= " ";
                break;
            case 4:
                $cond .= " AND (A.ultimoPago>='$newdate' OR (A.Miembro='Ad-Vitam' OR A.Miembro='Ad-Meritum') OR A.Estado=2 )";
                break;
            case 5:
                $cond .= " ";
                break;
        }
        if ($resul > 3) {
            $qry = "SELECT count(case when A.Miembro='Regular' AND A.Estado <>2 THEN 1 ELSE NULL END) AS regulares, count(case when A.Miembro='Honorario'AND A.Estado <>2  AND A.Estado <>2 THEN 1 ELSE NULL END) AS honorarios,
count(case when A.Miembro='Ad-Vitam' THEN 1 ELSE NULL END) AS advitam, count(case when A.Miembro='Ad-Meritum' AND A.Estado <>2 THEN 1 ELSE NULL END) AS admeritum,C.valle,
count(case when A.Miembro='Regular' AND (A.Grado=0 OR A.Grado=1) AND A.Estado <>2 THEN 1 ELSE NULL END) AS regaa,count(case when A.Miembro='Regular' AND A.Grado=2 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regcc,
count(case when A.Miembro='Regular' AND A.Grado=3 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regmm,count(case when A.Miembro='Regular' AND A.Grado=4 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regvh,
count(case when A.Miembro='Regular' AND A.Grado=0 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regce,B.numero,CONCAT(B.logia, ' Nro ', B.numero) AS taller,
count(case when A.Estado=2 THEN 1 ELSE NULL END) AS fallecidos FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero JOIN sgm_valles C ON B.valle=C.idValle
WHERE  B.valle=$valle $cond GROUP by C.valle,B.numero,B.logia ORDER BY B.numero";
        } else {
            $qry = "SELECT count(case when A.Miembro='Regular' AND A.Estado <>2 THEN 1 ELSE NULL END) AS regulares, count(case when A.Miembro='Honorario' AND A.Estado <>2 THEN 1 ELSE NULL END) AS honorarios,
count(case when A.Miembro='Ad-Vitam' AND A.Estado <>2 THEN 1 ELSE NULL END) AS advitam, count(case when A.Miembro='Ad-Meritum' AND A.Estado <>2 THEN 1 ELSE NULL END) AS admeritum,C.valle,
count(case when A.Miembro='Regular' AND (A.Grado=0 OR A.Grado=1) AND A.Estado <>2 THEN 1 ELSE NULL END) AS regaa,count(case when A.Miembro='Regular' AND A.Grado=2 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regcc,
count(case when A.Miembro='Regular' AND A.Grado=3 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regmm,count(case when A.Miembro='Regular' AND A.Grado=4 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regvh,
count(case when A.Miembro='Regular' AND A.Grado=0 AND A.Estado <>2 THEN 1 ELSE NULL END) AS regce,count(case when A.Estado=2 THEN 1 ELSE NULL END) AS fallecidos
FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero JOIN sgm_valles C ON B.valle=C.idValle
WHERE  B.valle=$valle $cond GROUP by C.valle ";
                // echo $qry;
        }

        $results = DB::select($qry);
        return $results;

    }
}

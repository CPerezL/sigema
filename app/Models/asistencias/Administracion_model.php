<?php

namespace App\Models\asistencias;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administracion_model extends Model
{
    use HasFactory;
    private static $diavalido = 0;
    private static $regular = 6;

    public static function setRegular($reg)
    {
        self::$regular = $reg;
    }
    public static function getDataAsistencia($logia, $gestion)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $fechagestion = date($gestion . "-01-01");
        $query = DB::select("SELECT *,DATE_FORMAT(fechaTenida,'%a-%d-%b-%Y') AS fechaTrabajo,EXTRACT(MONTH FROM fechaTenida) AS mes,EXTRACT(DAY FROM fechaTenida) AS dia FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida>'$fechagestion' ORDER BY fechaTenida");
        return $query;
    }
    public static function getListaAsistencia($taller, $gestion, $mes, $diat)
    {
        if ($diat > 0 && $gestion > 2000 && $mes > 0 && $taller > 0) {
            $regular = self::$regular;
            $hoy = date("Y-m-d");
            $dosemanas = date("Y-m-d", strtotime("$hoy -2 weeks"));
            $fechaten = "$gestion-$mes-$diat";
            $date1 = strtotime($dosemanas);
            $date2 = strtotime($hoy);
            $date3 = strtotime($fechaten);
            if ($date3 > $date1) {
                if ($date3 <= $date2) {
                    $fvalida = 2;
                } else {
                    $fvalida = 1;
                }

            } else {
                $fvalida = 0;
            }

            $newdate = date("Y-m", strtotime("$fechaten -$regular months")) . '-01';
            $anterior = $regular - 1;
            $newdate2 = date("Y-m", strtotime("$fechaten -$anterior months")) . '-01';
            //**************************************************************************************************************************

            $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,A.ultimoPago,O.idoficial,
            CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, B.idAsistencia,
            CASE WHEN B.idAsistencia IS NULL THEN 'No asistio' ELSE 'Asistio' END AS EstadoAsis, '$newdate' AS fecha,
            CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk,$fvalida AS FechaValida,
            C.id as idOficialPT ,case WHEN(C.id=O.idoficial) THEN C.oficial ELSE CONCAT('PT ',C.oficial) END AS oficial
            FROM sgm_miembros A
            INNER JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.fechaTenida='$fechaten' AND B.idLogia='$taller')
            LEFT JOIN sgm_oficialidades O ON (O.idmiembro=A.id AND O.gestion=$gestion)
            LEFT JOIN sgm_oficiales C ON (B.idOficialPT = C.id OR (A.id=O.idmiembro AND C.id=O.idoficial))
            WHERE (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND (A.LogiaActual = '$taller' OR A.LogiaAfiliada = '$taller' OR A.LogiaInspector = '$taller')
            ORDER BY A.NombreCompleto ASC ";
            //echo $qry;
            $results = DB::select($qry);
            return $results;
        }
        return null;
    }
    public static function getMiembros($regular, $taller, $gestion, $mes, $diat)
    {
        if ($diat > 0 && $gestion > 2000 && $mes > 0 && $taller > 0) {
            $fechaten = "$gestion-$mes-$diat";
            $newdate = date("Y-m", strtotime("$fechaten -$regular months")) . '-01';
            $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,A.ultimoPago,
    CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
    FROM sgm_miembros A
    LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.fechaTenida='$fechaten' AND B.idLogia='$taller')
    LEFT JOIN sgm_oficialidades O ON (O.idmiembro=A.id AND O.gestion=$gestion)
    LEFT JOIN sgm_oficiales C ON (B.idOficialPT = C.id OR (A.id=O.idmiembro AND C.id=O.idoficial))
    WHERE A.Estado<>2  AND  (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND B.idAsistencia is null AND (A.LogiaActual = '$taller' OR A.LogiaAfiliada = '$taller' OR A.LogiaInspector = '$taller')
    ORDER BY A.NombreCompleto ASC ";
            $query = DB::select($qry);
            return $query;
        }
        return '';
    }
    public static function getDataAsis($logia, $ftenida)
    {
        $ex = explode($ftenida, '-');
        $year = $ex[0];
        $query = DB::select("SELECT *, DATE_FORMAT(fechaCierre,'%d/%m/%Y') AS fechaCierreForm FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida='$ftenida'");
        if (count($query) > 0) {
            return $query[0];
        } else {
            $numeros = self::getUltimaActa($logia, $year);
            $objeto = new stdClass();
            $objeto->idLogia = $logia;
            $objeto->grado = 1;
            $objeto->fechaTenida = $ftenida;
            $objeto->numeroActa1 = $numeros->acta1 + 1;
            $objeto->numeroActa2 = $numeros->acta2 + 1;
            $objeto->numeroActa3 = $numeros->acta3 + 1;
            return $objeto;
        }
    }
    private function getUltimaActa($logia, $gestion)
    {
        $qry = "SELECT MAX(numeroActa1) AS acta1,MAX(numeroActa2) AS acta2, MAX(numeroActa3) AS acta3 FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida>'$gestion-01-01'";
        $query = DB::select($qry);
        return $query[0];
    }
}

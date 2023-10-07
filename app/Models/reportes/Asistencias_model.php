<?php

namespace App\Models\reportes;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencias_model extends Model
{
    use HasFactory;

    public static function getDatosLogia($nlog)
    {
        $query = DB::select('SELECT logia,valle,diatenida,rito,nombreCompleto FROM sgm_logias WHERE numero=' . $nlog);
        return $query[0];
    }
    public static function getItems($gestion, $logia)
    {
        $ant = $gestion - 1;
        $pago = $ant . '-12-01';
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago, CASE A.Grado WHEN 0 THEN 'NA' WHEN 1 THEN 'A:.M:.' WHEN 2 THEN 'C:.M:.' WHEN 3 THEN 'M:.M:.' WHEN 4 THEN 'Ex VM' END AS GradoActual, count(B.idAsistencia) AS ordinaria,
        G.protocolo
              FROM sgm_miembros A LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.idLogia = $logia) LEFT JOIN sgm_grados G on A.Grado=G.Grado
              WHERE (A.ultimoPago>='$pago' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND (A.LogiaActual = $logia OR A.LogiaAfiliada = $logia OR A.LogiaInspector = $logia) AND (A.Grado>2)
              GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.ultimoPago ORDER BY A.Grado DESC,A.NombreCompleto ASC";
        $results = DB::select($qry);
        return $results;
    }

    public static function getCantidadTenidas($gestion, $logia)
    {
        $qry = "SELECT COUNT(DISTINCT A.fechaTenida) AS nTenidas FROM sgm_asistenciadata A WHERE A.idLogia=$logia AND A.fechaTenida>'$gestion-01-01' AND A.fechaTenida<'$gestion-12-31' AND (A.numeroActa1>0 OR A.numeroActa2>0 OR A.numeroActa3>0)";
        $results = DB::select($qry);
        return $results[0]->nTenidas;
    }
    public static function getCantidadTenidasGrado($gestion, $logia)
    {
        $qry = "SELECT COUNT(DISTINCT CASE WHEN A.numeroActa1>0 THEN A.fechaTenida ELSE NULL END ) AS nTenidas1, COUNT(DISTINCT CASE WHEN A.numeroActa1>0 OR A.numeroActa2>0 THEN A.fechaTenida ELSE NULL END ) AS nTenidas2 ,
COUNT(DISTINCT A.fechaTenida) AS nTenidas3 FROM sgm_asistenciadata A WHERE A.idLogia=$logia AND A.fechaTenida>'$gestion-01-01' AND A.fechaTenida<'$gestion-12-31' AND (A.numeroActa1>0 OR A.numeroActa2>0 OR A.numeroActa3>0)";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getAsistencia($gestion, $logia, $idm)
    {
        $qry = "SELECT count(DISTINCT A.fechaTenida) AS ordinaria FROM  sgm_asistencia A INNER JOIN sgm_asistenciadata B ON A.fechaTenida=B.fechaTenida AND A.idLogia=B.idLogia WHERE A.idMiembro=$idm AND (B.numeroActa1>0 OR B.numeroActa2>0 OR B.numeroActa3>0) AND B.idLogia=$logia AND A.gestion=$gestion";
        $results = DB::select($qry);
        return $results[0]->ordinaria;
    }
    public static function getExtraTemplos($gestion, $logia, $idm)
    {
        $qry = "SELECT COUNT(C.idExtraTemplo) AS nExtraT FROM  sgm_extratemplos C INNER JOIN sgm_extratemploasis B ON (C.idExtraTemplo = B.idExtraTemplo)
        WHERE  C.idLogia=$logia AND C.gestion=$gestion AND B.idMiembro = $idm ";
        $results = DB::select($qry);
        return $results[0]->nExtraT;
    }
    public static function getItemsRito($gestion, $logia, $rito = 1)
    {
        if ($rito == 2) //york
        {
            $vig = 'COUNT(CASE WHEN (O.idoficial=19 OR O.idoficial=20 or O.idoficial=33 OR O.idoficial=34 or O.idoficial=6 or O.idoficial=7 ) THEN 1 ELSE null END)';
            $vem = 'O.idoficial=16 OR O.idoficial=17';
        } elseif ($rito == 3) //emulacion
        {
            $vig = 'COUNT(CASE WHEN (O.idoficial=19 OR O.idoficial=20 or O.idoficial=33 OR O.idoficial=34 or O.idoficial=6 or O.idoficial=7 ) THEN 1 ELSE null END)';
            $vem = 'O.idoficial=30 OR O.idoficial=31';
        } else // escoces
        { //(tesorero y orador) or seceretari
            $vig = 'SUM(CASE WHEN O.idoficial=7 THEN 3  WHEN O.idoficial=5 THEN 4  WHEN O.idoficial=6 THEN 7 ELSE null END)';
            //$vig = 'COUNT(CASE WHEN (O.idoficial=6 OR (O.idoficial=5 and O.idoficial=7) ) THEN 1 ELSE null END)';
            $vem = 'O.idoficial=3 OR O.idoficial=4';
        }
        DB::select("SET lc_time_names = 'es_ES'");
        $ant = $gestion - 1;
        $pago = $ant . '-12-01';
        $ultpago = $gestion . '-11-27';
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto,LEFT (A.Miembro,3) AS Miembro,A.FechaExaltacion,D.FechaNacimiento, A.Grado, DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago, CASE A.Grado WHEN 0 THEN 'No' WHEN 3 THEN 'M:.M:.' WHEN 4 THEN 'P/Ex VM' END AS GradoActual,
COUNT(O.idoficial) AS ncargos, $vig AS vigg, COUNT(CASE WHEN ($vem) THEN 1 ELSE null END) AS vene ,CASE WHEN (A.ultimoPago>'$ultpago' AND A.FechaExaltacion<'$pago') OR A.Miembro='Ad-Vitam' THEN 1 ELSE 0 END AS habil,G.protocolo
FROM sgm_miembros A  LEFT JOIN sgm_oficialidades O ON (O.idmiembro=A.id ) LEFT JOIN sgm_oficiales C ON ( (A.id=O.idmiembro AND C.id=O.idoficial)) left join sgm_miembrosdata D ON A.id=D.id LEFT JOIN sgm_grados G on A.Grado=G.Grado
WHERE (A.ultimoPago>='$pago' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND (A.LogiaActual = $logia OR A.LogiaAfiliada = $logia OR A.LogiaInspector = $logia) AND A.Grado > 2
GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,A.ultimoPago
ORDER BY A.NombreCompleto ASC";
        $results = DB::select($qry);
        return $results;
    }
    public static function getItemsGrado($gestion, $logia, $grado)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        if ($grado > 0) {
            $qry = "SELECT A.id,DATE_FORMAT(A.FechaIniciacion,'%d-%b-%Y') AS FechaIniciacion,DATE_FORMAT(A.FechaAumentoSalario,'%d-%b-%Y') AS FechaAumentoSalario,G.protocolo,
    DATE_FORMAT(A.FechaExaltacion,'%d-%b-%Y') AS FechaExaltacion ,A.LogiaActual, A.NombreCompleto, A.Grado,A.Miembro, DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago, CASE A.Grado WHEN 0 THEN 'NA' WHEN 1 THEN 'A:.M:.' WHEN 2 THEN 'C:.M:.' WHEN 3 THEN 'M:.M:.' WHEN 4 THEN 'Ex VM' END AS GradoActual
    FROM sgm_miembros A LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.idLogia='$logia') LEFT JOIN sgm_grados G on A.Grado=G.Grado
    WHERE (A.Estado=1) AND (A.LogiaActual = '$logia' OR A.LogiaAfiliada = '$logia' OR A.LogiaInspector = '$logia') AND (A.Grado=$grado)
    GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.ultimoPago
        ORDER BY A.Grado DESC,A.NombreCompleto ASC";
        } else {
            $qry = "SELECT A.id,DATE_FORMAT(A.FechaIniciacion,'%d-%b-%Y') AS FechaIniciacion,DATE_FORMAT(A.FechaAumentoSalario,'%d-%b-%Y') AS FechaAumentoSalario,G.protocolo,
    DATE_FORMAT(A.FechaExaltacion,'%d-%b-%Y') AS FechaExaltacion ,A.LogiaActual, A.NombreCompleto, A.Grado,A.Miembro,
    DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago,CASE A.Grado WHEN 0 THEN 'NA' WHEN 1 THEN 'A:.M:.' WHEN 2 THEN 'C:.M:.' WHEN 3 THEN 'M:.M:.' WHEN 4 THEN 'Ex VM' END AS GradoActual
    FROM sgm_miembros A LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.idLogia='$logia') LEFT JOIN sgm_grados G on A.Grado=G.Grado
    WHERE (A.Estado=1) AND (A.LogiaActual = '$logia' OR A.LogiaAfiliada = '$logia' OR A.LogiaInspector = '$logia')
    GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.ultimoPago
        ORDER BY A.Grado DESC,A.NombreCompleto ASC";
        }
        $results = DB::select($qry);
        return $results;
    }
    public static function getRitoTaller($logia)
    {
        $qry = "SELECT Rito FROM sgm_logias WHERE numero=$logia";
        $query = DB::select($qry);
        return $query[0]->Rito;
    }
    public static function getCantidadAsistida($gestion, $logia, $idm)
    {
        $qry = "SELECT count(DISTINCT A.fechaTenida) AS numero FROM  sgm_asistencia A INNER JOIN sgm_asistenciadata B ON A.fechaTenida=B.fechaTenida AND A.idLogia=B.idLogia WHERE A.idMiembro=$idm AND (B.numeroActa1>0 OR B.numeroActa2>0 OR B.numeroActa3>0) AND B.idLogia=$logia AND A.gestion=$gestion";
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getAsisExtra($gestion, $idm, $tipo = 0)
    {
        if ($tipo == 1) {
            $qry = "SELECT COUNT(idExtra) AS asisextra FROM sgm_asistenciaextra WHERE gestion=$gestion AND idMiembro=$idm  AND motivo=3";
        } else {
            $qry = "SELECT COUNT(idExtra) AS asisextra FROM sgm_asistenciaextra WHERE gestion=$gestion AND idMiembro=$idm AND motivo<>3";
        }
        $results = DB::select($qry);
        return $results[0]->asisextra;
    }
}

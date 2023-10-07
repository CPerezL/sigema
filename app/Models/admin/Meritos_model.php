<?php

namespace App\Models\admin;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meritos_model extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'idMerito';
    protected $table = 'sgm2_miembros_meritos';

    public static function getRegistros($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0, $estado = 0)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }
        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }
        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }
        if ($estado > 0) {
            $cond .= " AND A.Estado = '$estado'";
        }
        ///--------
        $qry = "SELECT A.id,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago,A.NombreCompleto, A.Miembro, DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,
        DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario, DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,C.valle,
        CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, B.logia,
        case when SUM(D.idMerito)>0 then 'Con antecedentes'  ELSE 'No' END AS obstxt,
        case when SUM(D.idMerito)>0 then 1  ELSE 0 END AS obs,B.numero
        FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero left join sgm_valles C on B.valle=C.idValle LEFT JOIN sgm2_miembros_meritos D ON A.id=D.idMiembro
        WHERE $cond GROUP BY  A.id,A.ultimoPago,A.NombreCompleto, A.Miembro, A.FechaIniciacion,A.FechaAumentoSalario, A.FechaExaltacion,C.valle,A.Grado,B.logia
        ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
//echo($qry);
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistros($palabra = '', $valle = 0, $taller = 0, $estado = 0)
    {
        $cond = " A.id > 0 ";
        if ($taller > 0) {
            $cond .= "AND A.LogiaActual=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.NombreCompleto Like '%$palabra%' ";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero  WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getMeritos($idm)
    {
        $qry = "SELECT A.idMerito,A.idTipoMerito,A.descripcion,DATE_FORMAT(A.fechaRegistro,'%d/%m/%Y') as fechaRegistro, A.estado,B.descripcion AS tipotxt,
        case A.estado when 1 then 'Descartado/Sin respaldo' when 2 then 'Aprobado/Comprobado' ELSE 'Registrado' END AS estadotxt
        FROM sgm2_miembros_meritos A JOIN sgm2_meritos_tipo B ON A.idTipoMerito=B.valor AND B.tipo=0  WHERE A.idMiembro=$idm ORDER BY A.fechaRegistro DESC  ";
        // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getTipoMeritos($tipo = 0)
    {
        $qry = DB::table('sgm2_meritos_tipo')->select('valor', 'descripcion')->where('tipo', '=', $tipo)->orderBy('valor', 'asc')->pluck('descripcion', 'valor');
        return $qry;
    }

    public static function getRegistrosLista($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0, $estado = 0)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }
        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }
        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }
        if ($estado > 0) {
            $cond .= " AND A.Estado = '$estado'";
        }
        ///--------
        $qry = "SELECT A.id,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago,A.NombreCompleto, A.Miembro, DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,
        DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario, DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,C.valle,
        CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, B.logia,
        case when SUM(D.idMerito)>0 then 'Con antecedentes'  ELSE '-' END AS obstxt,
        case when SUM(D.idMerito)>0 then 1  ELSE 0 END AS obs,B.numero
        FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero join sgm_valles C on B.valle=C.idValle LEFT JOIN sgm2_miembros_meritos D ON A.id=D.idMiembro
        WHERE $cond GROUP BY  A.id,A.ultimoPago,A.NombreCompleto, A.Miembro, A.FechaIniciacion,A.FechaAumentoSalario, A.FechaExaltacion,C.valle,A.Grado,B.logia
        HAVING SUM(D.idMerito)>0 ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
//echo($qry);
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistrosLista($palabra = '', $valle = 0, $taller = 0, $estado = 0)
    {
        $cond = " A.id > 0 ";
        if ($taller > 0) {
            $cond .= "AND A.LogiaActual=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.NombreCompleto Like '%$palabra%' ";
        }
        $qry = "SELECT count(DISTINCT A.id) as numero FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero JOIN sgm2_miembros_meritos D ON A.id=D.idMiembro WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
}

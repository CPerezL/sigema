<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observaciones_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idObservaciones';
    protected $table = 'sgm2_tramites_observaciones';

    public static function getRegistros($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        $cond = " LENGTH(A.nombres)> 3 ";
        if ($taller > 0) {
            $cond .= "AND B.numero=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $qry = "SELECT A.idTramite,A.fechaModificacion,A.apPaterno,A.apMaterno,A.nombres,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,
        case when SUM(E.idObservacion)>0 then 'Con observaciones'  ELSE 'No' END AS obstxt,A.documento
        FROM sgm_tramites_iniciacion A  LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero)  LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
        INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1
        LEFT JOIN sgm2_tramites_observaciones E ON E.idTramite=A.idTramite WHERE $cond
        GROUP BY  A.idTramite,A.fechaModificacion,A.apPaterno,A.apMaterno,A.nombres,B.logia,B.numero,C.valle,D.nivel ORDER BY A.fechaModificacion DESC,A.fInsinuacion DESC, A.idTramite DESC Limit $inicio,$cantidad ";
//echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistros($palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        $cond = " LENGTH(A.nombres)> 3 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getObservaciones($idt)
    {
        $qry = "SELECT A.fechaModificacion,E.idObservacion,DATE_FORMAT(E.fechaRegistro,'%d/%m/%Y') as fechaRegistro,E.descripcion,E.tipo,E.estado,
        case E.tipo when 1 then 'Balota negra' when 2 then 'Reporte de Miembro' END AS tipotxt, case E.estado when 1 then 'Descartado/Sin respaldo' when 2 then 'Aprobado/Comprobado' ELSE 'Registrado' END AS estadotxt
        FROM sgm_tramites_iniciacion A
        JOIN sgm2_tramites_observaciones E ON E.idTramite=A.idTramite
        WHERE E.grado=1 and A.idTramite=$idt ";
        // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getRegistrosLista($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " LENGTH(A.nombres)> 3 ";
        if ($taller > 0) {
            $cond .= "AND B.numero=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $qry = "SELECT A.idTramite,A.fechaModificacion,A.apPaterno,A.apMaterno,A.nombres,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,
        case when SUM(E.idObservacion)>0 then 'Con observaciones'  ELSE 'No' END AS obstxt,A.documento
        FROM sgm_tramites_iniciacion A  LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero)  LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
        INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1
        LEFT JOIN sgm2_tramites_observaciones E ON E.idTramite=A.idTramite WHERE $cond
        GROUP BY  A.idTramite,A.fechaModificacion,A.apPaterno,A.apMaterno,A.nombres,B.logia,B.numero,C.valle,D.nivel HAVING SUM(E.idObservacion)>0
        ORDER BY A.apPaterno,A.apMaterno,A.nombres Limit $inicio,$cantidad ";
//echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistrosLista($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " LENGTH(A.nombres)> 3 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        $qry = "SELECT count(distinct A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero)
        join sgm2_tramites_observaciones C ON A.idTramite=C.idTramite  WHERE $cond ";
        // echo $qry;
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
}

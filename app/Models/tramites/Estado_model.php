<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado_model extends Model
{
    use HasFactory;
    public static function getItems($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual>=0 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        /// aumentos
        $cond2 = " A.NivelActual>=0 ";
        if ($taller > 0) {
            $cond2 .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond2 .= "AND E.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond2 .= "AND B.valle=$valle ";
        }
        //exaltaciones
        $cond3 = " A.NivelActual>=0 ";
        if ($taller > 0) {
            $cond3 .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond3 .= "AND E.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond3 .= "AND B.valle=$valle ";
        }
        $inicio = $cantidad * ($pagina - 1);
        $qry = "SELECT 'Iniciacion' as tipo, A.idTramite,0 AS idMiembro,A.fechaCreacion,A.fechaModificacion,A.fInsinuacion AS fechaDato,CONCAT('R.L.S. ',B.Logia) AS nLogia,A.numTramite,B.numero,
    C.valle,D.nivel,concat(A.apPaterno,' ',A.apMaterno,' ',A.nombres) AS NombreCompleto
    FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1
    WHERE $cond union all
    SELECT 'Aumento' as tipo,A.idTramite,A.idMiembro,A.fechaCreacion,A.fechaModificacion,A.fechaIniciacion AS fechaDato,CONCAT('R.L.S. ',B.Logia) AS nLogia,A.idTramite AS numTramite,
    B.numero,C.valle,D.nivel, E.NombreCompleto
    FROM sgm_tramites_aumento A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=2
    JOIN sgm_miembros E ON A.idMiembro=E.id
    WHERE  $cond2 union all
    SELECT 'Exaltacion' as tipo,A.idTramite,A.idMiembro,A.fechaCreacion,A.fechaModificacion,A.fechaAumento AS fechaDato,CONCAT('R.L.S. ',B.Logia) AS nLogia,A.idTramite as numTramite,B.numero,C.valle,D.nivel, E.NombreCompleto
    FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=3
    JOIN sgm_miembros E ON A.idMiembro=E.id
    WHERE $cond3
    ORDER BY fechaModificacion DESC,fechaCreacion DESC Limit $inicio,$cantidad ";
        $query = DB::select($qry);
        return $query;
    }
    public static function getTotalItemsIni($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual>0 ";
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.logia Like '%$palabra%' ";
        }

        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $query = DB::select($qry);
        return $query[0]->numero;
    }
    public static function getTotalItemsAum($palabra = '', $taller = 0, $valle = 0)
    {
        $cond2 = " A.NivelActual>0 ";
        if ($taller > 0) {
            $cond2 .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond2 .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond2 .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_aumento A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=2
    JOIN sgm_miembros E ON A.idMiembro=E.id
    WHERE  $cond2 ";
        $query = DB::select($qry);
        return $query[0]->numero;
    }
    public static function getTotalItemsExa($palabra = '', $taller = 0, $valle = 0)
    {
        $cond3 = " A.NivelActual>0 ";
        if ($taller > 0) {
            $cond3 .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond3 .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond3 .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idTramite) as numero  FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=3
    JOIN sgm_miembros E ON A.idMiembro=E.id
    WHERE $cond3 ";
        $query = DB::select($qry);
        return $query[0]->numero;
    }
}

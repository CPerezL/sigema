<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obolos_model extends Model
{
    use HasFactory;
    private static $regular = 6;
    public $timestamps = false;
    protected $table = 'sgm_obolos';

    public static function getItems($pagina, $cantidad, $palabra, $valle, $taller, $grado, $estado, $regular = 6)
    {
        $inicio = $cantidad * ($pagina - 1);
        DB::select("SET lc_time_names = 'es_ES'");
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . $regular . " months")) . '-01';
        $anterior = $regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        $cond = "A.Estado = '$estado'";

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = $valle";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        $qry = "SELECT A.id,A.LogiaActual,A.NombreCompleto, A.Grado, A.Miembro, A.Estado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, "
            . "B.logia,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago, CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 2 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk"
            . " FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra, $valle, $taller, $grado, $estado)
    {
        $cond = "sgm_miembros.Estado = '$estado'";
        if ($grado > 0) {
            $cond .= " AND sgm_miembros.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND sgm_miembros.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND sgm_miembros.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND sgm_logias.valle = $valle";
        }

        $res = self::select('id')->from('sgm_miembros')->join('sgm_logias', 'sgm_miembros.LogiaActual', '=', 'sgm_logias.numero')->whereraw($cond)->count();
        return $res;
    }
    public static function getListaMiembros($pagina, $cantidad, $palabra, $valle, $taller, $grado, $estado)
    {
        $inicio = $cantidad * ($pagina - 1);
        DB::select("SET lc_time_names = 'es_ES'");
        $cond = "A.Estado = '$estado'";
        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = $valle";
        }
        ///------ controlde pagos
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01';
        $anterior = self::$regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        $obols = "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 2 ELSE 0 END AS upago";
        ///--------
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago, CASE WHEN A.ultimoPago is null THEN '1/1/1900' ELSE DATE_FORMAT(A.ultimoPago,'%m/1/%Y') END AS fechaPago,
        A.Grado, A.Miembro, A.Estado, B.logia ,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,$obols "
            . "FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumMiembros($palabra, $valle, $taller, $grado, $estado)
    {
        $cond = "sgm_miembros.Estado = '$estado'";
        if ($grado > 0) {
            $cond .= " AND sgm_miembros.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND sgm_miembros.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND sgm_miembros.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND sgm_logias.valle = $valle";
        }

        $res = self::select('id')->from('sgm_miembros')->join('sgm_logias', 'sgm_miembros.LogiaActual', '=', 'sgm_logias.numero')->whereraw($cond)->count();
        return $res;
    }
    public static function getUltimoObolo($id)
    {
        $query = DB::select("SELECT CASE WHEN ultimoPago is null THEN '31/12/2000' ELSE DATE_FORMAT(ultimoPago,'01/%m/%Y') END AS fechaPago FROM sgm_miembros WHERE id=$id");
        $row = $query[0];
        return $row->fechaPago;
    }
    public static function getTaller($id)
    {
        $query = DB::select('SELECT LogiaActual FROM sgm_miembros WHERE id=' . $id);
        $row = $query[0];
        return $row->LogiaActual;
    }
    public static function checkFecha($id, $fecha)
    {
        $query = DB::select("SELECT CASE WHEN ultimoPago is null THEN '2000-12-31' ELSE ultimoPago END AS ultimoPago FROM sgm_miembros WHERE id='$id'");
        $actual = $query[0]->ultimoPago;
        $date2 = date_create($fecha);
        $date1 = date_create($actual);
        if (is_null($actual) || $date2 > $date1) {
            return $actual;
        } else {
            return NULL;
        }
    }
}

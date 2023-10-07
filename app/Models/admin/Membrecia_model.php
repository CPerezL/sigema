<?php

namespace App\Models\admin;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membrecia_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_miembros';
    public static $regular = 6;
    use HasFactory;

    public static function setRegular($reg)
    {
        self::$regular = $reg;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado <>4 ) {
            $cond .= " AND A.Estado = '$estado'";
        }
        ///------ controlde pagos
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01';
        $anterior = self::$regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        $obols = "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 2 ELSE 0 END AS upago";
        ///--------
        $qry = "SELECT A.id,A.foto,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago,case when A.LogiaAfiliada>0 then concat('R.L.S. Nro. ',A.LogiaAfiliada) else '' end as LogiaAfiliada, A.NombreCompleto, A.Miembro, A.Estado, DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,
              DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario, DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,CONCAT(B.logia,' Nro. ',A.LogiaActual) AS nlogia,
              E.GradoActual, B.logia,D.texto AS estadotxt,C.valle,
              CASE A.jurisdiccion WHEN 0 THEN 'ND' WHEN 1 THEN 'G.L.S.P' WHEN 2 THEN 'Regularizado' WHEN 4 THEN 'Regular de otro oriente' ELSE 'G.L.S.P.' END AS Ingreso,FROM_UNIXTIME(lastlogin) AS lastlogin,$obols,'$newdate' AS uno,'$newdate2' AS dos
            FROM sgm_miembros A join sgm_grados E on E.Grado=A.Grado JOIN sgm2_miembrosestado D ON D.estado=A.Estado LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero left join sgm_valles C on B.valle=C.idValle
            WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
            // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {

        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado <> 4 ) {
            $cond .= " AND A.Estado = '$estado'";
        }

        $qry = "select count(*) as numero from sgm_miembros A left join sgm_logias B on A.LogiaActual = B.numero where $cond";
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
    public static function getForm($id, $task)
    {
        if ($task == 1) {
            $qry = "SELECT A.id,A.Kardex,A.Nombres,A.Paterno,A.Materno,A.Estado,A.username,B.ProfesionOficio,B.CI,B.Celular,B.Domicilio,B.EstadoCivil,B.NombreEsposa,B.NombrePadre,B.NombreMadre,B.Cargo,B.Trabajo,B.Pais,B.LugarNacimiento,B.email,B.TelefonoOficina,"
                . "DATE_FORMAT(B.FechaNacimiento,'%d/%m/%Y') AS FechaNacimiento,DATE_FORMAT(A.fechaDeceso,'%d/%m/%Y') AS fechaDeceso,B.Domicilio,B.TelefonoDomicilio FROM sgm_miembros A left join sgm_miembrosdata B ON A.id=B.id WHERE A.id=$id";
        } elseif ($task == 2) {
            $qry = "SELECT A.id,A.observaciones,A.LogiaIniciacion,A.LogiaAumento,A.username,A.LogiaExaltacion,A.LogiaActual,A.LogiaAfiliada,A.LogiaInspector,A.Grado,A.Miembro,A.DecretoHonorario,A.LogiaHonorario,A.DecretoAdMeritum,A.LogiaAdMeritum,A.DecretoAdVitam,A.LogiaAdVitam,
    DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario,DATE_FORMAT(A.FechaHonorario,'%d/%m/%Y') AS FechaHonorario,A.DecretoAfiliacion,A.jurisdiccion,A.socio,A.mesesprofano,
    DATE_FORMAT(A.FechaAdMeritum,'%d/%m/%Y') AS FechaAdMeritum,DATE_FORMAT(A.FechaAdVitam,'%d/%m/%Y') AS FechaAdVitam,DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,
    concat(A.LogiaActual,'_',A.Grado,'_',IFNULL(A.FechaIniciacion,'0'),'_',A.mesesprofano) AS control FROM sgm_miembros A WHERE A.id=$id";
        } elseif ($task == 3) {
            $qry = "SELECT A.id,A.Kardex,A.Nombres,A.Paterno,A.Materno,A.username,B.email,A.clave FROM sgm_miembros A left join sgm_miembrosdata B ON A.id=B.id WHERE A.id=$id";
        }
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getItemsAll($pagina, $cantidad, $palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado >= 0 && $estado != 4 && $estado < 100) {
            $cond .= " AND A.Estado = '$estado'";
        }
        ///------ controlde pagos
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01';
        $anterior = self::$regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        $obols = "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 2 ELSE 0 END AS upago";
        ///--------
        $qry = "SELECT A.id,A.foto,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago,A.LogiaAfiliada, A.NombreCompleto, A.Miembro, A.Estado, DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,
              DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario, DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,CONCAT(B.logia,' Nro. ',A.LogiaActual) AS nlogia,
              CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, B.logia
              ,CASE A.jurisdiccion WHEN 0 THEN 'ND' WHEN 1 THEN 'G.L.B.' WHEN 2 THEN 'Reg.' WHEN 3 THEN 'Otro O.' END AS Ingreso,FROM_UNIXTIME(lastlogin) AS lastlogin,$obols,'$newdate' AS uno,'$newdate2' AS dos,
              C.valle
            FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero join sgm_valles C on B.valle=C.idValle WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItemsAll($palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {

        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado >= 0 && $estado != 4 && $estado < 100) {
            $cond .= " AND A.Estado = '$estado'";
        }

        $qry = "select count(*) as numero from sgm_miembros A left join sgm_logias B on A.LogiaActual = B.numero where $cond";
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
}

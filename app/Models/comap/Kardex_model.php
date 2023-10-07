<?php

namespace App\Models\comap;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex_model extends Model
{
    public static $regular = 6;
    use HasFactory;
    public static function getItems($pagina, $cantidad, $palabra, $valle, $taller, $grado, $estado)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        if ($estado == 4) {
            $cond = "A.Estado >=0";
        } else {
            $cond = "A.Estado = '$estado'";
        }
        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }
        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }
        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -" . self::$regular . " months")) . '-01';
        $anterior = self::$regular - 1;
        $newdate2 = date("Y-m", strtotime("$hoy -$anterior months")) . '-01';
        $obols = "CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 WHEN (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 2 ELSE 0 END AS upago";
        $qry = "SELECT A.id,A.Kardex,DATE_FORMAT(A.ultimoPago,'%b - %Y') AS ultimoPago,A.LogiaActual,A.jurisdiccion,case when A.LogiaAfiliada>0 then concat('R.L.S. Nro. ',A.LogiaAfiliada) else '' end as LogiaAfiliada,A.LogiaInspector,A.Nombres,A.Paterno,A.Materno,A.NombreCompleto,A.Grado, A.Miembro, A.Estado, DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion,
        DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario, DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,DATE_FORMAT(A.FechaHonorario,'%d/%m/%Y') AS FechaHonorario,
        DATE_FORMAT(A.FechaAdMeritum,'%d/%m/%Y') AS FechaAdMeritum,DATE_FORMAT(A.FechaAdVitam,'%d/%m/%Y') AS FechaAdVitam,A.DecretoAdVitam,A.DecretoAdMeritum,DecretoHonorario,LogiaHonorario,
        LogiaAdMeritum,LogiaAdVitam,CONCAT(B.logia,' Nro. ',A.LogiaActual) AS nlogia, B.logia,C.valle,
        CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
        ,CASE A.jurisdiccion WHEN 0 THEN 'ND' WHEN 1 THEN 'G.L.B.' WHEN 2 THEN 'Reg.' WHEN 3 THEN 'Otro O.' END AS Ingreso,$obols,
        CASE A.Estado WHEN 0 THEN 'Inactivo' WHEN 1 THEN 'Activo' WHEN 2 THEN 'Fallecido' ELSE 'Ninguno' END AS Estadotxt
        FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero left join sgm_valles C on B.valle=C.idValle WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra, $valle, $taller, $grado, $estado)
    {
        if ($estado == 4) {
            $cond = "sgm_miembros.Estado >=0";
        } else {
            $cond = "sgm_miembros.Estado = '$estado'";
        }
        if ($grado > 0) {
            $cond .= " AND sgm_miembros.Grado = '$grado'";
        }
        if ($taller > 0) {
            $cond .= " AND sgm_miembros.LogiaActual = '$taller'";
        }
        if ($valle > 0) {
            $cond .= " AND sgm_logias.valle = '$valle'";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND sgm_miembros.NombreCompleto Like '%$palabra%'";
        }
        $results = self::from('sgm_miembros')->leftjoin('sgm_logias', 'sgm_logias.numero', '=', 'sgm_miembros.LogiaActual')->select('sgm_miembros.id')->whereraw($cond)->count();
        return $results;
    }

    public static function getMiembro($id)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT A.Estado,A.Paterno,A.Materno,A.Nombres,B.FechaNacimiento,B.EstadoCivil,B.CI,B.ProfesionOficio,B.Domicilio,B.TelefonoDomicilio,B.Trabajo,B.Celular,B.email,A.LogiaActual,A.Grado,A.Miembro,
    A.LogiaIniciacion,A.LogiaAumento,A.LogiaExaltacion,A.LogiaHonorario,A.LogiaAdMeritum,A.LogiaAdVitam,A.FechaIniciacion,A.FechaAumentoSalario,A.FechaExaltacion,A.FechaHonorario,A.FechaAdMeritum,A.FechaAdVitam,
    A.DecretoHonorario,A.DecretoAdMeritum,A.DecretoAdVitam,A.DecretoAfiliacion,A.LogiaAfiliada,C.logia,D.valle,E.GradoActual, DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago,B.Pais,B.LugarNacimiento,A.foto,A.observaciones,B.TelefonoOficina,
    CASE A.Estado WHEN 0 THEN 'Inhabilitado' WHEN 1 THEN 'Activo-Regular' WHEN 2 THEN 'Fallecido' ELSE 'Sin Datos' END AS Estadotxt
    FROM sgm_miembros A LEFT JOIN sgm_miembrosdata B ON A.id=B.id LEFT JOIN sgm_logias C ON A.LogiaActual=C.numero  JOIN sgm_valles D ON C.valle=D.idValle JOIN sgm_grados E ON A.Grado=E.Grado
    WHERE A.id=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getTalleres($list)
    {
        if (count($list) > 0) {
            foreach ($list as $key => $link) {
                if ($link > 0) {

                } else {
                    unset($list[$key]);
                }
            }
        }
        //limpinado
        $arreglo[0] = '';
        if (count($list) > 0) {
            $aaa = implode(',', $list);
            $query = DB::select("SELECT distinct numero,logia FROM sgm_logias WHERE numero IN ($aaa) ORDER BY numero");
            foreach ($query as $sem) {
                if ($sem->numero > 0) {
                    $arreglo[$sem->numero] = 'R:.L:.S:. ' . $sem->logia . ' Nro. ' . $sem->numero;
                }
            }
        }
        return $arreglo;
    }
    public static function getCargos($idm)
    {
        $qry = "SELECT A.gestion,B.oficial,B.orden,A.idlogia,C.logia FROM sgm_oficialidades A JOIN  sgm_oficiales B ON A.idoficial=B.id JOIN sgm_logias C ON A.idlogia=C.numero WHERE A.idmiembro=$idm ORDER BY A.gestion ASC, B.orden DESC";
        $results = DB::select($qry);
        // dd($results);
        return $results;

    }
    public static function getCargosGLB($idm)
    {
        $qry = "SELECT C.descripcion,B.oficial,C.desde AS gestion,CONCAT(C.desde,'-',C.hasta) AS gestiontxt,E.nombre AS lugar FROM sgm_glbof_miembros A
        JOIN sgm_glbof_cargos B ON A.idOficial=B.idOficial JOIN sgm_glbof_gestiones C ON A.idGestion=C.idGestion JOIN sgm_miembros D ON A.idMiembro=D.id
        JOIN sgm_glbof_comisiones E ON C.tipo=E.idComision  WHERE A.idMiembro=$idm ORDER BY gestion";
        $results = DB::select($qry);
        return $results;
    }
}

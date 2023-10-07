<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificados_model extends Model
{
    use HasFactory;
    public static function getIniciados($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " A.certificado=1 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND (A.apPaterno Like '%$palabra%' OR A.apMaterno Like '%$palabra%')";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idTramite,A.fechaIniciacion,CASE WHEN (A.nivelActual=7) THEN 'Aprobada' ELSE 'No aprobada' END AS okCeremonia,B.Logia AS nLogia,B.numero,A.fechaModificacion,D.valle,
        A.nivelActual,case when A.pagoQR>0 THEN 'Pago con QR' when LENGTH(A.okPagoDerechos=1) then 'Deposito' ELSE 'Sin Pago' end AS estadotxt ,
        concat(A.nombres,' ',A.apPaterno,' ',A.apMaterno) AS NombreCompleto, A.certificado,A.fechaCertificado,A.numeroCertificado
        FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles D ON B.valle=D.idValle WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumIniciados($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = "A.certificado=1 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND (A.apPaterno Like '%$palabra%' OR A.apMaterno Like '%$palabra%')";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero WHERE $cond ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getAumentados($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " C.certificado=1 and C.nivelActual=5  ";
        if ($taller > 0) {
            $cond .= "AND C.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT C.idTramite,C.fechaAumento,B.Logia AS nLogia,B.numero,
        CASE WHEN (C.okDepoGDR=1) THEN 'Deposito revisado' ELSE 'Sin revision' END AS okDeposito,C.fechaModificacion,D.valle
             ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGDR)>5 then 'Deposito' ELSE 'Sin Pago' end AS estadotxt ,case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado
               ,C.idMiembro,E.NombreCompleto,C.idTramite,C.certificado,C.numeroCertificado,C.fechaCertificado,
               E.Grado,E.FechaAumentoSalario, case when E.Grado=1 then 'Falta Actualizar datos' when E.Grado >0 then 'Actualizado' ELSE 'Error' END AS estadomie
             FROM sgm_tramites_aumento C JOIN sgm_logias B ON C.logia=B.numero JOIN sgm_valles D ON B.valle=D.idValle JOIN sgm_miembros E ON C.idMiembro=E.id
      WHERE $cond ORDER BY C.fechaAumento DESC,B.numero Limit $inicio,$cantidad  ";
        // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumAumentados($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " A.certificado=1 and A.nivelActual=5  ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_aumento A LEFT JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_miembros E ON A.idMiembro=E.id  WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }

    public static function getDatosCertificado($id)
    {
        $qry = "SELECT B.nombreCompleto,A.fechaCertificado,D.valle,A.numeroCertificado,concat(A.nombres,' ',A.apPaterno,' ',A.apMaterno) AS nombretxt,B.rito,D.tipo,A.codGes
        FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles D ON B.valle=D.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu;
    }
    public static function getDatosCertificadoAum($id)
    {
        $qry = "SELECT B.nombreCompleto, A.fechaCertificado,C.valle,A.numeroCertificado,CONCAT(D.Nombres,' ',D.Paterno, ' ' ,D.Materno) AS nombretxt, B.rito,C.tipo,A.codGes
        FROM sgm_tramites_aumento A JOIN sgm_miembros D ON A.idMiembro=D.id JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu;
    }
    public static function getDatosCertificadoAfi($id)
    {
        $qry = "SELECT A.fechaJuramento,B.Grado,CONCAT (B.Nombres,' ',B.Paterno, ' ', B.Materno) AS nombretxt ,C.nombreCompleto,D.valle,C.numero
        FROM sgm2_tramites_afiliaciones A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_logias C ON A.idLogiaNueva=C.idLogia JOIN sgm_valles D ON C.valle=D.idValle
        WHERE A.id=$id";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu;
    }
    public static function getExaltados($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " C.certificado=1 and C.nivelActual=5  ";
        if ($taller > 0) {
            $cond .= "AND C.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT C.idTramite,C.fechaExaltacion as fechaCere,B.Logia AS nLogia,B.numero,CASE WHEN (C.okDepoGDR=1) THEN 'Deposito revisado' ELSE 'Sin revision' END AS okDeposito,C.fechaModificacion,D.valle
             ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGDR)>5 then 'Deposito' ELSE 'Sin Pago' end AS estadotxt,
             case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado,C.idMiembro,E.NombreCompleto,C.idTramite,C.certificado,C.numeroCertificado,C.fechaCertificado,
               E.Grado,E.fechaExaltacion, case when E.Grado=2 then 'Falta Actualizar datos' when E.Grado >0 then 'Actualizado' ELSE 'Error' END AS estadomie
             FROM sgm_tramites_exaltacion C JOIN sgm_logias B ON C.logia=B.numero JOIN sgm_valles D ON B.valle=D.idValle JOIN sgm_miembros E ON C.idMiembro=E.id
      WHERE $cond ORDER BY C.fechaExaltacion DESC,B.numero Limit $inicio,$cantidad  ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumExaltados($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " A.certificado=1 and A.nivelActual=5  ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        // $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_exaltacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) LEFT JOIN sgm_tramites_ceremonias F ON A.idCeremonia=F.idCeremonia JOIN sgm_miembros E ON A.idMiembro=E.id  WHERE $cond  ";
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_exaltacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_miembros E ON A.idMiembro=E.id  WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getDatosCertificadoExa($id)
    {
        $qry = "SELECT B.nombreCompleto, A.fechaCertificado,C.valle,A.numeroCertificado,CONCAT(D.Nombres,' ',D.Paterno, ' ' ,D.Materno) AS nombretxt, B.rito,C.tipo,A.codGes
  FROM sgm_tramites_exaltacion A JOIN sgm_miembros D ON A.idMiembro=D.id JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu;
    }
    public static function getFirmaCertificado($idges, $idof)
    { //3-----1,7,6
        $qry = "SELECT B.Nombres,B.Paterno,B.Materno,C.oficial,A.id,A.idOficial,A.idMiembro,A.idGestion from sgm_glbof_miembros A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_glbof_cargos C ON A.idOficial=C.idOficial
        WHERE A.idGestion=$idges AND A.idOficial=$idof";
        $results = DB::select($qry);
        if (count($results) > 0) {
            return $results[0];
        } else {
            return null;
        }

    }
}

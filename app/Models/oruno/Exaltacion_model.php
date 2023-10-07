<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exaltacion_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idTramite';
    protected $table = 'sgm_tramites_exaltacion';
    private static $grado = 3;
    public static function getItems($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " A.idCeremonia>0 ";
        if ($taller > 0) {
            $cond .= "AND C.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $cond .= " and A.okCeremonia=1";
        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Aprobada' ELSE 'No aprobada' END AS okCeremonia,B.Logia AS nLogia,B.numero,
    (C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
    ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Deposito' ELSE 'Sin Pago' end AS estadotxt ,case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado
      ,C.idMiembro,E.NombreCompleto,C.idTramite,C.certificado,C.numeroCertificado,C.fechaCertificado
    FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle JOIN sgm_miembros E ON C.idMiembro=E.id
    WHERE $cond ORDER BY A.fechaCeremonia DESC,B.numero Limit $inicio,$cantidad  ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " A.idCeremonia>0 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND E.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $cond .= " and F.okCeremonia=1";
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_exaltacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) LEFT JOIN sgm_tramites_ceremonias F ON A.idCeremonia=F.idCeremonia JOIN sgm_miembros E ON A.idMiembro=E.id  WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramites($ids)
    {
        $data = array();
        $cc = 1;
        foreach ($ids as $apres) {
            $query = DB::select("SELECT A.NombreCompleto,DATE_FORMAT(B.fechaAumento,'%d/%m/%Y') AS fechaCeremonia,B.certificado,B.numeroCertificado,B.okDepoGDR
            FROM sgm_miembros A join sgm_tramites_exaltacion B on B.idMiembro=A.id WHERE B.idTramite=$apres");
            $value = $query[0];
            $data['apreName' . $cc] = $value->NombreCompleto;
            $data['idMiembro' . $cc] = $apres;
            $data['certificado' . $cc] = $value->certificado;
            $data['fechaCeremonia' . $cc] = $value->fechaCeremonia;
            $numero = self::select('numeroCertificado as numero')->orderBy('numeroCertificado', 'DESC')->first('numero');
            if ($value->numeroCertificado > 0) {
                $data['numero' . $cc] = $value->numeroCertificado;
            } else {
                $data['numero' . $cc] = $numero->numero + 1;
            }
            $cc++;
        }
        return $data;
    }
    public static function getDepositos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta

        $cond = 'A.Grado=3 ';
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.apPaterno Like '%$palabra%' ";
        }
        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
COUNT(C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero LEFT JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle
WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";

        $results = DB::select($qry);
        return $results;
    }
    public static function getNumDepositos($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = 'A.Grado=3 ';
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.logia Like '%$palabra%' ";
        }
        $qry = "SELECT A.idCeremonia FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero
    WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,B.valle";
        $results = DB::select($qry);
        $resu = count($results);
        return $resu;
    }
    public static function getDatosTramite($id)
    {
        $query = DB::select("SELECT B.idMiembro,B.logia,A.NombreCompleto,B.depositoGLB,B.depositoGDR,B.okDepoGLB,B.okDepoGDR FROM sgm_tramites_exaltacion B JOIN sgm_miembros A ON B.idMiembro=A.id WHERE B.idCeremonia=$id");
        $cc = 1;
        foreach ($query as $value) {
            $data['apreName' . $cc] = $cc . ' .- C:.M:. ' . $value->NombreCompleto;
            $data['idMiembro' . $cc] = $value->idMiembro;
            if ($cc == 1) {
                $data['depositoGLB'] = $value->depositoGLB;
                $data['depositoGDR'] = $value->depositoGDR;
                $data['okDepoGLB'] = $value->okDepoGLB;
                $data['okDepoGDR'] = $value->okDepoGDR;
            }
            $cc++;
        }
        $qry = "SELECT DATE_FORMAT(B.fechaCeremonia,'%d/%m/%Y') AS fechaCeremonia,CONCAT('R.L.S. ',A.Logia,' Nro. ',A.numero) AS nLogia,C.valle,B.okCeremonia from sgm_tramites_ceremonias B JOIN sgm_logias A ON B.idLogia=A.numero
          JOIN sgm_valles C ON C.idValle=A.valle WHERE B.idCeremonia=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['okCeremonia'] = $valuec->okCeremonia;
        $data['fechaCeremonia'] = $valuec->fechaCeremonia;
        return $data;
    }
    /***********************************************************************************************************************************************************/
    public static function getCompaneros($pagina, $cantidad,  $taller = 0, $regu = 6, $apremes = 6)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -$regu months")) . '-01'; //fecha de corte de obolo
        $newdate2 = date("Y-m", strtotime("$hoy -$apremes months")) . '-01'; //un año de iniciciacoi
        $cond = "A.Grado=2 ";
        if ($taller > 0) {
            $cond .= "AND A.LogiaActual=$taller ";
        }
        $qry = "SELECT A.id,A.NombreCompleto,A.ultimoPago,A.FechaIniciacion,A.FechaAumentoSalario,CASE WHEN (A.ultimoPago>='$newdate') THEN 1 ELSE 0 END AS pagoOk,CASE WHEN (A.FechaAumentoSalario<'$newdate2') THEN 1 ELSE 0 END AS antOk,
CASE WHEN (A.ultimoPago>='$newdate') THEN 'A Cubierto' ELSE 'Inhabil' END AS cubierto,CASE WHEN (A.FechaAumentoSalario<'$newdate2') THEN 'Suficiente' ELSE 'Insuficiente' END AS antiguedad,
CASE WHEN (B.idTRamite) THEN 'En proceso' ELSE '' END AS tramite,L.nombreCompleto as logian FROM sgm_miembros A JOIN sgm_logias L ON A.LogiaActual=L.numero
LEFT JOIN sgm_tramites_exaltacion B ON A.id=B.idMiembro WHERE $cond AND A.ultimoPago>='$newdate' ORDER BY pagoOK DESC,antOk DESC Limit $inicio,$cantidad";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCompaneros($taller = 0, $regu = 6)
    {

        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -$regu months")) . '-01'; //fecha de corte de obolo
        $cond = "A.Grado=2 ";
        if ($taller > 0) {
            $cond .= "AND A.LogiaActual=$taller ";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm_miembros A WHERE $cond AND A.ultimoPago>='$newdate' ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }

    public static function getAsisExtra($gestion, $idm, $tipo = 0)
    {
        if ($tipo == 1) {
            $qry = "SELECT COUNT(idExtra) AS asisextra FROM sgm_asistenciaextra WHERE gestion=$gestion AND idMiembro=$idm  AND motivo=3";
        } else {
            $qry = "SELECT COUNT(idExtra) AS asisextra FROM sgm_asistenciaextra WHERE gestion=$gestion AND idMiembro=$idm AND motivo<>3";
        }
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->asisextra;
    }
    public static function getCantidadTenidas($gestion, $logia)
    {
        $qry = "SELECT COUNT(DISTINCT A.fechaTenida) AS nTenidas FROM sgm_asistenciadata A WHERE A.idLogia=$logia AND A.fechaTenida>'$gestion-01-01' AND A.fechaTenida<'$gestion-12-31' AND (A.numeroActa1>0)";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->nTenidas;
    }
    public static function getExtraTemplos($gestion, $logia, $idm)
    {
        $qry = "SELECT COUNT(C.idExtraTemplo) AS nExtraT FROM  sgm_extratemplos C INNER JOIN sgm_extratemploasis B ON (C.idExtraTemplo = B.idExtraTemplo)
WHERE  C.idLogia=$logia AND C.gestion=$gestion AND B.idMiembro = $idm ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->nExtraT;
    }
    public static function getAsistencia($gestion, $logia, $idm)
    {
        $qry = "SELECT count(DISTINCT A.fechaTenida) AS ordinaria FROM sgm_asistencia A INNER JOIN sgm_asistenciadata B ON A.fechaTenida=B.fechaTenida AND A.idLogia=B.idLogia WHERE A.idMiembro=$idm AND (B.numeroActa1>0) AND B.idLogia=$logia AND A.gestion=$gestion";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->ordinaria;
    }
    public static function getMinimo($id, $fecha)
    {
        $qry = "SELECT COUNT(A.idAsistencia) AS numero FROM sgm_asistencia  A JOIN sgm_asistenciadata B ON A.idLogia=B.idLogia AND A.fechaTenida=B.fechaTenida WHERE A.idMiembro=$id AND A.fechaTenida>'$fecha' AND B.numeroActa1>0";
        $results = DB::select($qry);
        if (count($results) > 0) {
            $resu = $results[0];
            return $resu->numero;
        } else {
            return 0;
        }
    }
    public static function getTramite($id)
    {
        $qry = "SELECT A.idTramite,M.id AS idMiembro,M.NombreCompleto AS compaName,CONCAT('R.L.S',' ',L.logia,' Nro. ',L.numero) AS logiaName,V.valle, A.logia,A.actaIniciacion,DATE_FORMAT(A.fechaIniciacion,'%d/%m/%Y') AS fechaIniciacion,
      A.actaPase,DATE_FORMAT(A.fechaPase,'%d/%m/%Y') AS fechaPase,A.actaExamen,DATE_FORMAT(A.fechaExamen,'%d/%m/%Y') AS fechaExamen,DATE_FORMAT(A.fechaAumento,'%d/%m/%Y') AS fechaAumento,A.actaAumento,
      A.okIniciacion,A.okAumento,A.okSolicitud,A.okExamen,A.okAsistencia,A.okTrabajos,A.okComision,DATE_FORMAT(A.fechaExaltacion,'%d/%m/%Y') AS fechaExaltacion
      FROM sgm_miembros M JOIN sgm_logias L ON M.LogiaActual=L.numero JOIN sgm_valles V ON L.valle=V.idValle JOIN sgm_tramites_exaltacion A ON M.id=A.idMiembro WHERE M.id=$id";
        $results = DB::select($qry);
        if (count($results) > 0) {
            $resu = $results[0];
            return $resu;
        } else {
            return null;
        }
    }
    public static function getExaTramite($id)
    {
        $qry = "SELECT M.id AS idMiembro,M.NombreCompleto AS compaName,CONCAT('R.L.S',' ',L.logia,' Nro. ',L.numero) AS logiaName,V.valle,DATE_FORMAT(M.FechaIniciacion,'%d/%m/%Y') AS fechaIniciacion,DATE_FORMAT(M.FechaAumentoSalario,'%d/%m/%Y') AS fechaAumento, 0 AS idTramite FROM sgm_miembros M JOIN sgm_logias L ON M.LogiaActual=L.numero JOIN sgm_valles V ON L.valle=V.idValle WHERE M.id=$id";
        // echo($qry);
        $results = DB::select($qry);
        if (count($results) > 0) {
            $resu = $results[0];
            return $resu;
        } else {
            return null;
        }
    }
    public static function getRegistros($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT A.idTramite,A.idMiembro,A.fechaCreacion,A.fechaModificacion,A.fechaIniciacion,A.fechaAumento,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,
        E.NombreCompleto,A.fechaExaltacion
        FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=3
        JOIN sgm_miembros E ON A.idMiembro=E.id WHERE  $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistros($palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) WHERE  $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramitesListos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idTramite,A.idMiembro,A.fechaCreacion,A.fechaModificacion,A.fechaIniciacion,A.fechaAumento,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel, E.NombreCompleto
,IFNULL(F.fechaCeremonia, 'Sin fecha programada') AS fCeremonia,CASE WHEN (F.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaExaltacion,
case when A.pagoQR>0 THEN 'Pago con QR' when LENGTH(A.depositoGDR)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt
FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero)
LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=3 JOIN sgm_miembros E ON A.idMiembro=E.id
LEFT JOIN sgm_tramites_ceremonias F ON A.idCeremonia=F.idCeremonia
WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad  ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumTramitesListos($palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_exaltacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getListaExaltados($ids)
    {
        $data = array();
        $cc = 1;
        foreach ($ids as $apres) {
            $query = DB::select("SELECT A.NombreCompleto FROM sgm_miembros A WHERE A.id=$apres");
            $value = $query[0];
            $data['apreName' . $cc] = $value->NombreCompleto;
            $data['idMiembro' . $cc] = $apres;
            $cc++;
        }
        $qry = "SELECT CONCAT('R.L.S',' ',L.logia,' Nro. ',L.numero) AS logiaName,V.valle, A.logia,A.depositoGDR,A.depositoGLB, DATE_FORMAT(C.fechaCeremonia,'%d/%m/%Y') AS fechaCeremonia ,V.idValle
      FROM sgm_miembros M JOIN sgm_logias L ON M.LogiaActual=L.numero JOIN sgm_valles V ON L.valle=V.idValle JOIN sgm_tramites_exaltacion A ON M.id=A.idMiembro LEFT JOIN sgm_tramites_ceremonias C ON A.idCeremonia =C.idCeremonia
      WHERE M.id=$ids[0]";
        $results = DB::select($qry);
        $value = $results[0];
        $data['depositoGDR'] = $value->depositoGDR;
        $data['depositoGLB'] = $value->depositoGLB;
        $data['monto'] = self::getMontoTotal(3, $value->idValle) * ($cc - 1);
        $data['valle'] = $value->valle;
        $data['logiaName'] = $value->logiaName;
        $data['fechaCeremonia'] = $value->fechaCeremonia;
        return $data;
    }
    private static function getMontoTotal($grado, $vall)
    {
        $tipo = $grado + 1;
        $qry = "SELECT SUM(A.monto) AS montos FROM sgm_mecom_tramites_montos A WHERE A.idValle IN(0,$vall) AND A.tipo=$tipo GROUP BY A.idValle,A.miembro,A.orden ORDER BY A.idValle DESC";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->montos;
    }
    public static function getCeremonias($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = 'A.Grado=3 ';
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

//         $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
// COUNT(C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
// FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero LEFT JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle
// WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";

        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
COUNT(C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle,C.depositoGDR,C.depositoGLB,
case when SUM(C.pagoQR)>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS pagados, C.pagoQR,case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 2 ELSE 0 end AS estado,
case when(COUNT(C.idTramite) - SUM(case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 END))>0 then 'Pago incompleto' when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt
FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle
WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremonias($palabra = '', $valle = 0, $taller = 0)
    {
        $cond = 'A.Grado=3 ';
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idCeremonia) as numero FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,B.valle";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getCeremoniaExa($id)
    {
        $query = DB::select("SELECT B.idMiembro,B.logia,A.NombreCompleto,B.depositoGLB,B.depositoGDR,B.okDepoGLB,B.okDepoGDR,B.pagoQR  FROM sgm_tramites_exaltacion B JOIN sgm_miembros A ON B.idMiembro=A.id WHERE B.idCeremonia=$id");
        $cc = 1;
        foreach ($query as $value) {
            if ($value->pagoQR > 0 || strlen($value->depositoGLB) > 7) {
                $data['apreName' . $cc] = 'C:.M:. ' . $value->NombreCompleto;
            } else {
                $aux = 0;
                $data['apreName' . $cc] = 'NO PAGADO - A:.M:. ' . $value->NombreCompleto;
            }
            $data['idMiembro' . $cc] = $value->idMiembro;
            $data['depositoGLB'] = $value->depositoGLB;
            $data['depositoGDR'] = $value->depositoGDR;
            $data['okDepoGLB'] = $value->okDepoGLB;
            $data['okDepoGDR'] = $value->okDepoGDR;
            $cc++;
        }
        $qry = "SELECT DATE_FORMAT(B.fechaCeremonia,'%d/%m/%Y') AS fechaCeremonia,CONCAT('R.L.S. ',A.Logia,' Nro. ',A.numero) AS nLogia,C.valle,B.okCeremonia from sgm_tramites_ceremonias B JOIN sgm_logias A ON B.idLogia=A.numero
        JOIN sgm_valles C ON C.idValle=A.valle WHERE B.idCeremonia=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['okCeremonia'] = $valuec->okCeremonia;
        $data['fechaCeremonia'] = $valuec->fechaCeremonia;
        return $data;
    }
    public static function getTramiteExa($id)
    {
        $qry = "SELECT A.idTramite,M.id AS idMiembro,M.NombreCompleto AS apreName,CONCAT('R.L.S',' ',L.logia,' Nro. ',L.numero) AS logiaName,V.valle, A.logia,A.actaIniciacion,
        DATE_FORMAT(A.fechaIniciacion,'%d/%m/%Y') AS fechaIniciacion,A.actaPase,DATE_FORMAT(A.fechaPase,'%d/%m/%Y') AS fechaPase,A.actaExamen,DATE_FORMAT(A.fechaExamen,'%d/%m/%Y') AS fechaExamen,
        A.okIniciacion,A.okSolicitud,A.okExamen,A.okTrabajos,okAsistencia,DATE_FORMAT(A.fechaExaltacion,'%d/%m/%Y') AS fechaExaltacion
      FROM sgm_miembros M JOIN sgm_logias L ON M.LogiaActual=L.numero JOIN sgm_valles V ON L.valle=V.idValle JOIN sgm_tramites_exaltacion A ON M.id=A.idMiembro WHERE A.idTramite=$id";
        $results = DB::select($qry);
        $resu = count($results);
        if ($resu > 0) {
            return $results[0];
        } else {
            return null;
        }
    }
    public static function getTramitesPagos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT A.idTramite,A.idMiembro,A.fechaModificacion,A.fechaIniciacion,B.logia,B.numero,D.nivel, E.NombreCompleto,
        IFNULL(A.fechaExaltacion, 'Sin fecha programada') AS fechaExaltacion,case when A.pagoQR=0 then 'Pago con deposito' ELSE 'Pago con QR' end AS estadopago,A.numeroCertificado,A.fechaCertificado,
        case when A.okDepoGDR=1 then 'Revisado' ELSE 'Sin Revision' end AS estadotxt,A.depositoGDR,A.okDepoGDR
        FROM sgm_tramites_exaltacion A JOIN sgm_logias B ON (A.logia=B.numero)
        LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=2 LEFT JOIN sgm_miembros E ON A.idMiembro=E.id
         WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad  ";
//  echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumTramitesPagos($palabra = '', $taller = 0, $valle = 0, $paso = 0)
    {
        $cond = " A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND E.apPaterno Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT count(distinct A.idTramite)  as numero FROM sgm_tramites_exaltacion A JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_miembros E ON A.idMiembro=E.id WHERE $cond ";
//  echo $qry;
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
       ////-**************
       public static function getAsistenciaMeses($logia, $idm, $meses, $opcion = 0)
       {
           $hoy = date("Y-m-d");
           $corte = date("Y-m-d", strtotime("$hoy -$meses months")); //fecha de corte de obolo
           if ($opcion == 1) { //si es visita
               $qry = "SELECT count(DISTINCT A.fechaTenida) AS numero FROM sgm_asistencia A JOIN sgm_asistenciadata B ON A.fechaTenida=B.fechaTenida AND A.idLogia=B.idLogia
               WHERE A.idMiembro=$idm AND (B.numeroActa2>0) AND B.fechaTenida>'$corte'";
           } else {
               $qry = "SELECT count(DISTINCT A.fechaTenida) AS numero FROM sgm_asistencia A JOIN sgm_asistenciadata B ON A.fechaTenida=B.fechaTenida AND A.idLogia=B.idLogia
               WHERE A.idMiembro=$idm AND (B.numeroActa2>0) AND B.idLogia=$logia AND B.fechaTenida>'$corte'";
           }
           $results = DB::select($qry);
           return $results[0]->numero;
       }
       public static function getTenidasMeses($logia, $meses)
       {
           $hoy = date("Y-m-d");
           $corte = date("Y-m-d", strtotime("$hoy -$meses months")); //fecha de corte de obolo
           $qry = "SELECT  count(B.fechaTenida) AS numero  from sgm_asistenciadata B WHERE B.numeroActa2>0 AND B.idLogia=$logia AND B.fechaTenida>'$corte'";
           $results = DB::select($qry);
           $results = DB::select($qry);
           return $results[0]->numero;
       }
       public static function getAsisExtraMeses($idm, $meses, $tipo = 0)
       {
           $hoy = date("Y-m-d");
           $corte = date("Y-m-d", strtotime("$hoy -$meses months")); //fecha de corte de obolo
           if ($tipo == 1) {
               $qry = "SELECT COUNT(A.idExtra) AS asisextra FROM sgm_asistenciaextra A WHERE A.idExtra=$idm AND A.fechaExtra>'$corte' AND A.motivo=3";
           } else {
               $qry = "SELECT COUNT(A.idExtra) AS asisextra FROM sgm_asistenciaextra A WHERE A.idExtra=$idm AND A.fechaExtra>'$corte' AND A.motivo<>3";
           }
           $results = DB::select($qry);
           $resu = $results[0];
           return $resu->asisextra;
       }
}

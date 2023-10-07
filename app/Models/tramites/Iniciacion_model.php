<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iniciacion_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idTramite';
    protected $table = 'sgm_tramites_iniciacion';
    protected static $espera = 10;

    public static function getItems($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " A.idTramite>0 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND (A.apPaterno Like '%$palabra%' OR A.apMaterno Like '%$palabra%')";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $cond .= " AND (A.okPagoDerechos=1 OR A.pagoQR=1)";
        $qry = "SELECT A.idTramite,A.fechaIniciacion,CASE WHEN (A.nivelActual=7) THEN 'Aprobada' ELSE 'No aprobada' END AS okCeremonia,B.Logia AS nLogia,B.numero,A.fechaModificacion,D.valle,
        A.nivelActual,case when A.pagoQR>0 THEN 'Pago con QR' when LENGTH(A.okPagoDerechos=1) then 'Deposito' ELSE 'Sin Pago' end AS estadotxt ,
        concat(A.nombres,' ',A.apPaterno,' ',A.apMaterno) AS NombreCompleto, A.certificado,A.fechaCertificado,A.numeroCertificado
        FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles D ON B.valle=D.idValle WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = " A.idTramite>0 ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND (A.apPaterno Like '%$palabra%' OR A.apMaterno Like '%$palabra%')";
        }
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $cond .= " AND (A.okPagoDerechos=1 OR A.pagoQR=1)";
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero WHERE $cond ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramites($ids)
    {
        $data = array();
        $cc = 1;
        foreach ($ids as $apres) {
            $query = DB::select("SELECT concat(A.nombres,' ',A.apPaterno,' ',A.apMaterno) AS NombreCompleto,DATE_FORMAT(A.fechaIniciacion,'%d/%m/%Y') AS fechaCeremonia,A.certificado,A.numeroCertificado FROM sgm_tramites_iniciacion A WHERE A.idTramite=$apres");
            $value = $query[0];
            $data['apreName' . $cc] = $value->NombreCompleto;
            $data['idMiembro' . $cc] = $apres;
            $data['certificado' . $cc] = $value->certificado;
            $data['fechaCeremonia' . $cc] = $value->fechaCeremonia;
            $numero = self::select('numeroCertificado as numero')->orderBy('numeroCertificado', 'DESC')->first('numero');
            if (is_null($value->numeroCertificado)) {
                $data['numero' . $cc] = $numero->numero + 1;
            } else {
                $data['numero' . $cc] = $value->numeroCertificado;
            }
            $cc++;
        }
        return $data;
    }
    public static function getDepositos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = " LENGTH(A.nombres)> 3 AND A.NivelActual=7 ";
        if ($taller > 0) {
            $cond .= " AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle=$valle ";
        }

        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,B.logia,B.numero,
    C.valle,D.nivel,A.fechaPagoDerechos,A.docDepositoDer,A.docDepositoGDR,A.docDepositoGLB,A.fechaIniciacion,A.pagoQR,case when A.pagoQR=0 then 'Pago con deposito' ELSE 'Pago con QR' end AS estadopago
    FROM sgm_tramites_iniciacion A
    LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero)
    LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
    INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1  WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumDepositos($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=7 ";
        //$cond = 'A.NivelActual=0 ';
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.logia Like '%$palabra%' ";
        }

        $qry = "SELECT count(A.idTramite) AS  numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getDatosTramite($id)
    {
        $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,CONCAT(A.apPaterno,' ',A.apMaterno,' ',A.nombres) As nombre, A.fechaDepositoDer,A.fechaPagoDerechos,A.docDepositoGDR,A.docDepositoGLB,A.fechaIniciacion,
        DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
        B.logia,C.valle,A.numCircular,A.fechaCircular,A.pagoQR,case when A.pagoQR=0 then 'Pago con deposito' ELSE 'Pago con QR' end AS estadopago,A.docDeposito FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    //*********************************** registro de datos ***************************** */
    public static function getRegistros($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.foto,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,A.documento
FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegistros($palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }
        if ($valle > 0) {
            $cond .= "AND A.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.logia Like '%$palabra%' ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramiteIni($id, $tipo = 0)
    {
        if ($tipo == 1) {
            $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,concat(A.apPaterno,' ',A.apMaterno,' ',A.nombres) AS nombre,A.foto, A.maestro,DATE_FORMAT(A.fInsinuacion,'%d/%m/%Y') AS fInsinuacion,A.actaInsinuacion, DATE_FORMAT(A.fechaAprobPase,'%d/%m/%Y') AS fechaAprobPase,A.actaAprobPase,
        DATE_FORMAT(A.fechaActaInforme,'%d/%m/%Y') AS fechaActaInforme,A.actaInformePase,DATE_FORMAT(A.fechaDepositoDer,'%d/%m/%Y') AS fechaDepositoDer,A.okcurriculump,A.okcompromisop,A.okactainsinuacion,A.okactaaprobacion,A.okactainforme,
        A.docDepositoDer,B.logia,C.valle FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 2) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,CONCAT(A.apPaterno,' ',A.apMaterno,' ',A.nombres) As nombre,
DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
B.logia,C.valle,A.numCircular,A.fechaCircular FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 3) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,CONCAT(A.apPaterno,' ',A.apMaterno,' ',A.nombres) As nombre,
DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
B.logia,C.valle,A.numCircular,A.fechaCircular,A.okInformeSocial,A.okInformeLaboral FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 4) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,CONCAT(A.apPaterno,' ',A.apMaterno,' ',A.nombres) As nombre, A.fechaDepositoDer,A.fechaPagoDerechos,A.docDepositoGDR,A.docDepositoGLB,A.fechaIniciacion,
            DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
            B.logia,C.valle,A.numCircular,A.fechaCircular FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } else {
            $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,A.apPaterno,A.apMaterno,A.nombres,A.foto, DATE_FORMAT(A.fechaNac,'%d/%m/%Y') AS fechaNac ,A.lugarNac,A.nacionalidad,A.profesion,A.documento,A.domicilio,A.fonoDomicilio,A.celular,A.email,A.estadoCivil,A.esposa,A.padre,
        A.madre,A.empresa,A.direccionEmpresa,A.fonoEmpresa,A.cargo,A.resideBolivia,A.aval1,A.aval1Logia,A.aval2,A.aval2Logia,A.aval3,A.aval3Logia,A.maestro,DATE_FORMAT(A.fInsinuacion,'%d/%m/%Y') AS fInsinuacion,A.actaInsinuacion, DATE_FORMAT(A.fechaAprobPase,'%d/%m/%Y') AS fechaAprobPase,A.actaAprobPase,
        DATE_FORMAT(A.fechaActaInforme,'%d/%m/%Y') AS fechaActaInforme,A.actaInformePase,DATE_FORMAT(A.fechaDepositoDer,'%d/%m/%Y') AS fechaDepositoDer,A.docDeposito,
        A.docDepositoDer,B.logia,C.valle FROM sgm_tramites_iniciacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        }
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getnlogia($id)
    {
        $qry = "SELECT Concat(A.logia, ' Nº ',A.numero) AS logiaName,B.valle FROM sgm_logias A INNER JOIN sgm_valles B ON A.valle=B.idValle WHERE A.numero=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getTramitesListos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= " AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.apPaterno Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= " AND B.valle=$valle ";
        }
        if ($limite > 0) {
            $hoy = date("Y-m-d");
            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de modificacion
            $cond .= "AND A.fechaModificacion > '$fecha' ";
        }
        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.foto,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,
        D.nivel,E.circular,A.fechaCircular,A.fechaIniciacion FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
        INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 INNER JOIN sgm_tramites_ini_circulares E ON A.numCircular=E.id
        WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        //  echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumTramitesListos($palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
        if ($valle > 0) {
            $cond .= " AND B.valle=$valle ";
        }
        if ($taller > 0) {
            $cond .= " AND A.logia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= " AND A.logia Like '%$palabra%' ";
        }
        if ($limite > 0) {
            $hoy = date("Y-m-d");
            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de corte de obolo
            $cond .= "AND A.fechaModificacion > '$fecha' ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";

        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getCeremonias($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 7)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.apPaterno,A.apMaterno,A.nombres,A.fechaIniciacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,E.circular,A.fechaCircular,CONCAT(A.fechaIniciacion,'_',B.numero) AS separador
FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 INNER JOIN sgm_tramites_ini_circulares E ON A.numCircular=E.id WHERE $cond ORDER BY A.fechaIniciacion DESC,B.numero ASC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremonias($palabra = '', $taller = 0, $valle = 0, $paso = 7)
    {
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso ";
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
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getDatosCeremonia($id)
    {
        $qry = "SELECT A.logiaName, A.fechaCircular,A.fechaIniciacion,C.valle,B.logia,B.numero FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getIniciados($taller, $fecha, $estado = 7)
    {
        $qry = "SELECT apPaterno,apMaterno,nombres FROM sgm_tramites_iniciacion WHERE fechaIniciacion='$fecha' AND logia=$taller AND nivelActual=$estado ORDER by apPaterno,apMaterno,nombres";
        $results = DB::select($qry);
        return $results;
    }
    /*************************************************/
    public static function getCeremoniasHechas($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 7, $limite = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $date = date('Y-m-d');
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso AND (A.pagoQR>0 OR LENGTH(A.docDeposito)>1) AND A.fechaIniciacion<'$date' ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.apPaterno Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if ($limite > 0) {
            $hoy = date("Y-m-d");
            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de corte de obolo
            $cond .= "AND A.fechaIniciacion > '$fecha' ";
        }

        $qry = "SELECT A.idTramite,A.apPaterno,A.apMaterno,A.nombres,A.fechaIniciacion,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,E.circular,A.foto,
        A.fechaCircular,A.pagoQR, CASE WHEN A.pagoQR>0 then 'Pago QR' ELSE 'Deposito' END AS estadoPago,A.idMiembro,A.numeroCertificado,
        case when A.idMiembro>0 and LENGTH(G.foto)>7 then 'Miembro asignado' when A.idMiembro>0  then 'Asignado sin foto ' ELSE 'No asignado' END AS estado
        FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_valles C ON B.valle=C.idValle
        JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 JOIN sgm_tramites_ini_circulares E ON A.numCircular=E.id LEFT JOIN sgm_miembros G ON A.idMiembro=G.id
        WHERE $cond ORDER BY A.fechaIniciacion DESC,B.numero ASC Limit $inicio,$cantidad ";
//echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremoniasHechas($palabra = '', $taller = 0, $valle = 0, $paso = 7, $limite = 0)
    {
        $date = date('Y-m-d');
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso AND (A.pagoQR>0 OR LENGTH(A.docDeposito)>1) AND A.fechaIniciacion<'$date' ";
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND A.logia Like '%$palabra%' ";
        }
        if ($limite > 0) {
            $hoy = date("Y-m-d");
            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de corte de obolo
            $cond .= "AND A.fechaIniciacion > '$fecha' ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_iniciacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getNombres($id)
    { //
        $qry = "SELECT A.valle,A.logia,A.logiaName,A.apPaterno,A.apMaterno,A.nombres FROM sgm_tramites_iniciacion A WHERE A.idTramite=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function buscarMiembros($apa, $ama)
    {
        $qry = "SELECT A.id,A.Paterno,A.Materno,A.Nombres,CONCAT(B.logia,' Nº',B.numero) AS logia FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero
        WHERE A.Paterno='$apa' AND A.Materno='$ama' ORDER BY A.Paterno,A.Materno,A.Nombres";
        $results = DB::select($qry);
        return $results;
    }
    public static function checkObservacion($doc)
    { //revisa observaciones a tramite
        $qry = "SELECT A.descripcion,B.documento, concat(B.nombres,' ',B.apPaterno,' ',B.apMaterno) AS profano FROM sgm2_tramites_observaciones A JOIN sgm_tramites_iniciacion B ON A.idTramite=B.idTramite WHERE documento LIKE '$doc%'";
        $results = DB::select($qry);
        return $results;
    }
    public static function updateMiembro($id, $foto)
    {
        $res = DB::table("sgm_miembros")->where(['id' => $id])->update(['foto' => $foto]);
        return $res;
    }
    public static function insertMiembro($dato)
    {
        $ncom = strtoupper($dato->apPaterno . ' ' . $dato->apMaterno . ' ' . $dato->nombres);
        $ncomid = str_replace(' ', '', $ncom);
        if (strlen($dato->apPaterno) > 1) {
            $usr = strtolower(substr($dato->nombres, 0, 1) . str_replace(' ', '', $dato->apPaterno) . substr($dato->apMaterno, 0, 1)) . $dato->logia;
        } else {
            $usr = strtolower(substr($dato->nombres, 0, 1) . str_replace(' ', '', $dato->apMaterno)) . $dato->logia;
        }
        $aa = explode('-', $dato->fechaCertificado);
        $uobol = $aa[0] . '-' . $aa[1] . '-01';
        $data = array(
            'Materno' => strtoupper($dato->apMaterno),
            'Paterno' => strtoupper($dato->apPaterno),
            'Nombres' => strtoupper($dato->nombres),
            'Miembro' => 'Regular',
            'Grado' => 1,
            'LogiaActual' => $dato->logia,
            'LogiaIniciacion' => $dato->logia,
            'NombreCompleto' => strtoupper($ncom),
            'NombreCompletoID' => strtoupper($ncomid),
            'username' => $usr,
            'ultimoPago' => $uobol,
            'FechaIniciacion' => $dato->fechaIniciacion,
            'foto' => $dato->foto,
            'CertificadoIni' => $dato->numeroCertificado,
            'FechaCertificadoIni' => $dato->fechaCertificado,
            'clave' => 'amistad',
            'fechaModificacion' => date('Y-m-d'),
        );
        //falta insertar datosde padre y otros
        $idm = DB::table("sgm_miembros")->insertGetId($data);
        $eciv[0] = 'Soltero';
        $eciv[1] = 'Casado';
        $eciv[2] = 'Viudo';
        $eciv[3] = 'Divorciado';
        $eciv[4] = 'union libre';
        $datamas = array(
            'id' => $idm,
            'Valle' => $dato->valle,
            'FechaNacimiento' => $dato->fechaNac,
            'ProfesionOficio' => $dato->profesion,
            'CI' => $dato->documento,
            'Domicilio' => $dato->domicilio,
            'TelefonoDomicilio' => $dato->fonoDomicilio,
            'Celular' => $dato->celular,
            'email' => $dato->email,
            'EstadoCivil' => $eciv[$dato->estadoCivil],
            'NombreEsposa' => $dato->esposa,
            'NombrePadre' => $dato->padre,
            'NombreMadre' => $dato->madre,
            'Trabajo' => $dato->empresa,
            'Cargo' => $dato->cargo,
            'Pais' => $dato->nacionalidad,
            'LugarNacimiento' => $dato->lugarNac,
            'TelefonoOficina' => $dato->fonoEmpresa,
        );
        $res = DB::table("sgm_miembrosdata")->insert($datamas);
        return $idm;
    }
}

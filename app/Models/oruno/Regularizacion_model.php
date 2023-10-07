<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regularizacion_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idTramite';
    protected $table = 'sgm_tramites_regularizacion';
    protected static $espera = 10;


    public static function getTramites($ids)
    {
        $data = array();
        $cc = 1;
        foreach ($ids as $apres) {
            $query = DB::select("SELECT concat(A.nombres,' ',A.apPaterno,' ',COALESCE(A.apMaterno,'')) AS NombreCompleto,DATE_FORMAT(A.fechaJuramento,'%d/%m/%Y') AS fechaCeremonia,certificado,docDepositoDer FROM sgm_tramites_regularizacion A WHERE A.idTramite=$apres");
            $value = $query[0];
            $data['apreName' . $cc] = $value->NombreCompleto;
            $data['idMiembro' . $cc] = $apres;
            $data['certificado' . $cc] = $value->certificado;
            $data['fechaCeremonia' . $cc] = $value->fechaCeremonia;
            $numero = self::select('numeroCertificado as numero')->orderBy('numeroCertificado', 'DESC')->first('numero');
            if (is_null($numero)) {
                $data['numero' . $cc] = 1;
            } else {
                $data['numero' . $cc] = $numero->numero + 1;
            }
            $cc++;
        }
        return $data;
    }

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
        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.foto,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,A.documento,
        CASE A.task when 0 then D.nivel when 1 then concat('Completar ', D.nivel) when 3 then D.nivel else 'Rechazado' end as nivel,A.antecedentes,A.antecedentes2,A.cuadernillo,G.GradoActual
FROM sgm_tramites_regularizacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 join sgm_grados G on A.grado=G.Grado WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
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
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_regularizacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramiteRegu($id, $tipo = 0)
    {
        if ($tipo == 1) {
            $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,concat(A.nombres,' ',A.apPaterno,' ',COALESCE(A.apMaterno,'')) AS nombre,A.foto,A.maestro,A.maestro2,DATE_FORMAT(A.fInsinuacion,'%d/%m/%Y') AS fInsinuacion,A.actaInsinuacion, DATE_FORMAT(A.fechaAprobPase,'%d/%m/%Y') AS fechaAprobPase,A.actaAprobPase,
        DATE_FORMAT(A.fechaActaInforme,'%d/%m/%Y') AS fechaActaInforme,A.actaInformePase,DATE_FORMAT(A.fechaDepositoDer,'%d/%m/%Y') AS fechaDepositoDer,A.okcurriculump,A.okcompromisop,A.okactainsinuacion,A.okactaaprobacion,A.okactainforme,
        A.docDepositoDer,B.logia,C.valle,A.cuadernillo,A.antecedentes,A.grado,A.okComision FROM sgm_tramites_regularizacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 2) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,concat(A.nombres,' ',A.apPaterno,' ',COALESCE(A.apMaterno,'')) As nombre,
DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
B.logia,C.valle,A.numCircular,A.fechaCircular,A.grado,A.okComision FROM sgm_tramites_regularizacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 3) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,concat(A.nombres,' ',A.apPaterno,' ',COALESCE(A.apMaterno,'')) As nombre,
DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
B.logia,C.valle,A.numCircular,A.fechaCircular,A.okInformeSocial,A.okInformeLaboral,A.grado,A.okComision FROM sgm_tramites_regularizacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } elseif ($tipo == 4) {
            $qry = "SELECT A.idTramite,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,concat(A.nombres,' ',A.apPaterno,' ',COALESCE(A.apMaterno,'')) As nombre, A.fechaDepositoDer,A.fechaPagoDerechos,A.docDepositoGDR,A.docDepositoGLB,A.fechaJuramento,
            DATE_FORMAT(A.fechaInfLaboral,'%d/%m/%Y') AS fechaInfLaboral,A.numActaInfLaboral, DATE_FORMAT(A.fechaInfSocial,'%d/%m/%Y') AS fechaInfSocial,A.numActaInfSocial,
            B.logia,C.valle,A.numCircular,A.fechaCircular,A.grado,A.okComision FROM sgm_tramites_regularizacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        } else {
            $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.nivelActual,A.valle,A.logia,A.logiaName,A.apPaterno,A.apMaterno,A.nombres,A.foto, DATE_FORMAT(A.fechaNac,'%d/%m/%Y') AS fechaNac ,A.lugarNac,A.nacionalidad,A.profesion,A.documento,A.domicilio,A.fonoDomicilio,A.celular,A.email,A.estadoCivil,A.esposa,A.padre,
        A.madre,A.empresa,A.direccionEmpresa,A.fonoEmpresa,A.cargo,A.resideBolivia,A.aval1,A.aval1Logia,A.aval2,A.aval2Logia,A.aval3,A.aval3Logia,A.maestro,A.maestro2,DATE_FORMAT(A.fInsinuacion,'%d/%m/%Y') AS fInsinuacion,A.actaInsinuacion, DATE_FORMAT(A.fechaAprobPase,'%d/%m/%Y') AS fechaAprobPase,A.actaAprobPase,
        DATE_FORMAT(A.fechaActaInforme,'%d/%m/%Y') AS fechaActaInforme,A.actaInformePase,DATE_FORMAT(A.fechaDepositoDer,'%d/%m/%Y') AS fechaDepositoDer,A.docDepositoDer,DATE_FORMAT(A.fechaJuramento,'%d/%m/%Y') AS fechaJuramento,
        A.docDepositoDer,B.logia,C.valle,A.grado,A.okComision FROM sgm_tramites_regularizacion A INNER JOIN sgm_logias B ON A.logia=B.numero INNER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
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
    public static function getTramitesListos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6, $dias = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $hoy = date("Y-m-d");
        if ($dias > 0) {
            $auxdias = $dias;
            $fechac = date("Y-m-d", strtotime("$hoy -$auxdias days"));
        } else {
            $fechac = date("Y-m-d", strtotime("$hoy -30 days"));
        }
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso AND A.fechaCircular > '$fechac' ";
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

            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de modificacion
            $cond .= "AND A.fechaModificacion > '$fecha' ";
        }
        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.foto,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,
        D.nivel,E.circular,A.fechaCircular,A.fechaJuramento,G.GradoActual FROM sgm_tramites_regularizacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
        INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 INNER JOIN sgm_tramites_regu_circulares E ON A.numCircular=E.id join sgm_grados G on A.grado=G.Grado
        WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        //    echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumTramitesListos($palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6, $dias = 0)
    {
        $hoy = date("Y-m-d");
        if ($dias > 0) {
            $auxdias = $dias + 1;
            $fechac = date("Y-m-d", strtotime("$hoy -$auxdias days")); //fecha de modificacion
        } else {
            $fechac = date("Y-m-d", strtotime("$hoy -31 days")); //fecha de modificacion
        }
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso  AND A.fechaCircular > '$fechac' ";

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
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_regularizacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";

        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramitesPagar($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6, $dias = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $hoy = date("Y-m-d");
        if ($dias > 0) {
            $auxdias = $dias - 1;
            $fechac = date("Y-m-d", strtotime("$hoy -$auxdias days"));
        } else {
            $fechac = date("Y-m-d", strtotime("$hoy -29 days"));
        }
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso AND A.fechaCircular < '$fechac' ";
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

            $fecha = date("Y-m", strtotime("$hoy -$limite months")) . '-01'; //fecha de modificacion
            $cond .= "AND A.fechaModificacion > '$fecha' ";
        }
        $qry = "SELECT A.idTramite,A.fechaCreacion,A.numTramite,A.foto,A.apPaterno,A.apMaterno,A.nombres,A.fechaModificacion,A.fInsinuacion,B.logia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,
        D.nivel,E.circular,A.fechaCircular,A.fechaJuramento,GradoActual FROM sgm_tramites_regularizacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
        INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 INNER JOIN sgm_tramites_regu_circulares E ON A.numCircular=E.id JOIN sgm_grados G on A.grado=G.Grado
        WHERE $cond ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        //   echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumTramitesPagar($palabra = '', $taller = 0, $valle = 0, $paso = 3, $limite = 6, $dias = 0)
    {
        $hoy = date("Y-m-d");
        if ($dias > 0) {
            $auxdias = $dias - 1;
            $fechac = date("Y-m-d", strtotime("$hoy -$auxdias days")); //fecha de modificacion
        } else {
            $fechac = date("Y-m-d", strtotime("$hoy -29 days")); //fecha de modificacion
        }
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual=$paso  AND A.fechaCircular < '$fechac' ";

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
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_regularizacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";

        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getCeremoniasHechas($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 6, $limite = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $date = date('Y-m-d');
        //$cond = "LENGTH(A.nombres)> 3 AND A.NivelActual IN ($paso) AND (A.pagoQR>0 OR LENGTH(A.docDepositoDer)>1) AND A.fechaJuramento<'$date' ";
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual IN ($paso) AND (A.pagoQR>0 OR LENGTH(A.docDepositoDer)>1) ";
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
            $cond .= "AND A.fechaCircular > '$fecha' ";
        }

        $qry = "SELECT A.idTramite,A.apPaterno,A.apMaterno,A.nombres,A.fechaJuramento,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,C.valle,D.nivel,E.circular,A.foto,
        A.fechaCircular,A.pagoQR, CASE WHEN A.pagoQR>0 then 'Pago QR' ELSE 'Deposito' END AS estadoPago,A.idMiembro,A.numeroCertificado,A.docDepositoDer,
        case when A.idMiembro>0 and LENGTH(G.foto)>7 then 'Miembro asignado' when A.idMiembro>0  then 'Asignado sin foto ' ELSE 'No asignado' END AS estado,
        case when A.okPagoDerechos=1 then 'Deposito revisado' ELSE 'Sin revisar' end AS estadotxt,A.okPagoDerechos,H.GradoActual
        FROM sgm_tramites_regularizacion A JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_valles C ON B.valle=C.idValle
        JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1 JOIN sgm_tramites_regu_circulares E ON A.numCircular=E.id LEFT JOIN sgm_miembros G ON A.idMiembro=G.id
        left JOIN sgm_grados H on A.grado=H.Grado WHERE $cond ORDER BY A.fechaJuramento DESC,B.numero ASC Limit $inicio,$cantidad ";
        //   echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremoniasHechas($palabra = '', $taller = 0, $valle = 0, $paso = 7, $limite = 0)
    {
        $date = date('Y-m-d');
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual IN ($paso) AND (A.pagoQR>0 OR LENGTH(A.docDepositoDer)>1) AND A.fechaJuramento<'$date' ";
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
            $cond .= "AND A.fechaJuramento > '$fecha' ";
        }
        $qry = "SELECT count(A.idTramite) as numero FROM sgm_tramites_regularizacion A LEFT JOIN sgm_logias B ON (A.logia=B.numero) WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }

    public static function checkObservacion($doc)
    { //revisa observaciones a tramite
        $qry = "SELECT A.descripcion,B.documento, concat(B.nombres,' ',B.apPaterno,' ',B.apMaterno) AS profano FROM sgm2_tramites_observaciones A JOIN sgm_tramites_regularizacion B ON A.idTramite=B.idTramite WHERE A.estado<>1 AND documento LIKE '$doc%'";
        $results = DB::select($qry);
        return $results;
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
        $aa = explode('-', $dato->fechaJuramento);
        $uobol = $aa[0] . '-' . $aa[1] . '-01';
        $data = array(
            'Materno' => strtoupper($dato->apMaterno),
            'Paterno' => strtoupper($dato->apPaterno),
            'Nombres' => strtoupper($dato->nombres),
            'Miembro' => 'Regular',
            'Grado' => $dato->grado,
            'LogiaActual' => $dato->logia,
            'NombreCompleto' => strtoupper($ncom),
            'NombreCompletoID' => strtoupper($ncomid),
            'username' => $dato->documento,
            'ultimoPago' => $uobol,
            'foto' => $dato->foto,
            'clave' => 'amistad',
            'fechaModificacion' => date('Y-m-d'),
        );
        if ($dato->grado == 3) {
            $data['CertificadoIni'] = 0;
            $data['CertificadoAum'] = 0;
            $data['CertificadoExal'] = 0;
            $data['LogiaIniciacion'] = 0;
            $data['LogiaAumento'] = 0;
            $data['LogiaExaltacion'] = $dato->logia;
            $data['FechaCertificadoExal'] = $dato->fechaJuramento;
            $data['FechaExaltacion'] = $dato->fechaJuramento;
        } elseif ($dato->grado == 2) {
            $data['CertificadoIni'] = 0;
            $data['CertificadoAum'] = 0;
            $data['CertificadoExal'] = 0;
            $data['LogiaIniciacion'] = 0;
            $data['LogiaAumento'] = $dato->logia;
            $data['LogiaExaltacion'] = 0;
            $data['FechaCertificadoAum'] = $dato->fechaJuramento;
            $data['FechaAumentoSalario'] = $dato->fechaJuramento;
        } else {
            $data['CertificadoIni'] = 0;
            $data['CertificadoAum'] = 0;
            $data['CertificadoExal'] = 0;
            $data['LogiaIniciacion'] = $dato->logia;
            $data['LogiaAumento'] = 0;
            $data['LogiaExaltacion'] = 0;
            $data['FechaCertificadoIni'] = $dato->fechaJuramento;
            $data['FechaIniciacion'] = $dato->fechaJuramento;
        }
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

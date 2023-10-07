<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliacion_model extends Model
{
    use HasFactory;
    private static $tipo = 5;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_tramites_afiliaciones';
    public static $prehash = 'GLB';

    public static function getListaTramites($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $estado = '')
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogiaNueva=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND F.valle=$valle ";
        }
        if (strlen($estado) > 0) {
            $cond .= " AND A.estado IN ($estado) ";
        }
        $qry = "SELECT A.id,A.estado,A.tipo,A.actaAprobacionLogia, DATE_FORMAT(A.fechaAprobacionLogia,'%d/%m/%Y') AS fechaAprobacionLogia,DATE_FORMAT(A.fechaAprobacion,'%d/%m/%Y') AS fechaAprobacion,
        DATE_FORMAT(A.fechaDeposito,'%d/%m/%Y') AS fechaDeposito,A.fechaModificacion,B.NombreCompleto, A.fechaCreacion,A.archivo,A.archivo2,DATE_FORMAT(A.fechaJuramento,'%d/%m/%Y') AS fechaJuramento,
        CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,A.activo,
            CONCAT(C.Logia,' N. ',A.idLogia) AS nLogia,D.valle,E.nombre AS estadotxt,CASE A.tipo WHEN 1 THEN 'Afiliacion como Logia Principal' WHEN 2 THEN 'Afiliacion como logia Principal y la otra como afiliada' ELSE 'Afiliacion como segunda Logia' END AS casotxt,
            CONCAT(F.Logia,' N. ',A.idLogiaNueva) AS nLogiaNueva FROM sgm2_tramites_afiliaciones A JOIN sgm_miembros B ON A.idMiembro=B.id
            left JOIN sgm_logias C ON A.idLogia=C.numero left JOIN sgm_parametros E ON A.estado=E.valor AND E.tipo=11
            left JOIN sgm_logias F ON A.idLogiaNueva=F.numero left JOIN sgm_valles D ON F.valle=D.idValle  WHERE $cond ORDER BY A.fechaModificacion DESC Limit " . $inicio . "," . $cantidad . ' ';
        // echo $qry;
        $res = DB::select($qry);
        return $res;
    }
    public static function getNumeroTramites($palabra = '', $taller = 0, $valle = 0, $estado = '')
    {
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogiaNueva=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND Z.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if (strlen($estado) > 0) {
            $cond .= " AND A.estado IN ($estado) ";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm2_tramites_afiliaciones A JOIN sgm_logias B ON (A.idLogiaNueva =B.numero) WHERE $cond ";
        // echo ($qry);
        $results = DB::select($qry);
        //dd($results);
        $resu = $results[0];
        return $resu->numero;
    }
    //---
    public static function getDepositos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $level = 1)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta

        $cond = "A.id> 0 AND LENGTH(A.archivo)>10 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogiaNueva=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND C.valle=$valle ";
        }

        if ($level == 4) {
            $cond .= "AND A.estado IN (3) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) {
            $cond .= "AND (A.estado IN (3) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (1,3,4))";
        } elseif ($level == 3) {
            $cond .= "AND ((A.estado IN (3) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) {
            $cond .= "AND A.estado IN (3)";
        }

        $qry = "SELECT A.*,B.NombreCompleto, CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual ,
        C.Logia AS nLogia,D.valle,E.nombre AS estadotxt,N.Logia AS nLogiaNueva,case when A.tipo=1 then 'Cambio de Logia actual' when A.tipo=2 then 'Logia Actual y la otra afiliada' ELSE 'Segunda Logia' END AS casotxt,
        DATE_FORMAT(A.fechaAprobacionLogia,'%d/%m/%Y') AS fAprobacionLogia ,DATE_FORMAT(A.fechaDeposito,'%d/%m/%Y') AS fDeposito
        FROM sgm2_tramites_afiliaciones A
        JOIN sgm_miembros B ON A.idMiembro=B.id
        JOIN sgm_logias C ON A.idLogia=C.numero
        JOIN sgm_valles D ON C.valle=D.idValle
        JOIN sgm_logias N ON A.idLogiaNueva=N.numero
        JOIN sgm_parametros E ON A.estado=E.valor AND E.tipo=9 WHERE $cond ORDER BY A.fechaModificacion DESC Limit " . $inicio . "," . $cantidad . ' ';

        $results = DB::select($qry);
        return $results;
    }
    public static function getNumDepositos($palabra = '', $taller = 0, $valle = 0, $level = 1)
    {
        $cond = "A.id> 0 AND LENGTH(A.archivo)>10 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogiaNueva=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        } else {
        }
        if ($level == 4) {
            $cond .= "AND A.estado IN (3) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) {
            $cond .= "AND (A.estado IN (3) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (1,3,4))";
        } elseif ($level == 3) {
            $cond .= "AND ((A.estado IN (3) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) {
            $cond .= "AND A.estado IN (3)";
        }

        $qry = "SELECT count(A.id) as numero FROM sgm2_tramites_afiliaciones A JOIN sgm_logias B ON (A.idLogia=B.numero) WHERE $cond ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }

    public static function getMiembros($idl, $palabra = '')
    {
        // $tiempouno = 6; //2años

        if ($idl > 0) {
            // $hoy = date("Y-m-d");
            // $limite = date("Y-m", strtotime("$hoy -$tiempouno months")) . '-01'; //fecha de corte de obolo
            if (strlen($palabra) > 2) {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
                DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual, A.Miembro,A.LogiaAfiliada,
                CASE WHEN A.LogiaAfiliada>0 THEN CONCAT('Afil. a N. ', A.LogiaAfiliada) ELSE 'No afiliado' END AS obstxt,CONCAT('Log:. N. ', A.LogiaActual) AS talltxt,C.texto
                FROM sgm_miembros A left JOIN sgm_logias B ON A.LogiaActual=B.numero LEFT JOIN sgm2_miembrosestado C ON A.Estado=C.estado
WHERE A.Estado=5 AND A.NombreCompleto like '%$palabra%' ORDER BY A.NombreCompleto LIMIT 20";
            } else {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
                DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual, A.Miembro,A.LogiaAfiliada,
                CASE WHEN A.LogiaAfiliada>0 THEN CONCAT('Afil. a N. ', A.LogiaAfiliada) ELSE 'No afiliado' END AS obstxt,CONCAT('Log:. N. ', A.LogiaActual) AS talltxt,C.texto
                FROM sgm_miembros A left JOIN sgm_logias B ON A.LogiaActual=B.numero LEFT JOIN sgm2_miembrosestado C ON A.Estado=C.estado
WHERE A.Estado=5 ORDER BY A.NombreCompleto LIMIT 20";
            }
            // echo $qry;
            $res = DB::select($qry);
            return $res;
        }
        return '';
    }
    public static function checkTramite($idm)
    {
        $query = self::where('idMiembro', $idm)->where('estado', '<', 3)->first('id');
        if (is_null($query)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTramitePago($id)
    {

        $qry = "SELECT CONCAT('Nro. ',B.numero) AS nLogia, C.valle, DATE_FORMAT(A.ultimoPago,'%d/%m/%Y') AS fechaTramite,C.idValle
        FROM sgm2_tramites_afiliaciones A JOIN sgm_logias B ON A.idLogiaNueva=B.numero JOIN sgm_valles C ON B.valle=C.idValle
        WHERE A.id=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['fechaTramite'] = $valuec->fechaTramite;
        $monto = self::getMontoTotal(self::$tipo, $valuec->idValle);
        $monts = self::getMontos(self::$tipo, $valuec->idValle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data['montoGLB'] = $montof[1];
        $data['montoCOMAP'] = $montof[3];
        $data['montoGDR'] = $montof[2];
        $data['montoTotal'] = $monto . ' Bs.';
        $data['ceremonia'] = 'Afiliacion';
        return $data;
    }
    public static function getMontoTotal($grado, $vall)
    {
        $qry = "SELECT SUM(A.monto) AS montos FROM sgm_mecom_tramites_montos A WHERE A.idValle IN(0,$vall) AND A.tipo=$grado GROUP BY A.idValle,A.miembro,A.orden ORDER BY A.idValle DESC";
        $query = DB::select($qry);
        return $query[0]->montos;
    }
    public static function getMontos($grado, $vall)
    {
        $qry = "SELECT (A.monto) AS montos,A.entidad FROM sgm_mecom_tramites_montos A WHERE A.idValle IN(0,$vall) AND A.tipo=$grado  ORDER BY A.idValle DESC LIMIT 3";
        $query = DB::select($qry);
        return $query;
    }
    /* pago qr*/
    public static function getUsuario($id)
    {
        $qry = "SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    public static function getTramiteDatos($id)
    {
        $qry = "SELECT A.idLogiaNueva AS logia, B.valle, DATE_FORMAT(A.ultimoPago,'%d/%m/%Y') AS fechaTramite,A.gradoActual,A.estado  FROM sgm2_tramites_afiliaciones A JOIN sgm_logias B ON A.idLogiaNueva=B.numero WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    public static function getDatos($id, $iduser)
    {
        $data = self::getTramiteDatos($id);
        $usuario = self::getUsuario($iduser);
        $data->email = $usuario->email;
        $data->username = $usuario->username;
        $data->nombre = $usuario->username;
        $data->CI = $usuario->logia;
        $mont1 = 0; //no paga nada al taller
        $mont2 = self::getMontoTotal(self::$tipo, $data->valle);
        $monts = self::getMontos(self::$tipo, $data->valle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data->montoGLB = $montof[1];
        $data->montoCOMAP = $montof[3];
        $data->montoGDR = $montof[2];
        $data->montoTaller = $mont1;

        $data->monto = $mont1 + $mont2; //pago
        $data->glosa = 'Pago de tramite de afiliacion';
        $data->nuevoPago = $data->fechaTramite;
        $data->params = 'Afiliacion|' . $data->logia . '|' . $data->fechaTramite . '|';
        $data->descripcion = 'Pago de Afiliacion de HH fecha ' . $data->fechaTramite;
        $data->total = ($mont1 + $mont2);
        return $data;
    }
    public static function insertTransaccion($datos, $tipo = 3)
    { //crea el registro y el hash
        $numero = time();
        self::genControl(self::$tipo, $datos->idCeremonia);
        $hash = self::get_hash();
        $data = array(
            'idRegistro' => $hash,
            'tipo' => self::$tipo, //---
            'usuario' => $datos->user, //--
            'idMiembro' => $datos->idCeremonia, //--
            'grado' => $datos->grado, //--
            'logia' => $datos->logia, //--
            'valle' => $datos->valle, //--
            'miembro' => $datos->Miembro, //--
            'cantidad' => 1, //--
            'aprobado' => 0, //--
            'ultimoPago' => $datos->nuevoPago, //--
            'nuevoPago' => $datos->nuevoPago, //--
            'monto' => $datos->monto,
            'montoGLB' => $datos->montoGLB,
            'montoCOMAP' => $datos->montoCOMAP,
            'montoGDR' => $datos->montoGDR,
            'montoTaller' => $datos->montoTaller,
            'parametros' => $datos->params,
        );
        $idd = DB::table('sgm_mecom_pagos_registros')->insertGetId($data);
        if ($idd > 0) {
            return $hash;
        } else {
            return null;
        }
    }
    /**** */
    public static function genControl($tipo = 10, $id = 0)
    {
        $tt = $tipo * 100000;
        $base = $tt + $id;
        if ($tipo > 9) {
            $hash = self::$prehash . $base;
        } else {
            $hash = self::$prehash . '0' . $base;
        }
        self::$prehash = $hash;
    }
    public static function get_hash()
    {
        $numero = time();
        $hash = self::$prehash . dechex($numero);
        $query = DB::select("SELECT A.contador FROM sgm_mecom_pagos_registros A WHERE A.idRegistro='$hash'");
        if (count($query) > 0) {
            $numero = time();
            $hash = self::$prehash . dechex($numero);
            $query = DB::select("SELECT A.contador FROM sgm_mecom_pagos_registros A WHERE A.idRegistro='$hash'");
            if (count($query) > 0) {
                $numero = time();
                $hash = self::$prehash . dechex($numero);
                $query = DB::select("SELECT A.contador FROM sgm_mecom_pagos_registros A WHERE A.idRegistro='$hash'");
                if (count($query) > 0) {
                    $numero = time();
                    $hash = self::$prehash . dechex($numero);
                    $query = DB::select("SELECT A.contador FROM sgm_mecom_pagos_registros A WHERE A.idRegistro='$hash'");
                    if (count($query) > 0) {
                        $numero = time();
                        $hash = self::$prehash . dechex($numero);
                    }
                }
            }
        }
        return $hash;
    }
}

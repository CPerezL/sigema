<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reincorporacion_model extends Model
{
    use HasFactory;
    private static $tipo = 6;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_reincorporaciones';
    public static $prehash = 'GLB';
    public static function getDepositos($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $level = 1)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND C.valle=$valle ";
        }

        if ($level == 4) { //glb
            $cond .= "AND A.estado IN (1,4) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) { //gdr
            $cond .= "AND (A.estado IN (1,3,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (1,3,4))";
        } elseif ($level == 3) { //gdr
            $cond .= "AND ((A.estado IN (1,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) { //gdr
            $cond .= "AND A.estado IN (1,2,3,4)";
        }

        $qry = "SELECT  A.*,B.NombreCompleto, CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual ,
    CONCAT('R.L.S. ',C.Logia) AS nLogia,D.valle,E.nombre AS estadotxt,CASE WHEN A.tipo=4 THEN 'Tramite en GLB y GDR/GLD' WHEN A.tipo=3 THEN 'Tramite en GLB' WHEN A.tipo=2 THEN 'Tramite en la GDR/GLD' ELSE 'Tramite Simple' END AS casotxt,
    DATE_FORMAT(A.fechaAprobacionLogia,'%d/%m/%Y') AS fAprobacionLogia ,DATE_FORMAT(A.fechaDeposito,'%d/%m/%Y') AS fDeposito
     FROM sgm_reincorporaciones A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_logias C ON A.idLogia=C.numero JOIN sgm_valles D ON C.valle=D.idValle JOIN sgm_parametros E
     ON A.estado=E.valor AND E.tipo=6  WHERE $cond ORDER BY A.fechaModificacion DESC Limit " . $inicio . "," . $cantidad . ' ';

        $results = DB::select($qry);
        return $results;
    }
    public static function getNumDepositos($palabra = '', $taller = 0, $valle = 0, $level = 1)
    {
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if ($level == 4) { //glb
            $cond .= "AND A.estado IN (1,4) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) { //gdr
            $cond .= "AND (A.estado IN (1,3,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (1,3,4))";
        } elseif ($level == 3) { //gdr
            $cond .= "AND ((A.estado IN (1,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) { //gdr
            $cond .= "AND A.estado IN (1,2,3,4)";
        }

        $qry = "SELECT count(A.id) as numero FROM sgm_reincorporaciones A JOIN sgm_logias B ON (A.idLogia=B.numero) WHERE $cond ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getMiembros($idl, $palabra = '')
    {
        $tiempouno = 12; //2años
        $tiempodos = 60; //5años

        if ($idl > 0) {
            $hoy = date("Y-m-d");
            $limite = date("Y-m", strtotime("$hoy -$tiempouno months")) . '-01'; //fecha de corte de obolo
            $limitedos = date("Y-m", strtotime("$hoy -$tiempodos months")) . '-01'; //fecha de corte de obolo
            if (strlen($palabra) > 2) {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
              CASE WHEN A.ultimoPago>'$limitedos' AND A.socio=1 THEN 1 WHEN A.ultimoPago>'$limitedos' AND A.socio>1 THEN 3 WHEN A.socio=1 THEN 2 ELSE 4 END AS caso,
              CASE WHEN A.ultimoPago>'$limitedos' AND A.socio=1 THEN 'Tramite Simple' WHEN A.ultimoPago>'$limitedos' AND A.socio>1 THEN 'Tramite GLB' WHEN A.socio=1 THEN 'Tramite en el Consejo' ELSE 'Tramite GLB y Consejo' END AS casotxt,

              DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual,CASE WHEN A.ultimoPago>'$limitedos' THEN 'Mas de 5 años' ELSE 'De 2 a 5 años' END AS tipotxt , CASE WHEN A.socio=1 THEN 'Sin Reincorp.' ELSE 'Reincorporado' END AS obstxt
    FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE B.numero=$idl AND A.ultimoPago<'$limite' AND A.Estado=1 AND A.NombreCompleto like '%$palabra%' AND A.Miembro<>'Ad-Vitam' ORDER BY A.NombreCompleto LIMIT 20";
            } else {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
              CASE WHEN A.ultimoPago>'$limitedos' AND A.socio=1 THEN 1 WHEN A.ultimoPago>'$limitedos' AND A.socio>1 THEN 3 WHEN A.socio=1 THEN 2 ELSE 4 END AS caso,
              CASE WHEN A.ultimoPago>'$limitedos' AND A.socio=1 THEN 'Tramite Simple' WHEN A.ultimoPago>'$limitedos' AND A.socio>1 THEN 'Tramite GLB' WHEN A.socio=1 THEN 'Tramite en el Consejo' ELSE 'Tramite GLB y Consejo' END AS casotxt,
              DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual,CASE WHEN A.ultimoPago<'$limitedos' THEN 'Mas de 5 años' ELSE 'De 2 a 5 años' END AS tipotxt , CASE WHEN A.socio=1 THEN 'Sin Reincorp.' ELSE 'Reincorporado' END AS obstxt
    FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE B.numero=$idl AND A.ultimoPago<'$limite' AND A.Estado=1 AND A.Miembro<>'Ad-Vitam' ORDER BY A.NombreCompleto LIMIT 20";
            }
            // echo $qry.'-----';
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }
    }
    public static function checkTramite($id)
    {
        $qry = self::where('idMiembro', $id)->first('id');
        if (is_null($qry)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getFormulario($gestion, $valle, $tipo = 4) //no separq eu fue esto

    {
        if ($valle > 0 && $gestion > 0) {
            $query = DB::select("select numero from sgm_mecom_formularios where idValle=$valle AND tipo IN ('$tipo') AND gestion=$gestion order by numero desc");
            if (!is_null($query)) {
                $id = $query[0];
                return $id->numero;
            }
        }
        return 0;
    }
    public static function getDatosMiembro($idmiembro)
    {
        $qry = "SELECT  A.Miembro,A.ultimoPago,A.Grado,A.LogiaActual as numero FROM sgm_miembros A WHERE A.id=$idmiembro ";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getDatosCuota($mes, $miembro, $valle)
    {
        $qry = "SELECT SUM(CASE WHEN A.entidad=1 THEN A.monto ELSE 0 END) AS monto1,SUM(CASE WHEN A.entidad=2 THEN A.monto ELSE 0 END) AS monto2,SUM(CASE WHEN A.entidad=3 THEN A.monto ELSE 0 END) AS monto3,
        SUM(A.monto) AS monto0,A.idValle
        FROM sgm_mecom_montos A WHERE A.idValle IN(0,$valle) AND A.miembro='$miembro' AND '$mes'>=A.fechaInicio AND '$mes'<=A.fechaFin
        GROUP BY A.idValle,A.miembro,A.orden ORDER BY A.idValle DESC  LIMIT 1";
        // dd($qry);
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getDatosTramite($id)
    {
        $qry = "SELECT A.idMiembro,B.valle FROM sgm_reincorporaciones A JOIN sgm_logias B ON  A.idLogia=B.numero  WHERE A.id=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getDatosCert($id)
    {
        $qry = "SELECT A.id, A.fechaAprobacionGDR,A.fechaDeposito, B.logia,C.NombreCompleto,D.valle,B.numero,
(CASE C.Grado WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' ELSE 'Ninguno' END) AS GradoActual FROM sgm_reincorporaciones A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_miembros C ON A.idMiembro=C.id JOIN sgm_valles D ON B.valle=D.idValle
WHERE A.id=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getListaTramitesQR($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0, $level = 1)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND C.valle=$valle ";
        } else {
        }
        if ($level == 4) //glb
        {
            $cond .= "AND A.estado IN (4) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) //gdr
        {
            $cond .= "AND (A.estado IN (3,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (1,3,4))";
        } elseif ($level == 3) //gdr
        {
            $cond .= "AND ((A.estado IN (4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) //gdr
        {
            $cond .= "AND A.estado IN (2,3,4)";
        } else //pers
        {
            $cond .= "AND A.estado IN (2,3,4)";
        }

        $qry = "SELECT  A.*,B.NombreCompleto, CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual ,
CONCAT('R.L.S. ',C.Logia) AS nLogia,D.valle,E.nombre AS estadotxt,CASE WHEN A.tipo=4 THEN 'Tramite en GLB y GDR/GLD' WHEN A.tipo=3 THEN 'Tramite en GLB' WHEN A.tipo=2 THEN 'Tramite en la GDR/GLD' ELSE 'Tramite Simple' END AS casotxt,
DATE_FORMAT(A.fechaAprobacionLogia,'%d/%m/%Y') AS fAprobacionLogia ,DATE_FORMAT(A.fechaDeposito,'%d/%m/%Y') AS fDeposito,case when A.pagoQR >0 then 1 ELSE 0 END AS estadop,case when A.pagoQR >0 then 'Pagado con QR' when LENGTH(A.archivo)>5  then 'Pagado con Deposito' ELSE 'Sin pago' END AS estadotxt
 FROM sgm_reincorporaciones A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_logias C ON A.idLogia=C.numero JOIN sgm_valles D ON C.valle=D.idValle JOIN sgm_parametros E
 ON A.estado=E.valor AND E.tipo=6  WHERE $cond ORDER BY A.fechaModificacion DESC Limit " . $inicio . "," . $cantidad . ' ';
//  echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumeroTramitesQR($palabra = '', $valle = 0,$taller = 0,  $level = 1)
    {
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if ($level == 4) //glb
        {
            $cond .= "AND A.estado IN (4) AND A.tipo IN (3,4) AND A.especial=0";
        } elseif ($level == 2) //gdr
        {
            $cond .= "AND (A.estado IN (3,4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1 AND A.estado IN (3,4))";
        } elseif ($level == 3) //gdr
        {
            $cond .= "AND ((A.estado IN (4) AND A.tipo IN (1,2)) OR (A.tipo IN (3,4) AND A.especial=1))";
        } elseif ($level == 1) //gdr
        {
            $cond .= "AND A.estado IN (2,3,4)";
        } else //pers
        {
            $cond .= "AND A.estado IN (2,3,4)";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm_reincorporaciones A JOIN sgm_logias B ON (A.idLogia=B.numero) WHERE $cond "; //    echo $qry;
        //  echo $qry;
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getTramitePago($id, $tipo = 5)
    {
        $qry = "SELECT CONCAT('Nro. ',B.numero) AS nLogia, C.valle, DATE_FORMAT(A.ultimoPago,'%d/%m/%Y') AS fechaTramite,C.idValle
        FROM sgm_reincorporaciones A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_valles C ON B.valle=C.idValle
        WHERE A.id=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['fechaTramite'] = $valuec->fechaTramite;
        //$monto = $this->getMontoTotal($tipo, $valuec->idValle);
        $monts = self::getMontos($valuec->idValle); //falta tipo de miembro
        $data['montoGLB'] = $monts[1];
        $data['montoCOMAP'] = $monts[3];
        $data['montoGDR'] = 0;
        $monto = ($monts[1] + $monts[3]) * 12;
        //$data['montoGDR'] = $montof[2];        $monto = $monts[1] + $monts[3];
        $data['montoTotal'] = $monto . ' Bs.';
        $data['ceremonia'] = 'Reincorporacion';
        $data['cantipagos'] = '12';
        return $data;
    }
    private static function getMontos($vall, $mie = 'Regular')
    { //ajusatdo a 12

        $mes = date("Y-m-d");
        $qry = "SELECT (A.monto) AS montos,A.entidad FROM sgm_mecom_montos A WHERE A.idValle IN(0,$vall) AND A.miembro='$mie' AND '$mes'>=A.fechaInicio GROUP BY A.idValle,A.miembro,A.orden,A.entidad ORDER BY A.idValle DESC LIMIT 3";
        $query = DB::select($qry);
        $dd = $query;
        $aret = array();
        foreach ($dd as $vv)
            $aret[$vv->entidad] = $vv->montos;
        return $aret;

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
        $montof = self::getMontos($data->valle);
        $data->montoGLB = $montof[1]*12;
        $data->montoCOMAP = $montof[3]*12;
        $data->montoGDR = 0;
        $data->montoTaller = 0;
        $monto = ($montof[1] + $montof[3]) * 12;
        $data->monto = $monto; //pago
        $data->glosa = 'Pago de tramite de exaltacion';
        $data->nuevoPago = $data->fechaTramite;
        $data->params = 'Reincorporacion|' . $data->logia . '|' . $data->fechaTramite . '|';
        $data->descripcion = 'Pago Reincorporacion de HH fecha ' . $data->fechaTramite;
        $data->total = $monto;
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
    public static function getTramiteDatos($id)
    {
        $qry = "SELECT A.idLogia AS logia, B.valle, DATE_FORMAT(A.ultimoPago,'%d/%m/%Y') AS fechaTramite,A.gradoActual,A.estado FROM sgm_reincorporaciones A JOIN sgm_logias B ON A.idLogia=B.numero WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    private static function getUsuario($id)
    {
        $qry = "SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    private static function genControl($tipo = 10, $id = 0)
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
    private static function get_hash()
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

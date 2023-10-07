<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagosqr_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'contador';
    protected $table = 'sgm_mecom_pagos_registros';
    public static $prehash = 'GLB';
    public static $tramiteini = 35;
    public static function getCeremoniasAum($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Grado=2 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
        COUNT(C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
        ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt ,case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado
        FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero LEFT JOIN sgm_tramites_aumento C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle
        WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle,C.pagoQR,C.depositoGLB having COUNT(C.idTramite) > 0 and NOT (C.pagoQR>0 OR LENGTH(C.depositoGLB)>5)
        ORDER BY estado,A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremoniasAum($palabra = '', $valle = 0, $taller = 0)
    {
        $cond = "A.Grado=2 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idCeremonia) as numero FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_tramites_aumento C ON A.idCeremonia=C.idCeremonia
        WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,B.valle,C.pagoQR,C.depositoGLB having COUNT(C.idTramite) > 0 AND NOT (C.pagoQR>0 OR LENGTH(C.depositoGLB)>5)";
        // echo $qry;
        $results = DB::select($qry);
        if (count($results) > 0) {
            $resu = $results[0];
            return $resu->numero;
        } else {
            return 0;
        }
    }
    public static function getCeremoniaPago($id, $num, $tipo)
    {
        $qry = "SELECT DATE_FORMAT(B.fechaCeremonia,'%d/%m/%Y') AS fechaCeremonia,CONCAT('Nro. ',A.numero) AS nLogia,C.valle,B.okCeremonia,C.idValle
        FROM sgm_tramites_ceremonias B JOIN sgm_logias A ON B.idLogia=A.numero JOIN sgm_valles C ON C.idValle=A.valle WHERE B.idCeremonia=$id";
        $results = DB::select($qry);
        $valuec = $results[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['okCeremonia'] = $valuec->okCeremonia;
        $data['fechaCeremonia'] = $valuec->fechaCeremonia;
        $monto = self::getMontoTotal($tipo, $valuec->idValle);
        $data['monto'] = $monto . ' Bs.';
        $data['montoTotal'] = ($monto * $num) . ' Bs.';
        if ($tipo == 4) {
            $data['ceremonia'] = 'Exaltacion';
        } else {
            $data['ceremonia'] = 'Aumento';
        }

        $data['cantidad'] = $num;
        return $data;
    }
    private static function getMontoTotal($tipo, $vall)
    {
        $qry = "SELECT SUM(A.monto) AS montos FROM sgm_mecom_tramites_montos A WHERE A.idValle IN(0,$vall) AND A.tipo=$tipo GROUP BY A.idValle,A.miembro,A.orden ORDER BY A.idValle desc limit 1";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->montos;
    }
    public static function getDatosAum($id, $iduser, $tipo)
    {
        $qry = "SELECT B.logia,B.okDepoGLB,B.okDepoGDR,A.fechaCeremonia,COUNT(B.idMiembro) AS cantidad,C.valle FROM sgm_tramites_aumento B JOIN sgm_tramites_ceremonias A ON A.idCeremonia=B.idCeremonia JOIN sgm_logias C ON B.logia=C.numero
        WHERE B.idTramite=$id GROUP BY B.logia,B.okDepoGLB,B.okDepoGDR,A.fechaCeremonia,C.valle";
        $results = DB::select($qry);
        $data = $results[0];

        $resultsu = DB::select("SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$iduser");
        $usuario = $resultsu[0];
        // $usuario = $this->getUsuario($iduser);
        $data->email = $usuario->email;
        $data->username = $usuario->username;
        $data->nombre = $usuario->username;
        $data->CI = $usuario->logia;
        $mont1 = 0; //no paga nada al taller
        $mont2 = self::getMontoTotal($tipo, $data->valle);
        $monts = self::getMontos($tipo, $data->valle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data->montoGLB = $montof[1] * $data->cantidad;
        $data->montoCOMAP = $montof[3] * $data->cantidad;
        $data->montoGDR = $montof[2] * $data->cantidad;
        $data->montoTaller = $mont1 * $data->cantidad;

        $data->glosa = 'Pago de tramite de Aumento de Sal.';
        $data->nuevoPago = $data->fechaCeremonia;
        $data->params = 'Aumento|' . $data->logia . '|' . $data->fechaCeremonia . '|' . $data->cantidad;
        $data->descripcion = 'Pago aumento de sal. de ' . $data->cantidad . ' fecha ' . $data->fechaCeremonia;

        if ($data->cantidad > 0) {
            $data->total = ($mont1 + $mont2) * $data->cantidad;
        } else {
            $data->total = 0;
        }
        $data->monto = $data->total;

        return $data;
    }
    public static function getDatosExa($id, $iduser, $tipo)
    {
        $qry = "SELECT B.logia,B.okDepoGLB,B.okDepoGDR,A.fechaCeremonia,COUNT(B.idMiembro) AS cantidad,C.valle FROM sgm_tramites_exaltacion B JOIN sgm_tramites_ceremonias A ON A.idCeremonia=B.idCeremonia JOIN sgm_logias C ON B.logia=C.numero
        WHERE B.idTramite=$id GROUP BY B.logia,B.okDepoGLB,B.okDepoGDR,A.fechaCeremonia,C.valle";
        $results = DB::select($qry);
        $data = $results[0];

        $resultsu = DB::select("SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$iduser");
        $usuario = $resultsu[0];
        // $usuario = $this->getUsuario($iduser);
        $data->email = $usuario->email;
        $data->username = $usuario->username;
        $data->nombre = $usuario->username;
        $data->CI = $usuario->logia;
        $mont1 = 0; //no paga nada al taller
        $mont2 = self::getMontoTotal($tipo, $data->valle);
        $monts = self::getMontos($tipo, $data->valle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data->montoGLB = $montof[1] * $data->cantidad;
        $data->montoCOMAP = $montof[3] * $data->cantidad;
        $data->montoGDR = $montof[2] * $data->cantidad;
        $data->montoTaller = $mont1 * $data->cantidad;

        $data->glosa = 'Pago de tramite de Exaltacion';
        $data->nuevoPago = $data->fechaCeremonia;
        $data->params = 'Exaltacion|' . $data->logia . '|' . $data->fechaCeremonia . '|' . $data->cantidad;
        $data->descripcion = 'Pago Exaltacion de ' . $data->cantidad . ' fecha ' . $data->fechaCeremonia;

        if ($data->cantidad > 0) {
            $data->total = ($mont1 + $mont2) * $data->cantidad;
        } else {
            $data->total = 0;
        }
        $data->monto = $data->total;

        return $data;
    }
    public static function insertTransaccion($datos, $tipo = 2)
    { //crea el registro y el hash
        self::genControl($tipo, $datos->idCeremonia);
        $hash = self::get_hash();
        $data = array(
            'idRegistro' => $hash,
            'tipo' => $tipo, //---
            'usuario' => $datos->user, //--
            'idMiembro' => $datos->idCeremonia, //--
            'grado' => $datos->grado, //--
            'logia' => $datos->logia, //--
            'valle' => $datos->valle, //--
            'miembro' => $datos->Miembro, //--
            'cantidad' => $datos->cantidad, //--
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
        $idd = self::insertGetId($data);
        if ($idd > 0) {
            return $hash;
        } else {
            return null;
        }

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
    private static function getMontos($grado, $vall)
    {
        $qry = "SELECT (A.monto) AS montos,A.entidad FROM sgm_mecom_tramites_montos A WHERE A.idValle IN(0,$vall) AND A.tipo=$grado  ORDER BY A.idValle DESC LIMIT 3";
        $results = DB::select($qry);
        return $results;
    }
    public static function getCeremoniasExa($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Grado=3 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
COUNT(C.idTramite) AS numeroAum,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt ,case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado
FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero LEFT JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia JOIN sgm_valles D ON B.valle=D.idValle
WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle ORDER BY A.fechaModificacion DESC Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCeremoniasExa($palabra = '', $valle = 0, $taller = 0)
    {
        $cond = "A.Grado=3 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(A.idCeremonia) as numero FROM sgm_tramites_ceremonias A JOIN sgm_logias B ON A.idLogia=B.numero JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia
        WHERE $cond GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,B.valle,C.pagoQR,C.depositoGLB having COUNT(C.idTramite) > 0 AND NOT (C.pagoQR>0 OR LENGTH(C.depositoGLB)>5)";
        // echo $qry;
        $results = DB::select($qry);
        if (count($results) > 0) {
            $resu = $results[0];
            return $resu->numero;
        } else {
            return 0;
        }
    }
    public static function getDatosPagoIni($id, $tipo = 2)
    {
        $qry = "SELECT A.apPaterno,A.apMaterno,A.nombres,B.logia,B.numero,C.valle,C.idValle FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->logia;
        $data['nombre'] = $valuec->nombres;
        $data['paterno'] = $valuec->apPaterno;
        $data['materno'] = $valuec->apMaterno;
        $monto = self::getMontoTotal($tipo, $valuec->idValle) + self::$tramiteini; //revisar 35
        $monts = self::getMontos($tipo, $valuec->idValle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data['montoGLB'] = $montof[1];
        $data['montoCOMAP'] = $montof[3];
        $data['montoGDR'] = $montof[2];
        $data['montoGLB2'] = self::$tramiteini;
        $data['montoTotal'] = $monto . ' Bs.';
        $data['ceremonia'] = 'Iniciacion profano';
        return $data;
    }
    public static function getDatosIni($id, $iduser, $tipo)
    {
        $qry = "SELECT A.apPaterno,A.apMaterno,A.nombres,B.logia,B.numero,C.valle,C.idValle,A.fechaIniciacion FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON (A.logia=B.numero) JOIN sgm_valles C ON B.valle=C.idValle WHERE A.idTramite=$id";
        $results = DB::select($qry);
        $data = $results[0];

        $resultsu = DB::select("SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$iduser");
        $usuario = $resultsu[0];
        // $usuario = $this->getUsuario($iduser);
        $data->email = $usuario->email;
        $data->username = $usuario->username;
        $data->nombre = $usuario->username;
        $data->CI = $usuario->logia;
        $data->logia = $data->numero;
        $data->valle = $data->idValle;
        $mont1 = 0; //no paga nada al taller
        $mont2 = self::getMontoTotal($tipo, $data->idValle);
        $monts = self::getMontos($tipo, $data->idValle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $data->montoGLB = $montof[1] + 35;
        $data->montoCOMAP = $montof[3];
        $data->montoGDR = $montof[2];
        $data->montoTaller = $mont1;

        $data->glosa = 'Pago de derechos de Iniciacion.';
        $data->nuevoPago = $data->fechaIniciacion;
        $data->params = 'Iniciacion|' . $data->logia . '|' . $data->fechaIniciacion . '|1';
        $data->descripcion = 'Pago Iniciacion';

        $data->total = ($mont1 + $mont2) + 35;
        $data->monto = ($mont1 + $mont2) + 35;
        $data->cantidad = 1;

        return $data;
    }
    //-----modificaciones de pagos de tramites
    public static function getAumentados($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Grado=2 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
        CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
        ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt ,
		  case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado,E.NombreCompleto,C.idTramite as reg
        FROM sgm_tramites_ceremonias A
		  JOIN sgm_logias B ON A.idLogia=B.numero
		  LEFT JOIN sgm_tramites_aumento C ON A.idCeremonia=C.idCeremonia
		  JOIN sgm_valles D ON B.valle=D.idValle
        LEFT JOIN sgm_miembros E ON C.idMiembro=E.id
        WHERE $cond
		  GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle,C.pagoQR,C.depositoGLB,C.idTramite
		  having COUNT(C.idTramite) > 0 and NOT (C.pagoQR>0 OR LENGTH(C.depositoGLB)>5)
        ORDER BY estado,A.fechaModificacion DESC Limit $inicio,$cantidad ";
       //echo $qry;
        $results = DB::select($qry);
        return $results;
    }

    public static function getExaltados($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Grado=3 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT A.idCeremonia,A.fechaCeremonia,CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,CONCAT('R.L.S. ',B.Logia) AS nLogia,B.numero,
        CASE WHEN (A.okCeremonia=1) THEN 'Cer. Aprobada' ELSE 'Sin aprobacion' END AS okCeremonia,A.fechaModificacion,D.valle
        ,case when C.pagoQR>0 THEN 'Pago con QR' when LENGTH(C.depositoGLB)>5 then 'Pago con deposito' ELSE 'Sin Pago' end AS estadotxt ,
		  case when C.pagoQR>0 THEN 1 when LENGTH(C.depositoGLB)>5 then 1 ELSE 0 end AS estado,E.NombreCompleto,C.idTramite as reg
        FROM sgm_tramites_ceremonias A
		  JOIN sgm_logias B ON A.idLogia=B.numero
		  LEFT JOIN sgm_tramites_exaltacion C ON A.idCeremonia=C.idCeremonia
		  JOIN sgm_valles D ON B.valle=D.idValle
        LEFT JOIN sgm_miembros E ON C.idMiembro=E.id
        WHERE $cond
		  GROUP BY A.idCeremonia,A.fechaCeremonia,A.okCeremonia,B.logia,B.numero,D.valle,C.pagoQR,C.depositoGLB,C.idTramite
		  having COUNT(C.idTramite) > 0 and NOT (C.pagoQR>0 OR LENGTH(C.depositoGLB)>5)
        ORDER BY estado,A.fechaModificacion DESC Limit $inicio,$cantidad ";
       //echo $qry;
        $results = DB::select($qry);
        return $results;
    }
}

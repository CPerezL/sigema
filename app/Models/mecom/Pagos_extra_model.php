<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos_extra_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'contador';
    protected $table = 'sgm_mecom_pagos_registros';
    public static $prehash = 'GLB';
    public static function getListaMiembros($pagina, $cantidad, $palabra = '', $valle = 0, $taller = 0, $pago = 0)
    {
        if ($pago > 0) {
            $pp = self::getSimplePago($pago);
            $inicio = $cantidad * ($pagina - 1);
            $cond = "A.Estado = '1' ";
            if ($taller > 0) {
                $cond .= " AND A.LogiaActual = '$taller'";
            }

            if (strlen($palabra) > 2) {
                $cond .= " AND A.NombreCompleto Like '%$palabra%'";
            }

            if ($valle > 0) {
                $cond .= " AND B.valle = $valle";
            }

            $qry = "SELECT A.id, A.NombreCompleto, B.logia, A.LogiaActual, A.Miembro, CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Apren.' WHEN 2 THEN 'Comp.' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
           case when C.idPago >0 then C.idPago ELSE 0 END as estadopago,C.fechaCreacion,'$pago'  AS pago ,case when (A.LogiaActual=$pp->taller OR $pp->taller=0 ) OR ((B.valle=$pp->valle AND NOT ($pp->valle>0))   OR $pp->valle=0) then 1 ELSE 0 END AS habil,
           '$pp->monto' AS monto FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero left JOIN sgm_pagos_registros C ON A.id=C.idMiembro AND C.idPago='$pago'
           WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
            //echo $qry . '<hr>';
            $query = DB::select($qry);
            return $query;
        } else {
            return '';
        }
    }
    public static function getNumeroMiembros($palabra = '', $valle = 0, $taller = 0, $pago = 0)
    {
        $cond = "Estado = '1' ";
        if ($taller > 0) {
            $cond .= " AND sgm_miembros.LogiaActual = '$taller'";
        }

        if ($valle > 0) {
            $cond .= " AND sgm_logias.valle = $valle";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND sgm_miembros.NombreCompleto like '%$palabra%'";
        }
        $qry = " select count(id) as numero from sgm_miembros join sgm_logias on sgm_logias.numero = sgm_miembros.LogiaActual where $cond";
        $query = DB::select($qry);
        $row = $query[0];
        return $row->numero;
    }
    private static function getSimplePago($id)
    {
        $query = DB::select("SELECT valle,taller,monto,numeroPagos,cobro FROM sgm_pagos_montos where idMonto=$id");
        $row = $query[0];
        return $row;
    }
    public static function getDatosSimple($id, $idp)
    {
        $data = self::getMiembroSimple($id); //
        $pago = self::getSimplePago($idp);
        $data->monto = $pago->monto;
        $data->pago = $pago->cobro;
        $data->maximo = $pago->numeroPagos;
        return $data;
    }
    private static function getMiembroSimple($id, $tipo = 0)
    {
        if ($tipo == 1) {
            $qry = "SELECT A.ultimoPago,A.Miembro,A.Grado,A.LogiaActual,A.LogiaAfiliada AS Logia,B.valle,A.username,A.NombreCompleto,C.CI,C.email,A.LogiaAfiliada FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero
  LEFT JOIN sgm_miembrosdata C ON A.id=C.id WHERE A.id=$id";
        } else {
            $qry = "SELECT A.ultimoPago,A.Miembro,A.Grado,A.LogiaActual,A.LogiaActual AS Logia,B.valle,A.NombreCompleto FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE A.id=$id";
        }
        $query = DB::select($qry);
        return $query[0];
    }
    public static function insertTransaccion($id, $datos, $idp, $tipo = 11)
    {
        self::genControl($tipo, $id);
        $hash = self::get_hash();
        $data = array(
            'idRegistro' => $hash,
            'tipo' => $tipo,
            'proceso' => $idp,
            'idMiembro' => $id,
            'grado' => $datos->Grado,
            'logia' => $datos->Logia,
            'valle' => $datos->valle,
            'miembro' => $datos->Miembro,
            'cantidad' => $datos->cantidad,
            'aprobado' => 0,
            'ultimoPago' => $datos->ultimoPago,
            'nuevoPago' => $datos->nuevoPago,
            'monto' => $datos->total,
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
    public static function getDatos($id, $idp, $cantidad = 1)
    {
        $data = self::get_data_miembro($id); //---
        $suma = 0;
        $mons = self::get_data_pagos($idp); //---
        if ($mons->entidad == 1) { //glb
            $data->montoGLB = $mons->monto * $cantidad;
            $data->montoCOMAP = 0;
            $data->montoGDR = 0;
            $data->montoTaller = 0; //

        } elseif ($mons->entidad == 2) { //gdr
            $data->montoGLB = 0;
            $data->montoCOMAP = 0;
            $data->montoGDR = $mons->monto * $cantidad;
            $data->montoTaller = 0; //
        } elseif ($mons->entidad == 3) { //taller
            $data->montoGLB = 0;
            $data->montoCOMAP = 0;
            $data->montoGDR = 0;
            $data->montoTaller = $mons->monto * $cantidad; //
        }
        $suma = $mons->monto;
        $data->obolo = $suma;
        $data->cantidad = $cantidad;
        $nuevomes = date("Y-m-d");
        $data->glosa = 'Pago Extra de ' . $mons->cobro;
        $data->nuevoPago = $nuevomes;
        $data->ultimoPago = $nuevomes;
        $data->params = $idp . '|' . $cantidad . '|' . $mons->entidad . '|' . $suma;
        $data->descripcion = 'Pago Extra de ' . $mons->cobro;
        if ($cantidad > 0) {
            $data->total = ($data->obolo) * $cantidad;
        } else {
            $data->total = 0;
        }
        return $data;
    }
    private static function get_data_miembro($id)
    {
        $qry = "SELECT A.Miembro,A.Grado,A.LogiaActual,A.LogiaActual AS Logia,B.valle,A.username,A.NombreCompleto,C.CI,C.email,A.LogiaAfiliada FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero
  LEFT JOIN sgm_miembrosdata C ON A.id=C.id WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    public static function get_data_pagos($id)
    {
        $qry = "SELECT A.cobro,A.descripcion,A.monto,A.entidad,A.tipo,B.tipo,A.numeroPersonas,A.numeroPagos FROM sgm_pagos_montos A JOIN  sgm_pagos_tipos B ON A.entidad=B.idTipo WHERE A.idMonto=$id";
        $query = DB::select($qry);
        return $query[0];
    }
}

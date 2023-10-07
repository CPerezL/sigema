<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos_registros_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'contador';
    protected $table = 'sgm_mecom_pagos_registros';
    public static $prehash = 'GLB';

    public static function insertTransaccion($datos)
    {
        self::genControl(10, $datos->reg);
        $hash = self::get_hash();
        $data = array(
            'idRegistro' => $hash,
            'tipo' => 10, //---
            'usuario' => $datos->user, //--
            'idMiembro' => $datos->reg, //--
            'grado' => 1, //--
            'logia' => $datos->taller, //--
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
}

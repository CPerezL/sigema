<?php

namespace App\Models\mecom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Registros_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idRegistro';
    protected $table = 'sgm_mecom_registros';
    public static function checkRegistro($idf, $idm)
    {
        $qry = self::select('idRegistro')->whereraw("idMiembro=$idm AND idFormulario=$idf")->first('idRegistro');
        if (is_null($qry)) {
            return true;
        } else {
            return false;
        }
    }
    public static function getObolo($id)
    {
        $qry = self::where('idRegistro', $id)->first(['idFormulario', 'monto']);
        return $qry;
    }
    public static function getMeses($idform, $tipo = 'Regular')
    {
        $qry = "SELECT SUM(A.numeroCuotas) AS canti, SUM(A.montoGLB) AS mglb,SUM(A.montoCOMAP) AS mcom FROM sgm_mecom_registros A WHERE A.idFormulario=$idform AND A.miembro='$tipo'";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getPagosHechos($idformulario)
    {
        $qry = "SELECT A.taller,A.numeroCuotas,A.ultimoPago,A.fechaPagoNuevo,A.idMiembro,A.monto from sgm_mecom_registros A WHERE A.idFormulario=$idformulario";
        $results = DB::select($qry);
        return $results;
    }
}

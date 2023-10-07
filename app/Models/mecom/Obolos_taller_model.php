<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obolos_taller_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idMonto';
    protected $table = 'sgm_mecom_pagos_montos';
    public static function getItems()
    {
        $qry = "SELECT CONCAT(B.logia,'-',A.idTaller) AS ordenar,A.tipo,B.logia,A.idTaller,A.concepto,A.miembro,A.monto,FORMAT(A.monto,0,'es_ES') AS montotxt,DATE_FORMAT(A.fechaInicio, '%b-%Y') AS fechaIniciotxt,DATE_FORMAT(A.fechaCreacion, '%d/%m/%Y') AS fechaModifica,
        DATE_FORMAT(A.fechaFin, '%b-%Y') AS fechaFintxt, DATE_FORMAT(A.fechaInicio, '%d/%m/%Y') AS fechaInicio,DATE_FORMAT(A.fechaFin, '%d/%m/%Y') AS fechaFin,A.orden,C.tipo AS tipotxt,A.idMonto
        FROM sgm_mecom_pagos_montos A JOIN sgm_logias B ON A.idTaller=B.numero JOIN sgm_mecom_pagos_tipo C ON A.tipo=C.idTipo ORDER BY A.idTaller,A.tipo";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function checkExiste($tall, $miembro, $tipo)
    {
        $val = self::select('idMonto')->where('idTaller', $tall)->where('miembro', $miembro)->where('tipo', $tipo)->first('idMonto');
        if (is_null($val)) {
            return 0;
        } else {
            return $val->idMonto;
        }
    }
}

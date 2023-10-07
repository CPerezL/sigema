<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramites_montos_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idMonto';
    protected $table = 'sgm_mecom_tramites_montos';
    public static function getItems()
    {
        $qry = "SELECT A.valle,A.idValle,A.miembro, SUM(CASE WHEN A.entidad=1 THEN A.monto ELSE 0 END) AS montoglb,
SUM(CASE WHEN A.entidad=2 THEN A.monto ELSE 0 END) AS montogdr, SUM(CASE WHEN A.entidad=3 THEN A.monto ELSE 0 END) AS montocomap,SUM(A.monto) AS montoTotal,
A.orden,A.concepto ,A.tipo
FROM sgm_mecom_tramites_montos A
JOIN sgm_mecom_tramites_tipo B ON A.tipo=B.orden
GROUP BY A.valle,A.idValle,A.miembro,A.orden,A.concepto,A.tipo
ORDER BY A.tipo,A.idValle,A.miembro,A.entidad,A.orden";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }

    public static function getTiposTramite()
    {
        $query = DB::select('SELECT orden,tramite FROM sgm_mecom_tramites_tipo where tipo in (2,3) ORDER BY orden');
        $arreglo = null;
        foreach ($query as $dta) {
            $arreglo[$dta->orden] = $dta->tramite;
        }
        return $arreglo;
    }

    public static function getOrden($valle, $tipo)
    {
        $val = self::select('orden')->where('tipo', $tipo)->where('idValle', $valle)->orderby('orden')->first('orden');
        if (is_null($val)) {
            return 2;
        } else {
            return $val->orden + 1;
        }
    }
}

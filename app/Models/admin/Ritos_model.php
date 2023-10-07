<?php

namespace App\Models\admin;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritos_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'idRito';
    protected $table = 'sgm_ritos';
    use HasFactory;
    public static function getRitosArray($oriente)
    {
        $cond = 'idOriente>0';
        if ($oriente > 0) {
            $cond .= " AND idOriente=$oriente";
        }
        $qry = self::select('idRito as numero', 'rito')->whereraw($cond)->orderBy('idRito', 'asc')->pluck('rito', 'numero');
        return $qry;
    }
    public static function getValue($id, $campo, $nodata = '')
    {
        $qry = self::where('idRito', $id)->value($campo);
        if (!is_null($qry)) {
            return $qry;
        } else {
            return $nodata;
        }
    }
    public static function getRitosOficiales($pagina, $cantidad)
    {
        $inicio = $cantidad * ($pagina - 1);
        $qry = "SELECT A.idRito,A.rito,A.nombreCompleto,A.textoPlanillas,A.nombreTexto,COUNT(B.id) AS cantidad FROM sgm_ritos A LEFT JOIN sgm_oficiales B ON A.idRito=B.rito
        GROUP BY A.idRito,A.rito,A.nombreCompleto,A.nombreTexto ORDER BY A.rito Limit $inicio,$cantidad ";
        $results = DB::select($qry);
        return $results;
    }
    public static function getRitosOficialesTotal()
    {
        $cond = "idRito>0";
        $count = self::select('idRito')->whereraw($cond)->count();
        return $count;
    }
    public static function getListaOficiales($rito)
    {
        $qry = "SELECT A.id,A.oficial,A.orden,B.rito FROM sgm_oficiales A join sgm_ritos B ON A.rito=B.idRito WHERE A.rito=$rito ORDER BY A.orden";
        $results = DB::select($qry);
        return $results;
    }
}

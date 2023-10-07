<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obolos_valle_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_mecom_montos';

    public static function getItems()
    {
        $qry = "SELECT DATE_FORMAT(A.fechaInicio, '%b-%Y') AS fechaIniciotxt,DATE_FORMAT(A.fechaFin, '%b-%Y') AS fechaFintxt,DATE_FORMAT(A.fechaInicio, '%d/%m/%Y') AS fechaInicio,DATE_FORMAT(A.fechaFin, '%d/%m/%Y') AS fechaFin,A.valle,A.idValle,A.concepto,A.miembro,
  SUM(CASE WHEN A.entidad=1 THEN A.monto ELSE 0 END) AS montoglb,SUM(CASE WHEN A.entidad=2 THEN A.monto ELSE 0 END) AS montogdr,
  SUM(CASE WHEN A.entidad=3 THEN A.monto ELSE 0 END) AS montocomap,FORMAT(SUM(A.montohabil),0,'es_ES') AS montoTotal,
  SUM(CASE WHEN A.entidad=2 THEN A.montohabil ELSE 0 END) AS montohabil,  FORMAT(SUM(CASE WHEN A.entidad=1 THEN A.monto ELSE 0 END),0,'es_ES') AS montoglbtxt,
  FORMAT(SUM(CASE WHEN A.entidad=2 THEN A.monto ELSE 0 END),0,'es_ES') AS montogdrtxt,FORMAT(SUM(CASE WHEN A.entidad=3 THEN A.monto ELSE 0 END),0,'es_ES') AS montocomaptxt,
  FORMAT(SUM(CASE WHEN A.entidad=2 THEN A.montohabil ELSE 0 END),0,'es_ES') AS montohabiltxt,A.orden
  FROM sgm_mecom_montos A JOIN sgm_mecom_montos_tipo B ON A.entidad=B.idTipo
  GROUP BY  A.valle,A.idValle,A.concepto,A.miembro,A.fechaInicio,A.fechaFin,A.orden
  ORDER BY  A.idValle,A.miembro,A.orden";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function checkFecha($miembro = 'Regular', $valle = 0)
    {
        if ($valle > 0) {
            $qry = "SELECT A.fechaFin FROM sgm_mecom_montos A WHERE  A.miembro='$miembro' AND A.idValle=$valle LIMIT 1 ";
        } else {
            $qry = "SELECT A.fechaFin FROM sgm_mecom_montos A WHERE  A.miembro='$miembro' AND A.idValle=0 LIMIT 1  ";
        }
        $results = DB::select($qry);
        if (empty($results)) {
            return true;
        } else {
            return false;
        }
      }

    public static function checkFechaFin($fecha, $miembro = 'Regular', $valle = 0)
    {
        if ($valle > 0) {
            $qry = "SELECT A.fechaFin FROM sgm_mecom_montos A WHERE  A.miembro='$miembro' AND A.fechaFin<='$fecha'  AND A.fechaInicio<='$fecha' AND A.idValle=$valle LIMIT 1 ";
        } else {
            $qry = "SELECT A.fechaFin FROM sgm_mecom_montos A WHERE  A.miembro='$miembro' AND A.fechaFin<='$fecha'  AND A.fechaInicio<='$fecha' AND A.idValle=0 LIMIT 1  ";
        }
        $results = DB::select($qry);
        if (empty($results)) {
            return 0;
        } else {
            return 1;
        }
    }
}

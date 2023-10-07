<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte_depositos_model extends Model
{
    use HasFactory;
    public static function getLogias($valle)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT B.numero,CONCAT(B.logia, ' Nro ',B.numero) AS Tallertxt FROM sgm_logias B WHERE B.valle=$valle ORDER BY B.numero";
        $results = DB::select($qry);
        return $results;
    }
    public static function getMesesPago($idt, $gestion, $tipo = 0)
    {
        if ($tipo == 2) //revisados
        {
            $qry = "SELECT MONTH(A.fechaAprobacion) AS mes, SUM(B.monto) AS sumatotal , SUM(B.montoGDR) as sumagdr , SUM(B.montoGLB) as sumaglb, SUM(B.montoCOMAP) as sumacomap
    FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    WHERE A.taller=$idt AND A.gestion=$gestion AND A.estado=4 and A.aprobadogdr=4
    GROUP BY MONTH(A.fechaAprobacion) ORDER BY A.idFormulario, MONTH(A.fechaAprobacion)";
        } elseif ($tipo == 3) //no revisados
        {
            $qry = "SELECT MONTH(A.fechaAprobacion) AS mes, SUM(B.monto) AS sumatotal , SUM(B.montoGDR) as sumagdr , SUM(B.montoGLB) as sumaglb, SUM(B.montoCOMAP) as sumacomap
    FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    WHERE A.taller=$idt AND A.gestion=$gestion AND A.estado=4 and A.aprobadogdr<>4
    GROUP BY MONTH(A.fechaAprobacion) ORDER BY A.idFormulario, MONTH(A.fechaAprobacion)";
        } else //todos
        {
            $qry = "SELECT MONTH(A.fechaAprobacion) AS mes, SUM(B.monto) AS sumatotal , SUM(B.montoGDR) as sumagdr , SUM(B.montoGLB) as sumaglb, SUM(B.montoCOMAP) as sumacomap
    FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    WHERE A.taller=$idt AND A.gestion=$gestion AND A.estado=4
    GROUP BY MONTH(A.fechaAprobacion) ORDER BY A.idFormulario, MONTH(A.fechaAprobacion)";
        }
        $results = DB::select($qry);
        $arreglo = array();
        foreach ($results as $sem) {
            $arreglo[$sem->mes] = $sem->sumagdr;
        }
        return $arreglo;
    }
    public static function getMesesPagoValle($idt, $gestion)
    {
        $ges1 = "$gestion-01-01";
        $ges2 = "$gestion-12-31";
        $qry = "SELECT MONTH(A.fechaAprobacion) AS mes, sum(A.montoTotal) AS sumat FROM sgm_mecom_formularios A WHERE A.taller=$idt AND (A.fechaAprobacion between '$ges1' AND '$ges2') GROUP BY MONTH(A.fechaAprobacion) ORDER BY MONTH(A.fechaAprobacion)";
        $results = DB::select($qry);
        $arreglo = array();
        foreach ($results as $sem) {
            $arreglo[$sem->mes] = $sem->sumat;
        }
        return $arreglo;
    }
}

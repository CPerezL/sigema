<?php

namespace App\Models\mecom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Webobolos_model extends Model
{
    use HasFactory;
    public static function getListaWebolos($valle, $taller, $desde, $hasta)
    {
        if ($valle > 0 || $taller > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $dd = explode('/', $desde);
            $hh = explode('/', $hasta);
            $desded = "$dd[2]-$dd[1]-$dd[0]";
            $hastad = "$hh[2]-$hh[1]-$hh[0]";
            $cond = "A.estado = '4'";
            if ($taller > 0) {
                $cond .= " AND B.taller = '$taller'";
            }

            if ($valle > 0) {
                $cond .= " AND D.valle = $valle";
            }
            $qry = "SELECT A.fechaAprobacion AS fechaPago,A.documento,C.NombreCompleto,C.Miembro,CASE C.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
      B.taller AS LogiaActual,D.logia,E.transaccion,E.idRegistro,E.monto,B.numeroCuotas AS cantidad,CASE when B.montoTaller>0 then B.montoTaller ELSE E.montoTaller end AS montoTaller,B.montoGDR, B.montoGLB,B.montoCOMAP,CONCAT(DATE_FORMAT(B.ultimoPago,'%b-%Y'),'/',DATE_FORMAT(B.fechaPagoNuevo,'%b-%Y')) AS Periodo
      FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario JOIN sgm_miembros C ON B.idMiembro=C.id JOIN sgm_logias D ON B.taller=D.numero
      JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
      WHERE $cond  AND A.tipo=10 AND B.fechaCreacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' ORDER BY B.taller,C.NombreCompleto ";
            $results = DB::select($qry);
            return $results;
        }
        return '';
    }
    public static function getNumeroWebolos($valle, $taller, $desde, $hasta)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT B.numero,CONCAT(B.logia, ' Nro ',B.numero) AS Tallertxt FROM sgm_logias B WHERE B.valle=$valle ORDER BY B.numero";
        $results = DB::select($qry);
        return $results;
    }

}

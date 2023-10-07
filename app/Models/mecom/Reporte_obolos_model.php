<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte_obolos_model extends Model
{
    use HasFactory;
    public static function getListaMiembros($gestion, $logia)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago,
    CASE A.Grado WHEN 0 THEN 'NA' WHEN 1 THEN 'A:.M:.' WHEN 2 THEN 'C:.M:.' WHEN 3 THEN 'M:.M:.' WHEN 4 THEN 'Ex VM' END AS GradoActual,
      DATE_FORMAT(A.ultimoPago,'%c') AS hastames, DATE_FORMAT(A.ultimoPago,'%Y')-$gestion AS gestionbase
        FROM sgm_miembros A WHERE (A.LogiaActual = $logia ) AND (A.Grado>0)  AND A.ultimoPago>='$gestion/01/01' 
         GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.ultimoPago ORDER BY A.NombreCompleto ASC";
        $results = DB::select($qry);
        if (!empty($results)) {
            return $results;
        } else {
            return null;
        }
    }
    public static function getMesPagado($idm, $fecha)
    {
        $qry = "SELECT A.idRegistro,B.documento FROM sgm_mecom_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
   WHERE A.idMiembro=$idm AND  '$fecha' BETWEEN A.ultimoPago AND A.fechaPagoNuevo  AND '$fecha' > A.ultimoPago and B.estado=4 ";
        $results = DB::select($qry);
        if (!empty($results)) {
            return $results;

        } else {
            return null;
        }
    }
}

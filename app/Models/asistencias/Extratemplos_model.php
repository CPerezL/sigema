<?php

namespace App\Models\asistencias;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extratemplos_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idExtraTemplo';
    protected $table = 'sgm_extratemplos';
    private static $fcreacion = '';
    private static $regular = 6;

    public static function getItems($taller, $gestion, $fextrat, $level = 1)
    {
        $fcreacion = self::$fcreacion;
        $regular = self::$regular;
        if ($fextrat > 0 && $gestion > 2015 && $taller > 0) {
            date_default_timezone_set("America/La_Paz");
            $hoy = date("Y-m-d");
            $hoydia = date("Y-m-d H:i:s");
            $ayerdia = date("Y-m-d H:i:s", strtotime("$hoydia -2 days"));
            $fechaten = self::getDiaExtraT($fextrat);
            $dosemanas = date("Y-m-d", strtotime("$fechaten +1 months")); //un mes despues de la tenida

            $date1 = strtotime($dosemanas); //un mes
            $date2 = strtotime($hoy); //hoy
            $date3 = strtotime($fechaten); //tenida
            if ($date1 > $date3) {

                if ($date3 > $date2) {
                    $fvalida = 1;
                } //ok
                else {
                    $fvalida = 2;
                } //futura
            } else {
                $fvalida = 0;
            } //pasada
            if ($ayerdia > $fcreacion) {
                $fvalida = 2;
            } else {
                $fvalida = 0;
            }
            //si es admin
            if ($level > 2 && $fvalida == 0) {
                $fvalida = 2;
            }

            $plazopago = date("Y-m", strtotime("$fechaten -$regular months")) . '-01';
            $newcasi = date("Y-m", strtotime("$fechaten -$regular months")) . '-01';
            $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago, CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
B.idExtraTemploAsis, CASE WHEN B.idExtraTemplo IS NULL THEN 'No asistio' ELSE 'Asistio' END AS EstadoAsis, '$plazopago' AS fecha, CASE WHEN A.UltimoPago>='$newcasi' THEN 1 ELSE 0 END AS pagoOk,$fvalida AS FechaValida
FROM sgm_miembros A INNER JOIN sgm_extratemplos C ON ((C.grado=A.Grado or A.Grado=4) AND C.idExtraTemplo=$fextrat) LEFT JOIN sgm_extratemploasis B ON (A.id = B.idMiembro AND C.idExtraTemplo = B.idExtraTemplo)
WHERE A.Estado=1 AND (A.UltimoPago >= '$plazopago' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND (A.LogiaActual= '$taller'  OR A.LogiaAfiliada = '$taller')
ORDER BY B.idExtraTemplo DESC,A.Grado ASC,A.NombreCompleto ASC";
            $results = DB::select($qry);
            return $results;
        }
        return '';
    }
    public static function getDiaExtraT($idet)
    {
        $query = DB::select('SELECT fechaExtraTemplo,fechaAlta FROM sgm_extratemplos WHERE idExtraTemplo=' . $idet);
        $dato = $query[0];
        self::$fcreacion = $dato->fechaAlta;
        return $dato->fechaExtraTemplo;
    }
    public static function getDiaExtraTemplo($gestion, $logia)
    {
        $qry = "SELECT idExtraTemplo,temaExtraTemplo,fechaExtraTemplo,numero,grado as gradoet,instructor1,instructor2,instructor3,DATE_FORMAT(fechaExtraTemplo,'%d/%m/%Y') as fechaet,CASE grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Primero' WHEN 2 THEN 'Segundo' WHEN 3 THEN 'Tercero' END AS GradoActual, CASE WHEN CURRENT_TIMESTAMP > DATE_ADD(fechaAlta, INTERVAL 1 DAY) then 0 ELSE 1 END AS abierto FROM sgm_extratemplos WHERE gestion=$gestion AND idLogia=$logia ORDER BY grado,fechaExtraTemplo";
        $results = DB::select($qry);
        return $results;
    }
    public static function getDataExtraT($idet, $todo = 0)
    {
        if ($todo == 1) {
            $qry = 'SELECT * FROM sgm_extratemplos WHERE idExtraTemplo=' . $idet;
        } else {
            $qry = 'SELECT fechaExtraTemplo,grado,temaExtraTemplo FROM sgm_extratemplos WHERE idExtraTemplo=' . $idet;
        }

        $results = DB::select($qry);
        return $results[0];
    }
    public static function getDatosLogia($nlog)
    {
        $qry = "SELECT A.logia,A.valle,A.diatenida,A.rito,A.nombreCompleto ,B.valle,B.logo,B.tipo FROM sgm_logias A INNER JOIN sgm_valles B ON A.valle=B.idValle WHERE numero='$nlog'";
        $query = DB::select($qry);
        $dato = $query[0];
        return $dato;
    }
    public static function getAsistenteset($idet)
    {
        $qry = "SELECT A.NombreCompleto, CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, B.idExtraTemploAsis
        FROM sgm_miembros A INNER JOIN sgm_extratemplos C ON ((C.grado=A.Grado OR A.Grado=4 ) AND C.idExtraTemplo= $idet )
        INNER JOIN sgm_extratemploasis B ON (A.id = B.idMiembro AND C.idExtraTemplo = B.idExtraTemplo) ORDER BY B.idExtraTemplo DESC,A.Grado ASC,A.NombreCompleto ASC";
        $query = DB::select($qry);
        return $query;
    }

}

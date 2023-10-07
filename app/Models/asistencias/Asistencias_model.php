<?php

namespace App\Models\asistencias;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencias_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idAsistencia';
    protected $table = 'sgm_asistencia';
    private static $diavalido = 0;
    private static $regular = 6;
    public static function setRegular($reg)
    {
        self::$regular = $reg;
    }
    public static function getAsistencia($taller, $gestion)
    {
        $qry = "SELECT A.idLogia, A.fechaTenida,A.grado,A.numeroActa1,A.numeroActa2,A.numeroActa3 from sgm_asistenciadata A
        WHERE A.idLogia=$taller AND A.fechaTenida>'$gestion-01-01' AND A.fechaTenida<'$gestion-12-31' ORDER BY A.fechaTenida ASC";
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumero($taller, $tenida, $grado)
    {
        $qry = "SELECT count(A.idMiembro) AS numero FROM sgm_asistencia A INNER JOIN sgm_miembros B ON A.idMiembro=B.id
        WHERE A.idLogia=$taller AND A.fechaTenida='$tenida' AND B.Grado=$grado";
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getNumeroTotal($taller, $tenida)
    {
        $qry = "SELECT count(A.idMiembro) AS numero FROM sgm_asistencia A INNER JOIN sgm_miembros B ON A.idMiembro=B.id WHERE A.idLogia=$taller AND A.fechaTenida='$tenida'";
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getDiaTenida($fechainicio, $fechacierre, $year, $month, $dian, $taller = 0)
    {
        // dd($fechainicio, $fechacierre, $year, $month, $dian, $taller);
        $gestion = date('Y'); //año de la vista este sera el que abrira las anterires gestiones
        $dianom[1] = 'lun';
        $dianom[2] = 'mar';
        $dianom[3] = 'mie';
        $dianom[4] = 'jue';
        $dianom[5] = 'vie';
        $dianom[6] = 'sab';
        $dianom[7] = 'dom';

        $factual = date('Y-m-d');
        if ((int) $month < 10) {
            $mes = '0' . (int) $month;
        } else {
            $mes = $month;
        }

        $cc = 0;
        $dia = 8 - $dian;
        $tenidas = array();
        # First weekday in specified month: 0 = monday, 6 = sunday
        $firstDay = date('N', mktime(0, 0, 0, $month, $dia, $year));
        /* Add 0 days if monday ... 6 days if tuesday, 1 day if sunday
        to get the first monday in month */
        $addDays = (7 - $firstDay);
        $diat = date('d', mktime(0, 0, 0, $month, 1 + $addDays, $year));
        //primer dia
        $tenidas[$cc]['dia'] = $diat;
        $dten = self::getDataAsisSimple($taller, $year . '-' . $month . '-' . $diat);
        $fechares2 = $year . '-' . $mes . '-' . $diat;
        if (($fechares2 >= $fechainicio && $fechares2 <= $fechacierre) || $year < $gestion) {
            if ($fechares2 > $factual) {
                $tenidas[$cc]['valido'] = 2;
            } else {
                $tenidas[$cc]['valido'] = 1;
            }
            //normal
        } else {
            $tenidas[$cc]['valido'] = 0;
        }
        //receso
        if (self::$diavalido == 0 && $year < $gestion) {
            $tenidas[$cc]['valido'] = 0;
        }

        $tenidas[$cc]['fechat'] = $year . '-' . $month . '-' . $diat;
        $tenidas[$cc]['fechadia'] = $dianom[$dian] . '-' . $diat . '-' . $month . '-' . $year;
        $tenidas[$cc]['acta1'] = $dten->numeroActa1;
        $tenidas[$cc]['acta2'] = $dten->numeroActa2;
        $tenidas[$cc]['acta3'] = $dten->numeroActa3;
        $tenidas[$cc]['gradot'] = $dten->grado;
        //--------------
        $cc++;
        $nextMonth = mktime(0, 0, 0, $month + 1, 1, $year);
        # Just add 7 days per iteration to get the date of the subsequent week
        for ($week = 1, $time = mktime(0, 0, 0, $month, 1 + $addDays + $week * 7, $year); $time < $nextMonth; ++$week, $time = mktime(0, 0, 0, $month, 1 + $addDays + $week * 7, $year)) {
            $fechares = date('Y-m-d', $time);
            $fecharesdia = date('d-m-Y', $time);
            $dten = self::getDataAsisSimple($taller, $fechares);
            if (($fechares > $fechainicio && $fechares <= $fechacierre) || $year < $gestion) {
                if ($fechares > $factual) {
                    $tenidas[$cc]['valido'] = 2;
                }
                //futuro
                else {
                    $tenidas[$cc]['valido'] = 1;
                }
                //normal
            } else {
                $tenidas[$cc]['valido'] = 0;
            }
            //reces/

            if (self::$diavalido == 0 && $year < $gestion) {
                $tenidas[$cc]['valido'] = 0;
            }

            $tenidas[$cc]['dia'] = date('d', $time);
            $tenidas[$cc]['fechat'] = $fechares;
            $tenidas[$cc]['fechadia'] = $dianom[$dian] . '-' . $fecharesdia;

            $tenidas[$cc]['acta1'] = $dten->numeroActa1;
            $tenidas[$cc]['acta2'] = $dten->numeroActa2;
            $tenidas[$cc]['acta3'] = $dten->numeroActa3;
            $tenidas[$cc]['gradot'] = $dten->grado;
            $cc++;
        }
        $extras = self::getDataAsisExtra($taller, $year, $month);
        if (!is_null($extras)) {
            foreach ($extras as $diase) {
                $tempDate = explode('-', $diase->fechaTenida);
                $tenidas[$cc]['dia'] = $tempDate[2];
                $tenidas[$cc]['fechadia'] = $diase->fechadia;
                $tenidas[$cc]['fechat'] = $diase->fechaTenida;
                $tenidas[$cc]['acta1'] = $diase->numeroActa1;
                $tenidas[$cc]['acta2'] = $diase->numeroActa2;
                $tenidas[$cc]['acta3'] = $diase->numeroActa3;
                $tenidas[$cc]['gradot'] = $diase->grado;
                $tenidas[$cc]['valido'] = 1;
                $cc++;
            }
        }
        return $tenidas;
    }

    public static function getDataAsis($logia, $ftenida)
    {
        $ex = explode('-', $ftenida);
        $year = $ex[0];
        $query = DB::select("SELECT * FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida='$ftenida'");
        if (count($query) > 0) {
            return $query[0];
        } else {
            $numeros = self::getUltimaActa($logia, $year);
            $objeto = new \stdClass();
            $objeto->idLogia = $logia;
            $objeto->grado = 1;
            $objeto->fechaTenida = $ftenida;
            $objeto->numeroActa1 = $numeros->acta1 + 1;
            $objeto->numeroActa2 = $numeros->acta2 + 1;
            $objeto->numeroActa3 = $numeros->acta3 + 1;
            return $objeto;
        }
    }
    private static function getUltimaActa($logia, $gestion)
    {
        $qry = "SELECT MAX(numeroActa1) AS acta1,MAX(numeroActa2) AS acta2, MAX(numeroActa3) AS acta3 FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida>'$gestion-01-01'";
        $query = DB::select($qry);
        $dato = $query[0];
        return $dato;
    }
    public static function getDataAsisSimple($logia, $ftenida)
    {
        $query = DB::select("SELECT grado,numeroActa1,numeroActa2,numeroActa3,visitadores,visitadores2,visitadores3,visitadores4 FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida='$ftenida'");
        if (count($query) > 0) {
            self::$diavalido = 1;
            return $query[0];
        } else {
            self::$diavalido = 0;
            $objeto = new \stdClass();
            $objeto->grado = '0';
            $objeto->numeroActa1 = '0';
            $objeto->numeroActa2 = '0';
            $objeto->numeroActa3 = '0';
            return $objeto;
        }
    }
    private static function getDataAsisExtra($logia, $gestion, $mes)
    {
        $query = DB::select("SELECT fechaTenida,DATE_FORMAT(fechaTenida,'%a-%d-%m-%Y') AS fechadia,grado,numeroActa1,numeroActa2,numeroActa3 FROM sgm_asistenciadata WHERE extra=1 AND idLogia=$logia AND fechaTenida>='$gestion-$mes-01' && fechaTenida<='$gestion-$mes-31'");
        if (count($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }
    public static function getFechaCierre($logia, $ftenida)
    {
        $query = DB::select("SELECT fechaCierre FROM sgm_asistenciadata WHERE idLogia=$logia AND fechaTenida='$ftenida'");
        if (count($query) > 0) {
            return $query[0]->fechaCierre;
        } else {
            return null;
        }
    }
    public static function getListaAsistencia($taller, $gestion, $mes, $diat)
    {
        if ($diat > 0 && $gestion > 2000 && $mes > 0 && $taller > 0) {
            $hoy = date("Y-m-d");
            $fechaten = "$gestion-$mes-$diat";
            $date2 = strtotime($hoy);
            $fechacierre = self::getFechaCierre($taller, $fechaten); //fecha de cierre de datos
            $date4 = strtotime($fechacierre);
            if ($date4 >= $date2) {
                $fvalida = 2;
            } else {
                $fvalida = 0;
            }
            //fecha pasada
            //nueva verison calcula en base a la fecha valida
            $mestenida = $gestion . '-' . $mes . '-01';
            $newdate = date("Y-m", strtotime("$mestenida -" . self::$regular . " months")) . '-01'; //fecha de corte de obolo
            $anterior = self::$regular - 1;
            $newdate2 = date("Y-m", strtotime("$mestenida -$anterior months")) . '-01';
            //**************************************************************************************************************************

            $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Miembro,DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,O.idoficial,  C.id as idOficialPT, B.idAsistencia,
CASE WHEN B.idAsistencia IS NULL THEN 'No asistio' ELSE 'Asistio' END AS EstadoAsis, '$newdate' AS fecha,
CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk,$fvalida AS FechaValida,
GROUP_CONCAT(COALESCE((case WHEN(C.id=O.idoficial) THEN C.oficial ELSE CONCAT('PT ',C.oficial) END),'') SEPARATOR '/') AS oficial,
CASE WHEN B.grado>0 then B.grado ELSE A.Grado END AS Grado,
(CASE WHEN B.grado>0 THEN ( CASE B.grado WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END)
ELSE (CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END) END) AS GradoActual
FROM sgm_miembros A
LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.fechaTenida='$fechaten' AND B.idLogia='$taller')
LEFT JOIN sgm_oficialidades O ON (O.idmiembro=A.id AND O.gestion=$gestion AND O.idlogia=$taller)
LEFT JOIN sgm_oficiales C ON (B.idOficialPT = C.id OR (A.id=O.idmiembro AND C.id=O.idoficial))
WHERE A.Estado<>2 AND B.idLogia=$taller
GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,A.ultimoPago,O.idoficial,C.id , B.idAsistencia
UNION ALL
SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Miembro,DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,O.idoficial,  C.id as idOficialPT, B.idAsistencia,
CASE WHEN B.idAsistencia IS NULL THEN 'No asistio' ELSE 'Asistio' END AS EstadoAsis, '$newdate' AS fecha,
CASE WHEN (A.ultimoPago>='$newdate2' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) THEN 1 ELSE 0 END AS pagoOk,$fvalida AS FechaValida,
GROUP_CONCAT(COALESCE((case WHEN(C.id=O.idoficial) THEN C.oficial ELSE CONCAT('PT ',C.oficial) END),'') SEPARATOR '/') AS oficial,
CASE WHEN B.grado>0 then B.grado ELSE A.Grado END AS Grado,
(CASE WHEN B.grado>0 THEN ( CASE B.grado WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END)
ELSE (CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END) END) AS GradoActual
FROM sgm_miembros A
LEFT JOIN sgm_asistencia B ON (A.id = B.idMiembro AND B.gestion=$gestion AND B.fechaTenida='$fechaten' AND B.idLogia='$taller')
LEFT JOIN sgm_oficialidades O ON (O.idmiembro=A.id AND O.gestion=$gestion AND O.idlogia=$taller)
LEFT JOIN sgm_oficiales C ON (B.idOficialPT = C.id OR (A.id=O.idmiembro AND C.id=O.idoficial))
WHERE A.Estado=1 AND (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND (A.LogiaActual = '$taller' OR A.LogiaAfiliada = '$taller' OR A.LogiaInspector = '$taller')  AND B.idAsistencia IS NULL
AND (A.FechaIniciacion <'$fechaten' OR A.FechaIniciacion IS null)
GROUP BY A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,A.ultimoPago,O.idoficial,C.id , B.idAsistencia
ORDER BY NombreCompleto ASC";
            //echo $qry;
            $results = DB::select($qry);
            return $results;
        }
        return null;
    }
    public static function getVisitas($fecha, $taller)
    {
        $qry = "SELECT A.idExtra,B.NombreCompleto, B.LogiaActual,C.gradoCorto AS GradoActual
        FROM sgm_asistenciaextra A JOIN sgm_miembros B ON A.idMiembro=B.id join sgm_grados C on A.Grado=C.Grado
        WHERE A.idLogia=$taller AND A.fechaExtra='$fecha' AND A.motivo=1 Order by B.NombreCompleto";
        $query = DB::select($qry);
        if (count($query) > 0) {
            return $query;
        } else {
            return '';
        }
    }
    public static function getOficiales($logia)
    {
        if ($logia > 0) {
            $rito = DB::table('sgm_logias')->select('rito')->where('numero', $logia)->first('rito')->rito;
            $qry = "SELECT id,oficial FROM sgm_oficiales  WHERE rito=$rito ORDER BY orden";
            //echo $qry.'<hr>';
            $query = DB::select($qry);
            return $query;
        } else {
            return null;
        }
    }
    public static function getMiembros($regular, $taller, $filtro = '')
    {
        $fechaten = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$fechaten -$regular months")) . '-01';
        $cond = "A.Estado=1  AND  (A.ultimoPago>='$newdate' OR (A.miembro='Ad-Vitam' AND A.Estado=1)) AND A.LogiaActual<>$taller AND (A.LogiaAfiliada IS NULL OR A.LogiaAfiliada<>$taller) ";
        if (strlen($filtro) > 3) {
            $filtera = json_decode($filtro);
            foreach ($filtera as $fill) {
                $cond .= " AND A." . $fill->field . " like '%" . $fill->value . "%'";
            }
        }
        $qry = "SELECT A.id,A.Nombres, A.Paterno, A.Materno, A.Miembro, C.gradoCorto as GradoActual,DATE_FORMAT(A.ultimoPago,'%b-%y') AS ultimoPago
    FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero join sgm_grados C on A.Grado=C.Grado WHERE $cond ORDER BY A.Paterno,A.Materno LIMIT 0,20";
        $query = DB::select($qry);
        return $query;
    }
    public static function getDiaTaller($param)
    {
        $query = DB::select('SELECT diatenida FROM sgm_logias WHERE numero=' . $param);
        return $query[0]->diatenida;
    }
    public static function checkAsis($taller, $gestion, $id, $fecha)
    {
        $qry = self::where('gestion', $gestion)->where('idMiembro', $id)->where('fechaTenida', $fecha)->where('idLogia', $taller)->first('idAsistencia');
        if (is_null($qry)) {
            return 0;
        } else {
            return $qry->idAsistencia;
        }
    }
    public static function checkCargo($taller, $gestion, $id, $fecha)
    {
        $qry = self::where('gestion', $gestion)->where('idOficialPT', $id)->where('fechaTenida', $fecha)->where('idLogia', $taller)->first('idAsistencia');
        if (is_null($qry)) {
            return 0;
        } else {
            return $qry->idAsistencia;
        }
    }
    public static function getGrado($id)
    {
        $query = DB::select("SELECT Grado FROM sgm_miembros WHERE id=$id");
        return $query[0]->Grado;
    }
    public static function getDatosLogia($nlog)
    {
        $qry = "SELECT A.logia,A.valle,A.diatenida,A.rito,A.nombreCompleto ,B.valle,B.logo FROM sgm_logias A INNER JOIN sgm_valles B ON A.valle=B.idValle WHERE numero='$nlog'";
        $query = DB::select($qry);
        return $query[0];
    }
    public static function getAsisOficiales($gestion, $logia, $ftenida, $rito = 0)
    {
        $qry = "SELECT B.NombreCompleto,B.Grado,D.orden, A.idOficialPT,case when C.idoficial=D.id then D.oficial  ELSE CONCAT(D.oficial, ' P.T.') END AS oficial
FROM sgm_asistencia A JOIN sgm_miembros B ON A.idMiembro=B.id
LEFT JOIN sgm_oficialidades C ON A.idMiembro=C.idmiembro AND C.gestion=$gestion AND C.idlogia=$logia
LEFT JOIN sgm_oficiales D ON C.idoficial=D.id OR A.idOficialPT=D.id
WHERE A.fechaTenida='$ftenida' AND A.idLogia=$logia AND (A.idOficialPT>0 OR C.idoficial>0)
ORDER BY D.orden ASC";
        $query = DB::select($qry);
        return $query;
    }
    public static function getAsisMiembros($gestion, $logia, $ftenida, $grado = 1)
    {
        $qry = "SELECT B.LogiaActual,B.NombreCompleto, A.grado AS Grado,A.idOficialPT ,C.idoficial
        FROM sgm_asistencia A JOIN sgm_miembros B ON A.idMiembro=B.id LEFT JOIN sgm_oficialidades C ON A.idMiembro=C.idmiembro AND C.gestion=$gestion AND C.idlogia=$logia
        WHERE A.fechaTenida='$ftenida' AND A.idLogia='$logia' AND C.idoficial IS null AND B.grado >=$grado ORDER BY A.grado DESC, B.NombreCompleto ASC";
        $query = DB::select($qry);
        return $query;
    }
}

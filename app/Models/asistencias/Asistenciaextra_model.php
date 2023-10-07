<?php

namespace App\Models\asistencias;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistenciaextra_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idExtra';
    protected $table = 'sgm_asistenciaextra';
    public static function checkAsisExtra($gestion, $id, $fecha)
    {
        list($inicio, $fin) = self::get_fechas($fecha);
        // $cond="gestion=$gestion AND idMiembro=$id AND fechaExtra>'$inicio' AND fechaExtra<='$fin' AND motivo=1";
        $qry = self::where('gestion', $gestion)->where('idMiembro', $id)->where('fechaExtra', '>', "$inicio")->where('fechaExtra', '<=', "$fin")->where('motivo', 1)->first('idExtra');
        // $qry = self::where('gestion', $gestion)->whereraw($cond)->first('idExtra');
        if (is_null($qry)) {
            return true;
        } else {
            return false;
        }
    }
    private static function get_fechas($fecha)
    {
        $diaSemana = date('w', strtotime($fecha));
        $fechaInicioSemana = date("Y-m-d", strtotime("$fecha-" . $diaSemana . " days"));
        $fechaDeFinDeSemana = date("Y-m-d", strtotime("$fechaInicioSemana+6 days")); # Sumamos +X days, pero partiendo del tiempo de inicio
        return [$fechaInicioSemana, $fechaDeFinDeSemana];
    }
    public static function getListaVisitas($fecha, $taller, $grado = 1)
    {
        $qry = "SELECT B.NombreCompleto,B.Grado, CASE A.grado WHEN 0 THEN (CASE B.Grado WHEN 1 THEN 'H:.A:.' WHEN 2 THEN 'H:.C:.' WHEN 3 THEN 'H:.M:.' WHEN 4 THEN 'R:.H:.' END) WHEN 1 THEN 'H:.A:.' WHEN 2 THEN 'H:.C:.' WHEN 3 THEN 'H:.M:.' WHEN 4 THEN 'R:.H:.' END AS GradoActual FROM sgm_asistenciaextra A JOIN sgm_miembros B ON A.idMiembro=B.id
        WHERE A.idLogia=$taller AND A.fechaExtra='$fecha' AND B.Grado>=$grado AND motivo=1 ORDER BY B.Grado,B.NombreCompleto";
        $query = DB::select($qry);
        if (count($query) > 0) {
            $tt = $query;
            $cc = 0;
            foreach ($tt as $ver) {
                $arr[$cc] = $ver->GradoActual . ' ' . ucwords(strtolower($ver->NombreCompleto));
                $cc++;
            }
            $ss = implode(',', $arr);
            return $ss;
        } else {
            return '';
        }
    }
    public static function getListaMiembros($pagina, $cantidad, $valle, $taller, $grado, $estado, $palabra = '')
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Estado = '$estado'";
        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle=$valle";
        }

        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago, A.Grado, A.Miembro, A.Estado, B.logia ,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual "
            . "FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        $query = DB::select($qry);
        return $query;
    }
    public static function getTotalListaMiembros($valle, $taller, $grado, $estado, $palabra = '')
    {
        $cond = "A.Estado = '$estado'";
        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle=$valle";
        }

        $qry = "SELECT count(A.id) as numero FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ";
        $query = DB::select($qry);
        return $query[0]->numero;
    }
    public static function getVisitas($id, $gestion)
    {
        $qry = "SELECT A.idExtra,A.idLogia,A.fechaExtra,B.nombre ,CASE A.idLogia WHEN 0 THEN 'Asistencia Extra' ELSE concat('No. ',A.idLogia) END AS Taller
        FROM sgm_asistenciaextra A INNER JOIN sgm_parametros B ON A.motivo=B.valor AND B.tipo=1 WHERE A.idMiembro=$id AND A.gestion=$gestion  Order by A.idExtra";
        $query = DB::select($qry);
        return $query;
    }
}

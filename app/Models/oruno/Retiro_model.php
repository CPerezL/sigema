<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retiro_model extends Model
{
    use HasFactory;
    private static $tipo = 5;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_tramites_retiro';
    public static $prehash = 'GLB';

    public static function getListaTramites($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $estado = '')
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND F.valle=$valle ";
        }
        if (strlen($estado) > 0) {
            $cond .= " AND A.estado IN ($estado) ";
        }
        $qry = "SELECT A.id,A.estado,A.tipo,A.actaAprobacionLogia, DATE_FORMAT(A.fechaAprobacionLogia,'%d/%m/%Y') AS fechaAprobacionLogia,DATE_FORMAT(A.fechaAprobacion,'%d/%m/%Y') AS fechaAprobacion,
        A.fechaModificacion,B.NombreCompleto, A.fechaCreacion,A.archivo,A.archivo2,DATE_FORMAT(A.fechasolicitud,'%d/%m/%Y') AS fechaSolicitud,
        CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,A.activo,
        CONCAT(C.Logia,' N. ',A.idLogia) AS nLogia,D.valle,E.nombre AS estadotxt,B.Estado as estadomie,F.texto as miembro
            FROM sgm2_tramites_retiro A JOIN sgm_miembros B ON A.idMiembro=B.id
            JOIN sgm_logias C ON A.idLogia=C.numero JOIN sgm_valles D ON C.valle=D.idValle JOIN sgm_parametros E ON A.estado=E.valor AND E.tipo=11 LEFT JOIN sgm2_miembrosestado F ON B.Estado=F.estado
        WHERE $cond ORDER BY A.fechaModificacion DESC Limit " . $inicio . "," . $cantidad . ' ';
        // echo $qry;
        $res = DB::select($qry);
        return $res;
    }
    public static function getNumeroTramites($palabra = '', $taller = 0, $valle = 0, $estado = '')
    {
        $cond = "A.id> 0 ";
        if ($taller > 0) {
            $cond .= "AND A.idLogia=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND Z.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }
        if (strlen($estado) > 0) {
            $cond .= " AND A.estado IN ($estado) ";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm2_tramites_retiro A JOIN sgm_logias B ON (A.idLogia =B.numero) WHERE $cond ";
        //   echo ($qry);
        $results = DB::select($qry);
        //dd($results);
        $resu = $results[0];
        return $resu->numero;
    }

    public static function getMiembros($idl, $palabra = '')
    {
        $tiempouno = 6; //2años

        if ($idl > 0) {
            $hoy = date("Y-m-d");
            $limite = date("Y-m", strtotime("$hoy -$tiempouno months")) . '-01'; //fecha de corte de obolo
            if (strlen($palabra) > 2) {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual, A.Miembro,A.LogiaAfiliada,
CASE WHEN A.LogiaAfiliada>0 THEN CONCAT('Afil. a N. ', A.LogiaAfiliada) ELSE 'No afiliado' END AS obstxt,CONCAT('Log:. N. ', A.LogiaActual) AS talltxt
FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero
WHERE B.numero=$idl AND A.ultimoPago>='$limite' AND A.Estado=1 AND A.NombreCompleto like '%$palabra%' ORDER BY A.NombreCompleto LIMIT 20";
            } else {
                $qry = "SELECT A.id, A.NombreCompleto,A.Grado,CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
DATE_FORMAT(A.ultimoPago,'%Y-%b') AS ultimoPago,ultimoPago AS ultimoPagoDate,A.LogiaActual, A.Miembro,A.LogiaAfiliada,
CASE WHEN A.LogiaAfiliada>0 THEN CONCAT('Afil. a N. ', A.LogiaAfiliada) ELSE 'No afiliado' END AS obstxt,CONCAT('Log:. N. ', A.LogiaActual) AS talltxt
FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero
WHERE B.numero=$idl AND A.ultimoPago>='$limite' AND A.Estado=1 ORDER BY A.NombreCompleto LIMIT 20";
            }
            //echo $qry.'\n';
            $res = DB::select($qry);
            return $res;
        }
        return '';
    }
    public static function checkTramite($idm)
    {
        $query = self::where('idMiembro', $idm)->where('estado', '<', 3)->first('id');
        if (is_null($query)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTramite($id)
    {
        $qry = "SELECT A.fechaAprobacion,CONCAT(D.GradoCorto,' ',B.Nombres,' ',B.Paterno, ' ',B.Materno) AS nombrem,CONCAT('R:.L:.S:. ',C.logia,' Nº ',C.numero) AS logianame
        FROM sgm2_tramites_retiro A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_logias C ON A.idLogia=C.idLogia JOIN sgm_grados D on A.gradoActual=D.Grado
                WHERE A.id=$id";
        $res = DB::select($qry);
        return $res[0];
    }
    public static function getListaRetirados($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "B.Estado=5 ";
        if ($taller > 0) {
            $cond .= "AND B.LogiaActual=$taller ";
        }
        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }
        if ($valle > 0) {
            $cond .= "AND F.valle=$valle ";
        }

        $qry = "SELECT A.id,A.estado, DATE_FORMAT(A.fechaAprobacion,'%d/%m/%Y') AS fechaAprobacion,A.fechaModificacion,B.NombreCompleto, A.fechaCreacion,
        CASE A.GradoActual WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual,
            CONCAT(C.Logia,' N. ',A.idLogia) AS nLogia,D.valle,E.nombre AS estadotxt,B.Estado AS estadomie,F.texto as miembro,DATE_FORMAT(A.fechaPlazo,'%d/%m/%Y') AS fechaPlazo
FROM sgm_miembros B LEFT JOIN sgm_logias C ON B.LogiaActual=C.numero LEFT JOIN sgm_valles D ON C.valle=D.idValle LEFT JOIN sgm2_miembrosestado F ON B.Estado=F.estado
LEFT JOIN  sgm2_tramites_retiro A ON A.idMiembro=B.id LEFT JOIN sgm_parametros E ON A.estado=E.valor AND E.tipo=11
        WHERE $cond ORDER BY A.fechaAprobacion DESC Limit $inicio,$cantidad";
        //  echo $qry;
        $res = DB::select($qry);
        return $res;
    }
    public static function getNumeroRetirados($palabra = '', $taller = 0, $valle = 0)
    {
        $cond = "B.Estado=5 ";
        if ($taller > 0) {
            $cond .= "AND B.LogiaActual=$taller ";
        }

        if (strlen($palabra) > 2) {
            $cond .= "AND B.NombreCompleto Like '%$palabra%' ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        $qry = "SELECT count(B.id) AS numero FROM sgm_miembros B LEFT  JOIN sgm_logias C ON B.LogiaActual=C.numero WHERE $cond ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
}

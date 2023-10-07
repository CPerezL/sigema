<?php

namespace App\Models\oruno;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logias_membrecia_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_logias_membrecia';
    use HasFactory;

    public static function getItems($pagina, $cantidad, $palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {
        // DB::select("SET lc_time_names = 'es_ES'");
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }

        if ($valle > 0) {
            $cond .= " AND C.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND B.idLogia = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado != 4) {
            $cond .= " AND A.Estado = '$estado'";
        }
        $qry = "SELECT A.id,G.GradoActual, A.NombreCompleto,A.Miembro, A.Estado,E.texto,
            concat(C.logia,' Nro ' ,C.numero) AS nlogia,case B.tipo when 0 then 'Actual' when 1 then 'Afiliado' ELSE 'Sin asignar' END AS estadolog,
            D.valle,B.id  AS idMem
            FROM sgm_miembros A
            JOIN sgm_grados G ON A.Grado=G.Grado
            JOIN sgm2_miembrosestado E ON A.Estado=E.estado
            LEFT JOIN sgm2_logias_membrecia B ON A.id=B.idMiembro
            LEFT JOIN sgm_logias C ON B.idLogia=C.idLogia
            left join sgm_valles D on C.valle=D.idValle
            WHERE $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $oriente = 0, $valle = 0, $taller = 0, $grado = 0, $estado = 0)
    {
        $cond = "A.id > 0";
        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }
        if ($oriente > 0) {
            $cond .= " AND A.idOriente = '$oriente'";
        }
        if ($valle > 0) {
            $cond .= " AND C.valle = '$valle'";
        }

        if ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($estado != 4) {
            $cond .= " AND A.Estado = '$estado'";
        }

        $qry = "SELECT count(A.id) AS numero FROM sgm_miembros A LEFT JOIN sgm2_logias_membrecia B ON A.id=B.idMiembro LEFT JOIN sgm_logias C ON B.idLogia=C.idLogia WHERE $cond ";
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
    public static function getLogiasMiembro($id)
    {
        $qry = "SELECT A.id,concat(B.logia,' Nro ' ,B.numero) AS nlogia,case A.tipo when 0 then 'Logia Principal' when 1 then 'Afiliado' ELSE '' END AS estadolog,C.valle
        FROM sgm2_logias_membrecia A JOIN sgm_logias B ON A.idLogia=B.idLogia JOIN sgm_valles C ON C.idValle=B.valle WHERE A.idMiembro=$id order by B.tipo ASC,B.numero ASC";
        // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
}

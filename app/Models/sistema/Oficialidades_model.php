<?php

namespace App\Models\sistema;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficialidades_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_oficialidades';
    public static function getOficiales($taller, $gestion)
    {
        if ($gestion > 1949 && $taller > 0) {
            $rito = self::getRito($taller);
            $qry = "SELECT A.id,A.oficial,B.idmiembro,B.gestion,C.Paterno,C.Materno,C.Nombres,Concat('Nº ',C.LogiaActual) as logia,CASE C.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS Grado
            FROM sgm_oficiales A LEFT JOIN sgm_oficialidades B ON (A.id = B.idoficial AND B.idlogia=$taller AND B.gestion=$gestion) LEFT JOIN sgm_miembros C ON C.id=B.idmiembro WHERE A.rito=$rito ORDER BY A.orden";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }
    }
    public static function getRito($idl)
    {
        $query = DB::select('SELECT rito FROM sgm_logias WHERE numero=' . $idl);
        return $query[0]->rito;
    }
    public static function getMiembros($pagina, $cantidad, $taller, $palabra = '', $mostrar = 1)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = " A.Grado >= 3 ";
        if (strlen($palabra) > 1) {
            $cond .= " AND A.Paterno like '%$palabra%'";
        }
        if ($mostrar == 1) {
            $cond .= " AND (A.LogiaActual=$taller OR A.LogiaAfiliada=$taller) ";
        }
        $qry = "SELECT A.id,A.Nombres, A.Paterno, A.Materno, A.Miembro,A.LogiaActual ,
        CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
    FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.Miembro, A.Paterno,A.Materno Limit " . $inicio . "," . $cantidad . ' ';
        // echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getTotalMiembros($taller, $palabra = '')
    {
        $cond = "Estado = 1 AND Grado >= 3 AND (LogiaActual = '$taller' OR LogiaAfiliada = '$taller' OR LogiaInspector = '$taller')";
        if (strlen($palabra) > 1) {
            $cond .= " AND Paterno like '%$palabra%'";
        }
        $qry = "SELECT count(id) as numero FROM sgm_miembros WHERE $cond";
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function checkCargo($gestion, $taller, $cargo)
    {
        $chk = self::where('idoficial', $cargo)->where('idlogia', $taller)->where('gestion', $gestion)->first('id');
        if (is_null($chk)) {
            return 0;
        } else {
            return
                $chk->id;
        }
    }
}

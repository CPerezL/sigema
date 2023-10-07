<?php

namespace App\Models\glb;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembros_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_glbof_miembros';
    public static function getItems($comision, $gestion)
    {
        if ($gestion > 0) {
            $qry = "SELECT A.idOficial,A.oficial,B.idmiembro,C.Paterno,C.Materno,C.Nombres,C.LogiaActual as logia,C.Grado,D.descripcion,'$gestion' AS idGestion,B.id,
            (CASE C.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END) AS GradoActual
    FROM sgm_glbof_cargos A LEFT JOIN sgm_glbof_miembros B ON A.idOficial=B.idOficial AND B.idGestion=$gestion LEFT JOIN sgm_miembros C ON C.id=B.idMiembro left JOIN sgm_glbof_gestiones D ON B.idGestion=D.idGestion
    WHERE A.idOficial>0 AND A.tipo=$comision ORDER BY A.orden";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }

    }
    public static function getMiembros($filtro = '')
    {
        $cantidad = 30;
        $pagina = 1;
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.Estado = 1 AND A.Grado >= 3 ";
        if (strlen($filtro) > 3) {
            $filtera = json_decode($filtro);
            foreach ($filtera as $fill) {
                $cond .= " AND A." . $fill->field . " like '%" . $fill->value . "%'";
            }
        }
        $qry = "SELECT A.id,A.Nombres, A.Paterno, A.Materno, A.Miembro, CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
    FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.Miembro, A.Paterno,A.Materno Limit " . $inicio . "," . $cantidad . ' ';
        $results = DB::select($qry);
        return $results;

    }
    public static function updateCargo($gestion, $id, $cargo)
    {
        $idec = self::select('id')->where('idOficial', $cargo)->where('idGestion', $gestion)->where('tipo', 1)->first('id');
        if (is_null($idec)) {
            $data = array('idOficial' => $cargo, 'idMiembro' => $id, 'idGestion' => $gestion, 'tipo' => 1);
            $res = self::insert($data);
        } else {
            $data = array('idMiembro' => $id);
            $res = self::where('id', $idec->id)->update($data);
        }
        return $res;
    }
}

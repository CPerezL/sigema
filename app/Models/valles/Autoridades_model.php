<?php

namespace App\Models\valles;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autoridades_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idAutoridad';
    protected $table = 'sgm_gld_gdr_autoridades';

    public static function getOficiales($tipo, $ges)
    {
        if ($ges > 0) {
            $qry = "SELECT A.idCargo,A.cargo,B.idMiembro,C.Paterno,C.Materno,C.Nombres,C.LogiaActual as logia,C.Grado,D.descripcion,B.idGestion,B.idAutoridad AS id,
            (CASE C.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END) AS GradoActual
    FROM sgm_gld_gdr_cargos A LEFT JOIN sgm_gld_gdr_autoridades B ON A.idCargo=B.idCargo AND B.idGestion=$ges
    LEFT JOIN sgm_miembros C ON C.id=B.idMiembro LEFT JOIN sgm_gld_gdr_gestiones D ON B.idGestion=D.idGestion WHERE A.idCargo>0 AND A.tipo=$tipo ORDER BY A.orden";
    // echo ($qry);
            $results = DB::select($qry);
            return $results;
        }
        return null;
    }

    public static function getGestiones($id = 0)
    {
        if ($id > 0) {
            $qry = "SELECT idGestion as id,descripcion as text FROM sgm_gld_gdr_gestiones WHERE valle=$id Order by desde,hasta";
            $results = DB::select($qry);
            if (count($results) > 0) {
                $inicio = new \stdClass();
                $inicio->id = 0;
                $inicio->text = 'SELECCIONAR GESTION';
                array_unshift($results, $inicio);
                return $results;
            } else {
                $inicio = new \stdClass();
                $inicio->id = 0;
                $inicio->text = 'NINGUNA GESTION';
                $res[0] = $inicio;
                return $res;
            }
        } else {
            return null;
        }
    }
    public static function getValles($valle = 0)
    {
        if ($valle > 0) {
            $qry = "SELECT concat(idValle,'_',tipo) as id,valle as text FROM sgm_valles WHERE idValle=$valle order by valle";
        } else {
            $qry = "SELECT concat(idValle,'_',tipo) as id,valle as text FROM sgm_valles order by valle";
        }
        $res = DB::select($qry);

        if (count($res) > 1) {
            $inicio = new \stdClass();
            $inicio->id = 0;
            $inicio->text = 'SELECCIONAR VALLE';
            array_unshift($res, $inicio);
            return $res;
        } else {
            return $res;
        }
    }
    public static function getVallesArray($valle = 0)
    {
        $lista = self::getValles($valle);
        $res = array();
        foreach ($lista as $ver) {
            $res[$ver->id] = $ver->text;
        }
        return $res;

    }
    public static function getMiembros($valle, $ges, $filtro = '')
    {
        $cantidad = 30;
        $pagina = 1;
        $inicio = $cantidad * ($pagina - 1);
        $cond = "B.valle=$valle AND A.Estado = 1 AND A.Grado >= 3 ";
        if (strlen($filtro) > 3) {
            $filtera = json_decode($filtro);
            foreach ($filtera as $fill) {
                $cond .= " AND A." . $fill->field . " like '%" . $fill->value . "%'";
            }
        }
        $qry = "SELECT A.id,A.Nombres, A.Paterno, A.Materno, A.Miembro, CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
    FROM sgm_miembros A INNER JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE $cond ORDER BY A.Miembro, A.Paterno,A.Materno Limit " . $inicio . "," . $cantidad . ' ';

        // echo $qry;
        $res = DB::select($qry);
        return $res;
    }
    public static function updateCargo($gestion, $id, $cargo, $valle)
    {
        $idex = self::checkCargo($gestion, $cargo, $valle);
        if ($idex > 0) {
            $data = array('idMiembro' => $id, 'fechaModificacion' => $date = date('Y-m-d H:i:s'));
            $res = self::where("idAutoridad", $idex)->update($data);
        } else {
            $data = array('idCargo' => $cargo, 'idValle' => $valle, 'idMiembro' => $id, 'idGestion' => $gestion, 'gestion' => '');
            $res = self::insert($data);
        }
        return $res;
    }
    private static function checkCargo($gestion, $cargo, $valle)
    {
        //$query = self::where('idCargo', $cargo)->where('idGestion', $gestion)->where('idValle', $valle)->first(['idAutoridad'])->idAutoridad;
        $query = self::select('idAutoridad')->where('idCargo', $cargo)->where('idGestion', $gestion)->where('idValle', $valle)->first();
        if (isset($query->idAutoridad)) {
            return $query->idAutoridad;
        } else {
            return 0;
        }

    }

}

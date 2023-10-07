<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Valles_datos_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idValle';
    protected $table = 'sgm_gld_gdr_datos';
    public static function getItems($pagina, $cantidad, $palabra = '', $estado = 0, $sel = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = "A.borrado = '$estado'";
        if ($sel > 0)
            $cond .= ' AND A.idOriente=' . $sel;
        if (strlen($palabra) > 2)
            $cond .= "AND A.valle Like '%$palabra%'";
        $qry = "SELECT A.*,B.oriente ,(case when A.tipo=1 then 'Gran Delegacion Regional' when A.tipo=2 then 'Gran Logia Distrital' else 'sin configurar' end) AS tipotxt FROM sgm_valles A
        JOIN sgm_orientes B ON A.idOriente=B.idOriente WHERE $cond ORDER BY B.oriente ASC,A.valle ASC Limit " . $inicio . "," . $cantidad . ' ';
        // echo $qry;
        $results = \DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $estado = 0, $sel = 0)
    {
        $cond = "borrado = '$estado'";
        if ($sel > 0)
            $cond .= ' AND idOriente=' . $sel;
        if (strlen($palabra) > 2)
            $cond .= "AND valle Like '%$palabra%'";
        $count = self::select('idValle')->from('sgm_valles')->whereraw($cond)->count();
        return $count;

    }
    public static function getForm($id, $task)
    {
        if ($task == 1) {
            $qry = "SELECT B.idValle,A.direccion,A.departamento,A.localidad,A.telefonos,B.valle,B.logo,B.tipo,B.nombreCompleto,DATE_FORMAT(A.fundacion,'%d/%m/%Y') AS fundacion
            FROM sgm_valles B LEFT JOIN sgm_gld_gdr_datos A ON B.idValle=A.idValle WHERE B.idValle=$id";
            $results = DB::select($qry);
            return $results[0];
        } else
            return null;
    }
    public static function updateValleData($id, $data)
    {
        if (self::where('idValle', $id)->exists()) {
            $resu = self::where("idValle", $id)->update($data);
        } else {
            $data['idValle'] = $id;
            $resu = self::insert($data);
        }
        return $resu;
    }
}

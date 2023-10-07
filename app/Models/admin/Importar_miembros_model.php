<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Importar_miembros_model extends Model
{
    public $timestamps = FALSE;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_miembros_import';
    use HasFactory;
    public static function getItems($pagina, $cantidad, $user, $oriente = 0, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //ok
        $cond = "A.idUsuario=$user";
        if ($oriente > 0)
            $cond .= " AND A.idOriente=$oriente";
        if ($valle > 0)
            $cond .= " AND B.valle=$valle";

        $qry = "SELECT A.NombreCompleto,A.LogiaActual,B.logia,C.valle,D.oriente,E.GradoActual,A.FechaCreacion,case when A.duplicado=1 then 'Duplicado' ELSE 'Nuevo registro' END AS estado
        FROM sgm2_miembros_import A JOIN sgm_logias B ON A.LogiaActual=B.numero AND A.idOriente=A.idOriente JOIN
        sgm_valles C ON B.valle=C.idValle JOIN sgm_orientes D ON A.idOriente=D.idOriente JOIN sgm_grados E ON A.Grado=E.Grado WHERE $cond  ORDER BY A.id desc Limit $inicio," . $cantidad;
        //  dd($qry);
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($user, $oriente = 0, $valle = 0)
    {
        $cond = "A.idUsuario=$user";
        if ($oriente > 0)
            $cond .= " AND A.idOriente=$oriente";
        if ($valle > 0)
            $cond .= " AND B.valle=$valle";
        $count = self::from('sgm2_miembros_import as A')->select('A.id')->join('sgm_logias as B', 'A.LogiaActual', '=', 'B.numero')
            ->whereraw(
                $cond
            )->count(
            );
        return $count;
    }
    public static function checkNombre($nombre) //19

    {
        $qry = "SELECT id,LogiaActual FROM sgm_miembros WHERE NombreCompletoID LIKE '%$nombre%'";
        $query = DB::select($qry);
        if (count($query) > 0) {
            $valor = $query[0];
            $idm = $valor->id;
            if ($idm > 0) {
                return $idm;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}

<?php

namespace App\Models\sistema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Errores_model extends Model
{
    public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'sgm_errores_logs';
    use HasFactory;
    public static function getItems($pagina, $cantidad, $user, $valle, $palabra = '')
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        $cond = 'WHERE A.id>0 ';
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND B.username Like '%$palabra%'";
        }

        if ($user > 0) {
            $cond .= " AND A.idUsuario=$user";
        }

        $qry = "SELECT A.id,A.modulo,A.descripcion,A.fechaModificacion,A.fechaCreacion,B.username,B.name as nombre,A.estado,M.nombre AS modulotxt, A.respuesta,
        case when B.logia>0 then CONCAT(C.logia,' Nro ',C.numero) ELSE 'Valle' END AS logiatxt, case when B.valle>0 then D.valle ELSE 'No' END AS valletxt,
        case A.estado when 1 then 'se necesita mas datos' when 4 then 'Resuelto/Solucionado' when 2 then 'En revision' when 3 then 'Descartado' Else 'Creado' end AS estadotxt,
        E.username AS urevisa
        FROM sgm_errores_logs A JOIN sgm2_users B ON A.idUsuario=B.id JOIN sgm2_menu_items M ON A.modulo=M.id LEFT JOIN sgm_logias C ON B.logia=C.numero LEFT JOIN sgm_valles D ON B.valle=D.idValle
        LEFT JOIN sgm2_users E ON A.usuarioRevisa=E.id
            $cond ORDER BY A.fechaModificacion DESC  Limit $inicio,$cantidad";
            // echo ($qry);
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($user, $valle, $palabra = '')
    {
        $cond = 'WHERE A.id>0 ';
        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND B.username Like '%$palabra%'";
        }

        if ($user > 0) {
            $cond .= " AND A.idUsuario=$user";
        }

        $qry = "SELECT count(A.id) as numero FROM sgm_errores_logs A join sgm2_users B ON A.idUsuario=B.id $cond";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getListaModulos($rol = 0)
    {
        $mm = self::getModulos($rol);
        $menu1 = DB::table('sgm2_menu_items')->select('id', 'nombre', 'tipo', 'icono')->where('activo', '=', 1)->where('idMenu', '=', 0)->orderby('orden', 'ASC')->get();

        $mods = array();
        foreach ($menu1 as $mm1) {
            $aux = 0;
            $menu2 = DB::table('sgm2_menu_items')->select('sgm2_menu_items.id', 'sgm2_menu_items.nombre', 'sgm2_menu_items.tipo', 'sgm2_menu_items.icono', 'sgm2_modulos.link as enlace')
                ->join('sgm2_modulos', 'sgm2_menu_items.idModulo', '=', 'sgm2_modulos.id')
                ->wherein('sgm2_menu_items.idModulo', $mm)
                ->where('sgm2_menu_items.activo', '=', 1)
                ->where('sgm2_menu_items.idMenu', '=', $mm1->id)
                ->orderby('sgm2_menu_items.orden', 'ASC')
                ->get();
            foreach ($menu2 as $mm2) {
                $aux++;
                $mods[$mm2->id] = $mm2->nombre;
            }
        }
        return $mods;
    }
    private static function getModulos($rol)
    {
        $mods = DB::table('sgm2_modulos_roles')->select('idModulo')->where('idRol', '=', $rol)->orderby('idModulo', 'ASC')->pluck('idModulo');
        return $mods;
    }

}

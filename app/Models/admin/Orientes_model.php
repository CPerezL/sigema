<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientes_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idOriente';
    protected $table = 'sgm_orientes';
    static $cantidad = 20;
    static $pagina = 1;
    public static function getOrientesArray($ori = 0)
    {
        if ($ori > 0)
            $qry = self::select('idOriente', 'oriente')->where('idOriente', '=', $ori)->orderBy('oriente', 'asc')->pluck('oriente', 'idOriente');
        else
            $qry = self::select('idOriente', 'oriente')->where('idOriente', '>', 0)->orderBy('oriente', 'asc')->pluck('oriente', 'idOriente');
        return $qry;
    }
    public static function getNumItems($palabra = '', $sel = 0)
    {
        $cond = 'idOriente>0 ';
        if (strlen($palabra) > 0)
            $cond .= " AND name Like '%$palabra%'";
        if ($sel > 0)
            $cond .= " AND idOriente=$sel";
        $count = self::select('idOriente')->whereraw($cond)->count();
        return $count;
    }
    public static function getItems($pagina, $cantidad, $palabra = '', $sel = 0)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        if ($sel > 0)
            $menu1 = self::select('idOriente')->where('idOriente', '=', $sel)->orderBy('pais', 'asc')->get();
        else
            $menu1 = self::select('idOriente')->where('idOrientePadre', '=', 0)->orderBy('pais', 'asc')->get();

        $menu = array();
        foreach ($menu1 as $val) {
            $menu1 = self::get_Oriente($val->idOriente);
            $menu2 = self::get_subOriente($val->idOriente);
            $menu = array_merge($menu, $menu1, $menu2);
        }
        return $menu;
    }
    private static function get_Oriente($id)
    {
        $qry = "SELECT A.*,A.oriente as orientetxt,B.GradoActual as gradoMinimotxt,C.GradoActual as gradoMaximotxt FROM sgm_orientes A join sgm_grados B on A.gradoMinimo=B.Grado join sgm_grados C on A.gradoMaximo=C.Grado WHERE A.idOriente=$id";
        $results = \DB::select($qry);
        $data = (array) $results;
        return $data;
    }

    private static function get_subOriente($padre)
    {
        $qry = "SELECT A.*,CONCAT('|--> ',A.oriente) as orientetxt,B.GradoActual as gradoMinimotxt,C.GradoActual as gradoMaximotxt FROM sgm_orientes A join sgm_grados B on A.gradoMinimo=B.Grado join sgm_grados C on A.gradoMaximo=C.Grado  WHERE A.idOrientePadre=$padre ORDER BY A.oriente ";
        $results = \DB::select($qry);
        $data = (array) $results;
        return $data;
    }
    public static function getOrientesPadre()
    {
        $qry = self::select('idOriente', 'oriente')->where('idOrientePadre', '=', 0)->orderBy('oriente', 'asc')->pluck('oriente', 'idOriente');
        return $qry;
    }
}

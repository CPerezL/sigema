<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iniciacion_circulares_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sgm_tramites_ini_circulares';
    public static function getCirculares($pagina, $cantidad, $palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        $inicio = $cantidad * ($pagina - 1); //se debe mejorr la consuta
        if ($valle > 0) {
            $cond = " A.id > 0 AND A.valle= $valle ";
        } else {
            $cond = " A.id > 0 ";
        }
        $qry = "SELECT A.id,A.fecha,A.circular,A.numero,B.valle from sgm_tramites_ini_circulares A INNER JOIN sgm_valles B ON A.valle=B.idValle
        WHERE $cond ORDER BY A.id DESC Limit $inicio,$cantidad ";

        $results = DB::select($qry);
        return $results;
    }
    public static function getNumCirculares($palabra = '', $taller = 0, $valle = 0, $paso = 1)
    {
        if ($valle > 0) {
            $cond = " A.id > 0 AND A.valle= $valle ";
        } else {
            $cond = " A.id > 0 ";
        }
        $qry = "SELECT count(A.id) as numero FROM sgm_tramites_ini_circulares A WHERE $cond  ";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu->numero;
    }
    public static function getInfo($id)
    {
        $qry = "SELECT A.id,A.fecha,A.circular,A.contenido,A.numero,B.valle,B.tipo from sgm_tramites_ini_circulares A INNER JOIN sgm_valles B ON A.valle=B.idValle WHERE id=$id";
        $results = DB::select($qry);
        $resu = $results[0];
        return $resu;
    }
    public static function getLista($id)
    {
        $qry = "SELECT A.nombres,A.apPaterno,A.apMaterno,A.nacionalidad,A.fechaNac,A.estadoCivil,A.profesion,A.foto,B.logia,B.numero,A.lugarNac FROM sgm_tramites_iniciacion A JOIN sgm_logias B ON A.logia=B.numero WHERE A.numCircular=$id";
        $results = DB::select($qry);
        return $results;
    }
}

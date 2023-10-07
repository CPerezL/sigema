<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membrecia_clave_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idClave';
    protected $table = 'sgm_miembrosclave';
    public static function getItems()
    {

        $qry = "SELECT A.tipo,A.idClave,A.clave,CASE when A.tipo=1 THEN 'Palabra de pase' ELSE 'Clave General' END AS info, CASE WHEN A.valle>0 THEN B.valle ELSE 'Todos' END AS valletxt,A.valle FROM sgm_miembrosclave A LEFT JOIN sgm_valles B ON A.valle=B.idValle ";
        $results = \DB::select($qry);
        return $results;
    }
}

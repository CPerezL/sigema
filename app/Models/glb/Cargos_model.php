<?php

namespace App\Models\glb;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargos_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idOficial';
    protected $table = 'sgm_glbof_cargos';
    public static function getItems($comision)
    {
        if ($comision > 0) {
            $qry = "SELECT * FROM sgm_glbof_cargos WHERE tipo=$comision ORDER BY orden";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }
    }
}

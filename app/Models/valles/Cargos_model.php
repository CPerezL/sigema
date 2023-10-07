<?php

namespace App\Models\valles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargos_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idOficial';
    protected $table = 'sgm_gld_gdr_cargos';
    public static function getComisiones()
    {
        $arreglo[1] = 'GDR';
        $arreglo[2] = 'GLD';
        return $arreglo;
    }
    public static function getItems($comision = 0)
    {
        if ($comision > 0) {

            $qry = "SELECT * FROM sgm_gld_gdr_cargos WHERE tipo=$comision ORDER BY orden";
            $results = \DB::select($qry);
            return $results;
        } else {
            return null;
        }

    }
}

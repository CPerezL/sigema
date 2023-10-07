<?php

namespace App\Models\glb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Gestiones_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idGestion';
    protected $table = 'sgm_glbof_gestiones';
    public static function getItems($comision)
    {
        if ($comision > 0) {
            $qry = "SELECT *,concat(desde,'-',hasta) AS gestion FROM sgm_glbof_gestiones  where tipo=$comision ORDER BY desde,hasta";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }
    }
}

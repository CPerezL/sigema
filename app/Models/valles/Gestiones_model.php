<?php

namespace App\Models\valles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestiones_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'idOficial';
    protected $table = 'sgm_gld_gdr_gestiones';
    use HasFactory;
    public static function getItems($valle = 0)
    {
        if ($valle > 0) {

            $qry = "SELECT *,concat(desde,'-',hasta) AS gestion FROM sgm_gld_gdr_gestiones  where valle=$valle ORDER BY desde,hasta";
            $results = \DB::select($qry);
            return $results;
        } else {
            return null;
        }

    }
    public static function getGestionesArray($valle)
    {
        if ($valle > 0) {
            $qry = self::select('idGestion', 'descripcion')->where('valle', '=', $valle)->orderBy('desde', 'asc')->orderBy('hasta', 'asc')->pluck('descripcion', 'idGestion');
        } else {
            $qry = self::select('idGestion', 'descripcion')->orderBy('desde', 'asc')->orderBy('hasta', 'asc')->pluck('descripcion', 'idGestion');
        }

        return $qry;
    }
}

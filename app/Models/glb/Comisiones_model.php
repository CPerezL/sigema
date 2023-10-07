<?php

namespace App\Models\glb;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comisiones_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idComision';
    protected $table = 'sgm_glbof_comisiones';
    public static function getItems()
    {
        $qry = "SELECT * FROM sgm_glbof_comisiones ORDER BY orden";
        $results = DB::select($qry);
        return $results;
    }
    public static function getComisiones()
    {
        $qry = self::select('idComision', 'nombre')->orderBy('nombre', 'asc')->pluck('nombre', 'idComision');
        return $qry;
    }
}

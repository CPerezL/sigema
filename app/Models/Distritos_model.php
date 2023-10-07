<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distritos_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'idTipo';
    protected $table = 'sgm2_distritos';
    use HasFactory;
    public static function getValue($id, $campo, $nodata = '')
    {
        $qry = self::where('idTipo', $id)->value($campo);
        if (!is_null($qry)) {
            return $qry;
        } else {
            return $nodata;
        }
    }
    public static function getDato($id)
    {
        $qry = self::where('idTipo', $id)->first();
        return $qry;
    }
    public static function getDistritosArray()
    {
        $qry = self::select('idTipo', 'nombre')->orderBy('idTipo', 'asc')->pluck('nombre', 'idTipo');
        return $qry;
    }
}

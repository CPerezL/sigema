<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembrostipo_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idTipo';
    protected $table = 'sgm_miembrostipo';
    public static function getMiembrosArray()
    {
        $qry = self::select('idTipo', 'miembro')->where('idTipo', '>', 0)->orderBy('orden', 'asc')->pluck('miembro', 'idTipo');
        return $qry;
    }
}

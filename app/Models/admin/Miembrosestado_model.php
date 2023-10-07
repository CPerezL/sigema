<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembrosestado_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sgm2_miembrosestado';
    use HasFactory;
    public static function getEstadosArray()
    {
        $qry = self::select('estado', 'texto')->where('id', '>', 0)->orderBy('orden', 'asc')->pluck('texto', 'estado');
        return $qry;
    }
}

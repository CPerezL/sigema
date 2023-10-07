<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametros_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idParam';
    protected $table = 'sgm_parametros';
    public static function getParamsArray($tipo = 1)
    {
        $qry = self::select('valor', 'nombre')->where('tipo', '=', $tipo)->orderBy('valor', 'asc')->pluck('nombre','valor');
        return $qry;
    }
}

<?php

namespace App\Models\mecom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos_tipo_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idTipo';
    protected $table = 'sgm_mecom_pagos_tipo';
    public static function getTipoPagosArray()
    {
        $qry = self::select('idTipo', 'tipo')->orderBy('idTipo', 'asc')->pluck('tipo', 'idTipo');
        return $qry;
    }
}

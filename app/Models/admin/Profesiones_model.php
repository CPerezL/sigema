<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesiones_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_profesiones';
    public static function getProfesionesArray()
    {
        $qry = self::select('id', 'profesion')->orderBy('profesion', 'asc')->pluck('profesion', 'id');
        return $qry;
    }
}

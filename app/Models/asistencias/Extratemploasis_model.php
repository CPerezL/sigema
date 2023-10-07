<?php

namespace App\Models\asistencias;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extratemploasis_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idExtraTemploAsis';
    protected $table = 'sgm_extratemploasis';
    public static function checkAsisET($idm, $idet)
    {
        $ret = self::where("idMiembro", $idm)->where('idExtraTemplo', $idet)->first('idExtraTemploAsis');
        if (is_null($ret)) {
            return false;
        } else {
            return true;
        }

    }
}

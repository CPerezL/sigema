<?php

namespace App\Models\asistencias;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistenciadata_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idAsistenciaData';
    protected $table = 'sgm_asistenciadata';
    public static function checkFecha($logia, $fecha)
    {
        $diasem1 = date("w", strtotime($fecha));
        $diasem2 = self::getDiaTaller($logia);
        if ($diasem1 == $diasem2) {
            return false;
        } else {
            $query = self::where('fechaTenida', $fecha)->where('idLogia', $logia)->first('idAsistenciaData');
            if (is_null($query)) {
                return true;
            } else {
                return false;
            }
        }
    }
    private static function getDiaTaller($logia)
    {
        $query = DB::select('SELECT diatenida FROM sgm_logias WHERE numero=' . $logia);
        $dato = $query[0];
        return $dato->diatenida;
    }
    // public static function getDatosTenida($id)
    // {
    //     $query = self::where('idAsistenciaData', $id)->first('idLogia', 'fechaTenida');
    //     return $query;
    // }
}

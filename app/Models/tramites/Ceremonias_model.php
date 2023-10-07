<?php

namespace App\Models\tramites;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ceremonias_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'idTramite';
    protected $table = 'sgm_tramites_ceremonias';
    use HasFactory;
    public static function updateCeremonia($logia, $fc, $grado = 2)
    {
        $idcer = self::checkCeremonia($logia, $fc, $grado);
        if ($idcer > 0) {
            return $idcer;
        } else {
            $cdata = array(
                'idLogia' => $logia,
                'fechaCeremonia' => \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d'),
                'grado' => $grado,
                'fechaModificacion' => date('Y-m-d'),
            );
            $id = self::insertGetId($cdata);
            return $id;
        }
    }
    private static function checkCeremonia($logia, $fc, $grado)
    {
        $fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
        $qry = "SELECT A.idCeremonia FROM sgm_tramites_ceremonias A WHERE A.idLogia=$logia AND A.fechaCeremonia='$fccc' AND A.grado=$grado";
        $results = DB::select($qry);
        if (count($results) > 0) {
            $row = $results[0];
            return $row->idCeremonia;
        } else {
            return 0;
        }

    }
}

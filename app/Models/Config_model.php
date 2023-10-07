<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config_model extends Model
{

    public $timestamps = false;
    protected $primaryKey = 'idconfig';
    protected $table = 'sgm_config';
    use HasFactory;
    public static function getItems()
    {
        $qry = "SELECT idconfig,estado,title,DATE_FORMAT(fechaApertura, '%d/%m/%Y') AS fechaApertura ,DATE_FORMAT(fechaCierre, '%d/%m/%Y') AS fechaCierre,mesesDeuda,gestion,diasCircular,mesesIrregular,control
        ,mesesAumento,mesesExaltacion,asisAumento,asisExaltacion FROM sgm_config WHERE idconfig=1";
        $results = DB::select($qry);
        return $results;
    }
    public static function getMesesMora()
    {
        $val = self::where('idconfig', 1)->first(['mesesDeuda'])->mesesDeuda;
        return $val;
    }
    public static function getValue($name)
    {
        $val = self::where('idconfig', 1)->first([$name])->$name;
        return $val;
    }
    public static function regular()
    {
        return self::where('idconfig', 1)->first('mesesDeuda')->mesesDeuda;
    }
    public static function getTiposMiembro()
    {
        $qry = "SELECT idconfig,estado,title,DATE_FORMAT(fechaApertura, '%d/%m/%Y') AS fechaApertura ,DATE_FORMAT(fechaCierre, '%d/%m/%Y') AS fechaCierre,mesesDeuda,gestion,diasCircular,mesesIrregular,control
        FROM sgm_config WHERE idconfig=1";
        $results = DB::select($qry);
        return $results;
    }
}

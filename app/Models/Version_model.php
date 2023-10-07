<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm2_version';
    use HasFactory;
    public static function getVersion()
    {
        $qry = self::where('id', 1)->first();
        return $qry;
    }
    public static function getValue($campo, $nodata = '')
    {
        $qry = self::where('id', 1)->value($campo);
        if (!is_null($qry)) {
            return $qry;
        } else {
            return $nodata;
        }
    }
}

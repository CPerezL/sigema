<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembrosdata_model extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_miembrosdata';
    // protected $fillable = ['Valle','email'];
    use HasFactory;
    public static function updateMiembroData($id, $data)
    {
        if (self::where('id', $id)->exists()) {
            $resu = self::where("id", $id)->update($data);
        } else {
            $data['id'] = $id;
            $resu = self::insert($data);
        }
        return $resu;
    }
}

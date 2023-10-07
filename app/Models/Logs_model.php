<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_logs';
    protected $fillable = ['tipo'];
    public static function autoLog($tipo, $usuario, $accion, $miembro = 0, $logia = 0, $valle = 0, $params = '')
    {
        $data['tipo'] = $tipo;
        $data['idUsuario'] = $usuario;
        $data['accion'] = self::getTipo($tipo) . ' ' . $accion;
        $data['params'] = $params;
        $data['idMiembro'] = $miembro;
        if ($miembro > 0) {
            $dd = self::getUserData($miembro);
            if (!is_null($dd)) {
                $data['logia'] = $dd->LogiaActual;
                $data['valle'] = $dd->valle;
                $data['accion'] .= $dd->NombreCompleto;
            }
        } else {
            if ($valle > 0) {
                $data['valle'] = $valle;
            }

            if ($logia > 0) {
                $data['logia'] = $logia;
            }
        }
        $data['fecha'] = date('Y-m-d H:i:s', time());
        $resu = self::create($data);
        return $resu->idLog;
    }
    public static function insertLog($tipo, $usuario, $accion = '', $params = '', $miembro = 0, $logia = 0, $valle = 0)
    {
        $data['tipo'] = $tipo;
        $data['idUsuario'] = $usuario;
        $data['accion'] = $accion;
        $data['params'] = $params;
        $data['idMiembro'] = $miembro;
        if ($miembro > 0) {
            $dd = self::getUserData($miembro);
            if (!is_null($dd)) {
                $data['logia'] = $dd->LogiaActual;
                $data['valle'] = $dd->valle;
            }
        }
        if ($valle > 0) {
            $data['valle'] = $valle;
        }

        if ($logia > 0) {
            $data['logia'] = $logia;
        }
        $data['fecha'] = date('Y-m-d H:i:s', time());
        $ret = self::insertGetId($data);
        return $ret;
    }
    private static function getUserData($idm)
    {
        $qrycon = "SELECT A.LogiaActual,A.Estado,B.valle,A.NombreCompleto FROM sgm_miembros A JOIN sgm_logias B  ON A.LogiaActual=B.numero WHERE A.id=$idm";
        $results = DB::select($qrycon);
        if (count($results))
            return $results[0];
        else
            return null;
    }
    private static function getTipo($id)
    {
        $qrycon = "SELECT tipo FROM sgm_logs_tipo WHERE idTipo=$id";
        $results = DB::select($qrycon);
        return $results[0]->tipo;
    }
}

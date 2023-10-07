<?php

namespace App\Models\mecom;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuentas_entidades_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idEntidad';
    protected $table = 'sgm_entidades';
    public static function getItems()
    {
        $qry = "SELECT A.idEntidad,A.descripcion,case when A.tipo=3 then CONCAT('GDR/GLD de ',D.valle) when A.tipo=4 then CONCAT('R.L.S. ',C.logia,' Nro ',C.numero) else B.nombre END AS entidad,A.fechaModificacion, A.cuenta,case when C.pagosweb=1 THEN '1' else '0' end as activo,C.numero AS llave,
        case when A.tipo=3 then D.idValle when A.tipo=4 then C.numero else A.tipo END AS iden,A.tipo,B.nombre, E.username as usuario,A.fechaCreacion,A.codigo,A.banco, A.identificador AS valle, A.identificador AS numero,case when C.pagosweb=1 and A.tipo=4 then 'Si' when A.tipo=4 then 'No' else '-' end as activotxt
       FROM sgm_entidades A JOIN
       sgm_parametros B ON A.tipo=B.valor AND B.tipo=8 JOIN sgm2_users E ON A.usuarioModificacion=E.id
       LEFT JOIN sgm_logias C ON A.identificador=C.numero LEFT JOIN sgm_valles D ON A.identificador=D.idValle
       ORDER BY A.tipo,A.identificador  ";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function checkExiste($tipo,$id)
    {
        $val = self::select('idEntidad')->where('tipo', $tipo)->where('identificador', $id)->first('idEntidad');
        if (is_null($val)) {
            return 0;
        } else {
            return $val->idEntidad;
        }
    }
}

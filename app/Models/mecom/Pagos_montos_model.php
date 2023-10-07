<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos_montos_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idMonto';
    protected $table = 'sgm_pagos_montos';

    public static function getItems($pagina, $cantidad)
    {
        $inicio = $cantidad * ($pagina - 1);
        $qry = "SELECT A.idMonto,A.tipo,A.entidad,A.cobro,A.descripcion,DATE_FORMAT(A.fechaInicio,'%d/%m/%Y') AS fechaInicio,DATE_FORMAT(A.fechaFin,'%d/%m/%Y') AS fechaFin,A.numeroPagos,A.numeroPersonas,
        B.tipo AS tipotxt,A.taller, A.valle,A.monto,A.fechaModificacion,A.activo, case when A.taller > 0 then C.logia ELSE 'Todas' END AS logiatxt, case when A.valle>0 then D.valle ELSE 'Todos' END AS valletxt,
        case when A.numeroPagos>0 then A.numeroPagos ELSE 'Indefinido' END AS cantidadtxt, case when A.numeroPersonas>0 then A.numeroPersonas ELSE 'Indefinido' END AS numerotxt,COUNT(F.idRegistro) AS transac
        FROM sgm_pagos_montos A JOIN sgm_pagos_tipos B ON A.entidad=B.idTipo LEFT JOIN sgm_logias C ON A.taller=C.numero
        LEFT JOIN sgm_valles D ON A.valle=D.idValle LEFT JOIN sgm_pagos_registros F ON A.idMonto=F.idPago
        WHERE A.borrado=0
        GROUP BY A.idMonto,A.tipo,A.entidad,A.cobro,A.descripcion,A.fechaInicio,A.fechaFin,A.numeroPagos,A.numeroPersonas,
        B.tipo ,A.taller, A.valle,A.monto,A.fechaModificacion,A.activo ORDER BY A.activo,A.fechaInicio DESC Limit $inicio,$cantidad";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems()
    {
        $qry = "SELECT count(A.IdMonto) AS numero FROM sgm_pagos_montos A JOIN sgm_pagos_tipos B ON A.entidad=B.idTipo WHERE A.borrado=0";
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
    public static function getReporte($id)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT D.GradoActual,B.monto,CONCAT(E.logia,' Nro. ',E.numero) AS logiatxt,F.valle, CONCAT(C.Nombres,' ',C.Paterno,' ',C.Materno) AS miembro,DATE_FORMAT(B.fechaCreacion,'%e de %M de %Y') AS fechaPago
      FROM sgm_pagos_montos A
      JOIN sgm_pagos_registros B ON A.idMonto=B.idPago
      JOIN sgm_miembros C ON B.idMiembro=C.id
      JOIN sgm_grados D ON C.Grado=D.Grado
      JOIN sgm_logias E ON E.numero=C.LogiaActual
      JOIN sgm_valles F ON E.valle=F.idValle
      WHERE A.idMonto=$id ORDER BY F.valle,E.numero,C.Paterno,C.Materno,C.Nombres";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getPagosExtra($valle = 0, $logia = 0)
    {
        $hoy = date("Y-m-d");
        $cond = "A.activo=1 AND A.fechaInicio<'$hoy' AND A.fechaFin>='$hoy'";
        if ($valle > 0)
          $cond .= " AND A.valle IN (0,$valle)";
        if ($logia > 0)
          $cond .= " AND A.taller IN (0,$logia)";
        $qry = "SELECT A.idMonto, CONCAT('Bs.',A.monto,' - ',A.cobro) as pago FROM sgm_pagos_montos A WHERE $cond Order By A.fechaInicio";
        $query = DB::select($qry);
        $arreglo = array();
        foreach ($query as $sem) {
          $arreglo[$sem->idMonto] = $sem->pago;
        }
        return $arreglo;
    }
}

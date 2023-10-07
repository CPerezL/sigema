<?php

namespace App\Models\mecom;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descuentos_model extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $primaryKey = 'idDescuento';
    protected $table = 'sgm_mecom_descuentos';
    public static function getItems()
    {
        $qry = "SELECT A.idDescuento,A.desde,A.hasta,A.gestion,A.numeroCuotas,A.comentario,A.fechaCreacion,A.fechaModificacion,B.username,CASE WHEN A.activo=1 THEN 'Activo' ELSE 'Desactivado' END AS activotxt,A.activo,
        DATE_FORMAT(A.desde,'01/%m/%Y') AS desdefrm,DATE_FORMAT(A.hasta,'%01/%m/%Y') AS hastafrm
  FROM sgm_mecom_descuentos A JOIN sgm2_users B ON A.usuario=B.id ORDER BY A.fechaCreacion ";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getListaDescuentos($gestion, $valle)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry="SELECT A.numeroCuotas, DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago, DATE_FORMAT(A.fechaPagoNuevo,'%b-%Y') AS fechaPagoNuevo,A.idRegistro,A.mesesDescuento,B.NombreCompleto,A.taller,
   D.estado, case when D.estado=4 then 'Aprobado' ELSE 'Sin aprobar' ENd AS estadotxt,A.miembro,
  (A.numeroCuotas - A.mesesDescuento) AS cuotaInicial, G.GradoActual,A.fechaCreacion,T.valle,V.valle AS valletxt,T.logia,
  concat(DATE_FORMAT(C.desde,'%b/%Y'),'-',DATE_FORMAT(C.hasta,'%b/%Y')) AS mesesRango
  FROM sgm_mecom_registros A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_mecom_descuentos C ON A.idDescuento=C.idDescuento JOIN sgm_mecom_formularios D ON A.idFormulario=D.idFormulario
  JOIN sgm_grados G ON B.Grado=G.Grado JOIN sgm_logias T ON A.taller=T.numero JOIN sgm_valles V ON T.valle=V.idValle
  WHERE A.mesesDescuento > 0  AND A.fechaCreacion>= '$gestion-01-01' AND A.fechaCreacion<= '$gestion-12-31' AND T.Valle=$valle
  ORDER BY A.fechaPagoNuevo";
        //echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
}

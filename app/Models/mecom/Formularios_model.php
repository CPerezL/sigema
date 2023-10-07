<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formularios_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idFormulario';
    protected $table = 'sgm_mecom_formularios';
    public static function getFormularios($page, $cantidad, $gestion, $valle, $taller = 0, $estado = 4, $tipo = 1, $level = 1, $aprobado = 0)
    {
        $inicio = $cantidad * ($page - 1);
        $cond = '';
        if ($taller > 0) {
            $cond = " AND A.taller=$taller";
        } else {
            $cond = '';
        }

        if ($aprobado == 4) {
            $orderby = 'A.fechagdr';
        } else {
            $orderby = 'A.fechaEnvio';
        }

        if ($level > 2 && $valle == 0) {
        } else {
            $cond .= " AND A.idValle=$valle";
        }
        if ($taller > 0) {
            $cond .= " AND  A.taller=$taller ";
        }

        $qry = "SELECT A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.montoTotal,A.estado,A.idValle,A.documento,DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,B.descripcion,
CONCAT(C.logia,' Nro ',A.taller) AS tallertxt,A.aprobadogdr, CASE WHEN A.aprobadogdr=4 THEN 'Revisado' ELSE 'Sin revisar' END AS aprobadogdrtxt,A.fechagdr,SUM(R.montoGDR) AS montoGDR
FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado JOIN sgm_logias C ON A.taller=C.numero JOIN sgm_mecom_registros R ON A.idFormulario=R.idFormulario
WHERE  A.tipo=$tipo AND A.estado=$estado AND A.aprobadogdr=$aprobado AND A.gestion=$gestion  $cond
GROUP BY A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.montoTotal,A.estado,A.idValle,A.documento,A.fechagdr,B.descripcion,A.fechaAprobacion,A.aprobadogdr
ORDER BY $orderby DESC Limit $inicio,$cantidad";
        //  echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getFormulariosTotal($gestion, $valle, $taller = 0, $estado = 2, $tipo = 1, $level = 1, $aprobado = 0)
    {
        if ($taller > 0) {
            $cond = " AND A.taller=$taller";
        } else {
            $cond = '';
        }
        if ($level > 4) {
            $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A  WHERE A.tipo=$tipo AND A.estado=$estado AND A.aprobadogdr=$aprobado AND A.gestion=$gestion $cond";
        } elseif ($level > 2) {
            $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A  WHERE A.tipo=$tipo AND A.estado=$estado AND A.aprobadogdr=$aprobado AND A.gestion=$gestion $cond";
        } else {
            if ($taller > 0) {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.idValle=$valle AND A.taller=$taller AND A.tipo=$tipo AND A.estado=$estado AND A.aprobadogdr=$aprobado AND A.gestion=$gestion $cond";
            } else {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.idValle=$valle AND A.tipo=$tipo AND A.estado=$estado AND A.aprobadogdr=$aprobado AND A.gestion=$gestion $cond";
            }
        }
        $results = DB::select($qry);
        $num = $results[0]->numero;
        return $num;
    }
    public static function getAportantes($idformulario)
    {
        $qry = "SELECT B.NombreCompleto,B.Miembro,FORMAT(A.monto,0,'es_ES') AS monto,A.idRegistro,FORMAT(A.montoGDR,0,'es_ES') AS montoGDR,A.numeroCuotas,DATE_FORMAT(A.ultimoPago, '%b-%Y') AS fechaPago,A.ultimoPago,C.GradoActual, D.estado,DATE_FORMAT(A.fechaPagoNuevo, '%b-%Y') AS fechaNuevo
        FROM sgm_mecom_registros A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_grados C ON B.Grado=C.Grado JOIN sgm_mecom_formularios D ON D.idFormulario=A.idFormulario
        WHERE A.idFormulario=$idformulario ORDER BY B.NombreCompleto";
        $results = DB::select($qry);
        return $results;
    }
    /* genera pdf funciones de datos */
    public static function getDatosForm($idform)
    {
        $qry = "SELECT taller,idValle,documento,estado,fechaEnvio,numero,gestion,fechaAprobacion,estado FROM sgm_mecom_formularios WHERE idFormulario=$idform";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getDatosLogia($nlog)
    {
        $qry = "SELECT A.logia,A.valle,A.diatenida,A.rito,A.nombreCompleto ,B.logo,B.valle AS valletxt FROM sgm_logias A INNER JOIN sgm_valles B ON A.valle=B.idValle WHERE A.numero='$nlog'";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getMontosFormulario($idform)
    {
        $qry = "SELECT A.montoTotal,SUM(B.monto) AS msuma,SUM(B.montoGLB) AS mglb,SUM(B.montoGDR) AS mgdr,SUM(B.montoCOMAP) AS mcomap
  FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario WHERE A.idFormulario=$idform";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getMontosFormularioQR($idform)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT FORMAT(A.montoTotal,0,'es_ES'),FORMAT(SUM(B.monto),0,'es_ES') AS msuma,FORMAT(SUM(B.montoGLB),0,'es_ES') AS mglb,SUM(B.montoGDR) AS mgdr,FORMAT(SUM(B.montoCOMAP),0,'es_ES') AS mcomap ,FORMAT(SUM(B.montoTaller),0,'es_ES') AS mtall
        FROM sgm_mecom_formularios A JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario WHERE A.idFormulario=$idform";
        $query = DB::select($qry);
        $dato = $query[0];
        if (is_null($dato)) {
            $qry = "SELECT FORMAT(A.montoTotal,0,'es_ES'),FORMAT(SUM(B.monto),0,'es_ES') AS msuma,FORMAT(SUM(B.montoGLB),0,'es_ES') AS mglb,FORMAT(SUM(B.montoGDR),0,'es_ES') AS mgdr,FORMAT(SUM(B.montoCOMAP),) AS mcomap ,FORMAT(SUM(B.montoTaller),) AS mtall
            FROM sgm_mecom_formularios A JOIN sgm_mecom_pagos_registros B ON A.idFormulario=B.idFormulario WHERE A.idFormulario=$idform";
            $query = DB::select($qry);
            $dato = $query[0];
        }
        return $dato;
    }
    public static function getAportantesForm($idformulario)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $qry = "SELECT B.NombreCompleto,LEFT (B.Miembro,1) AS miembro,FORMAT(A.monto,0,'es_ES') as monto,FORMAT(A.montoGLB,0,'es_ES') as montoGLB,FORMAT(A.montoGDR,0,'es_ES') as montoGDR,FORMAT(A.montoCOMAP,0,'es_ES') as montoCOMAP,
        FORMAT(case when A.montoTaller>0 then A.montoTaller ELSE E.montoTaller END,0,'es_ES') AS montoTaller,A.numeroCuotas,DATE_FORMAT(A.fechaPagoNuevo, '%b-%Y') AS fechaDos,
        DATE_FORMAT(A.ultimoPago, '%b-%Y') AS fechaUno,LEFT (C.GradoActual,1) AS grado,A.mesesDescuento
        FROM sgm_mecom_registros A JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_grados C ON B.Grado=C.Grado JOIN sgm_mecom_formularios D ON D.idFormulario=A.idFormulario LEFT JOIN
        sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
WHERE A.idFormulario=$idformulario ORDER BY B.NombreCompleto";
        $results = DB::select($qry);
        return $results;
    }
    public static function getMontosTipo($id)
    {
        $qry = "select *  from sgm_mecom_montos_tipo WHERE idTipo=$id";
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getMiembros($taller)
    {
        DB::select("SET lc_time_names = 'es_ES'");
        $hoy = date("Y-m-d");
        $newdate = date("Y-m", strtotime("$hoy -5 years")) . '-01';
        $qry = "SELECT A.id, A.LogiaActual, A.NombreCompleto, A.Grado, A.Miembro,DATE_FORMAT(A.ultimoPago,'%b-%Y') AS ultimoPago,B.GradoActual FROM sgm_miembros A JOIN sgm_grados B ON A.Grado=B.Grado
        WHERE A.Estado=1  AND  (A.LogiaActual = $taller) AND A.ultimoPago>'$newdate' ORDER BY A.NombreCompleto ASC";
        $results = DB::select($qry);
        return $results;
    }
    public static function getListaForms($page, $rows, $gestion, $logia, $tipo = 1, $level = 1)
    {
        $inicio = $rows * ($page - 1);
        if ($level > 4) {
            if ($logia > 0) {
                $qry = "SELECT A.idFormulario,A.numero,A.gestion,A.taller,A.numeroMiembros,A.montoTotal,A.estado,A.idValle,DATE_FORMAT(A.fechaCreacion, '%Y/%m/%d') as fechaCreacion,
                DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion
                FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado WHERE A.tipo=$tipo AND A.gestion=$gestion AND A.taller=$logia ORDER BY A.fechaCreacion DESC Limit $inicio,20";
            } else {
                $qry = "SELECT A.idFormulario,A.numero,A.gestion,A.taller,A.numeroMiembros,A.montoTotal,A.estado,A.idValle,DATE_FORMAT(A.fechaCreacion, '%Y/%m/%d') as fechaCreacion,
                DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion
                FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado WHERE A.tipo=$tipo AND A.gestion=$gestion ORDER BY A.fechaCreacion DESC Limit $inicio,20";
            }
        } else {
            $qry = "SELECT A.idFormulario,A.numero,A.gestion,A.taller,A.numeroMiembros,A.montoTotal,A.estado,A.idValle,DATE_FORMAT(A.fechaCreacion, '%Y/%m/%d') as fechaCreacion,
            DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion
            FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado WHERE A.taller=$logia AND A.tipo=$tipo AND A.gestion=$gestion ORDER BY A.fechaCreacion DESC Limit $inicio,20";
        }
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumeroForms($gestion, $logia, $tipo = 1, $level = 1)
    {
        if ($level > 4) {
            if ($logia > 0) {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.taller=$logia AND A.tipo=$tipo AND A.gestion=$gestion";
            } else {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.tipo=$tipo AND A.gestion=$gestion";
            }
        } else {
            $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.taller=$logia AND A.tipo=$tipo AND A.gestion=$gestion";
        }

        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getDatosMiembro($idmiembro, $valle = 0)
    {
        $qry = "SELECT  A.Miembro,A.ultimoPago,A.Grado,SUM(B.montohabil) AS montoCuotaMes,
        SUM(CASE WHEN B.entidad=1 THEN B.montohabil ELSE 0 END) AS monto1,SUM(CASE WHEN B.entidad=2 THEN B.montohabil ELSE 0 END) AS monto2,SUM(CASE WHEN B.entidad=3 THEN B.montohabil ELSE 0 END) AS monto3,B.idValle
        FROM sgm_miembros A JOIN sgm_mecom_montos B ON A.Miembro=B.miembro WHERE id=$idmiembro AND B.idValle IN (0,$valle) GROUP BY A.Miembro,A.ultimoPago,A.Grado,B.idValle
        ORDER by B.idValle DESC LIMIT 1";
        $results = DB::select($qry);
        if (count($results) > 0) {
            return $results[0];
        } else {
            return null;
        }

    }
    //**pagos QR */
    public static function checkFormulario($gestion, $logia, $estado = '1', $tipo = 10)
    {
        if ($gestion > 0 && $logia > 0) {
            $query = self::whereraw("taller=$logia AND estado IN ('$estado') AND gestion=$gestion AND tipo=$tipo")->first('idFormulario');
            if (is_null($query)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function getUltimoForm($nlog, $ges = 0, $tipo = 10)
    {
        if ($ges > 2000) {
            $qry = "SELECT MAX(numero) AS ultimo FROM sgm_mecom_formularios WHERE taller='$nlog' and tipo IN($tipo) and gestion=$ges";
        } else {
            $qry = "SELECT MAX(numero) AS ultimo FROM sgm_mecom_formularios WHERE taller='$nlog' and tipo IN($tipo) ";
        }
        $query = DB::select($qry);
        $dato = $query[0];
        if ($dato->ultimo > 0) {
            return $dato->ultimo;
        } else {
            return 0;
        }
    }
    /****************************************************************/
    public static function getMontoTaller($id)
    {
        $qry = "SELECT SUM(B.monto) AS sumapar FROM sgm_miembros A JOIN sgm_mecom_pagos_montos B ON A.LogiaActual=B.idTaller WHERE A.Miembro=B.miembro AND A.id=$id";
        $query = DB::select($qry);
        $dato = $query[0];
        return $dato->sumapar;
    }
    public static function getMontoValle($mie, $vall)
    {
        $mes = date("Y-m-d");
        $qry = "SELECT SUM(A.monto) AS montos FROM sgm_mecom_montos A WHERE A.idValle IN(0,$vall) AND A.miembro='$mie' AND '$mes'>=A.fechaInicio GROUP BY A.idValle,A.miembro,A.orden ORDER BY A.idValle DESC  LIMIT 1";
        $query = DB::select($qry);
        $dato = $query[0];
        return $dato->montos;
    }

    public static function getMontosUnicos($mie, $vall)
    {
        $mes = date("Y-m-d");
        $qry = "SELECT (A.monto) AS montos,A.entidad FROM sgm_mecom_montos A WHERE A.idValle IN(0,$vall) AND A.miembro='$mie' AND '$mes'>=A.fechaInicio GROUP BY A.idValle,A.miembro,A.orden,A.entidad ORDER BY A.idValle DESC LIMIT 3";
        $query = DB::select($qry);
        return $query;
    }
    public static function checkFormEstado($idform, $estado)
    {
        $query = self::whereraw("idFormulario=$idform AND estado IN ('$estado')")->first('idFormulario');
        if (is_null($query)) {
            return true;
        } else {
            return false;
        }
    }
    public static function getDatosQR($id)
    {
        $qry = "SELECT A.idValle,A.numero,A.taller,A.montoTotal,A.numeroMiembros,concat('Nro. ',B.numero) AS nLogia,C.valle
        FROM sgm_mecom_formularios A JOIN sgm_logias B ON A.taller=B.numero JOIN sgm_valles C ON A.idValle=C.idValle
        WHERE idFormulario=$id";
        $queryc = DB::select($qry);
        $valuec = $queryc[0];
        $data['formulario'] = $valuec->numero;
        $data['valle'] = $valuec->valle;
        $data['logiaName'] = $valuec->nLogia;
        $data['cantidad'] = $valuec->numeroMiembros;
        $data['montoTotal'] = $valuec->montoTotal . ' Bs.';
        $data['tipopago'] = 'Pago usando QR';
        return $data;
    }
    public static function getDatosDePago($id, $iduser)
    {
        // $data = $getFormularioDatos($id);
        $data = self::where('idFormulario', $id)->first(['idValle', 'numero', 'taller', 'montoTotal']);
        $usuario = self::getUsuario($iduser);
        $data->email = $usuario->email;
        $data->username = $usuario->username;
        $data->nombre = $usuario->username;
        $monts = self::getMontosForm($id);
        $data->montoGLB = $monts->montoGLB;
        $data->montoCOMAP = $monts->montoCOMAP;
        $data->montoGDR = $monts->montoGDR;
        $data->montoTaller = $monts->montoTaller;
        $data->valle = $data->idValle;
        $hoy = date("Y-m-d");
        $data->monto = $monts->montoTotal;
        $data->glosa = 'Pago de obolos con formulario';
        $data->nuevoPago = $hoy;
        $data->params = 'Formulario de obolos|' . $data->taller . '|' . $hoy . '|' . $id;
        $data->descripcion = 'Formulario de obolos|' . $data->taller . '|' . $hoy . '|' . $id;
        $data->total = $monts->montoTotal;
        $data->CI = $data->taller;
        return $data;
    }
    private static function getUsuario($id)
    {
        $qry = "SELECT A.username,A.name as nombre,A.email,A.logia FROM sgm2_users A WHERE A.id=$id";
        $query = DB::select($qry);
        return $query[0];
    }
    private static function getMontosForm($idt)
    {
        $qry = "SELECT SUM(A.monto) AS montoTotal,SUM(A.montoGLB) AS montoGLB, SUM(A.montoGDR) AS montoGDR,SUM(A.montoCOMAP) AS montoCOMAP,SUM(A.montoTaller) AS montoTaller FROM sgm_mecom_registros A WHERE A.idFormulario=$idt";
        $query = DB::select($qry);
        return $query[0];
    }
    /*    revisar forms */
    public static function getListaFormsRev($page, $cantidad, $gestion, $valle, $taller, $estado, $tipo = 1, $level = 1)
    {
        $inicio = $cantidad * ($page - 1);
        if ($taller > 0) {
            $cond = " AND A.taller=$taller";
        } else {
            $cond = '';
        }
        if ($level > 4) {
            $qry = "SELECT A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.archivoGLB,A.archivoCOMAP,A.montoTotal,A.estado,A.idValle,A.documento,DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion,
  CONCAT(C.logia,' Nro ',A.taller) AS tallertxt
  FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado JOIN sgm_logias C ON A.taller=C.numero WHERE A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion $cond ORDER BY A.fechaEnvio DESC Limit $inicio,$cantidad";
        } elseif ($level > 2) {
            $qry = "SELECT A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.archivoGLB,A.archivoCOMAP,A.montoTotal,A.estado,A.idValle,A.documento,DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion,
  CONCAT(C.logia,' Nro ',A.taller) AS tallertxt
  FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado JOIN sgm_logias C ON A.taller=C.numero WHERE A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion  $cond ORDER BY A.fechaEnvio Limit $inicio,$cantidad";
        } else {
            if ($taller > 0) {
                $qry = "SELECT A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.archivoGLB,A.archivoCOMAP,A.montoTotal,A.estado,A.idValle,A.documento,DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion,
  CONCAT('Nro ',A.taller,' ',C.logia) AS tallertxt FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado JOIN sgm_logias C ON A.taller=C.numero WHERE A.idValle=$valle AND A.taller=$taller AND A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion  $cond ORDER BY A.fechaEnvio DESC Limit $inicio,$cantidad";
            } else {
                $qry = "SELECT A.idFormulario,A.numero,A.numeroMiembros,A.archivoGDR,A.archivoGLB,A.archivoCOMAP,A.montoTotal,A.estado,A.idValle,A.documento,DATE_FORMAT(A.fechaAprobacion, '%Y/%m/%d') as fechaAprobacion,DATE_FORMAT(A.fechaEnvio, '%Y/%m/%d') as fechaEnvio ,B.descripcion,A.fechaAprobacion,
  CONCAT(C.logia,' Nro ',A.taller) AS tallertxt
  FROM sgm_mecom_formularios A JOIN sgm_mecom_estados B ON A.estado=B.estado JOIN sgm_logias C ON A.taller=C.numero WHERE A.idValle=$valle AND A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion  $cond ORDER BY A.fechaEnvio Limit $inicio,$cantidad";
            }
        }
        //   echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumeroFormsRev($gestion, $valle, $taller, $estado, $tipo = 1, $level = 1)
    {
        if ($taller > 0) {
            $cond = " AND A.taller=$taller";
        } else {
            $cond = '';
        }
        if ($level > 4) {
            $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A  WHERE A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion $cond";
        } elseif ($level > 2) {
            $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A  WHERE A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion $cond";
        } else {
            if ($taller > 0) {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.idValle=$valle AND A.taller=$taller AND A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion $cond";
            } else {
                $qry = "SELECT count(A.idFormulario) as numero FROM sgm_mecom_formularios A WHERE A.idValle=$valle AND A.tipo=$tipo AND A.estado=$estado AND A.gestion=$gestion $cond";
            }
        }
        $results = DB::select($qry);
        return $results[0]->numero;
    }
}

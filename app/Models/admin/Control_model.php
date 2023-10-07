<?php

namespace App\Models\admin;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'idControl';
    protected $table = 'sgm_control';
    private static $estado = 4;
    public static function getItems($pagina, $cantidad, $valle, $estado)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = 'A.idControl>0 ';
        if ($valle > 0) {
            $cond .= " AND A.valle=$valle";
        }

        if ($estado > 0) {
            $cond .= " AND A.estado=$estado";
        } else {
            $cond .= " AND A.estado=1";
        }
        $qry = "SELECT A.idControl,concat(A.accion,': ',A.modificacion) as tipo,A.valorAntes,A.valorCambio,A.estado,A.taller,A.valle,A.fechaModificacion,case when B.id is null then 'Nuevo' else B.NombreCompleto end as NombreCompleto ,C.username,
  case when A.estado=1 then 'Esperando aprobacion' when A.estado=4 then 'Aprobado' when A.estado=3 then 'Rechazado' ELSE 'Desechado' END AS estadotxt, V.valle AS valletxt
  FROM sgm_control A left JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm2_users C ON A.usuarioCambia=C.id join sgm_valles V ON A.valle=V.idValle WHERE $cond Order By A.fechaModificacion DESC Limit $inicio,$cantidad ";
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($valle, $tipo)
    {
        $cond = ' idControl>0 ';
        if ($valle > 0) {
            $cond .= " AND valle=$valle";
        }
        if ($tipo > 0) {
            $cond .= " AND estado=$tipo";
        } else {
            $cond .= " AND estado=1";
        }
        $count = self::select('idControl')->whereraw($cond)->count();
        return $count;
    }
    public static function apruebaEstado($id)
    {
        $query = DB::select("SELECT * FROM sgm_control A WHERE idControl=$id");
        $reg = $query[0];
        if ($reg->tipo > 49) //inserta dato en miembros
        {
            $dnew = (array) json_decode($reg->valorCambio);
            $dnew['fechaModificacion'] = date('Y-m-d');
            $dnew['fechaCreacion'] = date('Y-m-d');
            $res = DB::table('sgm_miembros')->insert($dnew);
        } else {
            $campos = explode('|', $reg->campos);
            $valores = explode('|', $reg->valorCambio);
            foreach ($campos as $key => $ccc) {
                if (strlen($ccc) > 1) {
                    $data[$ccc] = trim($valores[$key]);
                }
            }
            $res = DB::table('sgm_miembros')->where('id', $reg->idMiembro)->update($data);
        }
        return $res;
    }
    public static function getRegulariza($pagina, $cantidad, $palabra, $valle = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        $cond = 'A.tipo> 49';
        if ($valle > 0) {
            $cond .= " AND A.valle=$valle";
        }

        if (strlen($palabra) > 0) {
            $cond .= " AND A.valorCambio like '%$palabra%'";
        }

        $qry = "SELECT A.idControl,A.tipo,A.accion,A.valorAntes,A.valorCambio,A.estado,A.taller,A.valle,A.fechaModificacion ,C.username,A.modificacion,
case when A.estado=1 then 'Esperando aprobacion' when A.estado=4 then 'Aprobado' when A.estado=3 then 'Rechazado' ELSE 'Desechado' END AS estadotxt, V.valle AS valletxt
FROM sgm_control A left JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm2_users C ON A.usuarioCambia=C.id join sgm_valles V ON A.valle=V.idValle WHERE $cond Order By A.fechaModificacion DESC Limit $inicio,$cantidad ";
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumRegulariza($palabra, $valle = 0)
    {
        $cond = 'A.tipo> 49';
        if ($valle > 0) {
            $cond .= " AND A.valle=$valle";
        }

        if (strlen($palabra) > 0) {
            $cond .= " AND A.valorCambio like '%$palabra%'";
        }

        $qry = "SELECT count(idControl) as numero FROM sgm_control A WHERE $cond";
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getModifica($pagina, $cantidad, $palabra, $valle = 0, $taller = 0, $grado = 0)
    {
        $inicio = $cantidad * ($pagina - 1);
        if (self::$estado == 4) {
            $cond = "A.id > 0";
        } else {
            $cond = "A.Estado = " . self::$estado;
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller == 1000) {
            $cond .= " AND (A.LogiaActual NOT BETWEEN 1 AND 999)";
        } elseif ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        $qry = "SELECT A.id,A.LogiaAfiliada, A.NombreCompleto, A.Miembro,DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS FechaIniciacion, DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS FechaAumentoSalario,
  DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS FechaExaltacion,CONCAT(B.logia,' Nro. ',A.LogiaActual) AS nlogia,C.idControl,
  CASE A.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual, concat(C.accion,': ',C.modificacion) as tipo, CASE when C.estado=1 then 'Procesando' ELSE '' END AS estadotxt,C.fechaModificacion
  FROM sgm_miembros A LEFT JOIN sgm_logias B ON A.LogiaActual=B.numero LEFT JOIN sgm_control C ON A.id=C.idMiembro AND C.estado=1
  WHERE  $cond ORDER BY A.NombreCompleto ASC Limit " . $inicio . "," . $cantidad . ' ';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumModifica($palabra, $valle = 0, $taller = 0, $grado = 0)
    {
        if (self::$estado == 4) {
            $cond = "A.Estado >=0";
        } else {
            $cond = "A.Estado = " . self::$estado;
        }

        if ($grado > 0) {
            $cond .= " AND A.Grado = '$grado'";
        }

        if ($taller == 1000) {
            $cond .= " AND A.LogiaActual NOT BETWEEN 1 AND 999 ";
        } elseif ($taller > 0) {
            $cond .= " AND A.LogiaActual = '$taller'";
        }

        if (strlen($palabra) > 2) {
            $cond .= " AND A.NombreCompleto Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND B.valle = '$valle'";
        }

        $qry = "SELECT COUNT(DISTINCT A.id) AS numero FROM sgm_miembros A left join sgm_logias B ON A.LogiaActual=B.numero WHERE $cond";
        // echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results[0]->numero;
    }
    public static function getFormMiembro($id, $task)
    {
        if ($task == 1) //nombre
        {
            $qry = "SELECT A.id,A.Nombres,A.Paterno,A.Materno FROM sgm_miembros A WHERE A.id=$id";
        } elseif ($task == 2) //grado
        {
            $qry = "SELECT A.id,A.Grado FROM sgm_miembros A WHERE A.id=$id";
        } elseif ($task == 3) //ini
        {
            $qry = "SELECT A.id,DATE_FORMAT(A.FechaIniciacion,'%d/%m/%Y') AS Fecha FROM sgm_miembros A WHERE A.id=$id";
        } elseif ($task == 4) //aume
        {
            $qry = "SELECT A.id,DATE_FORMAT(A.FechaAumentoSalario,'%d/%m/%Y') AS Fecha FROM sgm_miembros A WHERE A.id=$id";
        } elseif ($task == 5) //exa
        {
            $qry = "SELECT A.id,DATE_FORMAT(A.FechaExaltacion,'%d/%m/%Y') AS Fecha FROM sgm_miembros A WHERE A.id=$id";
        }
        $results = DB::select($qry);
        return $results[0];
    }
    public static function getValores($id, $campos)
    {
        $qry = "SELECT A.LogiaActual as taller,B.valle,$campos FROM sgm_miembros A JOIN sgm_logias B ON A.LogiaActual=B.numero WHERE id=$id";
        $results = DB::select($qry);
        return $results[0];
    }
}

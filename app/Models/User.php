<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\admin\Roles_model;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // static $valle = 0;
    // static $level = 1;
    // static $logia = 0;
    public $table = 'sgm2_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'userpassword',
        'password',
        'idRol',
        'template',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getNivelAttribute()
    {
        return Roles_model::select('level')->where('id', $this->idRol)->first()->level;
    }
    public function getIdOrienteAttribute()
    { //3
        $rol = Roles_model::select('level')->where('id', $this->idRol)->first()->level;
        $ori = self::select('oriente')->where('id', $this->id)->first()->oriente;
        if ($ori > 0) {
            return $ori;
        } else { // si no tiene le permiso de taller
            if ($rol > 2) {
                return $ori;
            } else {
                return 1000;
            }

        }
    }
    public function getIdValleAttribute()
    { //2
        $rol = Roles_model::select('level')->where('id', $this->idRol)->first()->level;
        $val = self::select('valle')->where('id', $this->id)->first()->valle;
        if ($val > 0) {
            return $val;
        } else { // si no tiene le permiso de valle
            if ($rol > 1) {
                return $val;
            } else {
                return 1000;
            }

        }
    }
    public function getIdLogiaAttribute()
    { //1
        $rol = Roles_model::select('level')->where('id', $this->idRol)->first()->level;
        $log = self::select('logia')->where('id', $this->id)->first()->logia;
        if ($log > 0) {
            return $log;
        } else { // si no tiene le permiso de taller
            if ($rol > 0) {
                return $log;
            } else {
                return 1000;
            }

        }
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public static function getItems($pagina, $cantidad, $palabra = '', $oriente = 0, $valle = 0, $rol = '')
    {
        $inicio = $cantidad * ($pagina - 1); //ok
        $cond = 'A.id>0';
        if ($oriente > 0) {
            $cond .= " AND A.oriente=$oriente";
        }

        if (strlen($palabra) > 0) {
            $cond .= " AND A.username Like '%$palabra%'";
        }

        if ($valle > 0) {
            $cond .= " AND A.valle=$valle";
        }

        if ($rol > 0) {
            $cond .= " AND A.idRol=$rol";
        }

        $qry = "SELECT A.id,A.username,A.name,A.email,A.oriente,A.logia,A.valle,CASE A.logia WHEN 0 THEN 'Todas las Logias' ELSE CONCAT('R.L.S. ',B.logia,' Nro ',B.numero) END AS logiatxt,
(A.last_login) as fecha, CASE WHEN C.valle IS NULL THEN 'Todos los Valles' ELSE C.valle END AS valletxt,A.idRol,R.name AS roltxt,CASE WHEN A.oriente>0 THEN O.oriente ELSE 'Todos los Orientes' END AS orientetxt,P.nombre AS permisostxt,A.permisos
FROM sgm2_users A
JOIN sgm2_roles R ON A.idRol=R.id
JOIN sgm_parametros P ON A.permisos=P.valor AND P.tipo=7
LEFT JOIN sgm_orientes O ON A.oriente=O.idOriente
  LEFT JOIN sgm_logias B ON A.logia=B.numero
  LEFT JOIN sgm_valles C ON A.valle=C.idValle
  WHERE $cond  ORDER BY A.permisos DESC,R.level DESC,R.name ASC Limit $inicio," . $cantidad;
        //  echo $qry.'<hr>';
        $results = DB::select($qry);
        return $results;
    }
    public static function getNumItems($palabra = '', $oriente = 0, $valle = 0, $rol = '')
    {
        $cond = 'id>0';
        if ($oriente > 0) {
            $cond .= " AND oriente=$oriente";
        }

        if ($valle > 0) {
            $cond .= " AND valle=$valle";
        }

        if ($rol > 0) {
            $cond .= " AND idRol=$rol";
        }

        if (strlen($palabra) > 0) {
            $cond .= " AND username Like '%$palabra%'";
        }

        $count = self::select('id')->whereraw($cond)->count();
        return $count;
    }
    public function getPlantillaAttribute()
    {
        $temp = $this->template;
        if ($temp > 1) {
            $direcc = "jquery-easyui/template$temp/template.css";
        } else {
            $direcc = "jquery-easyui/template1/template.css";
        }
        return $direcc;
    }
    public static function getUltimos($taller = 0, $valle = 0)
    {
        $hoy = date("Y-m-d");
        $fecha = date("Y-m-d", strtotime("$hoy -3 days"));
        $cond = "LENGTH(A.nombres)> 3 AND A.NivelActual>=0 AND A.fechaModificacion>'$fecha' ";
        if ($taller > 0) {
            $cond .= "AND A.logia=$taller ";
        }

        if ($valle > 0) {
            $cond .= "AND B.valle=$valle ";
        }

        /// aumentos
        $cond2 = " A.NivelActual>=0 AND A.fechaModificacion>'$fecha' ";
        if ($taller > 0) {
            $cond2 .= "AND A.logia=$taller ";
        }

        if ($valle > 0) {
            $cond2 .= "AND B.valle=$valle ";
        }

        //exaltaciones
        $cond3 = " A.NivelActual>=0 AND A.fechaModificacion>'$fecha' ";
        if ($taller > 0) {
            $cond3 .= "AND A.logia=$taller ";
        }

        if ($valle > 0) {
            $cond3 .= "AND B.valle=$valle ";
        }

        //otros
        $cond4 = " A.idControl>0 AND A.estado IN(3,4) AND A.fechaModificacion>'$fecha' ";
        if ($taller > 0) {
            $cond4 .= "AND A.taller=$taller ";
        }

        if ($valle > 0) {
            $cond4 .= "AND A.valle=$valle ";
        }

        $qry = "SELECT 'Iniciacion' as tipo,A.fechaModificacion,B.Logia AS nLogia,B.numero, C.valle,
D.nivel,concat(A.apPaterno,' ',A.apMaterno,' ',A.nombres) AS NombreCompleto
FROM sgm_tramites_iniciacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero)
LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=1
WHERE $cond
union ALL
SELECT 'Aumento' as tipo,A.fechaModificacion,B.Logia AS nLogia, B.numero,C.valle,D.nivel, E.NombreCompleto
FROM sgm_tramites_aumento A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=2 JOIN sgm_miembros E ON A.idMiembro=E.id
WHERE $cond2
union ALL
SELECT 'Exaltacion' as tipo,A.fechaModificacion,B.Logia AS nLogia,B.numero,C.valle,D.nivel, E.NombreCompleto
FROM sgm_tramites_exaltacion A LEFT OUTER JOIN sgm_logias B ON (A.logia=B.numero) LEFT OUTER JOIN sgm_valles C ON B.valle=C.idValle
INNER JOIN sgm_tramites_tipo D ON D.idNivel=A.NivelActual AND D.idTramiteGrado=3 JOIN sgm_miembros E ON A.idMiembro=E.id
WHERE $cond3
union ALL
SELECT A.accion as tipo,A.fechaModificacion,C.Logia AS nLogia,A.taller as numero,V.valle,
case when A.estado=1 then 'Esperando aprobacion' when A.estado=4 then 'Aprobado' when A.estado=3 then 'Rechazado' ELSE 'Desechado' END AS nivel,
case when B.id is null then 'Nuevo' else B.NombreCompleto end as NombreCompleto
FROM sgm_control A left JOIN sgm_miembros B ON A.idMiembro=B.id JOIN sgm_logias C ON A.taller=C.numero join sgm_valles V ON A.valle=V.idValle
WHERE $cond4
ORDER BY fechaModificacion DESC,numero"; //    echo $qry.'<hr>';
        $query = DB::select($qry);
        if (count($query) > 0) {
            return $query;
        } else {
            return [];
        }

    }
}

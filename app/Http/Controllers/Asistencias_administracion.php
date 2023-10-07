<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\asistencias\Administracion_model;
use App\Models\asistencias\Asistenciadata_model;
use App\Models\asistencias\Asistencias_model;
use App\Models\Config_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Asistencias_administracion extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '061';
    public $controlador = 'asistencias_administracion';
    private $meses = 3;
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        self::permiso($request->input('_'));
        self::iniciarModulo();
        $data['_mid'] = $this->idmod; //---
        $data['year'] = date('Y'); //---
        Session::put($this->controlador . '.gestion', date('Y'));
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('asistencias.administracion', $data);
    }
    public function get_datos(Request $request)
    {
        $diat = Session::get($this->controlador . '.ftenida', 0);
        if ($diat > 0) {
            $ges = Session::get($this->controlador . '.gestion');
            $mes = Session::get($this->controlador . '.mes');
            $tal = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            Administracion_model::setRegular(Config_model::getValue('mesesDeuda'));
            $salida = Administracion_model::getListaAsistencia($tal, $ges, $mes, $diat);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function filter_taller(Request $request)
    {
        $taller = $request->input('taller', 0);
        if ($taller > 0 && $taller != Session::get($this->controlador . '.taller')) {
            $dia = Asistencias_model::getDiaTaller($taller);
            $data = array($this->controlador . '.taller' => $taller, $this->controlador . '.tenida' => $dia);
            Session::put($data);
            return response()->json(['success' => 1, 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function get_dias(Request $request)
    {
        $dia = Session::get($this->controlador . '.tenida'); //revisar
        if ($dia > 0) {
            $salida = Administracion_model::getDataAsistencia(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'));
            $total = count($salida);

            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }

        return response()->json($qry2);
    }
    public function get_miembros(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0) {
            $diat = Session::get($this->controlador . '.ftenida', 0);
            $ges = Session::get($this->controlador . '.gestion');
            $mes = Session::get($this->controlador . '.mes');

            $salida = Administracion_model::getMiembros(Config_model::getValue('mesesDeuda'), $taller, $ges, $mes, $diat);
            $total = 0;
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function filter_diatenida(Request $request)
    {
        if ($request->input('ftenida', 0) > 0 && Session::get($this->controlador . '.taller') > 0) {
            $gest = '/' . Session::get($this->controlador . '.gestion');
            $fechatt = Session::get($this->controlador . '.gestion') . '-' . $request->input('mtenida') . '-' . $request->input('ftenida');
            $dataten = Asistencias_model::getDataAsisSimple(Session::get($this->controlador . '.taller'), $fechatt);
            $grado = 0;
            if (is_null($dataten)) {
                $datasis = "Fecha: $fechatt<br>GRADO : No asignado<br>Nro Acta: Sin numero";
            } else {
                $datasis = "Fecha: $fechatt<br>GRADO : $dataten->grado<br>Nro Acta: $dataten->numeroActa1, $dataten->numeroActa2, $dataten->numeroActa3 " . $gest;
                $grado = $dataten->grado;
            }
            $data = array($this->controlador . '.ftenida' => $request->input('ftenida'), $this->controlador . '.mes' => $request->input('mtenida'));

            Session::put($data);
            $qry2 = (object) ['success' => 'true', 'Msg' => $datasis, 'Grado' => $grado];
            return response()->json($qry2);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function update_asis(Request $request)
    {
        $id = $request->input('idmiembro');
        if ($id > 0) {
            $ften = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
            $dif = $this->mesesConta($ften);
            if ($dif <= $this->meses || Auth::user()->nivel > 3) {
                $check = Asistencias_model::checkAsis(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'), $id, Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'));
                if ($check > 0) {
                    Asistencias_model::where("idAsistencia", $check)->delete();
                    return response()->json(['success' => 'true', 'Msg' => 'Asistencia quitada']);
                } else {
                    $datas = array(
                        'idLogia' => Session::get($this->controlador . '.taller'),
                        'gestion' => Session::get($this->controlador . '.gestion'),
                        'idMiembro' => $id,
                        'fechaTenida' => $ften,
                        'fechaAlta' => date("Y-m-d", time()),
                    );
                    $resu = Asistencias_model::insert($datas);
                    if ($resu > 0) {
                        return response()->json(['success' => 'true', 'Msg' => 'Asistencia asignada correctamente']);
                    } else {
                        return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al borrar los datos']);
                    }
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'No se puede modificar, fecha de mas de ' . $this->meses . ' meses de antiguedad']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Identificador no valido']);
        }
    }
    public function set_asistencia(Request $request)
    {
        $ften = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
        $dif = $this->mesesConta($ften);
        if ($dif <= $this->meses || Auth::user()->nivel > 3) {
            $id = $request->input('idmiembro');
            $check = Asistencias_model::checkAsis(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'), $id, Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'));
            if ($check > 0) {
            } else {
                $grado = Asistencias_model::getGrado($id);
                $datas = array(
                    'idLogia' => Session::get($this->controlador . '.taller'),
                    'gestion' => Session::get($this->controlador . '.gestion'),
                    'idMiembro' => $id,
                    'grado' => $grado,
                    'fechaTenida' => Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'),
                    'fechaAlta' => date("Y-m-d", time()),
                );
                if ($id > 0) {
                    $resu = Asistencias_model::insert($datas);
                    if ($resu > 0) {
                        return response()->json(['success' => 'true', 'Msg' => 'Asistencia asignada correctamente']);
                    } else {
                        return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al procesar los datos ']);
                    }
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Identificador no valido']);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'No se puede modificar, fecha de mas de ' . $this->meses . ' meses de antiguedad']);
        }
    }
    public function get_datasis(Request $request)
    {
        $dia = $request->input('id');
        $mes = $request->input('mes');
        $data = array($this->controlador . '.diat' => $dia, $this->controlador . '.mes' => $mes);
        Session::put($data);
        $fechat = Session::get($this->controlador . '.gestion') . '-' . $mes . '-' . $dia;
        $taller = Session::get($this->controlador . '.taller');
        $salida = Administracion_model::getDataAsis($taller, $fechat);
        return response()->json($salida);
    }
    public function update_datasis(Request $request)
    {
        $id = $request->input('idAsistenciaData');
        $resu = 0;
        if ($id > 0) {
            $tempDate = explode('/', $request->input('fechaCierreForm', ''));
            $fechacie = $tempDate[2] . '-' . $tempDate[1] . '-' . $tempDate[0];
            $datas = array(
                'grado' => $request->input('grado'),
                'fechaCierre' => $fechacie,
                'numeroActa1' => $request->input('numeroActa1'),
                'numeroActa2' => $request->input('numeroActa2'),
                'numeroActa3' => $request->input('numeroActa3'),
            );
            $resu = Asistenciadata_model::where('idAsistenciaData', $id)->update($datas);
        }
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => 'Datos actualizados correctamente', 'Ret' => '']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al procesar los datos ']);
        }
    }
    public function save_fechan(Request $request)
    {
        $taller = Session::get($this->controlador . '.taller');
        if ($taller > 0) {
            $tempDate2 = explode('/', $request->input('fechaTrabajofn', ''));
            $fechanew = $tempDate2[2] . '-' . $tempDate2[1] . '-' . $tempDate2[0];
            $dif = $this->mesesConta($fechanew);
            if ($dif <= $this->meses || Auth::user()->nivel > 3) {
                $check = Asistenciadata_model::checkFecha($taller, $fechanew);
                if ($check) {
                    $tempDate = explode('/', $request->input('fechaCierrefn', ''));
                    $fechacie = $tempDate[2] . '-' . $tempDate[1] . '-' . $tempDate[0];
                    $datas = array(
                        'idLogia' => $taller,
                        'fechaTenida' => $fechanew,
                        'fechaCierre' => $fechacie,
                        'grado' => $request->input('gradofn'),
                        'extra' => '1',
                    );
                    $resu = Asistenciadata_model::insert($datas);
                    if ($resu > 0) {
                        $data = ["success" => 'true', "Msg" => "Datos creados correctamente"];
                    } else {
                        $data = ["success" => 0, "Msg" => "Ocurrio un error al crear los datos"];
                    }
                } else {
                    $data = ["success" => 0, "Msg" => "Identificador no valido"];
                }
            } else { $data = ["success" => 0, "Msg" => "Fecha demasiado anterior, mas de '.$this->meses.' meses de la actual"];}
        } else {
            $data = ["success" => 0, "Msg" => "Error datos faltantes"];
        }
        return response()->json($data);
    }
    public function destroy_fechan(Request $request)
    {
        $id = $request->input('id');
        $resu = 0;
        if ($id > 0) {
            $ften = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
            $dif = $this->mesesConta($ften);
            if ($dif <= $this->meses || Auth::user()->nivel > 3) {
                $dbor = Asistenciadata_model::where('idAsistenciaData', $id)->select('idLogia', 'fechaTenida')->first();
                if (is_null($dbor)) {
                    $resu = 0;
                } else {
                    $re1 = Asistencias_model::where('fechaTenida', $dbor->fechaTenida)->where('idLogia', $dbor->idLogia)->delete();
                    $resu = Asistenciadata_model::where('idAsistenciaData', $id)->delete() + $re1; //deberia borrar datos mas
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'No se puede modificar, fecha de mas de ' . $this->meses . ' meses de antiguedad']);
            }
        }
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => 'Datos eliminados correctamente', 'Ret' => '']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al procesar los datos ']);
        }
    }
    private function mesesConta($ini)
    {
        $fin = date('Y-m-d');
        $datetime1 = new \DateTime($ini);
        $datetime2 = new \DateTime($fin);
        # obtenemos la diferencia entre las dos fechas
        $interval = $datetime2->diff($datetime1);
        # obtenemos la diferencia en meses
        $intervalMeses = $interval->format("%m");
        # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
        $intervalAnos = $interval->format("%y") * 12;
        $res = $intervalMeses + $intervalAnos;
        return $res;
    }
}

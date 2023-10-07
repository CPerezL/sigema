<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\Logs_model;
use App\Models\tramites\Afiliacion_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Tramites_afilia_registro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '062';
    public $controlador = 'tramites_afilia_registro';
    private $error = '';
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
        $data['_folder'] = url('/');
        $data['mesactual'] = date('01/n/Y'); //10/09/2021
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.afilia_registro', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Afiliacion_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val, Auth::user()->nivel);

        $total = Afiliacion_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val, Auth::user()->nivel);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_miembros(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $pal = $request->input('filterRules');
            if (strlen($pal) > 3) {
                $palabra = json_decode($pal);
                $filtro = $palabra[0]->value;
            } else {
                $filtro = '';
            }
            if (strlen($filtro) > 1) {
                $salida = Afiliacion_model::getMiembros($id, $filtro);
            } else {
                $salida = Afiliacion_model::getMiembros($id, '');
            }
            $total = count($salida);
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
            return response()->json($qry2);
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }

    }
    public function save_tramite(Request $request)
    {
        $id = $request->input('id', '0');
        if ($id > 0) {
            if (Afiliacion_model::checkTramite($id)) {
                $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
                $data = array(
                    'idMiembro' => $request->input('id'),
                    'idLogia' => $request->input('LogiaActual'),
                    'idLogiaNueva' => $log,
                    'estado' => 1,
                    'tipo' => $request->input('tipo'),
                    'gradoActual' => $request->input('Grado', 1),
                    'ultimoPago' => $request->input('ultimoPagoDate'),
                    'usuario' => Auth::user()->id,
                );
                $resu = Afiliacion_model::insert($data);
                if ($resu > 0) {
                    Logs_model::insertLog('3', Auth::user()->id, 'Afiliacion', '', $id, $request->input('LogiaActual'));
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function unset_tramite(Request $request)
    {
        $id = $request->input('id', '0');
        if ($id > 0) {
            $resu = Afiliacion_model::where('id', $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function registra_pago(Request $request)
    {
        $id = $request->input('id', 0);
        $resu = 0;
        $msg='';
        if ($id > 0) {
            if ($request->hasFile('fileup')) {
                $archivo = $this->upload_file($request, 'fileup');
            } else {
                $archivo = '';
                $msg = 'archivo invalido o mas grande que 2 Megas';
            }

            if ($request->hasFile('fileup2')) {
                $archivo2 = $this->upload_file($request, 'fileup2');
            } else {
                $archivo2 = '';
                // $msg = 'archivo invalido o mas grande que 2 Megas';
            }

            if ($request->hasFile('fileup3')) {
                $archivo3 = $this->upload_file($request, 'fileup3');
            } else {
                $archivo3 = '';
                // $msg = 'archivo invalido o mas grande que 2 Megas';
            }

            if (strlen($archivo) > 10) {
                $data = array(
                    'archivo' => $archivo,
                    'archivo2' => $archivo2,
                    'archivo3' => $archivo3,
                    'fechaDeposito' => \DateTime::createFromFormat('d/m/Y', $request->input('fDeposito', '00-00-0000'))->format('Y-m-d'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                if ($request->input('okEnviar', 0) == 1) {
                    $data['estado'] = 2; //enviado
                }
                $resu = Afiliacion_model::where('id', $id)->update($data);
                if ($resu == 1) {
                    Logs_model::insertLog('8', Auth::user()->id, 'Registro de pago', '', 0, 0);
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert') . ' ' . $msg]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert') . ' ' . $msg]);
                }
            } else {
                if ($request->input('okEnviar', 0) == 1) {
                    $data = array(
                        'estado' => 2,
                        'fechaDeposito' => \DateTime::createFromFormat('d/m/Y', $request->input('fDeposito', '00-00-0000'))->format('Y-m-d'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                    $resu = Afiliacion_model::where('id', $id)->update($data);
                    return response()->json(['success' => 'true', 'Msg' => 'Tramite enviado correctamente']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => "Archivo invalido ' . $this->error . '"]);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function upload_file(Request $request, $fileup, $folder = 'media/tramites')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$fileup" => 'required|mimes:png,jpg,jpeg,pdf|max:2048',
            ]
        );
        if ($request->file($fileup)) {
            try {
                $file = $request->file($fileup);
                $filename = time() . md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension(); //nombre nuevo
                // File upload location
                $location = $folder;
                // Upload file
                $file->move($location, $filename);
                return $filename;
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 1;
        }
    }
    public function cambia_datos(Request $request)
    {
        $mes = 'Afiliacion exitosa y actualizacion de datos';
        $id = $request->input('id');
        $task = $request->input('task');
        if ($id > 0 && $task > 1) {
            if ($task == 4) //aporbacion de afiliacion
            {
                $data = array(
                    'estado' => 3,
                    'fechaAprobacionGDR' => date('Y-m-d h:m:s'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $dat = Afiliacion_model::where('id', $id)->first();
                $resu = Afiliacion_model::where('id', $id)->update($data);

                if ($dat->tipo == 1) {
                    $datam = array(
                        'LogiaActual' => $dat->idLogiaNueva,
                        'fechaAfiliacion' => date('Y-m-d'),
                    );
                    $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                    $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                } elseif ($dat->tipo == 2) {
                    $datam = array(
                        'LogiaActual' => $dat->idLogiaNueva,
                        'LogiaAfiliada' => $dat->idLogia,
                        'fechaAfiliacion' => date('Y-m-d'),
                    );
                    $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                    $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                } elseif ($dat->tipo == 3) {
                    $datam = array(
                        'LogiaAfiliada' => $dat->idLogiaNueva,
                        'fechaAfiliacion' => date('Y-m-d'),
                    );
                    $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                    $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                }
            }
            $res1 = $resu + $datos;
            if ($res1 == 1) {
                if ($task == 4) {
                    Logs_model::insertLog('4', Auth::user()->id, 'Afiliacion realizada con exito', '', 0, 0);
                } elseif ($task == 3) {
                    Logs_model::insertLog('4', Auth::user()->id, 'Rechazo de afiliacion', '', 0, 0);
                } else {
                    $mes = 'No encontrado';
                }

                return response()->json(['success' => 'true', 'Msg' => $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }

        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }

    }
}

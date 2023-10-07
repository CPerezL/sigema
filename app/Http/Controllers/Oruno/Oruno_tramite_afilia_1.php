<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\Logs_model;
use App\Models\oruno\Afiliacion_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_afilia_1 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '062';
    public $controlador = 'oruno_tramite_afilia_1';
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
        return view('oruno.afilia_1', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Afiliacion_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val, '0,1');
        $total = Afiliacion_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val, '0,1');
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
                    'estado' => 0,
                    'tipo' => $request->input('tipo', 1),
                    'gradoActual' => $request->input('Grado', 1),
                    'ultimoPago' => $request->input('ultimoPagoDate'),
                    'usuario' => Auth::user()->id,
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Afiliacion_model::insert($data);
                if ($resu > 0) {
                    //($tipo, $usuario, $accion = '', $params = '', $miembro = 0, $logia = 0, $valle = 0)
                    // dd('3',Auth::user()->id, 'Afiliacion', '', $id, $log);
                    Logs_model::insertLog('3', Auth::user()->id, 'Afiliacion', '', $id, $log);
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Tramite ya existe']);
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
    public function registra_datos(Request $request)
    {
        $id = $request->input('id', 0);
        $resu = 0;
        $msg = '';
        if ($id > 0) {
            if ($request->hasFile('fileup')) {
                $archivo = $this->upload_file($request, 'fileup');
            } else {
                $archivo = '';
                $msg = 'archivo invalido o mas grande que 2 Megas';
            }
            if (strlen($archivo) > 10) {
                $data = array(
                    'archivo' => $archivo,
                    'actaAprobacionLogia' => $request->input('actaAprobacionLogia'),
                    'fechaAprobacionLogia' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobacionLogia', '00-00-0000'))->format('Y-m-d'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                if ($request->input('okEnviar', 0) == 1) {
                    $data['estado'] = 2; //enviado
                }
                $resu = Afiliacion_model::where('id', $id)->update($data);
                if ($resu == 1) {
                    Logs_model::insertLog('9', Auth::user()->id, 'Registro de Tramite de afiliacion', '', 0, 0);
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert') . ' ' . $msg]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert') . ' ' . $msg]);
                }
            } else {
                if ($request->input('okEnviar', 0) == 1) {
                    $data = array(
                        'estado' => 2,
                        'fechaAprobacionLogia' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobacionLogia', '00-00-0000'))->format('Y-m-d'),
                        'actaAprobacionLogia' => $request->input('actaAprobacionLogia'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                    $resu = Afiliacion_model::where('id', $id)->update($data);
                    return response()->json(['success' => 'true', 'Msg' => 'Tramite enviado correctamente']);
                } else {
                    $data = array(
                        'fechaAprobacionLogia' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobacionLogia', '00-00-0000'))->format('Y-m-d'),
                        'actaAprobacionLogia' => $request->input('actaAprobacionLogia'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                    $resu = Afiliacion_model::where('id', $id)->update($data);
                    return response()->json(['success' => 'true', 'Msg' => "Cambios grabados no se cargo archivo"]);
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
}

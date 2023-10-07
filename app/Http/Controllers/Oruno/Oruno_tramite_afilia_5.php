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

class Oruno_tramite_afilia_5 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '099';
    public $controlador = 'oruno_tramite_afilia_5';
    private $error = '';
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('sites');
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
        return view('oruno.afilia_5', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Afiliacion_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val, '7');
        $total = Afiliacion_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val, '7');
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function cambia_datos(Request $request)
    {
        $mes = 'Afiliacion exitosa y actualizacion de datos';
        $id = $request->input('id');
        $task = $request->input('task');
        if ($id > 0 && $task > 1) {
            if ($task == 4) {
                $data = array(
                    'estado' => 5,
                    'fechaAprobacion' => date('Y-m-d h:m:s'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Afiliacion_model::where('id', $id)->update($data);
            }
            if ($resu == 1) {
                if ($task == 4) {
                    Logs_model::insertLog('4', Auth::user()->id, 'Deposito correcto', '', 0, 0);

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
    public function cambia_logia(Request $request)
    {
        $mes = 'Afiliacion exitosa y actualizacion de datos';
        $id = $request->input('id');
        $tipo = $request->input('tipo');
        if ($id > 0 && $tipo > 0) {
            $datos = Afiliacion_model::select('idMiembro', 'idLogia', 'idLogiaNueva', 'tipo')->find($id);
            $data = array(
                'estado' => 6,
                'activo' => 1,
                'fechaAprobacion' => date('Y-m-d h:m:s'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Afiliacion_model::where('id', $id)->update($data);
            if ($datos->tipo == 1) {
                $mdata = array(
                    'LogiaActual' => $datos->idLogiaNueva,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            } elseif ($datos->tipo == 2) {
                $mdata = array(
                    'LogiaActual' => $datos->idLogiaNueva,
                    'LogiaAfiliada' => $datos->idLogia,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            } elseif ($datos->tipo == 3) {
                $mdata = array(
                    'LogiaAfiliada' => $datos->idLogia,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            }
            $resu = Membrecia_model::where('id',$datos->idMiembro)->update($mdata);
            if ($resu == 1) {
                return response()->json(['success' => 'true', 'Msg' => $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

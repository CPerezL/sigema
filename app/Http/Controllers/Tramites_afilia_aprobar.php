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

class Tramites_afilia_aprobar extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '099';
    public $controlador = 'tramites_afilia_aprobar';
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
        return view('tramites.afilia_aprobar', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Afiliacion_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val, Auth::user()->nivel, 2);
        $total = Afiliacion_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val, Auth::user()->nivel, 2);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
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
                // $dat = Afiliacion_model::where('id', $id)->first();
                $resu = Afiliacion_model::where('id', $id)->update($data);

                // if ($dat->tipo == 1) {
                //     $datam = array(
                //         'LogiaActual' => $dat->idLogiaNueva,
                //         'fechaAfiliacion' => date('Y-m-d'),
                //     );
                //     $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                //     $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                // } elseif ($dat->tipo == 2) {
                //     $datam = array(
                //         'LogiaActual' => $dat->idLogiaNueva,
                //         'LogiaAfiliada' => $dat->idLogia,
                //         'fechaAfiliacion' => date('Y-m-d'),
                //     );
                //     $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                //     $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                // } elseif ($dat->tipo == 3) {
                //     $datam = array(
                //         'LogiaAfiliada' => $dat->idLogiaNueva,
                //         'fechaAfiliacion' => date('Y-m-d'),
                //     );
                //     $mes = $dat->idMiembro . '-' . $dat->idLogiaNueva . '-' . $dat->idLogia;
                //     $datos = Membrecia_model::where('id', $dat->idMiembro)->update($datam);
                // }
            }
            $datos=3;
            $res1 = $resu + $datos;
            if ($res1 == 1) {
                if ($task == 4) {
                    Logs_model::insertLog('4', Auth::user()->id, 'Tramite aprobado ahora puede pagar los derechos', '', 0, 0);
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

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\asistencias\Asistenciaextra_model;
use App\Models\asistencias\Asistencias_model;
use App\Models\Parametros_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Asistencias_extras extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '060';
    public $controlador = 'asistencias_extras';
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
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['motivos'] = Parametros_model::getParamsArray(1); //---+++
        $data['alllogias'] = Logias_model::getlogiasArray(0);
        Session::put($this->controlador . '.gestion', date('Y'));
        /*     varibles de pagina* */
        return view('asistencias.extras', $data);
    }
    public function get_datos(Request $request)
    {
        $pagina = $request->input('page', 1);
        $cantidad = $request->input('rows', 20);
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $grado = Session::get($this->controlador . '.grado');
        $estado = Session::get($this->controlador . '.estado', 1);
        $palabra = Session::get($this->controlador . '.palabra');
        $salida = Asistenciaextra_model::getListaMiembros($pagina, $cantidad, $valle, $taller, $grado, $estado, $palabra);
        $total = Asistenciaextra_model::getTotalListaMiembros($valle, $taller, $grado, $estado, $palabra);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_visitas(Request $request)
    {
        $id = Session::get($this->controlador . '.idm', 0);
        if ($id > 0) {
            $salida = Asistenciaextra_model::getVisitas($id, Session::get($this->controlador . '.gestion'));
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function set_visita(Request $request)
    {
        $idm = $request->input('idm');
        $fvisita = $request->input('fvisita');
        $motivo = $request->input('motivo', 0);
        $taller = $request->input('logiaVisita', 0);
        $grado = Asistencias_model::getGrado($idm);
        if (strlen($fvisita) > 6 && $idm > 0) {
            $exf = explode('/', $fvisita);
            $fecha = "$exf[2]-$exf[1]-$exf[0]";
            $datas = array(
                'idMiembro' => $idm,
                'grado' => $grado,
                'idLogia' => $taller,
                'motivo' => $motivo,
                'fechaExtra' => $fecha,
                'fechaAlta' => date("Y-m-d"),
                'gestion' => $exf[2],
            );
            $resu = Asistenciaextra_model::insert($datas);

            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
        }
    }
    public function destroy_visita(Request $request)
    {
        $id = $request->input('id', '0');
        if ($id > 0) {
            $resu = Asistenciaextra_model::where('idExtra', $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
        }
    }
}

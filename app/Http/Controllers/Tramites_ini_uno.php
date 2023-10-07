<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_uno extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //private $urlqr='https://hh.granlogiadebolivia.bo/';
    private $urlqr = 'https://develuser.granlogiadebolivia.bo/';
    public $idmod = '080';
    public $controlador = 'tramites_ini_uno';
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
        $data['_folder'] = url('/') . '/';
        $data['palabra'] = Session::get($this->controlador . '.palabra', '');
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.ini_uno', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getRegistros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log,$val, 1);
        $total = Iniciacion_model::getNumRegistros(Session::get($this->controlador . '.palabra', ''), $log, $val,1);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('idTra', 0);
        if ($tramite > 0) {
            $salida = Iniciacion_model::getTramiteIni($tramite, 1);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait"']);
        }
    }
    public function update_tramite(Request $request)
    {
        $id = $request->input('idTramite', 0);
        if ($id > 0) {
            $ok1 = $request->input('okcurriculump', 0);
            $ok2 = $request->input('okcompromisop', 0);
            $ok3 = $request->input('okinformefamiliar', 0);
            $ok4 = $request->input('okactainsinuacion', 0);
            $ok5 = $request->input('okactaaprobacion', 0);
            $ok6 = $request->input('okactainforme', 0);
            $okey = $ok1 + $ok2 + $ok3 + $ok4 + $ok5 + $ok6;
            if ($okey == 6) //si marco todo ok
            {
                $data = array(
                    'okcurriculump' => $ok1,
                    'okcompromisop' => $ok2,
                    'okinformefamiliar' => $ok3,
                    'okactainsinuacion' => $ok4,
                    'okactaaprobacion' => $ok5,
                    'okactainforme' => $ok6,
                    'nivelActual' => 2,
                    'fechaModificacion' => date('Y-m-d'),
                );
                Iniciacion_model::where('idTramite', $id)->update($data);
                $salida = ['success' => 0, 'Msg' => "Tramite validado correctamente"];
            } else {
                $data = array(
                    'okcurriculump' => $ok1,
                    'okcompromisop' => $ok2,
                    'okinformefamiliar' => $ok3,
                    'okactainsinuacion' => $ok4,
                    'okactaaprobacion' => $ok5,
                    'okactainforme' => $ok6,
                    'fechaModificacion' => date('Y-m-d'),
                );
                Iniciacion_model::where('idTramite', $id)->update($data);
                $salida = ['success' => 'true', 'Msg' => "Faltan documentos que revisar"];
            }
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }

        return response()->json($salida);
    }
}

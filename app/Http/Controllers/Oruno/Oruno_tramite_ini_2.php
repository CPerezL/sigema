<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\oruno\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_ini_2 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '080';
    public $controlador = 'oruno_tramite_ini_2';
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
        return view('oruno.ini_2', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getRegistros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 1);
        $total = Iniciacion_model::getNumRegistros(Session::get($this->controlador . '.palabra', ''), $log, $val, 1);
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
            // $ok1 = $request->input('okcurriculump', 0);
            $ok2 = $request->input('okcompromisop', 0);
            $ok3 = $request->input('okcurriculump', 0);
            $ok4 = $request->input('okactainsinuacion', 0);
            $ok5 = $request->input('okactaaprobacion', 0);
            $ok6 = $request->input('okactainforme', 0);
            $okey = $ok2 + $ok3 + $ok4 + $ok5 + $ok6;

            if ($okey == 5) {
                $data = array(
                    // 'okcurriculump' => $ok1,
                    'okcompromisop' => $ok2,
                    'okcurriculump' => $ok3,
                    'okactainsinuacion' => $ok4,
                    'okactaaprobacion' => $ok5,
                    'okactainforme' => $ok6,
                    'nivelActual' => 2,
                    'task' => 3,
                    'fechaModificacion' => date('Y-m-d'),
                );
                Iniciacion_model::where('idTramite', $id)->update($data);
                $salida = ['success' => 'true', 'Msg' => "Tramite revisado completamente"];
            } else {
                if ($okey > 0) {
                    $task = 1;
                } else {
                    $task = 0;
                }
                $data = array(
                    // 'okcurriculump' => $ok1,
                    'okcompromisop' => $ok2,
                    'okcurriculump' => $ok3,
                    'okactainsinuacion' => $ok4,
                    'okactaaprobacion' => $ok5,
                    'okactainforme' => $ok6,
                    'task' => $task,
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

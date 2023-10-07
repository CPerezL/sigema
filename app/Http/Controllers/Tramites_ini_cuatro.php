<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_cuatro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '094';
    public $controlador = 'tramites_ini_cuatro';
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
        return view('tramites.ini_cuatro', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $salida = Iniciacion_model::getTramitesListos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 4);
        foreach ($salida as $key => $mod) {
            $hoy = date('Y-m-d');
            $nf = date("Y-m-d", strtotime($mod->fechaCircular . "+ 30 days"));
            if ($hoy >= $nf) {
                $salida[$key]->habil = 1;
                $salida[$key]->observacion = 'Ya se puede aprobar balotaje';
                $salida[$key]->finCircular = $nf;
            } else {
                $salida[$key]->habil = 0;
                $salida[$key]->observacion = 'Aun no son 30 dias de Circular';
                $salida[$key]->finCircular = $nf;
            }
        }
        $total = Iniciacion_model::getNumTramitesListos(Session::get($this->controlador . '.palabra', ''), $log, $val, 4);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('idTra', 0);
        if ($tramite > 0) {
            $salida = Iniciacion_model::getTramiteIni($tramite, 3);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait"']);
        }
    }
    public function update_tramite(Request $request)
    {
        $id = $request->input('idTramite', 0);
        $trm1 = $request->input('okInformeSocial', '0');
        $trm2 = $request->input('okInformeLaboral', '0');
        $resu = 0;
        if ($id > 0) {
            if ($trm1 > 0 && $trm2 > 0) {
                $data = array(
                    'nivelActual' => 5,
                    'okInformeSocial' => $trm1,
                    'okInformeLaboral' => $trm2,
                    'fechaModificacion' => date('Y-m-d'),
                );
            } else {
                $data = array(
                    'okInformeSocial' => $trm1,
                    'okInformeLaboral' => $trm2,
                    'fechaModificacion' => date('Y-m-d'),
                );
            }
            Iniciacion_model::where('idTramite', $id)->update($data);
            $salida = ['success' => 'true', 'Msg' => "Tramite validado correctamente"];
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }

        return response()->json($salida);
    }
}

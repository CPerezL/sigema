<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Models\tramites\Ceremonias_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_cinco extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '095';
    public $controlador = 'tramites_ini_cinco';
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
        //$data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('tramites.ini_cinco', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getTramitesListos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 5);
        $total = Iniciacion_model::getNumTramitesListos(Session::get($this->controlador . '.palabra', ''), $log, $val, 5);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
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
        if ($id > 0) {
            $data = array(
                'fechaIniciacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', '00-00-0000'))->format('Y-m-d'),
                'nivelActual' => 6,
                'fechaModificacion' => date('Y-m-d'),
            );
            //$fc = $request->input('fechaIniciacion', '00-00-0000');
            //Ceremonias_model::updateCeremonia(Session::get($this->controlador . '.taller', ''), $fc, 1);
            Iniciacion_model::where('idTramite', $id)->update($data);
            $salida = ['success' => 'true', 'Msg' => "Tramite validado correctamente"];
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }

        return response()->json($salida);
    }
}

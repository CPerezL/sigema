<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\valles\Afiliados_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class V_afiliados extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '046';
    public $controlador = 'v_afiliados';
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
        /*     variables de pagina     */
        return view('valles.afiliados', $data);
    }
    public function get_datos(Request $request)
    {
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $task = Session::get($this->controlador . '.task', 0);
        if (($log > 0 || $val > 0) && $task > 0) {
            $salida = Afiliados_model::getListas($val, $log, Session::get($this->controlador . '.grado'), Session::get($this->controlador . '.estado'), Session::get($this->controlador . '.resul'));
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_valles(Request $request)
    {
        $val = self::validar('idValle', $request->input('valleid', 0));
        $or = self::validar('idOriente', $request->input('oriente', 0));
        $cond = 'borrado=0';
        if ($or > 0) {
            $cond .= " AND idOriente=$or";
        }

        if ($val > 0) {
            $cond .= " AND idValle=$val";
        }

        $qry = Valles_model::select('idValle', 'valle')->whereraw($cond)->orderBy('valle', 'asc')->get();
        if ($val == 0) {
            $qry[] = (object) ['valle' => 'Todos los Valles', 'idValle' => 0];
        }
        //"selected":"true"
        if (count($qry) == 1) {
            $qry[0]->selected = 'true';
        }

        return $qry->toJson();
    }
    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', 0));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);

        return $qry;
    }
    public function set_datos(Request $request)
    {
        $val = $request->input('valle');
        $tal = $request->input('taller');
        $grado = $request->input('grado');
        $estado = $request->input('estado', 0);
        $resul = $request->input('resul', 0);
        $data = array($this->controlador . '.task' => 1,$this->controlador . '.valle' => $val, $this->controlador . '.taller' => $tal, $this->controlador . '.grado' => $grado, $this->controlador . '.estado' => $estado, $this->controlador . '.resul' => $resul);
        Session::put($data);
        return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Users_logs_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Logs_usuarios extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '031';
    public $controlador = 'logs_usuarios';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina     */
        return view('admin.logs_usuarios', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $valle = Session::get($this->controlador . '.valle');
        $taller = Session::get($this->controlador . '.taller');
        $qry = Users_logs_model::getItems($page, $rows, $valle, $taller, Session::get($this->controlador . '.palabra'));
        $total = Users_logs_model::getNumItems($valle, $taller, Session::get($this->controlador . '.palabra'));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', 0));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
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
        return $qry->toJson();
    }
}

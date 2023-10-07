<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Afiliacion_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Tramites_afilia_ver_depositos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '052';
    public $controlador = 'tramites_afilia_ver_depositos';
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
        $data['_controller'] = $this->controlador;
        $data['mesactual'] = date('01/n/Y'); //10/09/2021
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.afilia_ver_depositos', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $qry = Afiliacion_model::getDepositos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, Auth::user()->nivel);
        $total = Afiliacion_model::getNumDepositos(Session::get($this->controlador . '.palabra', ''), $log, $val, Auth::user()->nivel);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

}

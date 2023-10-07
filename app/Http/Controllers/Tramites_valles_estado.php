<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Estado_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Tramites_valles_estado extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '032';
    public $controlador = 'tramites_valles_estado';
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
        $data['_folder'] = ''; //---
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.valles_estado', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Estado_model::getItems($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val);
        $total1 = Estado_model::getTotalItemsIni(Session::get($this->controlador . '.palabra'), $log, $val);
        $total2 = Estado_model::getTotalItemsAum(Session::get($this->controlador . '.palabra'), $log, $val);
        $total3 = Estado_model::getTotalItemsExa(Session::get($this->controlador . '.palabra'), $log, $val);
        $total = $total1 + $total2 + $total3;
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
}

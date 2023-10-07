<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use App\Models\admin\Miembrostipo_model;
use Illuminate\Http\Request;
use Session;
use Auth;

class V_membrecia extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '042';
    public $controlador = 'v_membrecia';
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
        self::iniciarModuloAll();
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        Session::put($this->controlador . '.valle', $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['miembros'] = Miembrostipo_model::getMiembrosArray();
        /*     variables de pagina     */
        return view('valles.membrecia', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = Session::get($this->controlador . '.valle');
        $log = Session::get($this->controlador . '.taller');
        $qry = Membrecia_model::getItemsAll($page, $rows, Session::get($this->controlador . '.palabra', ''), 0, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 100));
        $total = Membrecia_model::getNumItemsAll(Session::get($this->controlador . '.palabra', ''), 0, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 100));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

}

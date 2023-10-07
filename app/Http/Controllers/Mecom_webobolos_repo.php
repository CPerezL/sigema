<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Webobolos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_webobolos_repo extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '066';
    public $controlador = 'mecom_webobolos_repo';
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
        $data['desde'] = date("d/m/Y"); //6/1/2012'
        $data['hasta'] = date("d/m/Y");
        // Session::put($this->controlador . '.gestion', date('Y'));
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        /*     varibles de pagina* */
        return view('mecom.webobolos_repo', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        $valle = Session::get($this->controlador . '.valle');
        $taller = Session::get($this->controlador . '.taller');
        $salida = Webobolos_model::getListaWebolos($valle, $taller, $desde, $hasta);
        $total = 0;//$this->mecom_webobolos_model->getTotalItems($valle, $taller, $desde, $hasta);

        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function set_datos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $valle = $request->input('valle', 0);
        $taller = $request->input('taller', 0);
        if ($valle > 0 || $taller > 0) {
            $data = array($this->controlador . '.valle' => $valle, $this->controlador . '.taller' => $taller, $this->controlador . '.desde' => $desde, $this->controlador . '.hasta' => $hasta);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait..']);
        }
    }

}

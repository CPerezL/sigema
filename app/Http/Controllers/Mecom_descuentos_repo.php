<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Descuentos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_descuentos_repo extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '070';
    public $controlador = 'mecom_descuentos_repo';
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
        $data['year'] = date("Y");
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('mecom.descuentos_repo', $data);
    }
    public function get_datos(Request $request)
    {
        $gestion = Session::get($this->controlador . '.gestion');
        $valle = Session::get($this->controlador . '.valle');
        if ($gestion > 0 && $valle > 0) {
            $salida = Descuentos_model::getListaDescuentos($gestion, $valle);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }

    public function set_datos(Request $request)
    {
        $gestion = $request->input('gestion');
        $valle = $request->input('valle', 0);
        if ($valle > 0 && $gestion > 2000) {
            $data = array($this->controlador . '.gestion' => $gestion, $this->controlador . '.valle' => $valle);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait..']);
        }
    }

}

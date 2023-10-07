<?php

namespace App\Http\Controllers;

use App\Models\admin\Orientes_model;
use App\Models\admin\Valles_model;
use App\Models\comap\Comap_listas_model;
use App\Models\Config_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Comap_reporte extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '014';
    public $controlador = 'comap_reporte';
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
        /*     variables de pagina* */
        return view('comap.comap_reporte', $data);
    }
    public function get_datos(Request $request)
    {
        if (Session::get($this->controlador . '.valle', 0) > 0) {
            Comap_listas_model::setMesesMora(Config_model::getMesesMora());
            $qry = Comap_listas_model::getReporte(Session::get($this->controlador . '.valle', 0), Session::get($this->controlador . '.resul', 0));
            $qry2 = (object) ['total' => 0, 'rows' => $qry];
            return response()->json($qry2);
        } else {
            return response()->json(['total' => 0, 'rows' => '']);
        }
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
    public function get_orientes(Request $request)
    {
        $or = self::validar('idOriente', $request->input('oriente', 0));
        if ($or > 0) {
            $qry = Orientes_model::select('idOriente', 'oriente')->where('idOriente', '=', $or)->orderBy('oriente', 'asc')->get();
        } else {
            $qry = Orientes_model::select('idOriente', 'oriente')->orderBy('oriente', 'asc')->get();
            $qry[] = (object) ['oriente' => 'Todos los Orientes', 'idOriente' => 0];
        }
        return $qry->toJson();
    }
    public function set_datos(Request $request)
    {
        $val = self::validar('idValle', $request->input('valle', 0));
        $res = $request->input('resul', 0);
        if ($val > 0) {
            Session::put($this->controlador . '.valle', $val);
            Session::put($this->controlador . '.resul', $res);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
}

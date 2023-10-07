<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Orientes_model;
use App\Models\admin\Valles_model;
use App\Models\oruno\Logias_membrecia_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Logias_membrecia extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '010';
    public $controlador = 'logias_membrecia';
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
        if (Auth::user()->nivel > 3) {
            $data['editable'] = '';
        } else {
            $data['editable'] = ' disabled="true"';
        }
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        // $data['miembros'] = Miembrostipo_model::getMiembrosArray();
        // dd($data['miembros']);
        /*     varibles de pagina* */
        return view('oruno.logias_membrecia', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        // Logias_membrecia_model::$regular = Config_model::regular();
        $qry = Logias_membrecia_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));
        $total = Logias_membrecia_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function get_logias(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $salida = Logias_membrecia_model::getLogiasMiembro($id);
            return response()->json($salida);
        } else {
            return response()->json(['']);
        }
    }
    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Logias_membrecia_model::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function save_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $tipo = $request->input('tipo');
            if ($tipo == 0) {
                Logias_membrecia_model::where('idMiembro', $id)->update(['tipo' => 1]);
            }
            $data = array(
                'idMiembro' => $id,
                'idLogia' => $request->input('nulogia'),
                'tipo' => $request->input('tipo'),
                'usuario' => Auth::user()->id,
            );
            $resu = Logias_membrecia_model::insert($data);
        } else { $resu = 0;}
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }

    }

}

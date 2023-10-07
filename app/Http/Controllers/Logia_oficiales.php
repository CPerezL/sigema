<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\sistema\Oficialidades_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Logia_oficiales extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '059';
    public $controlador = 'logia_oficiales';
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
        $data['year'] = date('Y'); //---
        Session::put($this->controlador . '.gestion', date('Y'));
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('sistema.logia_oficiales', $data);
    }
    public function get_datos(Request $request)
    {
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $ges = Session::get($this->controlador . '.gestion');
        $salida = Oficialidades_model::getOficiales($log, $ges);
        $total = 14;
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_miembros(Request $request)
    {
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($log > 0) {
            $mostrar = Session::get($this->controlador . '.mostrar',1);
            $filter = $request->input('filterRules');
            $page = $request->input('page', 1);
            $rows = $request->input('rows', 20);
            if (strlen($filter) > 2) {
                $filtera = json_decode($filter);
                $salida = Oficialidades_model::getMiembros($page, $rows, $log, $filtera[0]->value,$mostrar);
            } else {
                $salida = Oficialidades_model::getMiembros($page, $rows, $log,'',$mostrar);
            }
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }

    public function update_cargo(Request $request)
    {
        $id = $request->input('id', '0');
        if ($id > 0) {
            $cargo = $request->input('cargo');
            $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            $gestion = Session::get($this->controlador . '.gestion');
            $idex = Oficialidades_model::checkCargo($gestion, $taller, $cargo);
            if ($idex > 0) {
                $data = array('idmiembro' => $id);
                $resu = Oficialidades_model::where('id', $idex)->update($data);
            } else {
                $data = array('idoficial' => $cargo, 'idmiembro' => $id, 'idlogia' => $taller, 'idmiembro' => $id, 'gestion' => $gestion, 'tipo' => 1);
                $resu = Oficialidades_model::insert($data);
            }
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function destroy_cargo(Request $request)
    {
        $id = $request->input('ido', '0');
        if ($id > 0) {
            $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            $gestion = Session::get($this->controlador . '.gestion');
            $resu = Oficialidades_model::where('idoficial', $id)->where('gestion', $gestion)->where('idlogia', $taller)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function mostrar(Request $request)
    {
        $filtro = $request->input('filtro', 0);
        Session::put($this->controlador . '.mostrar', $filtro);
        return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
    }
}

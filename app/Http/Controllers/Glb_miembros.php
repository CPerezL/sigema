<?php

namespace App\Http\Controllers;

use App\Models\glb\Comisiones_model;
use App\Models\glb\Gestiones_model;
use App\Models\glb\Miembros_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Glb_miembros extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '037';
    public $controlador = 'glb_miembros';
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
        return view('glb.miembros', $data);
    }
    public function get_datos(Request $request)
    {
        // dd(Session::get($this->controlador . '.comision'), Session::get($this->controlador . '.gestion'));
        $qry = Miembros_model::getItems(Session::get($this->controlador . '.comision'), Session::get($this->controlador . '.gestion'));
        if (is_null($qry)) {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => $qry];
        }
        return response()->json($qry2);
    }
    public function get_comisiones(Request $request)
    {
        $qry = Comisiones_model::select('idComision as id', 'nombre as text')->orderBy('orden', 'asc')->get();
        $qry[] = (object) ['text' => 'SELECCIONAR COMISION', 'id' => 0];
        return $qry->toJson();
    }
    public function get_gestiones(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            Session::put($this->controlador . '.comision', $id);
            $qry = Gestiones_model::select('idGestion as id', 'descripcion as text')->where('tipo', $id)->orderBy('desde', 'asc')->orderBy('hasta', 'asc')->get();
            if (is_null($qry)) {
                $inicio = new stdClass();
                $inicio->id = 0;
                $inicio->text = 'NINGUNA GESTION';
                $qry[0] = $inicio;
            } else {
                $qry[] = (object) ['text' => 'SELECCIONAR GESTION', 'id' => 0];
            }
            return $qry->toJson();
        } else {
            return response()->json([]);
        }

    }
    public function get_miembros(Request $request)
    {
        if (Session::get($this->controlador . '.gestion') > 0) {
            $filter = $request->input('filterRules');
            if (strlen($filter) > 2) {
                $qry = Miembros_model::getMiembros($filter); //aumetar materno y nombre
            } else {
                $qry = Miembros_model::getMiembros();
            }
            $qry2 = (object) ['total' => 0, 'rows' => $qry];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function update_cargo(Request $request)
    {
        $id = $request->input('id');

        if ($id > 0) {
            $cargo = $request->input('cargo');
            $res = Miembros_model::updateCargo(Session::get($this->controlador . '.gestion'), $id, $cargo);
            if ($res > 0) {
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
        $id = $request->input('ido');
        if ($id > 0) {
            $resu = Miembros_model::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

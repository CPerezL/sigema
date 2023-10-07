<?php

namespace App\Http\Controllers;

use App\Models\glb\Comisiones_model;
use App\Models\glb\Gestiones_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Glb_gestiones extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '036';
    public $controlador = 'glb_gestiones';
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
        $data['comisiones'] = Comisiones_model::getComisiones();
        /*     variables de pagina     */
        return view('glb.gestiones', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Gestiones_model::getItems(Session::get($this->controlador . '.comision'));
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        if ($desde > 1950 && $hasta > 1950) {
            $data = array(
                'tipo' => Session::get($this->controlador . '.comision'),
                'descripcion' => $request->input('descripcion'),
                'desde' => $desde,
                'hasta' => $hasta,
            );
            $res = Gestiones_model::insert($data);
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }
    public function update_datos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $id = $request->input('id');
        if ($desde > 1950 && $hasta > 1950 && $id > 0) {
            $data = array(
                'descripcion' => $request->input('descripcion'),
                'desde' => $desde,
                'hasta' => $hasta,
            );
            $res = Gestiones_model::where('idGestion', $id)->update($data);
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $resu = Gestiones_model::where("idGestion", $id)->delete();
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

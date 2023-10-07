<?php

namespace App\Http\Controllers;

// use App\Models\admin\Orientes_model;
// use App\Models\Parametros_model;
use App\Models\sistema\Errores_model;
use App\Models\valles\Cargos_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class V_cargos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '017';
    public $controlador = 'v_cargos';
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
        $data['comisiones'] = Cargos_model::getComisiones();
        /*     variables de pagina     */
        return view('valles.cargos', $data);
    }
    public function get_datos(Request $request)
    {
        $val = Session::get($this->controlador . '.comision');
        $qry = Cargos_model::getItems($val);
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $orden = $request->input('orden');
        $cargo = $request->input('cargo');
        if (strlen($orden) > 0 && strlen($cargo) > 4) {
            $data = array(
                'tipo' => Session::get($this->controlador . '.comision'),
                'cargo' => $cargo,
                'orden' => $orden,
            );
            $resu = Cargos_model::insert($data);
            if ($resu > 0) {
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
        $orden = $request->input('orden');
        $cargo = $request->input('cargo');
        $id = $request->input('id');
        if (strlen($orden) > 0 && strlen($cargo) > 4 && $id > 0) {
            $data = array(
                'cargo' => $cargo,
                'orden' => $orden,
            );

            $resu = Cargos_model::where("idCargo", $id)->update($data);
            if ($resu > 0) {
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
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Cargos_model::where("idCargo", $id)->delete();
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

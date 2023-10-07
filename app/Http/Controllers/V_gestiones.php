<?php

namespace App\Http\Controllers;

// use App\Models\admin\Orientes_model;
use App\Models\admin\Valles_model;
use App\Models\valles\Gestiones_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class V_gestiones extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '021';
    public $controlador = 'v_gestiones';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        /*     variables de pagina     */
        return view('valles.gestiones', $data);
    }
    public function get_datos(Request $request)
    {
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $qry = Gestiones_model::getItems($val);
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        if ($desde > 1950 && $hasta > 1950) {
            $data = array(
                'valle' => Session::get($this->controlador . '.valle'),
                'descripcion' => $request->input('descripcion'),
                'desde' => $desde,
                'hasta' => $hasta,
            );
            $resu = Gestiones_model::insert($data);
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
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $id = $request->input('id');
        if ($desde > 1950 && $hasta > 1950 && $id > 0) {
            $data = array(
                'descripcion' => $request->input('descripcion'),
                'desde' => $desde,
                'hasta' => $hasta,
            );
            $resu = Gestiones_model::where("idGestion", $id)->update($data);
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

<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\mecom\Tramites_montos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Mecom_montos_tramites extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '026';
    public $controlador = 'mecom_montos_tramites';
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
        $data['tipos'] = Tramites_montos_model::getTiposTramite();
        /*     variables de pagina     */
        return view('mecom.montos_tramites', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Tramites_montos_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $id = $request->input('idValle', 0);
        $miembro = $request->input('miembro', 'Regular');
        if (strlen($miembro) > 3) {
            $miem = explode('_', $miembro);
            $orden = Tramites_montos_model::getOrden($id, $miem[0]);
            $glb = $request->input('montoglb', 0);
            $gdr = $request->input('montogdr', 0);
            $comap = $request->input('montocomap', 0);
            $vall = Valles_model::getVallesArray(Auth::user()->idOriente, Auth::user()->idValle);
            $data['idValle'] = $id;
            $data['concepto'] = 'Derecho ' . $miem[1];
            $data['miembro'] = $miem[1];
            $data['usuario'] = Auth::user()->id;
            $data['tipo'] = $miem[0];
            $data['orden'] = $orden;
            if ($id > 0) {
                $data['valle'] = $vall[$id];
            } else {
                $data['valle'] = 'General';
            }
            //----GLB
            $data['entidad'] = 1;
            $data['monto'] = $glb;
            $res1 = Tramites_montos_model::insert($data);
            //---  GDR
            $data['monto'] = $gdr;
            $data['entidad'] = 2;
            $res2 = Tramites_montos_model::insert($data);
            //---- COMAP
            $data['monto'] = $comap;
            $data['entidad'] = 3;
            $res3 = Tramites_montos_model::insert($data);
            $res = $res1 + $res2 + $res3;
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
        $id = $request->input('idValle', 0);
        $miembro = $request->input('miembro', 'Regular');
        if (strlen($miembro) > 3) {
            $glb = $request->input('montoglb', 0);
            $gdr = $request->input('montogdr', 0);
            $comap = $request->input('montocomap', 0);
            $data['usuario'] = Auth::user()->id;
            //----montos
            $data['monto'] = $glb;
            $orden = $request->input('orden', 1);
            $res1 = Tramites_montos_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 1)->update($data);
            $data['monto'] = $gdr;
            $res2 = Tramites_montos_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 2)->update($data);
            $data['monto'] = $comap;
            $res3 = Tramites_montos_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 3)->update($data);
            $res = $res1 + $res2 + $res3;
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        $miembro = $request->input('miembro', '');
        $ord = $request->input('ord', 1);
        if (strlen($miembro) > 3) {
            $resu = Tramites_montos_model::where("idValle", $id)->where("miembro", $miembro)->where("orden", $ord)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Valles_model;
use App\Models\admin\Orientes_model;
use App\Models\Distritos_model;
use App\Traits\DatagridTrait;
use Session;

class Valles extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '007';
    public $controlador = 'valles';
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
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['distritos'] = Distritos_model::getDistritosArray($this->oriente);
        /*     varibles de pagina* */
        return view('admin.valles', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Valles_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0));
        $total = Valles_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $valle = $request->input('valle', '');
        if (strlen($valle) > 4) {
            $data = array(
                'tipo' => $request->input('tipo', 1),
                'idOriente' => $request->input('idOriente'),
                'valle' => $valle
            );
            $resu = Valles_model::insert($data);
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
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'tipo' => $request->input('tipo', 1),
                'idOriente' => $request->input('idOriente'),
                'valle' => $request->input('valle'),
                'fechaModificacion' => date('Y-m-d h:m:s')
            );

            $resu = Valles_model::where("idValle", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    // public function destroy_datos(Request $request)
    // { //manda a palera
    //     $id = $request->input('id', 0);
    //     if ($id > 0) {
    //         $bor = $request->input('flag', 0);
    //         if ($bor > 0)
    //             $resu = Valles_model::where("idValle", $id)->update(['borrado' => 0]);
    //         else
    //             $resu = Valles_model::where("idValle", $id)->update(['borrado' => 1]);
    //         if ($resu > 0) {
    //             return response()->json(['success' => 'true', 'Msg' => trans('mess.change')]);
    //         } else {
    //             return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
    //         }
    //     } else {
    //         return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
    //     }
    // }
    public function show_papelera()
    {
        $papelera = Session::get($this->controlador . '.papelera');
        if ($papelera == 1)
            Session::put($this->controlador . '.papelera', 0);
        else
            Session::put($this->controlador . '.papelera', 1);
        return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
    }
    public function destroy_valle(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Valles_model::where("idValle", $id)->delete();
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Logias_model;
use App\Models\Parametros_model;
use App\Models\admin\Roles_model;
// use App\Models\User;
// use App\Models\Valles_model;
use Session;
use App\Traits\DatagridTrait;

class Roles extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '002';
    public $controlador = 'roles';
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
        $data['levels'] = Parametros_model::getParamsArray(2); //---+++
        /*     varibles de pagina* */
        return view('admin.roles', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Roles_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.level', 0));
        $total = Roles_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.level', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('name', '');
        if (strlen($nna) > 4) {
            $data = array(
                'name' => $request->input('name'),
                'level' => $request->input('level')
            );
            $resu = Roles_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'name' => $request->input('name'),
                'level' => $request->input('level'),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $resu = Roles_model::where("id", $id)->update($data);
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
            $resu = Roles_model::where("id", $id)->delete();
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

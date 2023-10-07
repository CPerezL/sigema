<?php

namespace App\Http\Controllers;

use App\Models\admin\Membrecia_clave_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;

class Membrecia_clave extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $idmod = '011';
    public $controlador = 'membrecia_clave';
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
        $data['title'] = 'Configuracion de sistema';
        return view('admin.membrecia_clave', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Membrecia_clave_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('idClave', 0);
        if ($id > 0) {
            $data['tipo'] = $request->input('tipo', 1);
            $data['valle'] = $request->input('valle', 0);
            $data['clave'] = $request->input('clave', 'amistad');
            $resu = Membrecia_clave_model::where("idClave", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function save_datos(Request $request)
    {
        $tid = $this->input->postint('tipo', 1);
        if ($tid > 0) {
            $data['tipo'] = $tid;
            $data['valle'] = $request->input('valle', 0);
            $data['clave'] = $request->input('clave', 'amistad');
            $resu = Membrecia_clave_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errordata')]);
        }
    }
    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Membrecia_clave_model::where("idClave", $id)->delete();
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

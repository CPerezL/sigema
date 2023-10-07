<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\sistema\Errores_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Errores_lista extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '017';
    public $controlador = 'errores_lista';
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
        $data['modulos'] = Errores_model::getListaModulos(Auth::user()->idRol);
        /*     variables de pagina     */
        return view('sistema.errores_lista', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        if (Auth::user()->nivel > 4) {
            $user = 0;
        } else {
            $user = Auth::user()->id;

        }
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $qry = Errores_model::getItems($page, $rows, $user, $val, Session::get($this->controlador . '.palabra', ''));
        $total = Errores_model::getNumItems($user, $val, Session::get($this->controlador . '.palabra', ''));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('modulo', '');
        if ($nna > 0) {
            $data = array(
                'idUsuario' => Auth::user()->id,
                'modulo' => $request->input('modulo'),
                'descripcion' => $request->input('descripcion')
            );
            $resu = Errores_model::insert($data);
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
                'modulo' => $request->input('modulo'),
                'estado' => 0,
                'descripcion' => $request->input('descripcion'),
                'fechaModificacion' => date('Y-m-d h:m:s')
            );

            $resu = Errores_model::where("id", $id)->update($data);
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
            $resu = Errores_model::where("id", $id)->delete();
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

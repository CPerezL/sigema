<?php

namespace App\Http\Controllers;

use App\Models\admin\Orientes_model;
// use App\Models\Parametros_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Orientes extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '006';
    public $controlador = 'orientes';
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
        $data['oris'] = Orientes_model::getOrientesPadre($this->oriente); //---+++
        /*     varibles de pagina* */
        return view('admin.orientes', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Orientes_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.padre', 0));
        $total = Orientes_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.padre', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('oriente', '');
        if (strlen($nna) > 4) {
            $data = array(
                'oriente' => $request->input('oriente'),
                'gradoMinimo' => $request->input('gradoMinimo'),
                'gradoMaximo' => $request->input('gradoMaximo'),
                'idOrientePadre' => $request->input('idOrientePadre'),
                'pais' => $request->input('pais'),
            );
            $resu = Orientes_model::insert($data);
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
                'oriente' => $request->input('oriente'),
                'gradoMinimo' => $request->input('gradoMinimo'),
                'gradoMaximo' => $request->input('gradoMaximo'),
                'idOrientePadre' => $request->input('idOrientePadre'),
                'pais' => $request->input('pais'),
                'fechaModificacion' => date("Y-m-d H:i:s"),
            );

            $resu = Orientes_model::where("idOriente", $id)->update($data);
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
            $resu = Orientes_model::where("idOriente", $id)->delete();
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

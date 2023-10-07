<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Oficiales_model;
use App\Models\admin\Orientes_model;
use App\Models\admin\Ritos_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Ritos_admin extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '010';
    public $controlador = 'ritos_admin';
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
        if (Auth::user()->nivel > 3) {
            $data['editable'] = '';
        } else {
            $data['editable'] = ' disabled="true"';
        }
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('admin.ritos_admin', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);

        $qry = Ritos_model::getRitosOficiales($page, $rows);
        $total = Ritos_model::getRitosOficialesTotal();
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function get_oficiales(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $salida = Ritos_model::getListaOficiales($id);
            return response()->json($salida);
        } else {
            return response()->json(['']);
        }
    }
    public function update_oficial(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'oficial' => $request->input('oficial'),
                'orden' => $request->input('orden'),
            );
            $resu = Oficiales_model::where('id', $id)->update($data);

        } else { $resu = 0;}
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }

    }
    public function save_oficial(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'rito' => $id,
                'oficial' => $request->input('oficial'),
                'orden' => $request->input('orden'),
            );
            $resu = Oficiales_model::insert($data);
        } else { $resu = 0;}
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }

    }
    public function destroy_oficial(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Oficiales_model::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function update_rito(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'rito' => $request->input('rito'),
                'nombreCompleto' => $request->input('nombreCompleto'),
                'nombreTexto' => $request->input('nombreTexto'),
                'textoPlanillas' => $request->input('textoPlanillas'),
            );
            $resu = Ritos_model::where('idRito', $id)->update($data);

        } else { $resu = 0;}
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function save_rito(Request $request)
    {
        $data = array(
            'rito' => $request->input('rito'),
            'idOriente' => 1,
            'nombreCompleto' => $request->input('nombreCompleto'),
            'nombreTexto' => $request->input('nombreTexto'),
            'textoPlanillas' => $request->input('textoPlanillas'),
        );
        $resu = Ritos_model::insert($data);
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }
    }
    public function destroy_rito(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Ritos_model::where("idRito", $id)->delete();
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

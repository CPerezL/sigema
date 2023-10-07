<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
// use App\Models\Parametros_model;
use App\Models\admin\Orientes_model;
use App\Models\comap\Comap_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Comap extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '015';
    public $controlador = 'comap';
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
        $data['oris'] = Orientes_model::getOrientesPadre(); //---+++
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('comap.comap', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Comap_model::getItemsComap($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.taller', 0));
        $total = Comap_model::getTotalItemsComap(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.valle', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function get_beneficiarios(Request $request)
    {
        $qry = Comap_model::getBeneficiarios(Session::get($this->controlador . '.idm', ''));
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('nombreBeneficiario', '');
        if (strlen($nna) > 5) {
            $idm = Session::get($this->controlador . '.idm', '');
            $data = array(
                'idMiembro' => $idm,
                'nombreBeneficiario' => $request->input('nombreBeneficiario', 'Sin nombre'),
                'parentesco' => $request->input('parentesco', 'Familiar'),
                'porcentaje' => $request->input('porcentaje', 0),
                'fechaCreacion' => date('Y-m-d h:m:s'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Comap_model::insert($data);
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
                'nombreBeneficiario' => $request->input('nombreBeneficiario', 'Sin nombre'),
                'parentesco' => $request->input('parentesco', 'Familiar'),
                'porcentaje' => $request->input('porcentaje', 0),
                'fechaModificacion' => date('Y-m-d h:m:s')
              );

            $resu = Comap_model::where("idComap", $id)->update($data);
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
            $resu = Comap_model::where("idComap", $id)->delete();
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

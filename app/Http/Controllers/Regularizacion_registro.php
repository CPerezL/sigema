<?php

namespace App\Http\Controllers;

use App\Models\admin\Control_model;
use App\Models\admin\Logias_model;
use App\Traits\DatagridTrait;
// use App\Models\User;
// use App\Models\Valles_model;
use Auth;
use Illuminate\Http\Request;
use Session;

class Regularizacion_registro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '047';
    public $controlador = 'regularizacion_registro';
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
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.regularizacion_registro', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $qry = Control_model::getRegulariza($page, $rows, Session::get($this->controlador . '.palabra', ''), $val);
        $total = Control_model::getNumRegulariza(Session::get($this->controlador . '.palabra', ''), $val);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $ncom = strtoupper($request->input('Paterno') . ' ' . $request->input('Materno') . ' ' . $request->input('Nombres'));
        $ncomid = str_replace(' ', '', $ncom);
        if (strlen($request->input('Paterno', '')) > 1) {
            $usr = strtolower(substr($request->input('Nombres', ''), 0, 1) . str_replace(' ', '', $request->input('Paterno')) . substr($request->input('Materno', ''), 0, 1)) . $request->input('LogiaActual');
        } else {
            $usr = strtolower(substr($request->input('Nombres', ''), 0, 1) . str_replace(' ', '', $request->input('Materno'))) . $request->input('LogiaActual');
        }

        $datam = array(
            'Materno' => strtoupper($request->input('Materno', '')),
            'Paterno' => strtoupper($request->input('Paterno', '')),
            'Nombres' => strtoupper($request->input('Nombres', 'error')),
            'Miembro' => $request->input('Miembro', 'Regular'),
            'Grado' => $request->input('Grado'),
            'observaciones' => $request->input('params', ''),
            'LogiaActual' => $request->input('LogiaActual'),
            'jurisdiccion' => $request->input('jurisdiccion', 0),
            'NombreCompleto' => strtoupper($ncom),
            'NombreCompletoID' => strtoupper($ncomid),
            'username' => $usr,
        );
        $graba['valorCambio'] = json_encode($datam);
        $graba['tabla'] = 'sgm_miembros';
        if ($request->input('jurisdiccion', 0) == 2) {
            $graba['tipo'] = 51;
            $graba['accion'] = 'Regularizacion no jurisdiccional';
            $graba['modificacion'] = 'Regularizacion de ' . $ncom;
        } else {
            $graba['tipo'] = 50;
            $graba['accion'] = 'Afiliacion Internacional';
            $graba['modificacion'] = 'Afiliacion de ' . $ncom;
        }
        $graba['estado'] = 1;
        $graba['usuarioCambia'] = Auth::user()->id;
        $graba['usuarioAprueba'] = 0;
        $graba['taller'] = $request->input('LogiaActual');
        $graba['params'] = $request->input('params');
        $graba['valle'] = Logias_model::select('valle')->where('numero', $request->input('LogiaActual'))->first('valle')->valle;
        $resu = Control_model::insert($graba);
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }
    }
    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Control_model::where("idControl", $id)->delete();
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

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\mecom\Obolos_model;
use App\Models\mecom\Registros_model;
use App\Models\admin\Miembrosestado_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_obolos_admin extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '069';
    public $controlador = 'mecom_obolos_admin';
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
        Session::put($this->controlador . '.estado', 1);
        $data['_mid'] = $this->idmod; //---
        $data['level'] = Auth::user()->nivel;
        $data['_controller'] = $this->controlador;
        $data['estados'] = Miembrosestado_model::getEstadosArray();
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('mecom.obolos_admin', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $estado = Session::get($this->controlador . '.estado');
        $grado = Session::get($this->controlador . '.grado');
        $palabra = Session::get($this->controlador . '.palabra');
        $salida = Obolos_model::getListaMiembros($page, $rows, $palabra, $valle, $taller, $grado, $estado);
        $total = Obolos_model::getNumMiembros($palabra, $valle, $taller, $grado, $estado);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function get_obolos(Request $request)
    {
        $id = Session::get($this->controlador . '.idm');
        if ($id > 0) {
            $salida = Registros_model::where('idMiembro', $id)->orderby('idRegistro', 'DESC')->get();
            // dd($salida);
            //$salida = $this->mecom_obolos_model->getObolos($id);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_obolo(Request $request)
    { //datos de obolo de miembro
        $fecha = Obolos_model::getUltimoObolo($request->input('idm', 0));
        $qry2 = (object) ['fechaultimo' => $fecha];
        return response()->json($qry2);
    }
    public function set_obolo(Request $request)
    { //datos de obolo de miembro
        $fobol = $request->input('fechaultimo');
        $idm = Session::get($this->controlador . '.idm');
        if (strlen($fobol) > 6 && $idm > 0) {
            $taller = Session::get($this->controlador . '.taller');
            if ($taller > 0) {
            } else {
                $taller = Obolos_model::getTaller($idm);
            } //y-d-m---1974-08-14
            $exf = explode('/', $fobol);
            $fecha = "$exf[2]-$exf[1]-01";
            $difer = Obolos_model::checkFecha($idm, $fecha);
            if (!is_null($difer)) {
                $datas = array(
                    'idFormulario' => 0,
                    'taller' => $taller,
                    'idMiembro' => $idm,
                    'miembro' => '', //--
                    'grado' => 0, //---
                    'numeroCuotas' => 1,
                    'monto' => 0,
                    'montoGLB' => 0,
                    'montoGDR' => 0,
                    'montoCOMAP' => 0,
                    'ultimoPago' => $difer, //
                    'fechaPagoNuevo' => $fecha, //
                    'usuario' => Auth::user()->id,
                );
                $resu = Registros_model::insert($datas);
                if ($resu > 0) {
                    $resu2 = Membrecia_model::where('id', $idm)->update(['ultimoPago' => $fecha]);
                    if ($resu2 > 0) {
                        return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
                    } else {
                        return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                    }
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Error fecha es menor al ultimo pago']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }
}

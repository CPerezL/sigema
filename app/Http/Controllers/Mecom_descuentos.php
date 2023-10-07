<?php

namespace App\Http\Controllers;

//use App\Models\sistema\Errores_model;
use App\Models\mecom\Descuentos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Mecom_descuentos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '026';
    public $controlador = 'mecom_descuentos';
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
        /*     variables de pagina     */
        return view('mecom.descuentos', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Descuentos_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $des = explode('/', $request->input('desdefrm'));
        $has = explode('/', $request->input('hastafrm'));
        $desde = $des[2] . '-' . $des[1] . '-01';
        $hasta = $has[2] . '-' . $has[1] . '-01';
        $numero = $this->diferFecha($desde, $hasta);
        if ($numero > 0) {
            $data = array(
                'activo' => $request->input('activo'),
                'numeroCuotas' => $numero,
                'desde' => $desde,
                'hasta' => $hasta,
                'comentario' => $request->input('comentario'),
                'aplica' => 0,
                'gestion' => 0,
                'valle' => 0,
                'usuario' => Auth::user()->id,
                'fechaModificacion' => date('Y-m-d H:i:s'),
            );
            $res = Descuentos_model::insert($data);
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
        $id = $request->input('id', 0);
        if ($id > 0) {
            $des = explode('/', $request->input('desdefrm'));
            $has = explode('/', $request->input('hastafrm'));
            $desde = $des[2] . '-' . $des[1] . '-01';
            $hasta = $has[2] . '-' . $has[1] . '-01';
            $numero = $this->diferFecha($desde, $hasta);
            $data = array(
                'activo' => $request->input('activo'),
                'numeroCuotas' => $numero,
                'desde' => $desde,
                'hasta' => $hasta,
                'usuario' => Auth::user()->id,
                'comentario' => $request->input('comentario'),
                'fechaModificacion' => date('Y-m-d H:i:s'),
            );
            $resu = Descuentos_model::where('idDescuento', (int) $id)->update($data);
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
        $miembro = $request->input('miembro', '');
        $ord = $request->input('ord', 1);
        if (strlen($miembro) > 3) {
            $resu = Descuentos_model::where("idValle", $id)->where("miembro", $miembro)->where("orden", $ord)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function diferFecha($desde, $hasta)
    {
        $inicio = "$desde 00:00:00";
        $fin = "$hasta 23:59:59";
        $datetime1 = new \DateTime($inicio);
        $datetime2 = new \DateTime($fin);
        # obtenemos la diferencia entre las dos fechas
        $interval = $datetime2->diff($datetime1);
        # obtenemos la diferencia en meses
        $intervalMeses = $interval->format("%m");
        # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
        //    $intervalAnos = $interval->format("%y") * 12;
        return $intervalMeses + 1;
    }
}

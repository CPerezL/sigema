<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\mecom\Reporte_depositos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;

class Mecom_reporte_depositos_valles extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '043';
    public $controlador = 'mecom_reporte_depositos_valles';
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
        $data['year'] = date('Y');
        /*     variables de pagina     */
        return view('mecom.reporte_depositos_valles', $data);
    }
    public function get_datos(Request $request)
    {
        $val = Session::get($this->controlador . '.valle');
        $ges = Session::get($this->controlador . '.gestion');
        $tip = Session::get($this->controlador . '.tipo', 0);
        if ($val > 0 && $ges > 0) {
            $meses[1] = 'ene';
            $meses[2] = 'feb';
            $meses[3] = 'mar';
            $meses[4] = 'abr';
            $meses[5] = 'may';
            $meses[6] = 'jun';
            $meses[7] = 'jul';
            $meses[8] = 'ago';
            $meses[9] = 'sep';
            $meses[10] = 'oct';
            $meses[11] = 'nov';
            $meses[12] = 'dic';
            $resul = array();
            $cc = 0;
            $salida = Reporte_depositos_model::getLogias($val);
            foreach ($salida as $ver) {
                $tot = 0;
                $resul[$cc]['numero'] = $cc + 1;
                $resul[$cc]['gestion'] = $ges;
                $resul[$cc]['taller'] = $ver->Tallertxt;
                $pagos = Reporte_depositos_model::getMesesPago($ver->numero, $ges, $tip);
                foreach ($meses as $key => $mes) {
                    if (isset($pagos[$key])) {
                        $resul[$cc][$mes] = $pagos[$key];
                        $tot = $tot + $pagos[$key];
                    } else {
                        $resul[$cc][$mes] = '';
                    }
                }
                $resul[$cc]['sumaPagos'] = $tot;
                $cc++;
            }
            $qry2 = (object) ['total' => 0, 'rows' => $resul];
            return response()->json($qry2);
        } else {
            return response()->json(['total' => 0, 'rows' => '']);
        }
    }
    public function set_datos(Request $request)
    {
        $val = $request->input('valle');
        $ges = $request->input('gestion');
        $tipo = $request->input('tipo');
        $data = array($this->controlador . '.valle' => $val, $this->controlador . '.gestion' => $ges, $this->controlador . '.tipo' => $tipo);
        Session::put($data);
        return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
    }
}

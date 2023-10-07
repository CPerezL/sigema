<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\mecom\Reporte_obolos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_obolos_repo extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '068';
    public $controlador = 'mecom_obolos_repo';
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
        $data['year'] = date("Y");
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('mecom.obolos_repo', $data);
    }
    public function get_datos(Request $request)
    {
        $gestion = Session::get($this->controlador . '.gestion');
        $taller = Session::get($this->controlador . '.taller');
        if ($gestion > 0 && $taller) {
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
            $salida = Reporte_obolos_model::getListaMiembros($gestion, $taller);
            // dd($salida);
            if (!is_null($salida)) {
                foreach ($salida as $ver) {
                    foreach ($meses as $key => $mes) {
                        if ($ver->gestionbase > 0 || $ver->hastames >= $key) {
                            $fecha = "$gestion-$key-01";
                            $pago = Reporte_obolos_model::getMesPagado($ver->id, $fecha);
                            if (is_null($pago)) {
                                $resul[$cc][$mes] = '==|';
                            } else {
                                $resul[$cc][$mes] = $pago[0]->documento;
                            }
                        } else {
                            $resul[$cc][$mes] = '';
                        }
                    }
                    $resul[$cc]['numero'] = $cc + 1;
                    $resul[$cc]['gestion'] = $gestion;
                    $resul[$cc]['taller'] = $taller;
                    $resul[$cc]['nombre'] = $ver->NombreCompleto;
                    $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                    $cc++;
                }
            } else {
                $resul = array();
            }
            $qry2 = (object) ['total' => $cc, 'rows' => $resul];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }

    public function set_datos(Request $request)
    {
        $gestion = $request->input('gestion');
        $taller = $request->input('taller', 0);
        if ($taller > 0 && $gestion > 2000) {
            $data = array($this->controlador . '.gestion' => $gestion, $this->controlador . '.taller' => $taller);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait..']);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\reportes\Asistencias_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Reporte_habiles_voto extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '038';
    public $controlador = 'reporte_habiles_voto';
    var $numExtraT = 6;
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
        $data['year'] = date('Y');
        /*     varibles de pagina* */
        return view('reportes.habiles_voto', $data);
    }
    public function get_datos(Request $request)
    {
        if (Session::get($this->controlador . '.gestion') > 0 && Session::get($this->controlador . '.taller') > 0) {
            $resul = array();
            $cc = 0;
            $salida = Asistencias_model::getItems(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
            $ntenidas = Asistencias_model::getCantidadTenidas(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
            foreach ($salida as $ver) {
                $resul[$cc]['numero'] = $cc + 1;
                $resul[$cc]['taller'] = Session::get($this->controlador . '.taller');
                $resul[$cc]['GradoActual'] = $ver->protocolo;
                $resul[$cc]['nombre'] = $ver->NombreCompleto;
                $ord = Asistencias_model::getAsistencia(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                $ext = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                $ase = 0;
                $extratgdr = 0;
                $asis = $ord + $ase;
                //tenidas

                if ($ntenidas > 0) {
                    $porcen = (($asis / $ntenidas) * 100);
                    if ($porcen > 100) {
                        $resul[$cc]['ordinaria'] = '100%';
                    } else {
                        $resul[$cc]['ordinaria'] = round($porcen) . ' %';
                    }

                } else {
                    $resul[$cc]['ordinaria'] = 0;
                }
                //extratem
                if ($ext > 0) {
                    $porcent = round(100 * (($ext + $extratgdr) / $this->numExtraT), 0);
                    if ($porcent > 100) {
                        $resul[$cc]['extratemplo'] = '100%';
                    } else {
                        $resul[$cc]['extratemplo'] = $porcent . ' %';
                    }
                } else {
                    $resul[$cc]['extratemplo'] = 0;
                }
                ///--
                $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                $resul[$cc]['ntenidas'] = $ntenidas;
                $cc++;
            }
            $qry2 = (object) ['total' => 0, 'rows' => $resul];
            return response()->json($qry2);
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
            return response()->json($qry2);
        }

    }
    public function set_datos(Request $request)
    {
        $tal = $request->input('taller');
        $ges = $request->input('gestion');
        if ($tal > 0 && $ges > 0) {
            $data = array($this->controlador . '.taller' => $tal, $this->controlador . '.gestion' => $ges);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.wait')]);
        }
    }
}

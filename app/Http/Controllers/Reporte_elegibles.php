<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\reportes\Asistencias_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Reporte_elegibles extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '039';
    public $controlador = 'reporte_elegibles';
    public $numExtraT = 6;
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
        Session::put($this->controlador . '.gestion', date('Y'));
        /*     varibles de pagina* */
        return view('reportes.elegibles', $data);
    }
    public function get_datos(Request $request)
    {
        if (Session::get($this->controlador . '.gestion') > 0 && Session::get($this->controlador . '.taller') > 0) {
            $resul = array();
            $cc = 0;
            $datoslog = Asistencias_model::getDatosLogia(Session::get($this->controlador . '.taller'));
            $salida = Asistencias_model::getItemsRito(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $datoslog->rito);
            $ntenidas = Asistencias_model::getCantidadTenidas(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
            if ($ntenidas > 0) {
                foreach ($salida as $ver) {
                    $resul[$cc]['numero'] = $cc + 1;
                    $resul[$cc]['GradoActual'] = $ver->protocolo;
                    $resul[$cc]['Miembro'] = $ver->Miembro;
                    $resul[$cc]['nombre'] = $ver->NombreCompleto;
                    /* Años*/
                    if (strlen($ver->FechaNacimiento) > 6) {
                        $edad = $this->calculaedad($ver->FechaNacimiento);
                    } else {
                        $edad = -1;
                    }

                    if (strlen($ver->FechaExaltacion) > 6) {
                        $edadm = $this->calculaedad($ver->FechaExaltacion);
                    } else {
                        $edadm = -1;
                    }

                    if ($edad >= 0) {
                        $resul[$cc]['edadnac'] = $edad;
                    } else {
                        $resul[$cc]['edadnac'] = '0';
                    }

                    if ($edadm >= 0) {
                        $resul[$cc]['edadmas'] = $edadm;
                    } else {
                        $resul[$cc]['edadmas'] = '-';
                    }

                    $et = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                    if ($ntenidas > 0) {
                        $ord = Asistencias_model::getAsistencia(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $extras = 0;
                        $extratgdr = 0;
                        $porcentaje = (($ord + $extras) / $ntenidas) * 100;
                        if ($porcentaje > 100) {
                            $resul[$cc]['ordinaria'] = '100%';
                        } else {
                            $resul[$cc]['ordinaria'] = round($porcentaje, 0) . ' %';
                        }
                    } else {
                        $resul[$cc]['ordinaria'] = 0;
                    }

                    $extratem = round(100 * (($et + $extratgdr) / $this->numExtraT), 0);
                    if ($extratem > 100) {
                        $resul[$cc]['extratemplo'] = '100 %';
                    } else {
                        $resul[$cc]['extratemplo'] = $extratem . ' %';
                    }

                    $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                    if ($ver->vene > 0 && $porcentaje >= 70 && $ver->habil == 1) { //para vene
                        if ($edad >= 30 && $edadm >= 5) {
                            $resul[$cc]['ParaVM'] = 'SI';
                        } elseif ($edad <= 0 || $edad < 0) {
                            $resul[$cc]['ParaVM'] = 'ND';
                        }
                    } else {
                        $resul[$cc]['ParaVM'] = '-';
                    }

                    if ($ver->vigg > 0 && $resul[$cc]['extratemplo'] >= 60 && $resul[$cc]['ordinaria'] >= 60) { //para vigilante
                        if ($ver->vigg < 3 && $ver->habil == 1) {
                            if ($edad >= 28 && $edadm >= 4) {
                                $resul[$cc]['ParaVig'] = 'SI';
                            } elseif ($edad <= 0 || $edad < 0) {
                                $resul[$cc]['ParaVig'] = 'ND';
                            } else {
                                $resul[$cc]['ParaVig'] = '-';
                            }
                        } elseif ($ver->vigg >= 7 && $ver->habil == 1) { //caso reaa
                            if ($edad >= 28 && $edadm >= 4) {
                                $resul[$cc]['ParaVig'] = 'SI';
                            } elseif ($edad <= 0 || $edad < 0) {
                                $resul[$cc]['ParaVig'] = 'ND';
                            } else {
                                $resul[$cc]['ParaVig'] = '-';
                            }
                        } else {
                            $resul[$cc]['ParaVig'] = '-';
                        }
                    } else {
                        $resul[$cc]['ParaVig'] = '-';
                    }
                    if ($resul[$cc]['extratemplo'] >= 60 && $resul[$cc]['ordinaria'] >= 60 && $ver->habil == 1) { //para oficial
                        if ($edadm >= 1) {
                            $resul[$cc]['ParaOf'] = 'SI';
                        } else {
                            $resul[$cc]['ParaOf'] = '-';
                        }
                    } else {
                        $resul[$cc]['ParaOf'] = '-';
                    }

                    if ($ver->habil == 1) {
                        $resul[$cc]['estadoPago'] = 'Ok';
                    } else {
                        $resul[$cc]['estadoPago'] = '-';
                    }
                    $cc++;
                }
            } else {
                $resul = '';
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
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    private function calculaedad($fecha)
    {
        list($Y, $m, $d) = explode("-", $fecha);
        return (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
    }
}

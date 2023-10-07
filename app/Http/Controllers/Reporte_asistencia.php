<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\reportes\Asistencias_model;
use App\Traits\DatagridTrait;
use App\Traits\ReporteTrait;
use Illuminate\Http\Request;
use Session;

class Reporte_asistencia extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    use ReporteTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '040';
    public $controlador = 'reporte_asistencia';
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
        /*     varibles de pagina* */
        return view('reportes.asistencia', $data);
    }
    public function get_datos(Request $request)
    {
        if (Session::get($this->controlador . '.gestion') > 0 && Session::get($this->controlador . '.taller') > 0) {
            $resul = array();
            $cc = 0;
            $ntenidas = Asistencias_model::getCantidadTenidas(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
            $salida = Asistencias_model::getItemsGrado(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.grado'));
            $rito = Asistencias_model::getRitoTaller(Session::get($this->controlador . '.taller'));
            if ($ntenidas > 0) {
                if ($rito == 1) {
                    $gtenidas = Asistencias_model::getCantidadTenidasGrado(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
                    foreach ($salida as $ver) {
                        $nasis = Asistencias_model::getCantidadAsistida(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        if ($ver->Grado == 1) {
                            $ntenidas = $gtenidas->nTenidas1;
                        } elseif ($ver->Grado == 2) {
                            $ntenidas = $gtenidas->nTenidas2;
                        } else {
                            $ntenidas = $gtenidas->nTenidas3;
                        }
                        $resul[$cc]['numero'] = $cc + 1;
                        $resul[$cc]['taller'] = Session::get($this->controlador . '.taller');
                        $resul[$cc]['GradoActual'] = $ver->protocolo;
                        $resul[$cc]['nTenidas'] = $ntenidas; //
                        $resul[$cc]['Miembro'] = $ver->Miembro;
                        $resul[$cc]['nombre'] = $ver->NombreCompleto;
                        $resul[$cc]['ordinaria'] = $nasis;
                        $asisextra = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id);
                        $porcasis = round((($nasis + $asisextra) / $ntenidas) * 100, 2);
                        $resul[$cc]['porcentaje'] = ($porcasis >= 100) ? '100' : $porcasis;
                        $resul[$cc]['asisextra'] = $asisextra;
                        $extrat = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['extratemplo'] = $extrat;
                        $extratgdr = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id, 1);
                        $resul[$cc]['extratemploGDR'] = $extratgdr;
                        $pett = (($extrat + $extratgdr) / 6) * 100;
                        if ($pett >= 100) {
                            $resul[$cc]['ettPorcentaje'] = 100;
                        } else {
                            $resul[$cc]['ettPorcentaje'] = round(((($extrat + $extratgdr) / 6) * 100), 2);
                        }
                        $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                        $resul[$cc]['FechaIniciacion'] = $ver->FechaIniciacion;
                        $resul[$cc]['FechaAumentoSalario'] = $ver->FechaAumentoSalario;
                        $resul[$cc]['FechaExaltacion'] = $ver->FechaExaltacion;
                        $cc++;
                    }
                } else {
                    foreach ($salida as $ver) {
                        $nasis = Asistencias_model::getCantidadAsistida(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['numero'] = $cc + 1;
                        $resul[$cc]['GradoActual'] = $ver->GradoActual;
                        $resul[$cc]['nTenidas'] = $ntenidas; //
                        $resul[$cc]['Miembro'] = $ver->Miembro;
                        $resul[$cc]['nombre'] = $ver->NombreCompleto;
                        //$resul[$cc]['ordinaria'] = $ver->cantidad;
                        $resul[$cc]['ordinaria'] = $nasis;
                        $asisextra = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id);
                        $porcasis = round((($nasis + $asisextra) / $ntenidas) * 100, 2);
                        $resul[$cc]['porcentaje'] = ($porcasis >= 100) ? '100' : $porcasis;
                        $resul[$cc]['asisextra'] = $asisextra;
                        $extrat = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['extratemplo'] = $extrat;
                        $extratgdr = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id, 1);
                        $resul[$cc]['extratemploGDR'] = $extratgdr;
                        $pett = (($extrat + $extratgdr) / 6) * 100;
                        if ($pett >= 100) {
                            $resul[$cc]['ettPorcentaje'] = 100;
                        } else {
                            $resul[$cc]['ettPorcentaje'] = round(((($extrat + $extratgdr) / 6) * 100), 2);
                        }
                        $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                        $resul[$cc]['FechaIniciacion'] = $ver->FechaIniciacion;
                        $resul[$cc]['FechaAumentoSalario'] = $ver->FechaAumentoSalario;
                        $resul[$cc]['FechaExaltacion'] = $ver->FechaExaltacion;
                        $cc++;
                    }
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
        $gra = $request->input('grado');
        if ($tal > 0 && $ges > 0) {
            $data = array($this->controlador . '.taller' => $tal, $this->controlador . '.gestion' => $ges, $this->controlador . '.grado' => $gra);
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
    public function gen_reporte()
    {
        if (Session::get($this->controlador . '.gestion') > 0 && Session::get($this->controlador . '.taller') > 0) {
            $resul = array();
            $cc = 0;
            $ntenidas = Asistencias_model::getCantidadTenidas(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
            $salida = Asistencias_model::getItemsGrado(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.grado'));
            $rito = Asistencias_model::getRitoTaller(Session::get($this->controlador . '.taller'));
            //dd($rito);//hay que arreglar esot
            if ($ntenidas > 0) {
                if ($rito == 1) {
                    $gtenidas = Asistencias_model::getCantidadTenidasGrado(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'));
                    foreach ($salida as $ver) {
                        $nasis = Asistencias_model::getCantidadAsistida(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        if ($ver->Grado == 1) {
                            $ntenidas = $gtenidas->nTenidas1;
                        } elseif ($ver->Grado == 2) {
                            $ntenidas = $gtenidas->nTenidas2;
                        } else {
                            $ntenidas = $gtenidas->nTenidas3;
                        }
                        $resul[$cc]['numero'] = $cc + 1;
                        $resul[$cc]['taller'] = Session::get($this->controlador . '.taller');
                        $resul[$cc]['GradoActual'] = $ver->protocolo;
                        $resul[$cc]['nTenidas'] = $ntenidas; //
                        $resul[$cc]['Miembro'] = $ver->Miembro;
                        $resul[$cc]['nombre'] = $ver->NombreCompleto;
                        $resul[$cc]['ordinaria'] = $nasis;
                        $asisextra = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id);
                        $porcasis = round((($nasis + $asisextra) / $ntenidas) * 100, 2);
                        $resul[$cc]['porcentaje'] = ($porcasis >= 100) ? '100' : $porcasis;
                        $resul[$cc]['asisextra'] = $asisextra;
                        $extrat = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['extratemplo'] = $extrat;
                        $extratgdr = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id, 1);
                        $resul[$cc]['extratemploGDR'] = $extratgdr;
                        $pett = (($extrat + $extratgdr) / 6) * 100;
                        if ($pett >= 100) {
                            $resul[$cc]['ettPorcentaje'] = 100;
                        } else {
                            $resul[$cc]['ettPorcentaje'] = round(((($extrat + $extratgdr) / 6) * 100), 2);
                        }
                        $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                        // $resul[$cc]['FechaIniciacion'] = $ver->FechaIniciacion;
                        // $resul[$cc]['FechaAumentoSalario'] = $ver->FechaAumentoSalario;
                        // $resul[$cc]['FechaExaltacion'] = $ver->FechaExaltacion;
                        $cc++;
                    }
                } else {
                    foreach ($salida as $ver) {
                        $nasis = Asistencias_model::getCantidadAsistida(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['numero'] = $cc + 1;
                        $resul[$cc]['GradoActual'] = $ver->GradoActual;
                        $resul[$cc]['nTenidas'] = $ntenidas; //
                        $resul[$cc]['Miembro'] = $ver->Miembro;
                        $resul[$cc]['nombre'] = $ver->NombreCompleto;
                        //$resul[$cc]['ordinaria'] = $ver->cantidad;
                        $resul[$cc]['ordinaria'] = $nasis;
                        $asisextra = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id);
                        $porcasis = round((($nasis + $asisextra) / $ntenidas) * 100, 2);
                        $resul[$cc]['porcentaje'] = ($porcasis >= 100) ? '100' : $porcasis;
                        $resul[$cc]['asisextra'] = $asisextra;
                        $extrat = Asistencias_model::getExtraTemplos(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ver->id);
                        $resul[$cc]['extratemplo'] = $extrat;
                        $extratgdr = Asistencias_model::getAsisExtra(Session::get($this->controlador . '.gestion'), $ver->id, 1);
                        $resul[$cc]['extratemploGDR'] = $extratgdr;
                        $pett = (($extrat + $extratgdr) / 6) * 100;
                        if ($pett >= 100) {
                            $resul[$cc]['ettPorcentaje'] = 100;
                        } else {
                            $resul[$cc]['ettPorcentaje'] = round(((($extrat + $extratgdr) / 6) * 100), 2);
                        }
                        $resul[$cc]['ultimoPago'] = $ver->ultimoPago;
                        // $resul[$cc]['FechaIniciacion'] = $ver->FechaIniciacion;
                        // $resul[$cc]['FechaAumentoSalario'] = $ver->FechaAumentoSalario;
                        // $resul[$cc]['FechaExaltacion'] = $ver->FechaExaltacion;
                        $cc++;
                    }
                }
            } else {
                $resul = '';
            } //
            $logia = 'R:.L:.S:. ' . Logias_model::where('numero', Session::get($this->controlador . '.taller'))->first('nombreCompleto')->nombreCompleto;
            $campos[0] = 'numero';
            $campos[1] = 'Miembro';
            $campos[2] = 'GradoActual';
            $campos[3] = 'nombre';
            $campos[4] = 'nTenidas';
            $campos[5] = 'ordinaria';
            $campos[6] = 'asisextra';
            $campos[7] = 'porcentaje';
            $campos[8] = 'extratemplo';
            $campos[9] = 'ettPorcentaje';
            $campos[10] = 'ultimoPago';

            $titulos[0] = '#';
            $titulos[1] = 'Miembro';
            $titulos[2] = 'Grado';
            $titulos[3] = 'Nombre Completo';
            $titulos[4] = 'Nº Ten.';
            $titulos[5] = 'Asis. Ten.';
            $titulos[6] = 'AsisExtra';
            $titulos[7] = '%';
            $titulos[8] = 'ExtTemp';
            $titulos[9] = '% ET';
            $titulos[10] = 'Ult.Obol.';
            $title='Reporte de asistencia - '.date('d/m/Y');
            $nome='Asistencia_'.date('dmY');
            return self::crearReportePDF($logia, $title, $titulos, $resul, $campos,$nome);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
}

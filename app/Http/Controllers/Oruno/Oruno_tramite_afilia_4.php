<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\Logs_model;
use App\Models\oruno\Afiliacion_model;
use App\Models\oruno\Certificados_model;
use App\Traits\DatagridTrait;
use App\Models\Version_model;
use Auth;
use PDF;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_afilia_4 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '099';
    public $controlador = 'oruno_tramite_afilia_4';
    private $error = '';
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('sites');
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
        $data['_folder'] = url('/');
        $data['mesactual'] = date('01/n/Y'); //10/09/2021
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.afilia_4', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Afiliacion_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val, '4,5,6');
        $total = Afiliacion_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val, '4,5,6');
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function cambia_datos(Request $request)
    {
        $mes = 'Afiliacion exitosa y actualizacion de datos';
        $id = $request->input('id');
        $task = $request->input('task');
        if ($id > 0 && $task > 1) {
            if ($task == 4) {
                $data = array(
                    'estado' => 5,
                    'fechaAprobacion' => date('Y-m-d h:m:s'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Afiliacion_model::where('id', $id)->update($data);
            }
            if ($resu == 1) {
                if ($task == 4) {
                    Logs_model::insertLog('4', Auth::user()->id, 'Deposito correcto', '', 0, 0);

                } else {
                    $mes = 'No encontrado';
                }
                return response()->json(['success' => 'true', 'Msg' => $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function cambia_logia(Request $request)
    {
        $mes = 'Afiliacion exitosa y actualizacion de datos';
        $id = $request->input('id');
        $tipo = $request->input('tipo');
        if ($id > 0 && $tipo > 0) {
            $datos = Afiliacion_model::select('idMiembro', 'idLogia', 'idLogiaNueva', 'tipo')->find($id);
            $data = array(
                'estado' => 6,
                'activo' => 1,
                'fechaAprobacion' => date('Y-m-d h:m:s'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Afiliacion_model::where('id', $id)->update($data);
            if ($datos->tipo == 1) {
                $mdata = array(
                    'LogiaActual' => $datos->idLogiaNueva,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            } elseif ($datos->tipo == 2) {
                $mdata = array(
                    'LogiaActual' => $datos->idLogiaNueva,
                    'LogiaAfiliada' => $datos->idLogia,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            } elseif ($datos->tipo == 3) {
                $mdata = array(
                    'LogiaAfiliada' => $datos->idLogia,
                    'Estado' => 1,
                    'fechaModificacion' => date('Y-m-d'),
                );
            }
            $resu = Membrecia_model::where('id',$datos->idMiembro)->update($mdata);
            if ($resu == 1) {
                return response()->json(['success' => 'true', 'Msg' => $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function gen_reporte(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $dcere = Certificados_model::getDatosCertificadoAfi($id);

$ges = explode('-', $dcere->fechaJuramento);
$codges=$ges[0];
            // Load all views as normal
            $data['fechacert'] = $this->getFechaCert($dcere->fechaJuramento);
            $data['numecert'] = 'A.-';
            $data['taller'] = $dcere->nombreCompleto;
            $nomefile = 'Cert-Afiliacion-'.$dcere->numero;
            $data['nvalle'] = $dcere->valle; //
            $data['casotxt'] = 'Y ha sido afiliado a la Aug.·. y Resp.·. Log.·.Simb.·.';
            $data['suscrito'] = $dcere->nombretxt;
            if($dcere->Grado==1)
            $data['tipocert'] = 'Aprendiz Masón';
            elseif($dcere->Grado==2)
            $data['tipocert'] = 'Compañero Masón';
            else
            $data['tipocert'] = 'Maestro Masón';
            //*****firmantes *************/
            $dc = Version_model::getValue('certificados');
            $df = explode(',', $dc);
            $firma1 = Certificados_model::getFirmaCertificado($codges, $df[0]);
            $firma2 = Certificados_model::getFirmaCertificado($codges, $df[1]);
            $firma3 = Certificados_model::getFirmaCertificado($codges, $df[2]);
            if (!is_null($firma1) && !is_null($firma2) && !is_null($firma3)) {
                $data['firma1'] = ucwords(strtolower($firma1->Nombres)) . ' ' . ucwords(strtolower($firma1->Paterno)) . ' ' . ucwords(strtolower($firma1->Materno)) . '<br>' . ucwords(strtolower($firma1->oficial));
                $data['firma2'] = ucwords(strtolower($firma2->Nombres)) . ' ' . ucwords(strtolower($firma2->Paterno)) . ' ' . ucwords(strtolower($firma2->Materno)) . '<br>' . ucwords(strtolower($firma2->oficial));
                $data['firma3'] = ucwords(strtolower($firma3->Nombres)) . ' ' . ucwords(strtolower($firma3->Paterno)) . ' ' . ucwords(strtolower($firma3->Materno)) . '<br>' . ucwords(strtolower($firma3->oficial));
            } else {
                $data['firma1'] = '1';
                $data['firma2'] = '2';
                $data['firma3'] = '3';
            }
            $pdf = PDF::loadView('oruno.pdf_certificado', $data);
            $pdf->set_paper('Legal', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            echo 'Error Formulario no seleccionado';
        }
    }
    private function saber_dia($nombredia)
    {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        $fecha = $dias[date('N', strtotime($nombredia))];
        return $fecha;
    }
    private function getFechaCert($fecha)
    {
        //22 días del mes de abril de 2022
        //$fecha = date("d-m-Y");
        $ges = explode('-', $fecha);
        $ames[1] = 'enero';
        $ames[2] = 'febrero';
        $ames[3] = 'marzo';
        $ames[4] = 'abril';
        $ames[5] = 'mayo';
        $ames[6] = 'junio';
        $ames[7] = 'julio';
        $ames[8] = 'agosto';
        $ames[9] = 'septiembre';
        $ames[10] = 'octubre';
        $ames[11] = 'noviembre';
        $ames[12] = 'diciembre';
        $dia = self::saber_dia($fecha);
        $fecha = $dia . ' ' . (int) $ges[2] . ' de ' . $ames[(int) $ges[1]] . ' de ' . $ges[0] . ' e.·.v.·.';
        return $fecha;
    }
}

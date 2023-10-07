<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\Logs_model;
use App\Models\oruno\Retiro_model;
use App\Traits\DatagridTrait;
use Auth;
use PDF;
use Illuminate\Http\Request;
use Session;

class Tramite_retiro_2 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '062';
    public $controlador = 'tramite_retiro_2';
    private $error = '';
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
        $data['_folder'] = url('/');
        $data['mesactual'] = date('01/n/Y'); //10/09/2021
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.retiro_2', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'), 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Retiro_model::getListaTramites($page, $rows, Session::get($this->controlador . '.palabra'), $log, $val,'2,6,7');
        $total = Retiro_model::getNumeroTramites(Session::get($this->controlador . '.palabra'), $log, $val,'2,6,7');
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function cambia_datos(Request $request)
    {
        $mes = '';
        $id = $request->input('id');
        $task = $request->input('task');
        if ($id > 0 && $task > 1) {
            $data = array(
                'usuarioAprobacion' => Auth::user()->id,
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            if ($task == 2) {
                $data['estado'] = 1;
            } elseif ($task == 7) {
                $data['estado'] = 7;
            } else {
                $data['fechaAprobacion'] = date('Y-m-d h:m:s');
                $data['estado'] = 3;
            }
            $resu = Retiro_model::where('id', $id)->update($data);
            $res1 = $resu;
            if ($res1 == 1) {
                if ($task == 2) {
                    Logs_model::insertLog('8', Auth::user()->id, 'Tramite de Afiliacion devuelto', '', 0, 0);
                    $mes = 'Tramite devuelto';
                } elseif ($task == 7) {
                    Logs_model::insertLog('5', Auth::user()->id, 'Rechazo de tramite de afiliacion', '', 0, 0);
                    $mes = 'Tramite cancelado';
                } else {
                    Logs_model::insertLog('8', Auth::user()->id, 'Tramite de Afiliacion revisada', '', 0, 0);
                    $mes = 'Tramite aprobado';
                }

                return response()->json(['success' => 'true', 'Msg' => $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }

        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }

    }
    public function gen_certificado(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $nomefile = 'CertQP' . $id;

            $dcere = Retiro_model::getTramite($id);

            // $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            // $fecha = explode('-', $dcere->fechaAprobacion);

            $data['nombre'] = $dcere->nombrem;
            $data['logia'] = $dcere->logianame;
            $data['fechacert'] =$this->getFechaCert($dcere->fechaAprobacion); //
            //$data['fecha'] = date('d') . " de " . $meses[date('n') - 1] . " de " . date('Y');
            //
            //$data['gestionet'] = $fecha[0]; //--
//--
            $pdf = PDF::loadView('oruno.pdf_quite', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            echo trans('mess.errdata');
        }

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
        if ($ges[2] > 1) {
            $fecha = (int) $ges[2] . ' días del mes de ' . $ames[(int) $ges[1]] . ' de ' . $ges[0];
        } else {
            $fecha = ' 1er día del mes de ' . $ames[(int) $ges[1]] . ' de ' . $ges[2];
        }

        return $fecha;
    }
}

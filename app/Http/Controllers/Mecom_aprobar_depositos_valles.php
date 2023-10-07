<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\Config_model;
use App\Models\mecom\Formularios_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Response;
use Session;
use Auth;
use PDF;

class Mecom_aprobar_depositos_valles extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '044';
    public $controlador = 'mecom_aprobar_depositos_valles';
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
        $data['_folder'] = '/comprobantes/'; //---
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina     */
        return view('mecom.aprobar_depositos_valles', $data);
    }
    public function get_datos(Request $request)
    {
        $idform = $request->input('idform', 0);
        if ($idform > 0) {
            $salida = Formularios_model::getAportantes($idform);
            $total = 0;
            Session::put($this->controlador . '.idform', $idform);
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
            return response()->json($qry2);
        } else {
            return response()->json(['total' => 0, 'rows' => '']);
        }
    }
    public function get_formularios(Request $request)
    {
        if (Auth::user()->nivel > 1) {
            $page = $request->input('page', 1);
            $rows = $request->input('rows', 20);
            $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
            $ges = Config_model::getValue('gestion'); //($page, $cantidad, $gestion, $valle, $taller = 0, $estado = 4, $tipo = 1, $level = 1, $aprobado = 0)
            $salida = Formularios_model::getFormularios($page, $rows, $ges, $val, $log, 4, 1, Auth::user()->nivel, Session::get($this->controlador . '.tipo', 0));
            $total = Formularios_model::getFormulariosTotal($ges, $val, $log, 4, 1, Auth::user()->nivel, Session::get($this->controlador . '.tipo', 0));
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
            return response()->json($qry2);
        } else {
            return response()->json(['total' => 0, 'rows' => '']);
        }

    }
    public function gen_formulario()
    {
      $nform = Session::get($this->controlador . '.idform');
      if ($nform > 0)
      {
        $diassemana = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $data['diaformu'] = $diassemana[date('w')]." ".date('d')." de ".$meses[date('n') - 1]." de ".date('Y'); //Miercoles 05 de Septiembre del 2016
        $data['diaelabor'] = date('Y-n-d'); //
        $datosform = Formularios_model::getDatosForm($nform);
        $datoslog = Formularios_model::getDatosLogia($datosform->taller);
        $data['taller'] = $datoslog->nombreCompleto;
        $data['tallern'] = $datosform->taller;
        $data['nvalle'] = $datoslog->valle;
        $data['nform'] = $nform;
        $data['numero'] = $datosform->numero;
        $data['gestion'] = $datosform->gestion;
        //--montos
        $data['montos'] = Formularios_model::getMontosFormulario($nform);
        $data['lista'] = Formularios_model::getAportantesForm($nform);
        $data['dglb'] = Formularios_model::getMontosTipo(1);
        $data['dgdr'] = Formularios_model::getMontosTipo(2);
        $data['dcomap'] = Formularios_model::getMontosTipo(3);
        $nomefile = 'Form-'.$datosform->taller.'-'.$nform;
        // Load all views as normal
        $data['logo'] = 'glb-150.png';

        $pdf = PDF::loadView('pdfs.pdf_form_obolos', $data);
        $pdf->set_paper('letter', 'portrait');
        return $pdf->download($nomefile.'.pdf');
      }
      else
      {
        echo 'Error Formulario no seleccionado';
      }

    }
    public function send_formulario(Request $request)
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $data = array(
                'aprobadogdr' => 4,
                'fechagdr' => date("Y-m-d H:i:s"),
                'usuariogdr' => Auth::user()->id,
            );
            $resu = Formularios_model::where("idFormulario", $idform)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.error')]);
            }

        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }
}

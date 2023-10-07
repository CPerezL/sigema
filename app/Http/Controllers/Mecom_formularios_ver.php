<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\mecom\Formularios_model;
use App\Models\mecom\Registros_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Session;

class Mecom_formularios_ver extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '063';
    public $controlador = 'mecom_formularios_ver';
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
        $data['year'] = date('Y'); //---
        Session::put($this->controlador . '.gestion', date('Y'));
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('mecom.formularios_ver', $data);
    }
    public function get_datos(Request $request)
    {
        $idform = $request->input('idform', 0);
        if ($idform > 0) {
            $salida = Formularios_model::getAportantes($idform);
            $total = 0;
            Session::put($this->controlador . '.idform', $idform);
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_formularios(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0 || Auth::user()->nivel > 4) {
            $page = $request->input('page', 1);
            $rows = $request->input('rows', 20); //($page,$rows,$gestion, $logia, $tipo = 1, $level = 1)
            $salida = Formularios_model::getListaForms($page, $rows, Session::get($this->controlador . '.gestion'), $taller, 1, Auth::user()->nivel);
            $total = Formularios_model::getNumeroForms(Session::get($this->controlador . '.gestion'), $taller, 1, Auth::user()->nivel);
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_miembros(Request $request)
    {
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($log > 0) {
            $salida = Formularios_model::getMiembros($log);
            $total = 0;
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }


    public function gen_formulario(Request $request)
    {
        $nform = Session::get($this->controlador . '.idform');
        if ($nform > 0) {
            $datosform = Formularios_model::getDatosForm($nform);
            $tall = $datosform->taller;
            $diassemana = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $data['diaformu'] = $diassemana[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " de " . date('Y'); //Miercoles 05 de Septiembre del 2016
            $data['diaelabor'] = date('Y-n-d'); //
            $datoslog = Formularios_model::getDatosLogia($tall);
            $data['taller'] = $datoslog->nombreCompleto;
            $data['tallern'] = $tall;
            $data['nvalle'] = $datoslog->valletxt;
            $data['nform'] = Session::get($this->controlador . '.idform');
            $data['numero'] = $datosform->numero;
            $data['gestion'] = $datosform->gestion;
            //--montos
            $data['montos'] = Formularios_model::getMontosFormularioQR($nform);
            $data['lista'] = Formularios_model::getAportantesForm($nform);
            $data['dglb'] = Formularios_model::getMontosTipo(1);
            $data['dgdr'] = Formularios_model::getMontosTipo(2);
            $data['dcomap'] = Formularios_model::getMontosTipo(3);
            $nomefile = 'GLSP-' . $tall . '-PlanillaAporte-' . $datosform->numero.'-'.date('dmY');
            // Load all views as normal
            $data['logo'] = 'glsp-150.png';
            $pdf = PDF::loadView('oruno.pdf_form_obolos', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }
}

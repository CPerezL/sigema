<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\mecom\Reporte_pagosnet_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Response;
use Session;

class Mecom_pagosnet_repo_conta extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '072';
    public $controlador = 'mecom_pagosnet_repo_conta';
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
        $data['hasta'] = date("d/m/Y");
        $data['desde'] = date("d/m/Y");
        /*     variables de pagina     */
        return view('mecom.pagosnet_repo_conta', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $salida = Reporte_pagosnet_model::getReportePagosConta($desde, $hasta, $val);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
            return response()->json($qry2);
        } else {
            return response()->json(['total' => 0, 'rows' => '']);
        }
    }
    public function set_datos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $valle = $request->input('valle');
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $data = array($this->controlador . '.desde' => $desde, $this->controlador . '.hasta' => $hasta, $this->controlador . '.valle' => $valle);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function gen_formulario()
    {
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        if ($valle > 0) {
            $desde = Session::get($this->controlador . '.desde');
            $hasta = Session::get($this->controlador . '.hasta');
            $diassemana = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $data['diaformu'] = $diassemana[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " de " . date('Y'); //Miercoles 05 de Septiembre del 2016
            // $dvalle = $this->mecom_webcontador_model->getDatosValle($valle);
            $dvalle=Valles_model::where('idValle',$valle )->first('valle');
            $data['lista'] = Reporte_pagosnet_model::getReportePagosConta($desde, $hasta, $valle);
            $data['resu'] = Reporte_pagosnet_model::getReporteResumen($desde, $hasta, $valle);
            $data['nvalle'] = $dvalle->valle;
            $data['desdefechas'] = $desde . ' a ' . $hasta;
            $nomefile = 'Form-' . $valle;
            // Load all views as normal
            $data['logo'] = 'glb-150.png';
            $pdf = PDF::loadView('pdfs.pdf_form_webobolos', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }

}

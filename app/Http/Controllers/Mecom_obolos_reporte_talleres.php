<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\mecom\Reporte_contabilidad_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Response;
use Session;

class Mecom_obolos_reporte_talleres extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '104';
    public $controlador = 'mecom_obolos_reporte_talleres';
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
        $data['hasta'] = date("d/m/Y");
        $data['desde'] = '01/' . date("m/Y");
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina     */
        return view('mecom.obolos_reporte_talleres', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        $tipo = Session::get($this->controlador . '.tipot');
        $val = self::validar('idValle', 0);
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $salida = Reporte_contabilidad_model::getReporteTramites($desde, $hasta, $tipo, 0, $log);
            //$salida = Reporte_pagosnet_model::getReporteObolosTaller($desde, $hasta, $log);
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
        $tall = $request->input('log');
        $tipo = $request->input('tipo');
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $data = array($this->controlador . '.desde' => $desde, $this->controlador . '.hasta' => $hasta, $this->controlador . '.taller' => $tall, $this->controlador . '.tipot' => $tipo);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
}

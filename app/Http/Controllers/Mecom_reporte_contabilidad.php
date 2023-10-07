<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\mecom\Reporte_contabilidad_model;
use App\Models\mecom\Reporte_pagosnet_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use PDF;
use Response;
use Session;

class Mecom_reporte_contabilidad extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '072';
    public $controlador = 'mecom_reporte_contabilidad';
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
        $data['desde'] = '01/'.date("m/Y");
        //$data['desde'] = date("d/m/Y");
        /*     variables de pagina     */
        return view('mecom.reporte_contabilidad', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        $tipo = Session::get($this->controlador . '.tipot');
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        if ($tipo>=10) {
            $salida = Reporte_contabilidad_model::getReporteTramites($desde, $hasta, $tipo,$val);
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
        $tipo = $request->input('tipo');
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $data = array($this->controlador . '.desde' => $desde, $this->controlador . '.hasta' => $hasta, $this->controlador . '.valle' => $valle, $this->controlador . '.tipot' => $tipo);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\mecom\Reporte_pagosnet_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Response;
use Session;

class Mecom_pagosnet_repo_talleres extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '071';
    public $controlador = 'mecom_pagosnet_repo_talleres';
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
        $data['desde'] = date("d/m/Y");
        /*     variables de pagina     */
        return view('mecom.pagosnet_repo_talleres', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        $val = self::validar('idValle', 0);
        $log = self::validar('idLogia', 0);
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $salida = Reporte_pagosnet_model::getReportePagos($desde, $hasta, $val, $log);
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
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $data = array($this->controlador . '.desde' => $desde, $this->controlador . '.hasta' => $hasta);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
}

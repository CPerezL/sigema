<?php

namespace App\Http\Controllers;

use App\Models\mecom\Reporte_pagosnet_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;
use Response;

class Mecom_reporte_pagosnet_banco extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '024';
    public $controlador = 'mecom_reporte_pagosnet_banco';
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
        return view('mecom.reporte_pagosnet_banco', $data);
    }
    public function get_datos(Request $request)
    {
        $desde = Session::get($this->controlador . '.desde');
        $hasta = Session::get($this->controlador . '.hasta');
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            $qry = Reporte_pagosnet_model::getBanco($desde, $hasta);
            $qry2 = (object) ['total' => 0, 'rows' => $qry];
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
            //   $this->session->set_userdata($data);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.wait')]);
        }
    }
    public function ver_archivo()
    {
    //   $this->load->helper('download');
      $fileName = "archivo_banco.txt";
      //---
      $desde = Session::get($this->controlador . '.desde');
      $hasta = Session::get($this->controlador . '.hasta');
      $salida = Reporte_pagosnet_model::getBanco($desde, $hasta);
      $dataContent = array();
      $cc = 0;
      foreach ($salida AS $item)
      {
        //$dataContent[] = "\n".$item->montototal."#1#Deposito Obolos##$item->codigo#$item->cuenta#$item->entidad";
        $montot = round($item->montototal, 2);
  //      $monto = str_replace('.', ',', $montot);
        if ($montot > 0)
        {
          if ($cc > 0)
            $dataContent[] = "\n".$item->codigo.'@'.$montot.'@'.$item->numeroTrans;
          else
            $dataContent[] = $item->codigo.'@'.$montot.'@'.$item->numeroTrans;
          $cc++;
        }
      }
      $content=implode($dataContent);
      $headers = [
        'Content-type' => 'text/plain',
        'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        'Content-Length' => strlen($content)
      ];
    //   force_download($dataFile, implode($dataContent));
      return Response::make($content, 200, $headers);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\mecom\Pagos_montos_model;
use App\Models\mecom\Pagos_registros_model;
use App\Models\tramites\Iniciacion_model;
use App\Models\tramites\Pagosqr_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;

class Tramites_ini_pagarqr extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '095';
    public $controlador = 'tramites_ini_pagarqr';
    public $tipoqr = 2;
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * revisar el monot generado y el monto a cobrar
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
        $data['pagos'] = Pagos_montos_model::getPagosExtra($this->valle, $this->taller);
        /* variables de sintesis */
        $data['linkiframe'] = env('QR_LINKFRAME'); //enalce del pago por ahora esta directo
        $data['entidad'] = env('QR_ENTIDAD'); //enalce del pago por ahora esta directo
        $data['linkaccion'] = env('QR_LINKACCION'); //
        /*     variables de pagina     */
        return view('tramites.ini_pagarqr', $data);
    }
    public function get_datos(Request $request)
    {
        //$palabra = Session::get($this->controlador . '.palabra');
        //$pago = Session::get($this->controlador . '.pago');
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $salida = Iniciacion_model::getTramitesListos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 5);
        $total = Iniciacion_model::getNumTramitesListos(Session::get($this->controlador . '.palabra', ''), $log, $val, 5);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_ceremonia(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $salida = Pagosqr_model::getDatosPagoIni($id, 2);
        } else {
            $salida = ['success' => 0, 'Msg' => 'Error'];
        }
        return response()->json($salida);
    }

    public function get_datos_pagos(Request $request)
    {
        $reg = $request->input('reg'); //envia tramites

        $datos = Pagosqr_model::getDatosIni($reg, Auth::user()->id, $this->tipoqr); //datos de pago
        $datos->user = Auth::user()->id;
        $datos->idCeremonia = $reg;
        $datos->tipo = $this->tipoqr;
        $datos->Miembro = 'Iniciacion';
        $datos->grado = 1;
        $datos->nuevoPago = date("Y-m-d", time());
        // dd($datos);
        $hash = Pagosqr_model::insertTransaccion($datos, $this->tipoqr); //devulve hash
        if (is_null($hash)) {
            return response()->json(["ref" => "0", "red" => "0", "ok" => "0"]);
        } else {
            $registro = $this->regPago($hash, $datos); //funcion soap
            if (is_null($registro)) {
                return response()->json(['ref' => 0, 'red' => 0, 'ok', 0]);
            } else {
                if (strlen($registro['idTransaccion']) > 8) {
                    $actual['transaccion'] = $registro['idTransaccion'];
                    Pagos_registros_model::where('idRegistro', $hash)->update($actual);
                    return response()->json(["ref" => $registro['idTransaccion'], "red" => $hash, "ok" => 1]);
                } else {
                    return response()->json(['ref' => 0, 'red' => 0, 'ok', 0]);
                }
            }

        }
    }
    private function regPago($codigoRecaudacion, $obolo)
    {
        define("WSDL_PAGOSNET", env('QR_LINKPAGO'));
        //Request
        $categoriaProducto = env('QR_CATEGORIA');
        $codigoComprador = 'GLB ' . $obolo->username; //cÃ³digo interno del cliente
        $correoElectronico = $obolo->email;
        $descripcionRecaudacion = $obolo->descripcion;
        $documentoIdentidadComprador = $obolo->CI;
        $fecha = date("Ymd");
        $fechaVencimiento = date('Ymd', strtotime("+1 day"));
        $hora = '020000';
        $horaVencimiento = '235900'; //0 = para no tiene vencimiento
        $moneda = 'BS'; // BS=Bolivianos, US=Dolares, EU=Euros
        $nombreComprador = $obolo->nombre;
        // Datos para array planillas
        $montoCreditoFiscal = 0;
        $nombreFactura = 'SIGEMA WEB';
        $numeroPago = 1;
        $planillas = array(
            'descripcion' => $obolo->glosa,
            'montoCreditoFiscal' => $montoCreditoFiscal,
            'montoPago' => $obolo->total,
            'nitFactura' => $obolo->CI,
            'nombreFactura' => $nombreFactura,
            'numeroPago' => $numeroPago,
        );
        $precedenciaCobro = 'N';
        $transaccion = 'A'; //A=adicionar, B=baja, M=modificar
        $cuenta = env('QR_CUENTA');
        $password = env('QR_CLAVE');
        //----------------------------------------------
        // Arma el array
        $datos = array(
            'categoriaProducto' => $categoriaProducto,
            'codigoComprador' => $codigoComprador,
            'codigoRecaudacion' => $codigoRecaudacion,
            'correoElectronico' => $correoElectronico,
            'descripcionRecaudacion' => $descripcionRecaudacion,
            'documentoIdentidadComprador' => $documentoIdentidadComprador,
            'fecha' => $fecha,
            'fechaVencimiento' => $fechaVencimiento,
            'hora' => $hora,
            'horaVencimiento' => $horaVencimiento,
            'moneda' => $moneda,
            'nombreComprador' => $nombreComprador,
            'planillas' => $planillas,
            'precedenciaCobro' => $precedenciaCobro,
            'transaccion' => $transaccion,
        );

        $params = array(
            'datos' => $datos,
            'cuenta' => $cuenta,
            'password' => $password,
        );

        //   Preparo la invocaciÃ³n del WS con registroPlan
        $client = null;
        $metodo = '--undefined--';
        $resultPlan = null;
        try {
            //Instancio el ws
            $client = new \soapclient(WSDL_PAGOSNET, array('trace' => true, 'exceptions' => true));
            $resultPlan1 = $client->__soapCall("registroPlan", array($params));
            $resultPlan = get_object_vars($resultPlan1);
            $resultPlan = $resultPlan['return'];
            $resultPlan = get_object_vars($resultPlan);
            //Analizo la respuesta
            if ($resultPlan['codigoError'] === 0) //todo ok
            {
                return $resultPlan;
                //TODO: Cliente debe registrar las respuestas
            } else {
                return null;
            }
        } catch (Exception $ex) {
            if ($client != null) {
                //escribirHandler($client->__getLastRequest(), $metodo, 'Request');
                //escribirHandler($client->__getLastResponse(), $metodo, 'Response');
                return null;
            }
        }
    }

}

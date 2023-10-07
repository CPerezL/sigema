<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\mecom\Formularios_model;
use App\Models\mecom\Obolos_model;
use App\Models\mecom\Registros_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Session;

class Mecom_formularios_aprobar extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '067';
    public $controlador = 'mecom_formularios_aprobar';
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
        Session::put($this->controlador . '.gestion', 2022);
        //Session::put($this->controlador . '.gestion', Config_model::getValue('gestion'));
        Session::put($this->controlador . '.estado', 2);
        Session::put($this->controlador . '.valle', $this->valle);
        $data['_mid'] = $this->idmod; //---
        $data['_level'] = Auth::user()->nivel;
        $data['_folder'] = url('/media/comprobantes/') . '/';
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['year'] = date('Y'); //---
        Session::put($this->controlador . '.gestion', date('Y'));
        /*     variables de pagina* */
        return view('mecom.formularios_aprobar', $data);
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
        if (Auth::user()->nivel > 2) {
            $valle = self::validar('idValle', 0);
            $page = $request->input('page', 1);
            $rows = $request->input('rows', 20);
            $salida = Formularios_model::getListaFormsRev($page, $rows, Session::get($this->controlador . '.gestion'), $valle, $taller, Session::get($this->controlador . '.estado'), 1, Auth::user()->nivel);
            $total = Formularios_model::getNumeroFormsRev(Session::get($this->controlador . '.gestion'), $valle, $taller, Session::get($this->controlador . '.estado'), 1, Auth::user()->nivel);
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
    public function gen_reporte()
    {
        $nform = Session::get($this->controlador . '.idform');
        if ($nform > 0) {
            $diassemana = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $data['diaformu'] = $diassemana[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " de " . date('Y'); //Miercoles 05 de Septiembre del 2016
            //$data['diaelabor'] = date('Y-n-d'); //

            $datosform = Formularios_model::getDatosForm($nform);
            $tall = $datosform->taller;
            $datoslog = Formularios_model::getDatosLogia($tall);
            $data['taller'] = $datoslog->nombreCompleto;
            $data['tallern'] = $datosform->taller;
            $data['nvalle'] = $datoslog->valletxt;
            if ($datosform->estado == 4) {
                $data['documento'] = $datosform->documento;
            } else {
                $data['documento'] = 'No aprobado';
            }

            $data['nform'] = Session::get($this->controlador . '.idform');
            $data['diaelabor'] = $datosform->fechaEnvio;
            $data['numero'] = $datosform->numero;
            $data['gestion'] = $datosform->gestion;
            //--montos
            $data['montos'] = Formularios_model::getMontosFormularioQR($nform);
            $data['lista'] = Formularios_model::getAportantesForm($nform);
            $data['dglb'] = Formularios_model::getMontosTipo(1);
            $data['dgdr'] = Formularios_model::getMontosTipo(2);
            $data['dcomap'] = Formularios_model::getMontosTipo(3);
            //
            $data['mregu'] = Registros_model::getMeses($nform, 'Regular');
            $data['mhono'] = Registros_model::getMeses($nform, 'Honorario');
            $data['mause'] = Registros_model::getMeses($nform, 'Ausente');
            $nomefile = 'GLSP-' . $tall . '-PlanillaAporte-' . $datosform->numero.'-'.date('dmY');
            // Load all views as normal
            $data['logo'] = 'glsp-150.png';
            $pdf = PDF::loadView('oruno.pdf_reporte_obolos', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }
    public function recal_formulario()
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $checkhabi2 = Formularios_model::checkFormEstado($idform, '2'); //si ya hay anviados// valor 2 para anular
            if (!$checkhabi2) {
                $resu2 = $this->recal_obolo($idform);
                Session::put($this->controlador . '.idform', 0);
                if ($resu2 > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Formulario Aprobado por tesoreria, se actualizaron los obolos']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Este formulario ya fue procesado o anulado!!']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function anula_formulario()
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $checkhabi2 = Formularios_model::checkFormEstado($idform, '2'); //si ya hay anviados
            if (!$checkhabi2) {
                $datar['fechaAprobacion'] = date("Y-m-d");
                $datar['estado'] = 5;
                $datar['usuarioAprobacion'] = Auth::user()->id;
                Formularios_model::where('idFormulario', $idform)->update($datar);
                $datao['monto'] = 0;
                $datao['montoGLB'] = 0;
                $datao['montoGDR'] = 0;
                $datao['montoCOMAP'] = 0;
                $datao['fechaPagoNuevo'] = 'ultimoPago';
                $resu2 = Registros_model::where('idFormulario', $idform)->update($datao);
                Session::put($this->controlador . '.idform', 0);
                if ($resu2 > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Formulario Anulado, se anulo los obolos']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Este formulario ya fue procesado o anulado!!']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function recal_obolo($nform)
    {
        $cc = 0;
        $pagoslist = Registros_model::getPagosHechos($nform);
        if (!is_null($pagoslist)) {
            foreach ($pagoslist as $pagos) {
                Formularios_model::from('sgm_miembros')->where('id', $pagos->idMiembro)->update(['ultimoPago' => $pagos->fechaPagoNuevo]);
                $cc++;
            }
        }
        return $cc;
    }
    public function send_formulario(Request $request)
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $checkhabi2 = Formularios_model::checkFormEstado($idform, '2'); //si ya hay anviados
            if (!$checkhabi2) {
                $dataf = array(
                    'estado' => 4,
                    'documento' => $request->input('documento', ''),
                    'fechaAprobacion' => date("Y-m-d H:i:s"),
                    'usuarioAprobacion' => Auth::user()->id,
                    'gestion' => date("Y"),
                );
                $resu2 = Formularios_model::where('idFormulario', $idform)->update($dataf);
                // $this->aprobar_model->salvaFormulario($idform, $dataf);
                $datar['aprobado'] = date("Y-m-d");
                $resu2 = Registros_model::where('idFormulario', $idform)->update($datar);
                // $this->aprobar_model->updateRegistro($idform);
                $resu2 = $this->set_obolo($idform);
                Session::put($this->controlador . '.idform', 0);
                if ($resu2 > 0) {
                    return response()->json(['success' => 'true', 'Msg' => "Formulario Aprobado por tesoreria, se actualizaron los obolos"]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => "Ocurrio un error al salvar los datos"]);
                }

            } else {
                return response()->json(['success' => 0, 'Msg' => "Este formulario no esta completo!!"]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function set_obolo($nform)
    {
        $cc = 0;
        $pagoslist = Registros_model::getPagosHechos($nform);
        if (!empty($pagoslist)) {
            foreach ($pagoslist as $pagos) {
                $datas = array(
                    'IdMiembro' => $pagos->idMiembro,
                    'IdLogia' => $pagos->taller,
                    'NumeroPagos' => $pagos->numeroCuotas,
                    'FechaPago' => date("Y-m-d"),
                    'UltimoPago' => $pagos->fechaPagoNuevo,
                );
                Obolos_model::insert($datas);
                // $this->aprobar_model->setObolo($datas);
                $datam['ultimoPago'] = "$pagos->fechaPagoNuevo";
                Membrecia_model::where('id', $pagos->idMiembro)->update($datam);
                // $this->aprobar_model->updateObolo($pagos->idMiembro, $pagos->fechaPagoNuevo);
                $cc++;
            }
        }
        return $cc;
    }
}

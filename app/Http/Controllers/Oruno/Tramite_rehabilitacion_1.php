<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\Config_model;
use App\Models\mecom\Formularios_model;
use App\Models\mecom\Registros_model;
use App\Models\oruno\Rehabilitaciones_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Session;

class Tramite_rehabilitacion_1 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '055';
    public $controlador = 'tramite_rehabilitacion_1';
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
        $data['_controller'] = $this->controlador;
        $data['mesactual'] = date('01/n/Y'); //10/09/2021
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.rehabilitacion_1', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $qry = Rehabilitaciones_model::getDepositos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, Auth::user()->nivel);
        $total = Rehabilitaciones_model::getNumDepositos(Session::get($this->controlador . '.palabra', ''), $log, $val, Auth::user()->nivel);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_miembros(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $pal = $request->input('filterRules');
            if (strlen($pal) > 3) {
                $palabra = json_decode($pal);
                $filtro = $palabra[0]->value;
            } else {
                $filtro = '';
            }
            $uno = Config_model::getValue('mesesDeuda');
            $dos = Config_model::getValue('mesesIrregular');
            $salida = Rehabilitaciones_model::getMiembros($id, $filtro,$uno,$dos);
            $qry2 = (object) ['total' => 20, 'rows' => $salida];
        } else {
            $qry2 = ['success' => 0, 'Msg' => 'Error...'];
        }
        return response()->json($qry2);
    }

    public function save_tramite(Request $request)
    {
        if ($request->input('id') > 0) {
            $id = $request->input('id');
            if (Rehabilitaciones_model::checkTramite($id)) {
                $data = array(
                    'idMiembro' => $request->input('id'),
                    'idLogia' => $request->input('LogiaActual'),
                    'estado' => 1,
                    'fechaAprobacionLogia' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobacionLogia', '00-00-0000'))->format('Y-m-d'),
                    'actaAprobacionLogia' => $request->input('actaAprobacionLogia'),
                    'tipo' => $request->input('caso'),
                    'gradoActual' => $request->input('Grado', 1),
                    'observaciones' => $request->input('observaciones',''),
                    'ultimoPago' => $request->input('ultimoPagoDate'),
                    'usuario' => Auth::user()->id,
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Rehabilitaciones_model::insertGetId($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.alertramite')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Datos invalidos']);
        }
    }
    public function cambia_tramite(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $data = array(
                'observaciones' => $request->input('observaciones'),
                'fechaAprobacionLogia' => \DateTime::createFromFormat('d/m/Y', $request->input('fAprobacionLogia', '00-00-0000'))->format('Y-m-d'),
                'actaAprobacionLogia' => $request->input('actaAprobacionLogia'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Rehabilitaciones_model::where('id', $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.alertramite')]);
        }

    }
    public function unset_tramite(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Rehabilitaciones_model::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function cambia_datos(Request $request)
    {
        $mes = 'Datos actualizados correctamente';
        $id = $request->input('id');
        $task = $request->input('task');
        $tipo = $request->input('tipo');
        $espe = $request->input('especial');
        if ($id > 0 && $task > 1) {
            if ($task == 4) //proceso de reincorp aprobado
            {
                $mest = self::getFecha($request->input('mesreincorp'), 'd/m/Y', 'Y-n-01'); //solomes

                $mes2 = $this->createFormulario($id, $mest); //crea proceso de datos
                if ($mes2 == 7) {
                    $data = array(
                        'estado' => 4,
                        'fechaAprobacionGDR' => date('Y-m-d h:m:s'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                    $resu = Rehabilitaciones_model::where('id', $id)->update($data);
                    $mes = 'Reincoporacion exitosa, datos de miembro actualizados =' . $mes2;
                } else {
                    $resu = 0;
                }
            } else {
                if ($task == 2 && $tipo > 2 && $espe == 0) {
                    $data = array(
                        'especial' => 1,
                        'observaciones' => $request->input('observaciones'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                } else {
                    $data = array(
                        'estado' => $task,
                        'observaciones' => $request->input('observaciones'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                }
                $resu = Rehabilitaciones_model::where('id', $id)->update($data);
            }
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.alertramite')]);
        }
    }
    private function createFormulario($idt, $mest)
    { // funcion que registra el aporte yt actualiza los pagos
        //obtener id formulari general
        $ntram = Rehabilitaciones_model::getDatosTramite($idt);
        $idm = $ntram->idMiembro;
        $val = $ntram->valle;
        // $idm = $tram->idMiembro;
        // $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $gestion = Config_model::getValue('gestion');
        $numero = Rehabilitaciones_model::getFormulario($gestion, $val) + 1;
        //inserta formulario
        $datasf = array(
            'tipo' => 4, //obolos
            'estado' => 4,
            'numero' => $numero,
            'gestion' => $gestion,
            'taller' => 0,
            'documento' => 'Reinc-' . $gestion,
            'idValle' => $val,
            'usuarioCreacion' => Auth::user()->id,
            'usuarioAprobacion' => 0,
            'numeroMiembros' => 0,
            'montoTotal' => 0,
        );
        $idf = Formularios_model::insertGetId($datasf);
        // iserta registro
        if (Registros_model::checkRegistro($idf, $idm)) {
            $nc = 12;
            $hoy = date('Y-n-d'); //mes de reularizacion
            $mesahora = $mest;
            $mesante = date("Y-m-d", strtotime($mesahora . "- $nc month")); //se resta un año
            $miembro = Rehabilitaciones_model::getDatosMiembro($idm);
            $sueno = $this->mesesdif($mest, $miembro->ultimoPago); //ok
            $montos = $this->procesarCuotas($mesahora, $miembro->Miembro, $val, $nc); //ok
            $datar = array(
                'idFormulario' => $idf,
                'taller' => $miembro->numero,
                'idMiembro' => $idm,
                'miembro' => $miembro->Miembro,
                'grado' => $miembro->Grado,
                'numeroCuotas' => $nc,
                'mesesDescuento' => 0,
                'idDescuento' => 0,
                'aprobado' => $hoy,
                'monto' => $montos[0],
                'montoGLB' => $montos[1],
                'montoGDR' => $montos[2],
                'montoCOMAP' => $montos[3],
                'ultimoPago' => $mesante,
                'fechaPagoNuevo' => $mesahora,
                'usuario' => Auth::user()->id,
            );
            $idreg = Registros_model::insertGetId($datar); //
            if ($idreg > 0) {
                $datam['ultimoPago'] = "$mesahora";
                $datam['socio'] = 2;
                $datam['mesesprofano'] = $sueno;
                $res = Membrecia_model::where('id', $idm)->update($datam);
                if ((int) $res > 0) {
                    return 7;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
        return 1;
    }
    public function registra_pago(Request $request)
    {
        $id = $request->input('id', 0);
        $resu = 0;
        if ($id > 0) {
            $archivo = $this->upload_file($request, 'fileup');
            $archivo2 = $this->upload_file($request, 'fileup1');
            $archivo3 = $this->upload_file($request, 'fileup2');
            if (strlen($archivo) > 10) {
                $data = array(
                    'estado' => 3,
                    'archivo' => $archivo,
                    'archivo2' => $archivo2,
                    'archivo3' => $archivo3,
                    'fechaDeposito' => \DateTime::createFromFormat('d/m/Y', $request->input('fDeposito', '00-00-0000'))->format('Y-m-d'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Rehabilitaciones_model::where('id', $id)->update($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
            }
        }
    }
    private function upload_file(Request $request, $fileup, $folder = 'media/tramites')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$fileup" => 'required|mimes:png,jpg,jpeg,pdf|max:2048',
            ]
        );
        if ($request->file($fileup)) {
            try {
                $file = $request->file($fileup);
                $filename = time() . md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension(); //nombre nuevo
                // File upload location
                $location = $folder;
                //$location = 'import';
                // Upload file
                $file->move($location, $filename);
                return $filename;
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 1;
        }
    }
    private function mesesdif($uno, $dos)
    {
        $fechainicial = new \DateTime($uno);
        $fechafinal = new \DateTime($dos);
        $diferencia = $fechainicial->diff($fechafinal);
        $meses = ($diferencia->y * 12) + $diferencia->m;
        return $meses;
    }
    private function procesarCuotas($pago, $miembro, $valle, $nc)
    {
        $mnt = Rehabilitaciones_model::getDatosCuota($pago, $miembro, $valle);
        $monto[0] = $mnt->monto0 * $nc; //suma
        $monto[1] = $mnt->monto1 * $nc; //glb
        $monto[2] = $mnt->monto2 * $nc; //gdr
        $monto[3] = $mnt->monto3 * $nc; //comap
        return $monto;
    }
    public function gen_certificado(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $nomefile = 'CetDeposito-' . $id;
            $dcere = Rehabilitaciones_model::getDatosCert($id);
            // Load all views as normal
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $data['logo'] = 'glb-150.png'; //ok
            $data['nvalle'] = $dcere->valle;
            $data['docder'] = $dcere->valle;
            $data['tramite'] = $dcere->id;
            $data['fecha'] = date('d') . " de " . $meses[date('n') - 1] . " de " . date('Y');
            $fecha = explode('-', $dcere->fechaAprobacionGDR);
            $data['gestionet'] = $fecha[0]; //--
            $data['taller'] = $dcere->logia . ' Nro ' . $dcere->numero;
            $data['profano'] = $dcere->GradoActual . ' ' . strtoupper($dcere->NombreCompleto);
            $data['fechadep'] = $dcere->fechaDeposito;
//--
            $pdf = PDF::loadView('pdfs.pdf_cert_reincorporacion', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            echo trans('mess.errdata');
        }

    }
}

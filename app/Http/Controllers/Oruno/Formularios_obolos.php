<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\Config_model;
use App\Models\mecom\Formularios_model;
use App\Models\mecom\Pagos_registros_model;
use App\Models\mecom\Registros_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Session;

use function PHPUnit\Framework\isNull;

class Formularios_obolos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '064';
    public $controlador = 'formularios_obolos';
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
        /* variables de sintesis */
        $data['linkiframe'] = env('QR_LINKFRAME'); //enalce del pago por ahora esta directo
        $data['entidad'] = env('QR_ENTIDAD'); //enalce del pago por ahora esta directo
        $data['linkaccion'] = env('QR_LINKACCION'); //enalce del pago por ahora esta directo
        /* ------ */
        $data['_controller'] = $this->controlador;
        $data['_mid'] = $this->idmod; //---
        $data['year'] = date('Y'); //---
        Session::put($this->controlador . '.gestion', date('Y'));
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.formularios_obolos', $data);
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
            $rows = $request->input('rows', 20);
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
    public function createqr_formaporte(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0) {
            if (Config_model::getValue('gestion') == Session::get($this->controlador . '.gestion')) {
                $check = Formularios_model::checkFormulario(Config_model::getValue('gestion'), $taller, '1,2', 1); //COMPRUEBA SI NO HAY ORTO FORMULARI OACTIVO
                if ($check) {
                    $dlogia = Formularios_model::getDatosLogia($taller);
                    $valle = $dlogia->valle;
                    $numero = Formularios_model::getUltimoForm($taller, Config_model::getValue('gestion'), 1) + 1;
                    $datas = array(
                        'tipo' => 1, //obolos
                        'estado' => 1,
                        'numero' => $numero,
                        'gestion' => Config_model::getValue('gestion'),
                        'taller' => $taller,
                        'idValle' => $valle,
                        'usuarioCreacion' => Auth::user()->id,
                        'usuarioAprobacion' => 0,
                        'numeroMiembros' => 0,
                        'montoTotal' => 0,
                    );
                    $resu = Formularios_model::insert($datas);
                    if ($resu > 0) {
                        return response()->json(['success' => 'true', 'Msg' => 'Formulario nuevo creado correctamente']);
                    } else {
                        return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al crear los datos']);
                    }
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Ya tiene un formulario abierto solo se permite uno activo']);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'La gestion seleccionada no esta activa']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error datos faltantes']);
        }
    }
    public function set_pagomiembro(Request $request)
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $id = $request->input('idmiembro');
            if ($id > 0) {
                $dataform = Formularios_model::where('idFormulario', $idform)->first();
                if ($dataform->estado == 1) { //solo eactivo
                    $checkdoble = Registros_model::checkRegistro($idform, $id);
                    if ($checkdoble) {
                        $nc = $request->input('nqrcuotas');
                        $miembro = Formularios_model::getDatosMiembro($id, $dataform->idValle);
                        if (is_null($miembro)) {
                            return response()->json(['success' => 0, 'Msg' => 'Este miembro esta exento']);
                        } else {
                            $montos = $this->procesarCuotas($miembro->ultimoPago, $miembro->Miembro, $dataform->idValle, $nc, $id);
                            if ($montos[4] > 0) {
                                $nc = $nc + $montos[4];
                                $nuevomes = date("Y-m-d", strtotime($miembro->ultimoPago . "+ $nc month"));
                            } else {
                                $nuevomes = date("Y-m-d", strtotime($miembro->ultimoPago . "+ $nc month"));
                            }
                            $datas = array(
                                'idFormulario' => $idform,
                                'taller' => Session::get($this->controlador . '.taller'),
                                'idMiembro' => $id,
                                'miembro' => $miembro->Miembro,
                                'grado' => $miembro->Grado,
                                'numeroCuotas' => $montos[6],
                                'mesesDescuento' => $montos[3],
                                'idDescuento' => $montos[5],
                                'monto' => $montos[0],
                                'montoGLB' => $montos[1],
                                'montoGDR' => $montos[2],
                                'montoCOMAP' => $montos[3],
                                'montoTaller' => $montos[7],
                                'ultimoPago' => $miembro->ultimoPago, //edberi ade leer el ultim0ago del peerosanje
                                'fechaPagoNuevo' => $nuevomes, //edberi ade leer el ultim0ago del peerosanje
                                'usuario' => Auth::user()->id,
                            );
                            if ($montos[0] > 0 && $idform > 0 & $nc > 0) { ///--
                                $resu = Registros_model::insert($datas); //----------------------------------------------------------->
                                //$resu = 0;
                                if ($resu > 0) {
                                    // Formularios_model::updateFormulario($idform, $montos[0]); //suma montos al formulario final
                                    Formularios_model::where('idFormulario', $idform)->update(['numeroMiembros' => \DB::raw('numeroMiembros + 1'), 'montoTotal' => \DB::raw('montoTotal+' . $montos[0])]);
                                    if ($montos[5] > 0) {
                                        return response()->json(['success' => 'true', 'Msg' => "Pago asignado correctamente con descuento de ' . $montos[4] . ' meses"]);
                                    } else {
                                        return response()->json(['success' => 'true', 'Msg' => 'Pago asignado correctamente']);
                                    }
                                } else {
                                    return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al procesar los datos']);
                                }
                            } else {
                                return response()->json(['success' => 0, 'Msg' => 'Valores no encontrados']);
                            }
                        }
                    } else {
                        return response()->json(['success' => 0, 'Msg' => 'El aportante ya esta en lista, elimine primero si necesita modificar']);
                    }
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'El formulario no puede ser modificado, ya fue enviado, aprobado o anulado']);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Seleccione formulario']);
        }
    }
    public function remove_obolo(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $obolo = Registros_model::getObolo($id);
            $resu = Registros_model::where('idRegistro', $id)->delete();
            if ($resu > 0) {
                Formularios_model::where('idFormulario', $obolo->idFormulario)->update(['numeroMiembros' => \DB::raw('numeroMiembros - 1'), 'montoTotal' => \DB::raw('montoTotal - ' . $obolo->monto)]);
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function procesarCuotas($upago, $miembro, $valle, $cantidad, $idm)
    {
        $monto[0] = 0; //suma
        $monto[1] = 0; //glb
        $monto[2] = 0; //gdr
        $monto[3] = 0; //comap
        $monto[4] = 0; //descuentos
        $monto[5] = 0; //iddescuento
        $monto[6] = 0; //numerocuotas
        $monto[7] = 0; //taller
        $mont1 = Formularios_model::getMontoTaller($idm);
        $mont2 = Formularios_model::getMontoValle($miembro, $valle);
        $monts = Formularios_model::getMontosUnicos($miembro, $valle);
        foreach ($monts as $mm) {
            $montof[$mm->entidad] = $mm->montos;
        }
        $monto[0] = ($mont1 + $mont2) * $cantidad; //glb
        $monto[1] = $montof[1] * $cantidad; //glb
        $monto[2] = $montof[2] * $cantidad; //gdr
        $monto[3] = $montof[3] * $cantidad; //comap
        $monto[6] = $cantidad; //numerocuotas
        $monto[7] = $mont1 * $cantidad; //taller
        return $monto;
    }

    public function gen_qrformulario(Request $request)
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
            $nomefile = 'GLSP-' . $tall . '-PlanillaAporte-' . $datosform->numero . '-' . date('dmY');
            // Load all views as normal
            $data['logo'] = 'glsp-150.png';
            $pdf = PDF::loadView('oruno.pdf_form_obolos', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }
    public function anular_formulario(Request $request)
    {
        $idform = Session::get($this->controlador . '.idform');
        if ($idform > 0) {
            $checkhabi2 = Formularios_model::checkFormEstado($idform, '4'); //si ya hay anviados
            if ($checkhabi2) {
                $fdata['fechaAprobacion'] = date("Y-m-d");
                $fdata['estado'] = 5;
                $fdata['usuarioAprobacion'] = Auth::user()->id;
                //******/
                $odata['monto'] = 0;
                $odata['montoGLB'] = 0;
                $odata['montoGDR'] = 0;
                $odata['montoCOMAP'] = 0;
                $odata['montoTaller'] = 0;
                $resu = Registros_model::where('idFormulario', $idform)->update($odata);
                if ($resu > 0) {
                    $raf = Formularios_model::where('idFormulario', $idform)->update($fdata);
                    if ($raf > 0) {
                        Session::put($this->controlador . '.idform', 0);
                        return response()->json(['success' => 'true', 'Msg' => 'Formulario Anulado, se anulo obolos, cree o envie otro formulario ']);
                    } else {
                        return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                    }
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Formulario en blanco o ya esta anulado']);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Este formulario no esta completo!!']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function upload_file(Request $request, $fileup, $folder = 'media/comprobantes')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$fileup" => 'required|mimes:png,jpg,jpeg,pdf|max:1148',
            ]
        );
        if ($request->file($fileup)) {
            try {
                $file = $request->file($fileup);
                $filename = time() . md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension(); //nombre nuevo
                // File upload location
                $location = $folder;
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
    public function send_formulario(Request $request)
    {
        $id = Session::get($this->controlador . '.idform');
        $resu = 0;
        $msg = '';
        if ($id > 0) {
            if ($request->hasFile('fileup')) {
                $archivo = $this->upload_file($request, 'fileup');
            } else {
                $archivo = '';
                $msg = 'archivo invalido o mas grande que 10 Megas';
            }

            if ($request->hasFile('fileup2')) {
                $archivo2 = $this->upload_file($request, 'fileup2');
            } else {
                $archivo2 = '';
                $msg = 'archivo invalido o mas grande que 10 Megas';
            }

            if (strlen($archivo) > 10) {
                $fdata['archivoGLB'] = $archivo;
                $fdata['archivoCOMAP'] = $archivo2;
                $fdata['fechaEnvio'] = date('Y-m-d h:m:s');
                $fdata['estado'] = 2;
                $resu = Formularios_model::where('idFormulario', $id)->update($fdata);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Formulario Enviado ' . $msg]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'No se pudo enviar formulario ' . $msg]);
                }
            } else {

                return response()->json(['success' => 0, 'Msg' => "Archivo invalido "]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function get_datos_qr(Request $request)
    {
        $idf = $request->input('id');
        if ($idf > 0) {
            $salida = Formularios_model::getDatosQR($idf);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait"']);
        }
    }
        public function gen_recibo(Request $request)
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
            if ($datosform->estado == 4) {
                $data['estado'] = 'PLANILLA APROBADA';
                $data['pagado'] = $datosform->fechaAprobacion;
            } else {
                $data['estado'] = 'PLANILLA SIN APROBACION';
                $data['pagado'] = 'Obolo no procesado';
            }

            $data['numero'] = $datosform->numero;
            $data['gestion'] = $datosform->gestion;
            //--montos
            $data['montos'] = Formularios_model::getMontosFormularioQR($nform);

            $data['lista'] = Formularios_model::getAportantesForm($nform);
            $data['dglb'] = Formularios_model::getMontosTipo(1);
            $data['dgdr'] = Formularios_model::getMontosTipo(2);
            $data['dcomap'] = Formularios_model::getMontosTipo(3);
            $nomefile = 'GLSP-' . $tall . '-Comprobante-' . $datosform->numero;
            // Load all views as normal
            $data['logo'] = 'glsp-150.png';
            $pdf = PDF::loadView('pdfs.pdf_form_obolosrecibo', $data);
            $pdf->set_paper('letter', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }
}

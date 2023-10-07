<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Ritos_model;
use App\Models\Distritos_model;
use App\Models\oruno\Certificados_model;
use App\Models\oruno\Regularizacion_model;
use App\Models\Version_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use App\Models\Config_model;
use PDF;
use Session;

class Oruno_tramite_regu_6 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '460';
    public $controlador = 'oruno_tramite_regu_6';
    private $urlqr = 'https://hh.granlogia.org/';
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
        $data['_folder'] = url('/') . '/';
        $data['palabra'] = Session::get($this->controlador . '.palabra', '');
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.regu_6', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Regularizacion_model::getCeremoniasHechas($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, '6,8');
        $total = Regularizacion_model::getNumCeremoniasHechas(Session::get($this->controlador . '.palabra', ''), $log, $val, '6,8');
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function procesar_datos(Request $request)
    {
        $id = $request->input('id', 0);
        $idm = $request->input('idm', 0);
        $task = $request->input('task', 0);
        if ($id > 0) {
            if ($task == 1) { //copiar rum
                $tt = Regularizacion_model::find($id);
                //dd($tt);
                $idres = Regularizacion_model::insertMiembro($tt);
                $resf = $this->copiaFoto($tt->foto);
                $resu = Regularizacion_model::where('idTramite', $id)->update(['idMiembro' => $idres]);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Datos creados en el RUM']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Error de datos']);
                }

            } elseif ($task == 2) { //copiarfoto
                $mm = Regularizacion_model::select('idMiembro', 'foto')->find($id);
                $resf = $this->copiaFoto($mm->foto);
                if ($resf > 0) {
                    $res = Regularizacion_model::updateMiembro($mm->idMiembro, $mm->foto);
                } else {
                    $res = 0;
                }
                if ($res > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Foto copiada de tramite a Datos de Miembro RUM']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Error de datos ' . $resf]);
                }
            } elseif ($task == 3 && $idm > 0) { //asignar rum al tramite
                $data = array('idMiembro' => $idm);
                $resu = Regularizacion_model::where('idTramite', $id)->update($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'RUM asignado a tramite']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Error de datos']);
                }

            } else {
                return response()->json(['success' => 'true', 'Msg' => 'Proceso no hallado']);
            }

        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error de datos']);
        }
    }
    private function copiaFoto($foto)
    {
        if (strlen($foto) > 5 && file_exists('media/fotos/' . $foto)) {
            if (copy('media/fotos/' . $foto, 'media/miembros/' . $foto)) {
                return 1;
            }
        }
        return 0;
    }
    public function get_nombres(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $datos = Regularizacion_model::getNombres($id);
            $salida = Regularizacion_model::buscarMiembros($datos->apPaterno, $datos->apMaterno);
            // dd($salida);
            $total = 0;
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    //**** */

    public function gen_reporte(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $dcere = Certificados_model::getDatosCertificado($id);
            // dd($dcere);
            $nomefile = 'Cert-A-' . str_pad($dcere->numeroCertificado, 7, "0", STR_PAD_LEFT);
            // Load all views as normal
            $imaqr = $this->getCodigoQR($id);
            $data['imaqr'] = $imaqr;
            $data['fechacert'] = $this->getFechaCert($dcere->fechaCertificado); //

            $ritotxt = Ritos_model::getValue($dcere->rito, 'nombreTexto', 'Error');
            $data['ritocert'] = 'Primer Grado Simbólico del ' . $ritotxt;
            $datagdr = Distritos_model::getDato($dcere->tipo);
            $data['tipogdr'] = $datagdr->nombre;
            $data['gdr1'] = $datagdr->autoridad1;
            $data['gdr2'] = $datagdr->autoridad2;
            $data['gdr3'] = $datagdr->autoridad3;
            $data['tipocert'] = 'CERTIFICADO DE APRENDIZ';
            $data['numecert'] = 'A.-' . str_pad($dcere->numeroCertificado, 7, "0", STR_PAD_LEFT);

            $data['taller'] = $dcere->nombreCompleto;
            if (strlen($dcere->nombreCompleto) > 40) {
                $data['salto'] = '';
            } else {
                $data['salto'] = '<p>&nbsp;</p>';
            }
            $data['nvalle'] = $dcere->valle; //
            $data['suscrito'] = $dcere->nombretxt;
            //*****firmantes *************/
            $dc = Version_model::getValue('certificados');
            $df = explode(',', $dc);

            $firma1 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[0]);
            $firma2 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[1]);
            $firma3 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[2]);
            $data['firma1'] = ucwords(strtolower($firma1->Nombres)) . ' ' . ucwords(strtolower($firma1->Paterno)) . ' ' . ucwords(strtolower($firma1->Materno)) . '<br>' . ucwords(strtolower($firma1->oficial));
            $data['firma2'] = ucwords(strtolower($firma2->Nombres)) . ' ' . ucwords(strtolower($firma2->Paterno)) . ' ' . ucwords(strtolower($firma2->Materno)) . '<br>' . ucwords(strtolower($firma2->oficial));
            $data['firma3'] = ucwords(strtolower($firma3->Nombres)) . ' ' . ucwords(strtolower($firma3->Paterno)) . ' ' . ucwords(strtolower($firma3->Materno)) . '<br>' . ucwords(strtolower($firma3->oficial));
            $data['firma1qr'] = $this->getFirmaQR($dcere->codGes . '_' . $df[0]);
            $data['firma2qr'] = $this->getFirmaQR($dcere->codGes . '_' . $df[1]);
            $data['firma3qr'] = $this->getFirmaQR($dcere->codGes . '_' . $df[2]);

            $pdf = PDF::loadView('oruno.pdf_qr_certificado', $data);
            $pdf->set_paper('A4', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            echo 'Error Certificado no seleccionado';
        }
    }
    private function getFirmaQR($idf)
    {
        $cod = bin2hex('111_' . $idf);
        $imaurl = $this->urlqr . 'api/code/' . $cod;
        $fileqr = realpath(base_path()) . '/storage/app/qr/' . $cod . '.svg';
        \QrCode::generate($imaurl, $fileqr);
        return $fileqr;
    }
    private function getCodigoQR($idf)
    {
        $cod = bin2hex('11_' . $idf);
        $imaurl = $this->urlqr . 'api/code/' . $cod;
        $fileqr = realpath(base_path()) . '/storage/app/qr/' . $cod . '.svg';
        \QrCode::generate($imaurl, $fileqr);
        return $fileqr;
    }
    private function getFechaCert($fecha)
    {
        //22 días del mes de abril de 2022
        //$fecha = date("d-m-Y");
        $ges = explode('-', $fecha);
        $ames[1] = 'enero';
        $ames[2] = 'febrero';
        $ames[3] = 'marzo';
        $ames[4] = 'abril';
        $ames[5] = 'mayo';
        $ames[6] = 'junio';
        $ames[7] = 'julio';
        $ames[8] = 'agosto';
        $ames[9] = 'septiembre';
        $ames[10] = 'octubre';
        $ames[11] = 'noviembre';
        $ames[12] = 'diciembre';
        if ($ges[2] > 1) {
            $fecha = (int) $ges[2] . ' días del mes de ' . $ames[(int) $ges[1]] . ' de ' . $ges[0];
        } else {
            $fecha = ' 1er día del mes de ' . $ames[(int) $ges[1]] . ' de ' . $ges[2];
        }

        return $fecha;
    }
    public function get_tramites(Request $request)
    {
        $tramites[0] = $request->input('id');
        if (count($tramites) > 0) {
            $salida = Regularizacion_model::getTramites($tramites);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.error')]);
        }
    }
    public function update_ceremonia(Request $request)
    {
        $id1 = $request->input('id', '0');
        $ok = $request->input('okPagoDerechos', 0);
        if ($id1 > 0) {
            if ($ok == 1) {
                $fc = $request->input('fechaCeremonia1', '00-00-0000');
                $num = $request->input('numero1', 1);
                //$fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
                $codges = Config_model::getValue('codGes');
                $data = array(
                    'okPagoDerechos' => 1,
                    'certificado' => 1,
                    'codGes' => $codges,
                    'fechaModificacion' => date('Y-m-d'),
                    'nivelActual' => 8,
                );
                $resu = Regularizacion_model::where("idTramite", $id1)->update($data);
            } else {
                $fc = $request->input('fechaCeremonia1', '00-00-0000');
                $num = $request->input('numero1', 1);
                $fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
                $codges = Config_model::getValue('codGes');
                $data = array(
                    'codGes' => $codges,
                    'fechaModificacion' => date('Y-m-d'),
                );
                $resu = Regularizacion_model::where("idTramite", $id1)->update($data);
                $resu = 2;
            }
            if ($resu == 2) {
                return response()->json(['success' => 'true', 'Msg' => 'Derechos no aprobados']);
            }
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Ritos_model;
use App\Models\Distritos_model;
use App\Models\oruno\Certificados_model;
use App\Models\oruno\Iniciacion_model;
use App\Models\Version_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use PDF;
use Session;

class Oruno_tramite_ini_6 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '107';
    public $controlador = 'oruno_tramite_ini_6';
    private $urlqr = 'https://hh.granlogiadebolivia.bo/';
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
        return view('oruno.ini_6', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getCeremoniasHechas($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 7);
        $total = Iniciacion_model::getNumCeremoniasHechas(Session::get($this->controlador . '.palabra', ''), $log, $val, 7);
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
                $tt = Iniciacion_model::find($id);
                //dd($tt);
                $idres = Iniciacion_model::insertMiembro($tt);
                $resf = $this->copiaFoto($tt->foto);
                $resu = Iniciacion_model::where('idTramite', $id)->update(['idMiembro' => $idres]);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Datos creados en el RUM']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Error de datos']);
                }

            } elseif ($task == 2) { //copiarfoto
                $mm = Iniciacion_model::select('idMiembro', 'foto')->find($id);
                $resf = $this->copiaFoto($mm->foto);
                if ($resf > 0) {
                    $res = Iniciacion_model::updateMiembro($mm->idMiembro, $mm->foto);
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
                $resu = Iniciacion_model::where('idTramite', $id)->update($data);
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
            $datos = Iniciacion_model::getNombres($id);
            $salida = Iniciacion_model::buscarMiembros($datos->apPaterno, $datos->apMaterno);
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

            $data['tipocert'] = 'Aprendiz Masón';
            // Load all views as normal
            $data['fechacert'] = $this->getFechaCert($dcere->fechaCertificado);
            $data['numecert'] = 'C.-' . str_pad($dcere->numeroCertificado, 7, "0", STR_PAD_LEFT);
            $data['taller'] = $dcere->nombreCompleto;
            $data['nvalle'] = $dcere->valle; //
            //$data['casotxt'] = 'Y ha sido afiliado a la Aug.·. y Resp.·. Log.·.Simb.·.';
            $data['casotxt'] = 'Que le fue otorgado por la Aug.·. y Resp.·. Log.·.Simb.·.';
            $data['suscrito'] = $dcere->nombretxt;
            //*****firmantes *************/
            $dc = Version_model::getValue('certificados');
            $df = explode(',', $dc);
            $firma1 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[0]);
            $firma2 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[1]);
            $firma3 = Certificados_model::getFirmaCertificado($dcere->codGes, $df[2]);
            if (!is_null($firma1) && !is_null($firma2) && !is_null($firma3)) {
                $data['firma1'] = ucwords(strtolower($firma1->Nombres)) . ' ' . ucwords(strtolower($firma1->Paterno)) . ' ' . ucwords(strtolower($firma1->Materno)) . '<br>' . ucwords(strtolower($firma1->oficial));
                $data['firma2'] = ucwords(strtolower($firma2->Nombres)) . ' ' . ucwords(strtolower($firma2->Paterno)) . ' ' . ucwords(strtolower($firma2->Materno)) . '<br>' . ucwords(strtolower($firma2->oficial));
                $data['firma3'] = ucwords(strtolower($firma3->Nombres)) . ' ' . ucwords(strtolower($firma3->Paterno)) . ' ' . ucwords(strtolower($firma3->Materno)) . '<br>' . ucwords(strtolower($firma3->oficial));
            } else {
                $data['firma1'] = '1';
                $data['firma2'] = '2';
                $data['firma3'] = '3';
            }
            $pdf = PDF::loadView('oruno.pdf_certificado', $data);
            $pdf->set_paper('Legal', 'portrait');
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
}

<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Ritos_model;
use App\Models\admin\Valles_model;
use App\Models\admin\Membrecia_model;
use App\Models\Distritos_model;
use App\Models\oruno\Certificados_model;
use App\Models\oruno\Exaltacion_model;
use App\Models\Version_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use PDF;
use Session;

class Oruno_tramite_exa_4 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $urlqr = 'https://hh.granlogiadebolivia.bo/';
    public $idmod = '078';
    public $controlador = 'oruno_tramite_exa_4';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('oruno.exa_4', $data);
    }
    public function get_datos(Request $request)
    {

        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = Session::get($this->controlador . '.taller');
        $qry = Certificados_model::getExaltados($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val);
        $total = Certificados_model::getNumExaltados(Session::get($this->controlador . '.palabra', ''), $log, $val);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', Session::get($this->controlador . '.valle', 0)));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
    }
    public function gen_reporte(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $dcere = Certificados_model::getDatosCertificadoExa($id);
            $nomefile = 'Cert-M-' . str_pad($dcere->numeroCertificado, 7, "0", STR_PAD_LEFT);
            $data['tipocert'] = 'Maestro Masón';
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
            $pdf->set_paper('A4', 'portrait');
            return $pdf->download($nomefile . '.pdf');
        } else {
            echo 'Error Formulario no seleccionado';
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
        $cod = bin2hex('13_' . $idf);
        $imaurl = $this->urlqr . 'api/code/' . $cod;
        $fileqr = realpath(base_path()) . '/storage/app/qr/' . $cod . '.svg';
        \QrCode::generate($imaurl, $fileqr);
        return $fileqr;
    }
    private function getFechaCert($fecha)
    {
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
        $dia = self::saber_dia($fecha);
        $fecha = $dia . ' ' . (int) $ges[2] . ' de ' . $ames[(int) $ges[1]] . ' de ' . $ges[0] . ' e.·.v.·.';
        return $fecha;
    }
    public function update_data(Request $request)
    {
        $id = $request->input('idtra');
        if ($id > 0) {
            $datos = Exaltacion_model::select('idMiembro', 'fechaExaltacion', 'fechaCertificado','numeroCertificado', 'logia')->find($id);
            $data = array(
                'Grado' => 3,
                'FechaCertificadoExal' => $datos->fechaCertificado,
                'CertificadoExal' => $datos->numeroCertifiacdo,
                'LogiaExaltacion' => $datos->logia,
                'FechaExaltacion' => $datos->fechaExaltacion,
                'fechaModificacion' => date('Y-m-d'),
            );
            Membrecia_model::where('id', $datos->idMiembro)->update($data);

            return response()->json(['success' => true, 'Msg' => 'Datos actualizados correctamente']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error de datos']);
        }
    }
    private function saber_dia($nombredia)
    {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        $fecha = $dias[date('N', strtotime($nombredia))];
        return $fecha;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Pdf;
use Session;

class Tramites_ini_siete extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '099';
    public $controlador = 'tramites_ini_siete';
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
        //$data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('tramites.ini_siete', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getCeremonias($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 7);
        $total = Iniciacion_model::getNumCeremonias(Session::get($this->controlador . '.palabra', ''), $log, $val, 7);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    // public function get_tramite(Request $request)
    // {
    //     $tramite = $request->input('idTra', 0);
    //     if ($tramite > 0) {
    //         $salida = Iniciacion_model::getTramiteIni($tramite, 4);
    //         return response()->json($salida);
    //     } else {
    //         return response()->json(['success' => 0, 'Msg' => 'Wait"']);
    //     }
    // }
    // public function update_tramite(Request $request)
    // {
    //     $id = $request->input('idTramite', 0);
    //     if ($id > 0) {
    //         $data = array(
    //             'fechaIniciacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', '00-00-0000'))->format('Y-m-d'),
    //             'nivelActual' => 6,
    //             'fechaModificacion' => date('Y-m-d'),
    //         );
    //         Iniciacion_model::where('idTramite', $id)->update($data);
    //         $salida = ['success' => 'true', 'Msg' => "Tramite validado correctamente"];
    //     } else {
    //         $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
    //     }

    //     return response()->json($salida);
    // }
    public function get_reporte(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $dcere = Iniciacion_model::getDatosCeremonia($id);
            $data['logo'] = 'glb-150.png'; //ok
            $data['nvalle'] = $dcere->valle;
            $data['fecha'] = date('d/n/Y');
            $data['gestion'] = date('Y'); //--
            $data['solicitud'] = 'no hay com over ahora';
            $data['taller'] = $dcere->logia . ' Nro ' . $dcere->numero;
            $inis = Iniciacion_model::getIniciados($dcere->numero, $dcere->fechaIniciacion);
            $asis = '';
            $cc = 0;
            foreach ($inis as $lista) {
                $cc++;
                $asis .= "<li>A:.M:. " . ucfirst($lista->apPaterno) . " " . ucfirst($lista->apMaterno) . " " . ucfirst($lista->nombres) . "</li>";
            }
            if ($cc == 1) {
                $data['textounocert'] = 'el certificado de:';
                $data['textodoscert'] = 'el Hermano cuyo nombre';
            } else {
                $data['textounocert'] = 'los certificados de:';
                $data['textodoscert'] = 'los Hermanos cuyos nombres';
            }

            $data['asistencia'] = $asis;
            $data['monto'] = 800 * $cc;
            $filename = 'Form-GL-9' . $id . '.pdf';
            $pdf = PDF::loadView('pdfs.pdf_ceremonias', $data);
            $pdf->set_paper('Letter', 'portrait');
            return $pdf->download($filename);
        } else {
            echo 'Error Formulario no seleccionado';
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Valles_model;
use App\Models\tramites\Iniciacion_circulares_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use PDF;
use Session;

class Tramites_ini_circulares extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '082';
    public $controlador = 'tramites_ini_circulares';
    public $filename = '';
    public $paginas = 1;
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
        $data['_folder'] = url('/') . '/circulares/';
        $data['palabra'] = Session::get($this->controlador . '.palabra', '');
        //$data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('tramites.ini_circulares', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_circulares_model::getCirculares($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 2);
        $total = Iniciacion_circulares_model::getNumCirculares(Session::get($this->controlador . '.palabra', ''), $log, $val, 2);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function ver_circular(Request $request)
    {
        $idc = $request->input('idc', 0);
        if ($idc > 0) {
            $filename = $this->generateCircular($idc);
            $salida = ['success' => 0, 'Msg' => 'Buscando', 'filename' => $filename];
        }
        return response()->json($salida);
    }
    public function gen_circular(Request $request)
    {
        $idc = $request->input('idc', 0);
        $info = Iniciacion_circulares_model::getInfo($idc); //datos   que se imprimiran
        $data['fechagen'] = $this->getFechaGen($info);
        $data['circular'] = $info->circular;
        if ($info->valle == 2) {
            $auto = 'RESPETABLE MAESTRO DISTRITAL';
            $region = 'Gran Logia Distrital';
        } else {
            $auto = 'GRAN DELEGADO REGIONAL';
            $region = 'Gran Delegacion Regional';}
        $data['region'] = $region;
        $data['valle'] = $info->valle;
        $data['auto'] = $auto;
        $data['contenido'] = $this->getContent($idc);
        $data['paginas'] = $this->paginas;
        /*********************************************************************************************************/
        $filename = $info->circular . '.pdf';
        $pdf = PDF::loadView('pdfs.pdf_circulares', $data);
        $pdf->set_paper('Letter', 'portrait');
        $path = public_path('circulares/');
        $pdfFilePath = "circulares/$filename";
        if (file_exists($pdfFilePath) == true) {
            unlink($pdfFilePath);
        }
        $pdf->save($path . '/' . $filename);
        $salida = ['success' => 0, 'Msg' => 'Creado', 'filename' => $filename];
        return response()->json($salida);
    }
    private function getFechaGen($info)
    {
        //&there4; &#x2234; &#8756;
        $ges = explode('-', $info->fecha);
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
        $fecha = $info->valle . ', ' . (int) $ges[2] . ' de ' . $ames[(int) $ges[1]] . ' de ' . $ges[0] . ' e:.v:.';
        return $fecha;
    }
    private function getContent($idc)
    {
        $lista = Iniciacion_circulares_model::getLista($idc); //datos   que se imprimiran
        $ret = '<table  width="100%" cellspacing="0" cellpadding="2">';
        $numero = count($lista);
        if ($numero > 8) {
            $this->paginas = 3;
        } elseif ($numero > 4) {
            $this->paginas = 2;
        }

        for ($ii = 0; $ii < $numero; $ii++) {

            if ($ii == 6) {
                $ret .= '</table><p style="page-break-after: always;"><p>&nbsp;</p><table  width="100%" cellspacing="0" cellpadding="2">';
            }

            if ($ii % 2 == 0) {
                $ret .= '<tr><td width="50%">' . $this->getCeldaCir($lista[$ii]) . ' </td>';
            } else {
                $ret .= '<td>' . $this->getCeldaCir($lista[$ii]) . '</td></tr>';
            }
        }
        if ($numero % 2 == 0) {

        } else {
            $ret .= '<td width="50%">&nbsp;</td></tr>';
        }
        $ret .= '</table>';
        // if($numero==5 || $numero==6)
        // $ret.='<p style="page-break-after: always;">';
        return $ret;
    }
    private function getCeldaCir($info)
    {
        $edad = $this->calculaedad($info->fechaNac);
        $txt = '<table width="100%" border="1" cellspacing="0" cellpadding="5">
          <tr><td colspan="2"><center><b>R:.L:.S:. ' . $info->logia . '  Nro ' . $info->numero . '</b></center></td></tr>
    <tr><td rowspan="2" width="90" valign="top"><img src="media/fotos/' . $info->foto . '" width="90"></td>
    <td>' . strtoupper($info->nombres) . '<br>' . strtoupper($info->apPaterno) . ' ' . strtoupper($info->apMaterno) . '</td></tr>
    <tr><td>' . ($info->nacionalidad) . '<br>Nacido en ' . ($info->lugarNac) . '<br>' . ($info->fechaNac) . ' (' . $edad . ' años)<br>';
        if ($info->estadoCivil == 3) {
            $txt .= 'Divorciado<br>';
        } elseif ($info->estadoCivil == 2) {
            $txt .= 'Viudo<br>';
        } elseif ($info->estadoCivil == 1) {
            $txt .= 'Casado<br>';
        } else {
            $txt .= 'Soltero<br>';
        }
        $txt .= ($info->profesion) . '</td></tr>
    </table>';
        return $txt;
    }

    private function calculaedad($fechanacimiento)
    {
        list($ano, $mes, $dia) = explode("-", $fechanacimiento);
        $ano_diferencia = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia = date("d") - $dia;
        if ($dia_diferencia < 0 || $mes_diferencia < 0) {
            $ano_diferencia--;
        }
        return $ano_diferencia;
    }
}

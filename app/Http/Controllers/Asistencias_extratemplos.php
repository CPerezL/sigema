<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\asistencias\Extratemploasis_model;
use App\Models\asistencias\Extratemplos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Session;

class Asistencias_extratemplos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '058';
    public $controlador = 'asistencias_extratemplos';
    public $limitmonth = 1;
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
        if (Auth::user()->nivel == 3) {
            $this->limitmonth = 8;
        } elseif (Auth::user()->nivel == 5) {
            $this->limitmonth = 12;
        }
        self::permiso($request->input('_'));
        self::iniciarModulo();
        $data['_mid'] = $this->idmod; //---
        $data['lmes'] = $this->limitmonth;
        $data['year'] = date('Y'); //---
        // $data['month'] = date('m');
        // Session::put($this->controlador . '.mes', date('m'));
        Session::put($this->controlador . '.gestion', date('Y'));
        // $data['meses'] = $this->getMeses();
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('asistencias.extratemplos', $data);
    }
    public function get_dias(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0) {
            $salida = Extratemplos_model::getDiaExtraTemplo(Session::get($this->controlador . '.gestion'), $taller);
            $total = count($salida);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_datos(Request $request)
    {
        if (Session::get($this->controlador . '.fextrat') > 0) {
            $tal = Session::get($this->controlador . '.taller');
            $ges = Session::get($this->controlador . '.gestion');
            $fext = Session::get($this->controlador . '.fextrat');
            $salida = Extratemplos_model::getItems($tal, $ges, $fext, Auth::user()->nivel);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function filter_taller(Request $request)
    {
        $taller = $request->input('taller', 0);
        if ($taller > 0 && $taller != Session::get($this->controlador . '.taller')) {
            $dia = 0; //Asistencias_model::getDiaTaller($taller);
            $data = array($this->controlador . '.taller' => $taller, $this->controlador . '.tenida' => $dia);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Wait...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function filter_diatenida(Request $request)
    {
        $idet = $request->input('fextrat', 0);
        if ($idet != Session::get($this->controlador . '.fextrat', 0) && $idet > 0) {
            Session::put($this->controlador . '.fextrat', $idet);
            $dataet = Extratemplos_model::getDataExtraT($idet);
            $datasis = 'Fecha: ' . $dataet->fechaExtraTemplo . '<br>Tema: ' . $dataet->temaExtraTemplo . '<br>GRADO : ' . $dataet->grado;
            $qry2 = (object) ['success' => 'true', 'Msg' => $datasis];
            return response()->json($qry2);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function save_extra(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0) {
            $grado = $request->input('gradoet', 1);
            $tempDate = explode('/', $request->input('fechaet', ''));
            $fechaet = $tempDate[2] . '-' . $tempDate[1] . '-' . $tempDate[0];
            $datas = array(
                'idLogia' => $taller,
                'fechaExtraTemplo' => $fechaet,
                'temaExtraTemplo' => $request->input('temaExtraTemplo'),
                'grado' => $grado,
                'gestion' => Session::get($this->controlador . '.gestion'),
                'numero' => $grado,
                'instructor1' => $request->input('instructor1') . ' ',
                'instructor2' => $request->input('instructor2') . ' ',
                'instructor3' => $request->input('instructor3') . ' ',
            );
            $resu = Extratemplos_model::insert($datas);
            if ($resu > 0) {
                $data = ['success' => 'true', 'Msg' => 'Extratemplo adicionado correctamente'];
            } else {
                $data = ['success' => 0, 'Msg' => 'Error adicionado fecha'];
            }

        } else {
            $data = ['success' => 0, 'Msg' => trans('mess.errid')];
        }
        return response()->json($data);
    }
    public function update_datasiset(Request $request)
    {
        $id = $request->input('id');

        if ($id > 0) //actualiza
        {
            $tempDate = explode('/', $request->input('fechaet', ''));
            $fechaet = $tempDate[2] . '-' . $tempDate[1] . '-' . $tempDate[0];
            $datas = array(
                'fechaExtraTemplo' => $fechaet,
                'grado' => $request->input('gradoet'),
                'temaExtraTemplo' => $request->input('temaExtraTemplo'),
                'instructor1' => $request->input('instructor1'),
                'instructor2' => $request->input('instructor2'),
                'instructor3' => $request->input('instructor3'),
            );
            $resu = Extratemplos_model::where('idExtraTemplo', $id)->update($datas);
        } else //crea
        {
            $data = ['success' => 0, 'Msg' => trans('mess.errid')];
        }
        if ($resu > 0) {
            $fechat = $request->input('fechaet');
            $datasis = 'Fecha: ' . $fechat . '<br>Tema: ' . $request->input('temaExtraTemplo') . '<br>GRADO : ' . $request->input('gradoet');
            $data = ['success' => 'true', 'Msg' => 'Datos actualizados correctamente', "Ret" => "$datasis"];
        } else {
            $data = ['success' => 0, 'Msg' => trans('mess.errchange')];
        }

        return response()->json($data);
    }
    public function destroy_dataet(Request $request)
    {
        $id = $request->input('idet');
        if ($id > 0) //actualiza
        {
            $resu1 = Extratemplos_model::where('idExtraTemplo', $id)->delete();
            $resu = Extratemploasis_model::where('idExtraTemplo', $id)->delete() + $resu1;
        } else //
        {
            $data = ['success' => 0, 'Msg' => trans('mess.errid')];
        }
        if ($resu > 0) {
            $data = ['success' => 'true', 'Msg' => 'Fecha borrada correctamente'];
        } else {
            $data = ['success' => 0, 'Msg' =>trans('mess.errdel')];
        }

        return response()->json($data);
    }
    public function update_asiset(Request $request)
    {
        $id = $request->input('idmiembro');
        $idet = Session::get($this->controlador . '.fextrat');
        $check = Extratemploasis_model::checkAsisET($id, $idet);
        if ($check) {
            Extratemploasis_model::where('idExtraTemplo', $idet)->where('idMiembro', $id)->delete();
            $data = ['success' => 'true', 'Msg' => 'Asistencia quitada'];
        } else {
            if ($id > 0) {
                $datas = array(
                    'idMiembro' => $id,
                    'idExtraTemplo' => $idet,
                );
                $resu = Extratemploasis_model::insert($datas);
                if ($resu > 0) {
                    $data = ['success' => 'true', 'Msg' => 'Asistencia asignada correctamente'];

                } else {
                    $data = ['success' => 0, 'Msg' => trans('mess.errchange')];

                }

            } else {
                $data = ['success' => 0, 'Msg' => trans('mess.errid')];
            }
        }
        return response()->json($data);
    }
    public function gen_planilla(Request $request)
    {
        $agrado[0] = 'NO ASIGNADO';
        $agrado[1] = 'PRIMER';
        $agrado[2] = 'SEGUNDO';
        $agrado[3] = 'TERCER';
        $agrado[4] = 'MAESTROS INSTALADOS';
        $datoslog = Extratemplos_model::getDatosLogia(Session::get($this->controlador . '.taller'));
        $datosext = Extratemplos_model::getDataExtraT(Session::get($this->controlador . '.fextrat'), 1);
        $data['tipov'] = $datoslog->tipo;
        $data['nvalle'] = $datoslog->valle;
        if (strlen($datoslog->logo) > 5) {
            $data['logo'] = $datoslog->logo;
        } else {
            $data['logo'] = 'glb-150.png';
        }
        $data['ftenida'] = $datosext->fechaExtraTemplo;
        $data['taller'] = $datoslog->nombreCompleto;
        $data['etema'] = $datosext->temaExtraTemplo;
        $data['gradoet'] = $agrado[$datosext->grado];
        $data['gestionet'] = Session::get($this->controlador . '.gestion');
        $data['instructores'] = $this->listInstructores($datosext);
        $data['asistencia'] = $this->listAsistencia(Session::get($this->controlador . '.fextrat'));
        $nomefile = 'G.L.2-' . Session::get($this->controlador . '.taller') . '-' . $datosext->fechaExtraTemplo;
        $pdf = PDF::loadView('pdfs.pdf_extratemplo', $data);
        $pdf->set_paper('letter', 'portrait');
        return $pdf->download($nomefile . '.pdf');
    }
    private function &listInstructores($dataet)
    {
        $ret = '';
        if (strlen($dataet->instructor1) > 0) {
            $ret .= '<tr style="text-align: center"><td>' . $dataet->instructor1 . '</td></tr>';
        }

        if (strlen($dataet->instructor2) > 0) {
            $ret .= '<tr style="text-align: center"><td>' . $dataet->instructor2 . '</td></tr>';
        }

        if (strlen($dataet->instructor3) > 0) {
            $ret .= '<tr style="text-align: center"><td>' . $dataet->instructor3 . '</td></tr>';
        }

        return $ret;
    }
    private function &listAsistencia($idet)
    {
        $lista = Extratemplos_model::getAsistenteset($idet);
        $ret = '';
        foreach ($lista as $ver) {
            $ret .= '<tr style="text-align: center"><td>' . $ver->GradoActual . '</td><td>' . $ver->NombreCompleto . '</td></tr>';
        }
        return $ret;
    }
}

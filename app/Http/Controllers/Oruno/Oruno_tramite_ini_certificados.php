<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\Config_model;
use App\Models\oruno\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_ini_certificados extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '016';
    public $controlador = 'oruno_tramite_ini_certificados';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('oruno.ini_certificados', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $qry = Iniciacion_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val);
        $total = Iniciacion_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $log, $val);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', Session::get($this->controlador . '.valle', 0)));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller', 0));
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
    }
    public function get_tramites(Request $request)
    {
        $tramites[0] = $request->input('id');
        if (count($tramites) > 0) {
            $salida = Iniciacion_model::getTramites($tramites);
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
                $fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
                $codges = Config_model::getValue('codGes');
                $data = array(
                    'okPagoDerechos' => 1,
                    'certificado' => 1,
                    'codGes' => $codges,
                    'numeroCertificado' => $num,
                    'fechaCertificado' => $fccc,
                    'fechaModificacion' => date('Y-m-d'),
                    'nivelActual' => 7,
                );
                $resu = Iniciacion_model::where("idTramite", $id1)->update($data);
            } else {
                $fc = $request->input('fechaCeremonia1', '00-00-0000');
                $num = $request->input('numero1', 1);
                $fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
                $codges = Config_model::getValue('codGes');
                $data = array(
                    'codGes' => $codges,
                    'numeroCertificado' => $num,
                    'fechaCertificado' => $fccc,
                    'fechaModificacion' => date('Y-m-d'),
                );
                $resu = Iniciacion_model::where("idTramite", $id1)->update($data);
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

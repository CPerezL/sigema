<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Models\tramites\Observaciones_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_tres extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '092';
    public $controlador = 'tramites_ini_tres';
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
        return view('tramites.ini_tres', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Iniciacion_model::getTramitesListos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 3);
        $total = Iniciacion_model::getNumTramitesListos(Session::get($this->controlador . '.palabra', ''), $log, $val, 3);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('idTra', 0);
        if ($tramite > 0) {
            $salida = Iniciacion_model::getTramiteIni($tramite, 2);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait']);
        }
    }
    public function update_tramite(Request $request)
    {
        $id = $request->input('idTramite', 0);
        $resu = 0;
        if ($id > 0) {
            $data = array(
                'fechaInfLaboral' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaInfLaboral', '00-00-0000'))->format('Y-m-d'),
                'fechaInfSocial' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaInfSocial', '00-00-0000'))->format('Y-m-d'),
                'nivelActual' => 4,
                'numActaInfLaboral' => $request->input('numActaInfLaboral'),
                'numActaInfSocial' => $request->input('numActaInfSocial'),
                'fechaModificacion' => date('Y-m-d'),
            );
            Iniciacion_model::where('idTramite', $id)->update($data);
            $salida = ['success' => 'true', 'Msg' => "Tramite validado correctamente"];
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }

        return response()->json($salida);
    }
    public function save_observacion(Request $request)
    {/* registra observacion */
        if ($request->input('idTramite') > 0) {
            $data = array(
                'fechaRegistro' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaRegistro', '00-00-0000'))->format('Y-m-d'),
                'descripcion' => $request->input('descripcion'),
                'tipo' => $request->input('tipo'),
                'idTramite' => $request->input('idTramite'),
                'grado' => 1,
                'usuario' => \Auth::user()->id,
                'estado' => $request->input('estado'),
            );

            $resu = Observaciones_model::insert($data);
            if($resu>0)
            {
                $data = array(
                    'nivelActual' => 5,
                    'fechaModificacion' => date('Y-m-d'),
                );
                Iniciacion_model::where('idTramite', $request->input('idTramite'))->update($data);
            }

            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos insertados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }

        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos invalidos'];
        }

        return response()->json($salida);
        // return response()->json(['success' => 0, 'Msg' => 'observacion enviada']);
    }
}

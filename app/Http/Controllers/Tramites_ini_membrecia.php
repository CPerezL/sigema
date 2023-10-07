<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_membrecia extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '107';
    public $controlador = 'tramites_ini_membrecia';
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
        return view('tramites.ini_membrecia', $data);
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
}

<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Observaciones_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_ini_observaciones_listado extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '101';
    public $controlador = 'tramites_ini_observaciones_listado';
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
        self::iniciarModuloAll();
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['_folder'] = url('/') . '/';
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.ini_observaciones_listado', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $qry = Observaciones_model::getRegistrosLista($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, 0);
        $total = Observaciones_model::getNumRegistrosLista(Session::get($this->controlador . '.palabra', ''), $log, 0);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function save_tramite(Request $request)
    {
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
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos insertados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }

        } elseif ($request->input('idObservacion') > 0) {
            $data = array(
                'fechaRegistro' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaRegistro', '00-00-0000'))->format('Y-m-d'),
                'descripcion' => $request->input('descripcion'),
                'tipo' => $request->input('tipo'),
                'usuario' => \Auth::user()->id,
                'estado' => $request->input('estado'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Observaciones_model::where('idObservacion', $request->input('idObservacion'))->update($data);
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos modificados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }

        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos invalidos'];
        }

        return response()->json($salida);

    }
    public function get_tramites(Request $request)
    {
        $idt = $request->input('idt');
        if ($idt > 0) {
            $salida = Observaciones_model::getObservaciones($idt);
            $total = 0;
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }

    public function delete_tramite(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Observaciones_model::where("idObservacion", $id)->delete();
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos borrados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al borrar los datos'];
            }
        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos erroneos'];
        }
        return response()->json($salida);
    }
}

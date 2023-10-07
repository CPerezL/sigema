<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Meritos_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Miembros_meritos_listado extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '104';
    public $controlador = 'miembros_meritos_listado';
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
        if (Auth::user()->nivel > 3) {
            $data['editable'] = '';
        } else {
            $data['editable'] = ' disabled="true"';
        }
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['tmeritos'] = Meritos_model::getTipoMeritos();
        /*     varibles de pagina* */
        return view('admin.meritos_listado', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $qry = Meritos_model::getRegistrosLista($page, $rows, Session::get($this->controlador . '.palabra', ''), $val, $log, Session::get($this->controlador . '.estado', 0));
        $total = Meritos_model::getNumRegistrosLista(Session::get($this->controlador . '.palabra', ''), $val, $log, Session::get($this->controlador . '.estado', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_merito(Request $request)
    {
        if ($request->input('id') > 0) {
            $data = array(
                'fechaRegistro' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaRegistro', '00-00-0000'))->format('Y-m-d'),
                'descripcion' => $request->input('descripcion'),
                'idTipoMerito' => $request->input('idTipoMerito'),
                'idMiembro' => $request->input('id'),
                'usuario' => \Auth::user()->id,
                'estado' => $request->input('estado'),
            );
            $resu = Meritos_model::insert($data);
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos insertados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }

        } elseif ($request->input('idMerito') > 0) {
            $data = array(
                'fechaRegistro' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaRegistro', '00-00-0000'))->format('Y-m-d'),
                'descripcion' => $request->input('descripcion'),
                'idTipoMerito' => $request->input('idTipoMerito'),
                'usuario' => \Auth::user()->id,
                'estado' => $request->input('estado'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Meritos_model::where('idMerito', $request->input('idMerito'))->update($data);
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
    public function get_meritos(Request $request)
    {
        $idt = $request->input('idt');
        if ($idt > 0) {
            $salida = Meritos_model::getMeritos($idt);
            $total = 0;
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }

    public function delete_merito(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Meritos_model::where("idMerito", $id)->delete();
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

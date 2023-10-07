<?php

namespace App\Http\Controllers;

// use App\Models\Parametros_model;
use App\Models\valles\Autoridades_model;
use App\Models\valles\Cargos_model;
use App\Models\valles\Gestiones_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class V_autoridades extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '020';
    public $controlador = 'v_autoridades';
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
        $data['valles'] = Autoridades_model::getVallesArray($this->valle);
        if (count($data['valles']) == 1) {
            foreach ($data['valles'] as $key => $gdr) {
                $comi = explode('_', $key);
                $data['_vtxt'] = $gdr;
            }
            Session::put($this->controlador . '.valle', $comi[0]);
            Session::put($this->controlador . '.tipo', $comi[1]);
            $data['_gg'] = Gestiones_model::getGestionesArray($this->valle);
        }
        /*     variables de pagina     */
        return view('valles.autoridades', $data);
    }
    public function get_datos(Request $request)
    {
        $gest = Session::get($this->controlador . '.gestion');
        $tipo = Session::get($this->controlador . '.tipo');
        $qry = Autoridades_model::getOficiales($tipo, $gest);
        if (is_null($qry)) {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => $qry];
        }

        return response()->json($qry2);
    }
    public function get_valles(Request $request)
    {
        $val = Auth::user()->idValle;
        if ($val > 0) {
            $salida = Autoridades_model::getValles($val);
        } else {
            $salida = Autoridades_model::getValles();
            $salida[] = (object) ['valle' => 'Todos los Valles', 'idValle' => 0];
        }
        return response()->json($salida);
    }
    public function get_gestiones(Request $request)
    {
        $comia = $request->input('id', 0);
        $comi = explode('_', $comia);
        if ($comi[0] > 0) {
            Session::put($this->controlador . '.valle', $comi[0]);
            Session::put($this->controlador . '.tipo', $comi[1]);
            $salida = Autoridades_model::getGestiones($comi[0]);
            return response()->json($salida);
        } else {
            return '[]';
        }

    }
    public function save_datos(Request $request)
    {
        $orden = $request->input('orden');
        $cargo = $request->input('cargo');
        if (strlen($orden) > 0 && strlen($cargo) > 4) {
            $data = array(
                'tipo' => Session::get($this->controlador . '.comision'),
                'cargo' => $cargo,
                'orden' => $orden,
            );
            $resu = Cargos_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Datos invalidos']);
        }
    }
    public function update_datos(Request $request)
    {
        $orden = $request->input('orden');
        $cargo = $request->input('cargo');
        $id = $request->input('id');
        if (strlen($orden) > 0 && strlen($cargo) > 4 && $id > 0) {
            $data = array(
                'cargo' => $cargo,
                'orden' => $orden,
            );

            $resu = Cargos_model::where("idCargo", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Cargos_model::where("idCargo", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function get_miembros(Request $request)
    {
        $ges = Session::get($this->controlador . '.gestion');
        if ($ges > 0) {
            $filter = $request->input('filterRules');
            if (strlen($filter) > 2) {
                $salida = Autoridades_model::getMiembros(Session::get($this->controlador . '.valle'), $ges, $filter); //aumetar materno y nombre
                $total = 0;
            } else {
                $salida = Autoridades_model::getMiembros(Session::get($this->controlador . '.valle'), $ges);
                $total = 0;
            }
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function update_cargo(Request $request)
    {
        $cargo = $request->input('cargo');
        $id = $request->input('id');
        if ($id > 0) {
            $ges = Session::get($this->controlador . '.gestion');
            $val = Session::get($this->controlador . '.valle');
            $resu = Autoridades_model::updateCargo($ges, $id, $cargo, $val);
            if ($resu == 1) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.change')]);
            } elseif ($resu > 1) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function destroy_cargo(Request $request)
    {
        $id = $request->input('ido', 0);
        if ($id > 0) {
            $resu = Autoridades_model::where("idAutoridad", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

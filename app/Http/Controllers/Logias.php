<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Orientes_model;
use App\Models\admin\Ritos_model;
use App\Models\admin\Valles_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Logias extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '008';
    public $controlador = 'logias';
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
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['ritos'] = Ritos_model::getRitosArray($this->oriente);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('admin.logias', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Logias_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0), Session::get($this->controlador . '.valle', 0));
        $total = Logias_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0), Session::get($this->controlador . '.valle', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $existe = Logias_model::where("numero", $request->input('numero', 0))->exists();
        if ($existe == 1) {
            return response()->json(['success' => 0, 'Msg' => 'El Numero de Logia/Triangulo ya existe']);
        } else {
            $log = $request->input('logia', '');
            if (strlen($log) > 4) {
                $data = array(
                    'idOriente' => $request->input('idOriente', 0),
                    'numero' => $request->input('numero', 0),
                    'logia' => $request->input('logia', 'Si nombre'),
                    'nombreCompleto' => $request->input('nombreCompleto'),
                    'valle' => $request->input('valle'),
                    'maestro' => $request->input('maestro', ''),
                    'direccion' => $request->input('direccion', ''),
                    'telefono' => $request->input('telefono', ''),
                    'latitud' => $request->input('latitud', ''),
                    'longitud' => $request->input('longitud', ''),
                    'tipo' => 1,
                    'diatenida' => $request->input('diatenida'),
                    'fechaCreacion' => date('Y-m-d'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );

                $resu = Logias_model::insert($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
            }
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        $idnum = Logias_model::where("numero", $request->input('numero', 0))->first();
        if (is_null($idnum) || $id==$idnum->idLogia) {
            $data = array(
                'idOriente' => $request->input('idOriente', 0),
                'numero' => $request->input('numero', 0),
                'logia' => $request->input('logia', 'Si nombre'),
                'nombreCompleto' => $request->input('nombreCompleto'),
                'valle' => $request->input('valle'),
                'rito' => $request->input('rito'),
                'maestro' => $request->input('maestro', ''),
                'direccion' => $request->input('direccion', ''),
                'telefono' => $request->input('telefono', ''),
                'latitud' => $request->input('latitud', ''),
                'longitud' => $request->input('longitud', ''),
                'diatenida' => $request->input('diatenida'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Logias_model::where("idLogia", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            $existe = Logias_model::where("numero", $request->input('numero', 0))->exists();
            if ($existe == 1) {
                return response()->json(['success' => 0, 'Msg' => 'El Numero de Logia/Triangulo ya existe']);
            } else {
                $data = array(
                    'idOriente' => $request->input('idOriente', 0),
                    'numero' => $request->input('numero', 0),
                    'logia' => $request->input('logia', 'Si nombre'),
                    'nombreCompleto' => $request->input('nombreCompleto'),
                    'valle' => $request->input('valle'),
                    'rito' => $request->input('rito'),
                    'maestro' => $request->input('maestro', ''),
                    'direccion' => $request->input('direccion', ''),
                    'telefono' => $request->input('telefono', ''),
                    'latitud' => $request->input('latitud', ''),
                    'longitud' => $request->input('longitud', ''),
                    'diatenida' => $request->input('diatenida'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );

                $resu = Logias_model::where("idLogia", $id)->update($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
                }
            }
        }

        if ($id > 0) {

        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function destroy_datos(Request $request)
    { //manda a papelera
        $id = $request->input('id', 0);
        if ($id > 0) {
            $bor = $request->input('flag', 0);
            if ($bor > 0) {
                $resu = Logias_model::where("idLogia", $id)->update(['borrado' => 0]);
            } else {
                $resu = Logias_model::where("idLogia", $id)->update(['borrado' => 1]);
            }

            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function show_papelera()
    {
        $papelera = Session::get($this->controlador . '.papelera');
        if ($papelera == 1) {
            Session::put($this->controlador . '.papelera', 0);
        } else {
            Session::put($this->controlador . '.papelera', 1);
        }

        return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
    }
    public function get_valles(Request $request)
    {
        $val = Auth::user()->idValle;
        $or = Auth::user()->idOriente;
        if ($val > 0) {
            $qry = Valles_model::select('idValle', 'valle')->where('idValle', '=', $val)->orderBy('valle', 'asc')->get();
        } elseif ($or > 0) {
            $qry = Valles_model::select('idValle', 'valle')->where('idOriente', '=', $or)->orderBy('valle', 'asc')->get();
        } else {
            $idor = $request->input('oriente', 1000);
            Session::put($this->controlador . '.oriente', $idor);
            $qry = Valles_model::select('idValle', 'valle')->where('idOriente', '=', $idor)->orderBy('valle', 'asc')->get();
            $qry[] = (object) ['valle' => 'Todos los Valles', 'idValle' => 0];
        }
        return $qry->toJson();
    }
    public function get_orientes()
    {
        $or = Auth::user()->idOriente;
        if ($or > 0) {
            $qry = Orientes_model::select('idOriente', 'oriente')->where('idOriente', '=', $or)->orderBy('oriente', 'asc')->get();
        } else {
            $qry = Orientes_model::select('idOriente', 'oriente')->orderBy('oriente', 'asc')->get();
            $qry[] = (object) ['oriente' => 'Todos los Orientes', 'idOriente' => 0];
        }
        return $qry->toJson();
    }
    public function convertir(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Logias_model::where("idLogia", $id)->update(['tipo' => 2]);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

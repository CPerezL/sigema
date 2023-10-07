<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Orientes_model;
use App\Models\admin\Roles_model;
use App\Models\admin\Valles_model;
use App\Models\Parametros_model;
use App\Models\User;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Usuarios extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    public $idmod = '001';
    public $controlador = 'usuarios';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $data['_mid'] = $this->idmod; //
        $data['_controller'] = $this->controlador;
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        $data['roles'] = Roles_model::getRolesArray(Auth::user()->nivel); //---+++
        $data['permisos'] = Parametros_model::getParamsArray(7); //---+++
        /*     varibles de pagina* */
        return view('admin.usuarios', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $qry = User::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $or, $val, Session::get($this->controlador . '.rol', 0));
        $total = User::getNumItems(Session::get($this->controlador . '.palabra', ''), $or, $val, Session::get($this->controlador . '.rol', 0));

        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $clave = $request->input('clave', '');
        if (strlen($clave) > 4) {
            $existe = User::where("username", $request->input('username'))->exists();
            if ($existe == 1) {
                return response()->json(['success' => 0, 'Msg' => 'el usuario ya existe']);
            } else {
                $data = array(
                    'username' => $request->input('username'),
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'userpassword' => bcrypt($clave),
                    'idRol' => $request->input('idRol'),
                    'rol' => $request->input('rol', 'Usuario'),
                    'permisos' => $request->input('permisos'),
                    'logia' => (int) $request->input('logia', 0),
                    'valle' => (int) $request->input('valle', 0),
                    'oriente' => (int) $request->input('oriente', 0),
                );
                $resu = User::insert($data);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errlargo', ['var' => 'Clave', 'num' => 4])]);
        }
    }
    public function update_datos(Request $request)
    {
        $mes = '';
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'username' => $request->input('username'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'idRol' => $request->input('idRol'),
                'rol' => $request->input('rol', 'Usuario'),
                'permisos' => $request->input('permisos'),
                'logia' => $request->input('logia', 0),
                'valle' => $request->input('valle', 0),
                'oriente' => $request->input('oriente', 0),
                'updated_at' => date("Y-m-d H:i:s"),
            );
            $clave = $request->input('clave', '');
            if (strlen($clave) > 4) {
                $data['userpassword'] = bcrypt($clave);
            } else {
                $mes .= trans('mess.errlargo', ['var' => 'Clave', 'num' => 4]);
            }

            $resu = User::where("id", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange') . ' ' . $mes]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange') . ' ' . $mes]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = User::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', 1000));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
    }
    public function get_valles(Request $request)
    {
        $val = self::validar('idValle', 0);
        $or = self::validar('idOriente', $request->input('oriente', 1000));
        $cond = 'borrado=0';
        if ($or > 0) {
            $cond .= " AND idOriente=$or";
        }

        if ($val > 0) {
            $cond .= " AND idValle=$val";
        }

        $qry = Valles_model::select('idValle', 'valle')->whereraw($cond)->orderBy('valle', 'asc')->get();
        if ($val == 0) {
            $qry[] = (object) ['valle' => 'Todos los Valles', 'idValle' => 0];
        }

        return $qry->toJson();
    }
    public function get_orientes()
    {
        //$or = Auth::user()->idOriente;
        $or = $this->validar('idOriente', 0);
        if ($or > 0) {
            $qry = Orientes_model::select('idOriente', 'oriente')->where('idOriente', '=', $or)->orderBy('oriente', 'asc')->get();
        } else {
            $qry = Orientes_model::select('idOriente', 'oriente')->orderBy('oriente', 'asc')->get();
            $qry[] = (object) ['oriente' => 'Todos los Orientes', 'idOriente' => 0];
        }
        return $qry->toJson();
    }
}

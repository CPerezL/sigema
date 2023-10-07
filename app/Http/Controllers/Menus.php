<?php

namespace App\Http\Controllers;

use App\Models\admin\Menus_model;
use App\Models\admin\Modulos_model;
use App\Models\admin\Modulos_roles_model;
use App\Models\Parametros_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Menus extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    public $idmod = '004';
    public $controlador = 'menus';
    public function __construct()
    {
        // $this->middleware('auth');
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
        $data['modulos'] = Modulos_model::getModulosArray();
        $data['padres'] = Menus_model::getMenusArray();
        $data['levels'] = Parametros_model::getParamsArray(2); //---+++arreglar
        /*     varibles de pagina* */
        return view('admin.menus', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Menus_model::getMenus(Session::get($this->controlador . '.menusel', 0));
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('nombre', '');
        if (strlen($nna) > 3) {
            $data = array(
                'idMenu' => $request->input('idMenu'),
                'nombre' => $request->input('nombre'),
                'nivel' => $request->input('nivel'),
                'orden' => $request->input('orden'),
                'activo' => $request->input('activo'),
                'idModulo' => $request->input('idModulo'),
                'descripcion' => $request->input('descripcion'),
                'icono' => $request->input('icono'),
            );
            $resu = Menus_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errlargo', ['var' => 'Nombre', 'num' => 4])]);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'idMenu' => $request->input('idMenu'),
                'nombre' => $request->input('nombre'),
                'nivel' => $request->input('nivel'),
                'orden' => $request->input('orden'),
                'activo' => $request->input('activo'),
                'idModulo' => $request->input('idModulo'),
                'descripcion' => $request->input('descripcion'),
                'icono' => $request->input('icono'),
                'fechaCreacion' => date("Y-m-d H:i:s"),
            );

            $resu = Menus_model::where("id", $id)->update($data);
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
            $resu = Menus_model::where("id", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function get_roles(Request $request)
    {
        $id = $request->input('id');
        $qry = Menus_model::getRoles($id);
        return response()->json($qry);
    }
    public function update_roles(Request $request)
    {
        $id = $request->input('id');
        $roles = $request->input('roles', '');
        $rol1 = explode(',', $roles);
        $rol2 = Menus_model::getRolesArray($id);
        $borra = array_diff($rol2, $rol1);
        $aumenta = array_diff($rol1, $rol2);
        $aux = 0;
        if (count($borra) > 0) {
            foreach ($borra as $bb) {
                $aux += Modulos_roles_model::where("idRol", $bb)->where("idModulo", $id)->delete();
            }
        }
        if (count($aumenta) > 0) {
            foreach ($aumenta as $aa) {
                $aux += Modulos_roles_model::insert(array('idRol' => $aa, 'idModulo' => $id));
            }
        }
        if ($aux > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.nochange')]);
        }
    }
    public function fixmenu_datos(Request $request)
    {
        $id = $request->input('idm', 0);
        if ($id > 0) {
            $resu = Menus_model::corregirMenu($id);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.oktask', ['task' => 'Orden'])]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errtask')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function fixmenu_padres(Request $request)
    {
        $id = $request->input('idm', 0);
        if ($id > 0) {
            $resu = Menus_model::corregirPadres($id);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.oktask', ['task' => 'Orden'])]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errtask')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function menu_estado(Request $request)
    {
        $id = $request->input('idm', 0);
        $estado = $request->input('est', 0);
        if ($id > 0) {
            if ($estado == 1) {
                $resu = Menus_model::where("id", $id)->update(['activo' => 0,'fechaCreacion' => date("Y-m-d H:i:s")]);
            } else {
                $resu = Menus_model::where("id", $id)->update(['activo' => 1,'fechaCreacion' => date("Y-m-d H:i:s")]);
            }

            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Estado cambiado']);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function menu_revisado(Request $request)
    {
        $id = $request->input('idm', 0);
        //$estado = $request->input('mod', 0);
        if ($id > 0) {
                $resu = Modulos_model::where("id", $id)->update(['estado' => 2]);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Modulo Revisado']);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}

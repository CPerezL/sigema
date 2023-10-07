<?php

namespace App\Http\Controllers;

use App\Models\admin\Modulos_model;
use App\Models\admin\Modulos_roles_model;
use App\Models\Parametros_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Modulos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    public $idmod = '003';
    public $controlador = 'modulos';
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
        $data['levels'] = Parametros_model::getParamsArray(2); //---+++arreglar
        /*     varibles de pagina* */
        return view('admin.modulos', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 40);
        $sort = $request->input('sort', '');
        $order = $request->input('order', '');
        if (strlen($sort) > 1) {
            Session::put([$this->controlador . '.sort' => $sort, $this->controlador . '.order' => $order]);
        }

        $qry = Modulos_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.level', 0), Session::get($this->controlador . '.sort', 'id'), Session::get($this->controlador . '.order', 'ASC'));
        $total = Modulos_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.level', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $nna = $request->input('name', '');
        if (strlen($nna) > 4) {
            $data = array(
                'name' => $request->input('name'),
                'link' => $request->input('link'),
                'level' => $request->input('level'),
                'estado' => $request->input('estado'),
                'version' => $request->input('version'),
                'comentarios' => $request->input('comentarios'),

            );
            $resu = Modulos_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Datos insertados correctamente']);
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Clave invalida mayor a 4 caracteres']);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data = array(
                'name' => $request->input('name'),
                'link' => $request->input('link'),
                'level' => $request->input('level'),
                'estado' => $request->input('estado'),
                'version' => $request->input('version'),
                'comentarios' => $request->input('comentarios'),
            );

            $resu = Modulos_model::where("id", $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Datos actualizados correctamente']);
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Modulos_model::where("id", $id)->delete();
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
        $qry = Modulos_model::getRoles($id);
        return response()->json($qry);
    }
    public function update_roles(Request $request)
    {
        $id = $request->input('id');
        $roles = $request->input('roles', '');
        $rol1 = explode(',', $roles);
        $rol2 = Modulos_model::getRolesArray($id);
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
            return response()->json(['success' => 0, 'Msg' =>  trans('mess.nochange')]);
        }
    }
}

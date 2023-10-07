<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Pagos_montos_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_pagos_extra_montos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '029';
    public $controlador = 'mecom_pagos_extra_montos';
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
        /*     variables de pagina     */
        return view('mecom.pagos_extra_montos', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Pagos_montos_model::getItems($page, $rows);
        $total = Pagos_montos_model::getNumItems();
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', 0));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
    }
    public function get_valles(Request $request)
    {
        $val = self::validar('idValle', $request->input('valleid', 0));
        $or = self::validar('idOriente', $request->input('oriente', 0));
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

    public function save_datos(Request $request)
    {
        $tip = $request->input('tipo');
        if ($tip > 0) {
            $des = explode('/', $request->input('fechaInicio'));
            $has = explode('/', $request->input('fechaFin'));
            $desde = $des[2] . '-' . $des[1] . '-' . $des[0];
            $hasta = $has[2] . '-' . $has[1] . '-' . $has[0];
            $data = array(
                'activo' => $request->input('activo'),
                'fechaInicio' => $desde,
                'fechaFin' => $hasta,
                'entidad' => $tip,
                'parametros' => '',
                'tipo' => 1,
                'numeroPagos' => $request->input('numeroPagos'),
                'numeroPersonas' => $request->input('numeroPersonas'),
                'monto' => $request->input('monto'),
                'cobro' => $request->input('cobro'),
                'descripcion' => $request->input('descripcion'),
                'valle' => (int) $request->input('valle', 0),
                'taller' => (int) $request->input('taller', 0),
                'usuario' => Auth::user()->id,
            );
            $res = Pagos_montos_model::insert($data);
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $des = explode('/', $request->input('fechaInicio'));
            $has = explode('/', $request->input('fechaFin'));
            $desde = $des[2] . '-' . $des[1] . '-' . $des[0];
            $hasta = $has[2] . '-' . $has[1] . '-' . $has[0];
            $data = array(
                'activo' => $request->input('activo'),
                'fechaInicio' => $desde,
                'fechaFin' => $hasta,
                'numeroPagos' => $request->input('numeroPagos'),
                'numeroPersonas' => $request->input('numeroPersonas'),
                'monto' => $request->input('monto'),
                'cobro' => $request->input('cobro'),
                'descripcion' => $request->input('descripcion'),
                'valle' => $request->input('valle'),
                'taller' => $request->input('taller'),
                'usuario' => Auth::user()->id,
                'fechaModificacion' => date('Y-m-d H:i:s'),
            );
            $resu = Pagos_montos_model::where('idMonto', (int) $id)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Datos actualizados correctamente']);
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Identificador no valido']);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Pagos_montos_model::where("idMonto", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => 'Borrado correcto de Dato']);
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Ocurrio un error al borrar los datos']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Identificador no valido']);
        }
    }
    public function get_reporte(Request $request)
    {
        $id = $request->input('idm', 0);
        $qry = Pagos_montos_model::getReporte($id);
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }
}

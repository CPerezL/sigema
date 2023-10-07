<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Cuentas_entidades_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Mecom_pagosnet_cuentas extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '025';
    public $controlador = 'mecom_pagosnet_cuentas';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        $data['talleres'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina     */
        return view('mecom.pagosnet_cuentas', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Cuentas_entidades_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function update_datos(Request $request)
    {
        $id = $request->input('idEntidad', 0);
        if ($id > 0) {
            $data = array(
                'cuenta' => $request->input('cuenta'),
                'banco' => $request->input('banco'),
                'activo' => $request->input('activo'),
                'codigo' => $request->input('codigo', ''),
                'usuarioModificacion' => Auth::user()->id,
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            Logias_model::where('numero', $request->input('llave'))->update(['pagosweb' => $request->input('activo')]);
            $res = Cuentas_entidades_model::where('idEntidad', (int) $id)->update($data);
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function save_datos(Request $request)
    {
        if ($request->input('numero') > 0 || $request->input('valle') > 0) {
            if ($request->input('numero') > 0) {
                $iden = $resu = Cuentas_entidades_model::checkExiste(4, $request->input('numero'));
                if ($iden > 0) {
                    $resu = 0;
                } else {
                    $data = array(
                        'tipo' => 4,
                        'identificador' => $request->input('numero'),
                        'cuenta' => $request->input('cuenta'),
                        'banco' => $request->input('banco'),
                        'codigo' => $request->input('codigo', ''),
                        'activo' => $request->input('activo'),
                        'usuarioModificacion' => Auth::user()->id,
                    );
                    $resu = Cuentas_entidades_model::insert($data);
                }
            } elseif ($request->input('valle') > 0) {
                $iden = $resu = Cuentas_entidades_model::checkExiste(3, $request->input('valle'));
                if ($iden > 0) {
                    $resu = 0;
                } else {
                    $data = array(
                        'tipo' => 3,
                        'identificador' => $request->input('valle'),
                        'cuenta' => $request->input('cuenta'),
                        'banco' => $request->input('banco'),
                        'codigo' => $request->input('codigo', ''),
                        'activo' => 1,
                        'usuarioModificacion' => Auth::user()->id,
                    );
                    $resu = Cuentas_entidades_model::insert($data);
                }
            }

            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Cuentas_entidades_model::where("idEntidad", $id)->delete();
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

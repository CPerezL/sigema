<?php

namespace App\Http\Controllers;

use App\Models\Config_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;

class Configuracion extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $idmod = '005';
    public $controlador = 'configuracion';
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
        // self::iniciarModulo();
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        return view('admin.configuracion', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Config_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function update_datos(Request $request)
    {
        $date = str_replace('/', '-', $request->input('fechaApertura'));
        $newDate = date("Y-m-d", strtotime($date));
        $date2 = str_replace('/', '-', $request->input('fechaCierre'));
        $newDate2 = date("Y-m-d", strtotime($date2));
        $gestion = $request->input('gestion');
        $meses = $request->input('mesesDeuda');
        $diasc = $request->input('diasCircular');
        if ($gestion > 0) {
            $data = array(
                'fechaApertura' => $newDate,
                'fechaCierre' => $newDate2,
                'mesesDeuda' => $meses,
                'gestion' => $gestion,
                'diasCircular'=>$diasc,
                'mesesIrregular'=>$request->input('mesesIrregular'),
                'asisAumento'=>$request->input('asisAumento'),
                'mesesAumento'=>$request->input('mesesAumento'),
                'asisExaltacion'=>$request->input('asisExaltacion'),
                'mesesExaltacion'=>$request->input('mesesExaltacion'),
                'fechaModificacion' => date('Y-m-d h:m:s'),
            );
            $resu = Config_model::where("idconfig", 1)->update($data);
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

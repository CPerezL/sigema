<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Obolos_taller_model;
use App\Models\mecom\Pagos_tipo_model;
use App\Models\sistema\Miembrostipo_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Mecom_obolos_taller extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '022';
    public $controlador = 'mecom_obolos_taller';
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
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['tpagos'] = Pagos_tipo_model::getTipoPagosArray();
        $data['tipos'] = Miembrostipo_model::getTiposArray();
        /*     variables de pagina     */
        return view('mecom.obolos_taller', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Obolos_taller_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $data['usuario'] = Auth::user()->id;
            $miembro = $request->input('miembro', 'Regular');
            $data['miembro'] = $miembro;
            $data['monto'] = $request->input('monto', 0);
            $tipo = $request->input('tipo', 1);
            $data['tipo'] = $tipo;
            if ($tipo == 1) {
                $data['concepto'] = 'Cuota ' . $miembro;
            } else {
                $data['concepto'] = $request->input('concepto', 1);
            }

            $res = Obolos_taller_model::where('idMonto', (int) $id)->update($data);
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
        $id = $request->input('idTaller', 0);
        $miembro = $request->input('miembro', 'Regular');
        $tipo = $request->input('tipo', 1);
        if (strlen($miembro) > 3 && $id > 0)
        {
            $exis=Obolos_taller_model::checkExiste($id, $miembro, $tipo);
          if ($exis>0)
          {
            $data['usuario'] = Auth::user()->id;
            $miembro = $request->input('miembro', 'Regular');
            $data['miembro'] = $miembro;
            $data['monto'] = $request->input('monto', 0);
            $tipo = $request->input('tipo', 1);
            $data['tipo'] = $tipo;
            if ($tipo == 1) {
                $data['concepto'] = 'Cuota ' . $miembro;
            } else {
                $data['concepto'] = $request->input('concepto', 1);
            }
            $res = Obolos_taller_model::where('idMonto', (int) $exis)->update($data);
          }
          else
          {
            $data['tipo'] = $tipo;
            $data['idTaller'] = $id;
            if ($tipo == 1)
              $data['concepto'] = 'Cuota '.$miembro;
            else
              $data['concepto'] = $request->input('concepto', 1);
            $data['monto'] = $request->input('monto', 0);
            $data['fechaInicio'] = '1900-01-01';
            $data['fechaFin'] = '2050-12-01';
            $data['activo'] = 1;
            $data['orden'] = 1;
            $data['miembro'] = $miembro;
            $data['usuario'] = Auth::user()->id;
            $res = Obolos_taller_model::insert($data);
          }
            if ($res > 0) {
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
            $resu = Obolos_taller_model::where("idMonto", $id)->delete();
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

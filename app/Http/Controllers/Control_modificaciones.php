<?php

namespace App\Http\Controllers;

use App\Models\admin\Control_model;
use App\Models\admin\Valles_model;
// use App\Models\mecom\Obolos_taller_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Control_modificaciones extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '027';
    public $controlador = 'control_modificaciones';
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
        /*     variables de pagina     */
        return view('admin.control_modificaciones', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Control_model::getItems($page, $rows, Session::get($this->controlador . '.valle'), Session::get($this->controlador . '.filtro'));
        $total = Control_model::getNumItems(Session::get($this->controlador . '.valle'), Session::get($this->controlador . '.filtro'));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function estado(Request $request)
    {
        $content = ['success' => 'true', 'Msg' => trans('mess.updateok')]; //default
        $id = $request->input('id', 0);
        if ($id > 0) {
            $tt = $request->input('tipo', 0);
            if ($tt > 0) {
                if ($tt == 3) {
                    $ndata['estado'] = 3;
                    $ndata['usuarioAprueba'] = Auth::user()->id;
                    $ndata['fechaModificacion'] = date('Y-m-d h:m:s');
                    $res = Control_model::where('idControl', (int) $id)->update($ndata);
                } elseif ($tt == 4) //aprueba
                {
                    $op = Control_model::apruebaEstado($id);//hace los cambios pedidos
                    if ($op > 0) {
                        $ndata['estado'] = 4;
                        $ndata['usuarioAprueba'] = Auth::user()->id;
                        $ndata['fechaModificacion'] = date('Y-m-d h:m:s');
                        $res = Control_model::where('idControl', (int) $id)->update($ndata);
                    } else {
                        $res = 0;
                    }
                } else {
                    $res = 0;
                }
                if ($res > 0) {
                    $content = ['success' => 'true', 'Msg' => trans('mess.updateok')];
                } else {
                    $content = ['success' => 0, 'Msg' => trans('mess.okchange')];
                }
            }
        }
        // $this->load->view('vista', $datas);
        return response()->json($content);
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

}

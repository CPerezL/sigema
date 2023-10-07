<?php

namespace App\Http\Controllers;

//use App\Models\sistema\Errores_model;
use App\Models\admin\Valles_model;
use App\Models\mecom\Obolos_valle_model;
use App\Models\sistema\Miembrostipo_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Mecom_obolos_valle extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '022';
    public $controlador = 'mecom_obolos_valle';
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
        $data['tipos'] = Miembrostipo_model::getTiposArray();
        /*     variables de pagina     */
        return view('mecom.obolos_valle', $data);
    }
    public function get_datos(Request $request)
    {
        $qry = Obolos_valle_model::getItems();
        $qry2 = (object) ['total' => 0, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $id = $request->input('idValle', 0);
        $miembro = $request->input('miembro', 'Regular');

        if (strlen($miembro) > 3) {
            $orden1 = Obolos_valle_model::select('orden')->where('miembro', $miembro)->where('idValle', $id)->orderby('orden', 'asc')->first('orden');

            if (is_null($orden1)) {
                $orden = 1;
            } else {
                $orden = $orden1->orden + 1;
            }
            $fecha1 = \DateTime::createFromFormat('d/m/Y', $request->input('fechaInicio', '01-01-2000'))->format('Y-m-01');
            $fecha2 = \DateTime::createFromFormat('d/m/Y', $request->input('fechaFin', '01-12-2030'))->format('Y-m-01');
            //comprobando fechas 1 y 2
            $exis = Obolos_valle_model::checkFecha($miembro, $id);

            $ini = Obolos_valle_model::checkFechaFin($fecha1, $miembro, $id);
            $fin = Obolos_valle_model::checkFechaFin($fecha2, $miembro, $id);
            $es = $ini + $fin;
            if (($es == 2 || $exis) && $fecha1 < $fecha2) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                $data['fechaInicio'] = $fecha1;
                $data['fechaFin'] = $fecha2;
                $glb = $request->input('montoglb', 0);
                $gdr = $request->input('montogdr', 0);
                $gdrweb = $request->input('montohabil', 0);
                $comap = $request->input('montocomap', 0);
                $vall = Valles_model::getVallesArray(Auth::user()->idOriente);
                $data['idValle'] = $id;
                $data['concepto'] = 'Cuota ' . $miembro;
                $data['miembro'] = $miembro;
                $data['usuario'] = Auth::user()->id;
                $data['tipo'] = 1;
                $data['orden'] = $orden;
                if ($id > 0) {
                    $data['valle'] = $vall[$id];
                } else {
                    $data['valle'] = 'General';
                }
                //----GLB
                $data['entidad'] = 1;
                $data['monto'] = $glb;
                $data['montohabil'] = $glb;
                $res1 = Obolos_valle_model::insert($data);
                //---  GDR
                $data['monto'] = $gdr;
                $data['montohabil'] = $gdrweb;
                $data['entidad'] = 2;
                $res2 = Obolos_valle_model::insert($data);
                //---- COMAP
                $data['monto'] = $comap;
                $data['montohabil'] = $comap;
                $data['entidad'] = 3;
                $res3 = Obolos_valle_model::insert($data);
                $res = $res1 + $res2 + $res3;
                if ($res > 0) {
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            } else {
                if ($ini == 1) {
                    return response()->json(['success' => 0, 'Msg' => 'La fecha Final debe ser mayor, corrija']);
                } elseif ($fin == 1) {
                    return response()->json(['success' => 0, 'Msg' => 'La fecha Inicio debe ser mayor, corrija']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => 'Las dos fechas estan fuera de rango, corrija']);
                }
            }

        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errdata')]);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('idValle', 0);
        $miembro = $request->input('miembro', 'Regular');
        if (strlen($miembro) > 3) {
            $glb = $request->input('montoglb', 0);
            $gdr = $request->input('montogdr', 0);
            $gdrweb = $request->input('montohabil', 0);
            $comap = $request->input('montocomap', 0);
            $fecha1 = \DateTime::createFromFormat('d/m/Y', $request->input('fechaInicio', '01-01-2000'))->format('Y-m-01');
            $fecha2 = \DateTime::createFromFormat('d/m/Y', $request->input('fechaFin', '01-12-2030'))->format('Y-m-01');
            $data['fechaInicio'] = $fecha1;
            $data['fechaFin'] = $fecha2;
            $data['usuario'] = Auth::user()->id;
            //----montos
            $data['monto'] = $glb;
            $data['montohabil'] = $glb;
            $orden = $request->input('orden', 1);
            $res1 = Obolos_valle_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 1)->update($data);
            $data['monto'] = $gdr;
            $data['montohabil'] = $gdrweb;
            $res2 = Obolos_valle_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 2)->update($data);
            $data['montohabil'] = $comap;
            $data['monto'] = $comap;
            $res3 = Obolos_valle_model::where('idValle', (int) $id)->where('orden', $orden)->where('miembro', $miembro)->where('entidad', 3)->update($data);
            $resu = $res1 + $res2 + $res3;
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
        $miembro = $request->input('miembro', '');
        $ord = $request->input('ord', 1);
        if (strlen($miembro) > 3) {
            $resu = Obolos_valle_model::where("idValle", $id)->where("miembro", $miembro)->where("orden", $ord)->delete();
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

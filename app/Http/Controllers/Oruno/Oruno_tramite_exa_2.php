<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;

use App\Models\admin\Logias_model;
use App\Models\oruno\Exaltacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_exa_2 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '084';
    public $controlador = 'oruno_tramite_exa_2';
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
        $data['_folder'] = url('/') . '/';
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('oruno.exa_2', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Exaltacion_model::getRegistros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 0);
        $total = Exaltacion_model::getNumRegistros(Session::get($this->controlador . '.palabra', ''), $log, $val, 0);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('id', 0);
        if ($tramite > 0) {
            $salida = Exaltacion_model::getTramite($tramite);
            if (is_null($salida)) {
                $salida = Exaltacion_model::getAumTramite($tramite);
            }
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'No encontrado"']);
        }
    }

    public function update_tramite(Request $request)
    {
        $id = $request->input('idMiembro', 0);
        if ($id > 0) {
            $okini = $request->input('okIniciacion', '0');
            $okaum = $request->input('okAumento', '0');
            $oksol = $request->input('okSolicitud', '0');
            $okexa = $request->input('okExamen', '0');
            $okasi = $request->input('okAsistencia', '0');
            $oktra = $request->input('okTrabajos', '0');
            $okex = $request->input('okComision', '0');
            $sum = $okasi + $okini + $oksol + $okexa + $oktra + $okaum + $okex;
            $datas = array(
                'okIniciacion' => $okini,
                'okAumento' => $okaum,
                'okSolicitud' => $oksol,
                'okExamen' => $okexa,
                'okAsistencia' => $okasi,
                'okTrabajos' => $oktra,
                'okComision' => $okex,
                'fechaModificacion' => date('Y-m-d')
            );
            if ($sum == 7) {
                $datas['nivelActual'] = 1;
                $salida = ['success' => 'true', 'Msg' => 'Tramite validado correctamente'];
            } else {
                $datas['nivelActual'] = 0;
                $salida = ['success' => 0, 'Msg' => 'Aun falta revisar documentacion'];
            }
            Exaltacion_model::where('idMiembro', $id)->update($datas);
        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos incompletos'];
        }

        return response()->json($salida);
    }
}

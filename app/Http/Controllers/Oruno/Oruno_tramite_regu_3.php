<?php

namespace App\Http\Controllers\Oruno;
use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\oruno\Regularizacion_circulares_model;
use App\Models\oruno\Regularizacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_regu_3 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '081';
    public $controlador = 'oruno_tramite_regu_3';
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
        $data['palabra'] = Session::get($this->controlador . '.palabra', '');
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('oruno.regu_3', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Regularizacion_model::getRegistros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 2);
        $total = Regularizacion_model::getNumRegistros(Session::get($this->controlador . '.palabra', ''), $log, $val, 2);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_circular(Request $request)
    {
        $ida = $request->input('idsTram', '');
        $ok = $request->input('okcircular', 0);
        $ids = explode(',', $ida);
        $numero = count($ids);
        $valle = Logias_model::select('valle')->firstWhere('numero', $numero)->valle;
        if (strlen($ida) > 0 && $numero > 0 && $ok > 0) {
            $data = array(
                'circular' => '',
                'contenido' => $ida,
                'numero' => $numero,
                'valle' => $valle,//revisar este bug
                'fecha' => date('Y-m-d'),
            );
            $ncircular = Regularizacion_circulares_model::insertGetId($data);
            $circular = 'C-R-' . $ncircular . '-' . date('Y');
            $datac = array(
                'circular' => $circular,
            );
            $hecho = Regularizacion_circulares_model::where('id', $ncircular)->update($datac); //actualiza el numero de circular
            if ($ncircular > 0 && $hecho > 0) {
                foreach ($ids as $tram) {
                    $datat = array(
                        'numCircular' => $ncircular,
                        'nivelActual' => 3,
                        'fechaCircular' => date('Y-m-d'),
                        'fechaModificacion' => date('Y-m-d'),
                    );
                    Regularizacion_model::where('idTramite', $tram)->update($datat);
                }
                $salida = ['success' => 'true', 'Msg' => 'Datos actualizados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }
        return response()->json($salida);
    }
}

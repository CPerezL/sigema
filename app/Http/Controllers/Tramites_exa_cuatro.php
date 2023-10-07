<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\tramites\Exaltacion_model;
use App\Models\tramites\Ceremonias_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Response;
use Session;

class Tramites_exa_cuatro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '092';
    public $controlador = 'tramites_exa_cuatro';
    public $tipoqr = 4;
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
        $data['_folder'] = url('/');
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina     */
        return view('tramites.exa_cuatro', $data);
    }
    public function get_datos(Request $request)
    {
        $palabra = Session::get($this->controlador . '.palabra');
        $pago = Session::get($this->controlador . '.pago');
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $salida = Exaltacion_model::getCeremonias($page, $rows, $palabra, $valle, $taller, $pago);
        $total = Exaltacion_model::getNumCeremonias($palabra, $valle, $taller, $pago);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_ceremonia(Request $request)
    {
        $cere = $request->input('id');
        if ($cere > 0) {
            $salida = Exaltacion_model::getCeremoniaExa($cere);
        } else {
            $salida = ['success' => 0, 'Msg' => 'Error'];
        }
        return response()->json($salida);
    }
    public function update_ceremonia(Request $request)
    {
        $idcer = $request->input('id', '0');
        if ($idcer > 0) {
            $id1 = $request->input('idMiembro1', '0');
            $id2 = $request->input('idMiembro2', '0');
            $id3 = $request->input('idMiembro3', '0');
            $id4 = $request->input('idMiembro4', '0');
            $datas = array(
                'nivelActual' => '2',
                'fechaModificacion' => date('Y-m-d'),
            );
            if ($id1 > 0) {
                Exaltacion_model::where('idMiembro', $id1)->update($datas);
            }
            if ($id2 > 0) {
                Exaltacion_model::where('idMiembro', $id2)->update($datas);
            }
            if ($id3 > 0) {
                Exaltacion_model::where('idMiembro', $id3)->update($datas);
            }
            if ($id4 > 0) {
                Exaltacion_model::where('idMiembro', $id4)->update($datas);
            }
            $datasc = array(
                'okCeremonia' => $request->input('okCeremonia', ''),
                'fechaModificacion' => date('Y-m-d'),
            );
            $ok = Ceremonias_model::where('idCeremonia', $idcer)->update($datasc); //devuleve id de la ceremonia
            if ($ok > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Ceremonia autorizada correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Error'];
            }
        } else {
            $salida = ['success' => 0, 'Msg' => 'Ningun tramite en lista'];
        }
        return response()->json($salida);
    }
}
